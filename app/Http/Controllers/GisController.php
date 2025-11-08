<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Map;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SubBlock;
use Illuminate\Support\Facades\File;

class GisController extends Controller
{
    /**
     * Display a listing of the maps.
     */
    public function index(Request $request)
    {
        $focusPetak = $request->query('focus');
        $focusHarvest = $request->query('focusHarvest');

        $subBlocksData = DB::table('sub_blocks')
            ->select('kode_petak', 'divisi', 'luas_area', 'geom_json')
            ->get();

        $subblocks = $subBlocksData; // Keep the original query for backward compatibility

        $features = [];
        foreach ($subBlocksData as $item) {
            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'kode_petak'   => $item->kode_petak,
                    'divisi'       => $item->divisi,
                    'luas_area' => $item->luas_area
                ],
                'geometry' => json_decode($item->geom_json)
            ];
        }


        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $features
        ];

        // Get harvest sub-blocks data with status information
        $harvestSubBlocks = DB::table('harvest_sub_blocks')
            ->leftJoin('sub_blocks', 'harvest_sub_blocks.kode_petak', '=', 'sub_blocks.kode_petak')
            ->leftJoin('tracking_activity', 'harvest_sub_blocks.kode_petak', '=', 'tracking_activity.kode_petak')
            ->select(
                'harvest_sub_blocks.kode_petak',
                'sub_blocks.divisi',
                'sub_blocks.luas_area',
                'sub_blocks.geom_json',
                'harvest_sub_blocks.harvest_season',
                'harvest_sub_blocks.planned_harvest_date',
                'tracking_activity.status_tracking'
            )
            ->get();

        $harvestFeatures = [];
        foreach ($harvestSubBlocks as $item) {
            // Determine status based on tracking_activity.status_tracking
            $status = $item->status_tracking ?? 'planned';
            
            // Ensure the status is one of the valid values
            if (!in_array($status, ['not_started', 'in_progress', 'completed'])) {
                $status = 'planned';
            }

            $harvestFeatures[] = [
                'type' => 'Feature',
                'properties' => [
                    'kode_petak' => $item->kode_petak,
                    'divisi' => $item->divisi,
                    'luas_area' => $item->luas_area,
                    'harvest_season' => $item->harvest_season,
                    'planned_harvest_date' => $item->planned_harvest_date,
                    'status' => $status
                ],
                'geometry' => json_decode($item->geom_json)
            ];
        }

        $harvestGeojson = [
            'type' => 'FeatureCollection',
            'features' => $harvestFeatures
        ];

        // Ambil file GeoJSON tambahan dari storage (misalnya tebang.geojson)
        $tebangPath = storage_path('app/public/geojson/tebang.geojson');
        $tebangGeojson = file_exists($tebangPath) ? file_get_contents($tebangPath) : json_encode([
            'type' => 'FeatureCollection',
            'features' => []
        ]);

        return view('backend.gis.index', [
            'geojson' => json_encode($geojson),
            'harvestGeojson' => json_encode($harvestGeojson),
            'tebangGeojson' => $tebangGeojson,
            'subBlocksData' => $subBlocksData,
            'focusPetak' => $focusPetak,
            'focusHarvest' => $focusHarvest
        ]);

    }

    /**
     * Remove the specified map from storage.
     */
    public function destroy($id)
    {
        try {
            $map = Map::findOrFail($id);

            // Delete the file from storage
            if (Storage::disk('public')->exists($map->file_path)) {
                Storage::disk('public')->delete($map->file_path);
            }

            $map->delete();

            // Check if the request is AJAX
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Peta berhasil dihapus!'
                ]);
            }

            return redirect()->route('gis.create')
                ->with('success', 'Peta berhasil dihapus!');

        } catch (\Exception $e) {
            // Check if the request is AJAX
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus peta: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->with('error', 'Gagal menghapus peta: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $maps = Map::latest()->paginate(10);
        return view('backend.gis.create', compact('maps'));
    }

    public function store(Request $request)
    {
        // Define valid estate options
        $validEstates = ['LKL', 'PLG', 'RST'];
        
        // Validate the request
        $request->validate([
            'geojson_file' => 'required|file|mimes:json,geojson',
            'uploaded_by' => 'required|string',
            'estate_name' => [
                'required',
                'string',
                'in:' . implode(',', $validEstates),
            ],
            'description' => 'nullable|string',
            'update_existing' => 'sometimes|boolean',
        ], [
            'estate_name.required' => 'Nama estate wajib diisi.',
            'estate_name.in' => 'Pilihan estate tidak valid.',
        ]);

        $file = $request->file('geojson_file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('public/geojson', $filename);

        // Save the file info to maps table
        $map = Map::create([
            'file_name' => $filename,
            'file_path' => $path,
            'file_type' => 'geojson',
            'uploaded_by' => $request->uploaded_by,
            'estate_name' => $request->estate_name,
            'description' => $request->description,
            'upload_date' => now()->toDateString(),
        ]);

        // Process the GeoJSON file to import/update sub-blocks
        try {
            $content = file_get_contents($file->getRealPath());
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('File is not a valid JSON');
            }

            if (!isset($data['type']) || $data['type'] !== 'FeatureCollection' || !isset($data['features'])) {
                throw new \Exception('Invalid GeoJSON format: Must be a FeatureCollection');
            }

            $imported = 0;
            $updated = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($data['features'] as $index => $feature) {
                try {
                    $properties = isset($feature['properties']) ? $feature['properties'] : [];
                    $geometry = isset($feature['geometry']) ? $feature['geometry'] : [];

                    // Validate required fields
                    if (empty($properties['kode_petak'])) {
                        $errors[] = "Fitur #$index: kode_petak tidak ditemukan";
                        continue;
                    }

                    $subBlockData = [
                        'kode_petak' => $properties['kode_petak'],
                        'estate' => isset($properties['estate']) ? $properties['estate'] : $request->estate_name,
                        'divisi' => isset($properties['divisi']) ? $properties['divisi'] : null,
                        'blok' => isset($properties['blok']) ? $properties['blok'] : null,
                        'luas_area' => isset($properties['luas_area']) ? $properties['luas_area'] : null,
                        'age_months' => isset($properties['age_months']) ? $properties['age_months'] : null,
                        'zona' => isset($properties['zona']) ? $properties['zona'] : null,
                        'keterangan' => isset($properties['keterangan']) ? $properties['keterangan'] : null,
                        'geom_json' => $geometry,
                        'aktif' => isset($properties['aktif']) ? $properties['aktif'] : true,
                    ];

                    if ($request->boolean('update_existing')) {
                        // Update existing or create new
                        $subBlock = SubBlock::updateOrCreate(
                            ['kode_petak' => $subBlockData['kode_petak']],
                            $subBlockData
                        );
                        $updated++;
                    } else {
                        // Only create new, skip if exists
                        if (SubBlock::where('kode_petak', $subBlockData['kode_petak'])->exists()) {
                            $errors[] = "Petak {$subBlockData['kode_petak']}: Sudah ada di database";
                            continue;
                        }
                        SubBlock::create($subBlockData);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $kodePetak = isset($properties['kode_petak']) ? $properties['kode_petak'] : 'unknown';
                    $errors[] = "Fitur #$index ($kodePetak): " . $e->getMessage();
                    continue;
                }
            }

            DB::commit();

            $message = "File berhasil diupload. ";
            if ($imported > 0) $message .= "$imported data baru diimpor. ";
            if ($updated > 0) $message .= "$updated data diperbarui. ";
            if (!empty($errors)) {
                $message .= count($errors) . " error ditemukan.";
                session()->flash('import_errors', $errors);
            }

            return redirect()->route('gis.create')
                ->with('success', trim($message));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('gis.create')
                ->with('error', 'Gagal memproses file GeoJSON: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified map.
     */
    public function edit($id)
    {
        $map = Map::findOrFail($id);
        return view('backend.gis.edit', compact('map'));
    }

    /**
     * Update the specified map in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'geojson_file' => 'nullable|file|mimes:json,geojson',
            'uploaded_by' => 'required|string',
            'estate_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $map = Map::findOrFail($id);
        $data = [
            'uploaded_by' => $request->uploaded_by,
            'estate_name' => $request->estate_name,
            'description' => $request->description,
            'updated_at' => now(),
        ];

        // Handle file update if a new file is uploaded
        if ($request->hasFile('geojson_file')) {
            // Delete old file
            if (Storage::disk('public')->exists($map->file_path)) {
                Storage::disk('public')->delete($map->file_path);
            }

            // Store new file
            $file = $request->file('geojson_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/geojson', $filename);

            $data['file_name'] = $filename;
            $data['file_path'] = $path;
        }

        $map->update($data);

        return redirect()->route('gis.create')
            ->with('success', 'Data peta berhasil diperbarui.');
    }

    /**
     * Handle chunked file upload
     */
    public function uploadChunk(Request $request)
    {
        try {
            $file = $request->file('file');
            $chunkNumber = $request->input('chunkNumber');
            $totalChunks = $request->input('totalChunks');
            $fileId = $request->input('fileId');
            $fileName = $request->input('fileName');

            // Create temp directory if it doesn't exist
            $tempDir = storage_path('app/temp_uploads/' . $fileId);
            if (!file_exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            // Save the chunk
            $chunkPath = $tempDir . '/' . $chunkNumber;
            File::put($chunkPath, file_get_contents($file->getRealPath()));

            return response()->json([
                'success' => true,
                'chunk' => $chunkNumber,
                'message' => 'Chunk uploaded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading chunk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Merge uploaded chunks and process the file
     */
    public function mergeChunks(Request $request)
    {
        $fileId = $request->input('fileId');
        $fileName = $request->input('fileName');
        $tempDir = storage_path('app/temp_uploads/' . $fileId);
        $finalPath = '';
        $file = null;
        
        try {
            // Create final uploads directory if it doesn't exist
            $finalDir = storage_path('app/public/geojson');
            if (!file_exists($finalDir)) {
                File::makeDirectory($finalDir, 0755, true);
            }
            
            $finalPath = $finalDir . '/' . $fileId . '_' . $fileName;
            
            // Open final file for writing
            $out = fopen($finalPath, 'wb');
            if (!$out) {
                throw new \Exception('Gagal membuat file output');
            }
            
            // Get and sort chunk files
            $chunks = glob($tempDir . '/*');
            if (empty($chunks)) {
                throw new \Exception('Tidak ada bagian file yang ditemukan untuk digabungkan');
            }
            
            sort($chunks, SORT_NUMERIC);
            
            // Combine chunks
            foreach ($chunks as $chunk) {
                $in = fopen($chunk, 'rb');
                if (!$in) {
                    throw new \Exception('Gagal membuka bagian file: ' . $chunk);
                }
                
                while ($buff = fread($in, 4096)) {
                    if (fwrite($out, $buff) === false) {
                        fclose($in);
                        throw new \Exception('Gagal menulis ke file output');
                    }
                }
                
                fclose($in);
                
                // Delete chunk after merging
                if (!unlink($chunk)) {
                    Log::warning("Gagal menghapus file chunk: $chunk");
                }
            }
            
            fclose($out);
            
            // Remove temp directory
            if (!@rmdir($tempDir)) {
                Log::warning("Gagal menghapus direktori sementara: $tempDir");
            }
            
            // Validate the uploaded file
            if (!file_exists($finalPath)) {
                throw new \Exception('File hasil penggabungan tidak ditemukan');
            }
            
            // Process the uploaded file
            $file = new \Illuminate\Http\UploadedFile(
                $finalPath,
                $fileName,
                mime_content_type($finalPath),
                null,
                true // Mark as test to prevent moving the file again
            );
            
            // Initialize counters and error collection
            $imported = 0;
            $updated = 0;
            $errors = [];
            
            // First, validate the file content
            $content = file_get_contents($file->getRealPath());
            if ($content === false) {
                throw new \Exception('Gagal membaca isi file');
            }
            
            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('File bukan format JSON yang valid');
            }

            if (!isset($data['type']) || $data['type'] !== 'FeatureCollection' || !isset($data['features'])) {
                throw new \Exception('Format GeoJSON tidak valid: Harus berupa FeatureCollection');
            }
            
            // Validate features array
            if (!is_array($data['features']) || empty($data['features'])) {
                throw new \Exception('File GeoJSON tidak berisi data fitur (features)');
            }

            // Start transaction for database operations
            DB::beginTransaction();

            // First, process all features and collect data
            $subBlocksToCreate = [];
            $subBlocksToUpdate = [];
            $shouldUpdate = $request->boolean('update_existing');
            $estateName = $request->input('estate_name');

            // Validate all features before making any changes
            foreach ($data['features'] as $index => $feature) {
                try {
                    $properties = $feature['properties'] ?? [];
                    $geometry = $feature['geometry'] ?? [];

                    // Validate required fields
                    if (empty($properties['kode_petak'])) {
                        $errors[] = "Fitur #$index: kode_petak tidak ditemukan";
                        continue;
                    }

                    $subBlockData = [
                        'kode_petak' => $properties['kode_petak'],
                        'estate' => $properties['estate'] ?? $estateName,
                        'divisi' => $properties['divisi'] ?? null,
                        'blok' => $properties['blok'] ?? null,
                        'luas_area' => $properties['luas_area'] ?? null,
                        'age_months' => $properties['age_months'] ?? null,
                        'zona' => $properties['zona'] ?? null,
                        'keterangan' => $properties['keterangan'] ?? null,
                        'geom_json' => $geometry,
                        'aktif' => $properties['aktif'] ?? true,
                    ];

                    if ($shouldUpdate) {
                        $subBlocksToUpdate[] = $subBlockData;
                    } else if (!SubBlock::where('kode_petak', $subBlockData['kode_petak'])->exists()) {
                        $subBlocksToCreate[] = $subBlockData;
                    } else {
                        $errors[] = "Petak {$subBlockData['kode_petak']}: Sudah ada di database";
                    }
                } catch (\Exception $e) {
                    $kodePetak = $properties['kode_petak'] ?? 'unknown';
                    $errors[] = "Fitur #$index ($kodePetak): " . $e->getMessage();
                    continue;
                }
            }

            // If there are any errors and no successful operations, throw an exception
            if (empty($subBlocksToCreate) && empty($subBlocksToUpdate) && !empty($errors)) {
                throw new \Exception(implode("\n", $errors));
            }

            // Process updates
            foreach ($subBlocksToUpdate as $subBlockData) {
                try {
                    $kodePetak = $subBlockData['kode_petak'];
                    unset($subBlockData['kode_petak']);
                    SubBlock::updateOrCreate(
                        ['kode_petak' => $kodePetak],
                        $subBlockData
                    );
                    $updated++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal memperbarui petak {$subBlockData['kode_petak']}: " . $e->getMessage();
                }
            }

            // Process new entries
            foreach ($subBlocksToCreate as $subBlockData) {
                try {
                    SubBlock::create($subBlockData);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal membuat petak {$subBlockData['kode_petak']}: " . $e->getMessage();
                }
            }

            // Only if we have successful operations, save the file info to database
            if ($imported > 0 || $updated > 0) {
                Map::create([
                    'file_name' => $fileId . '_' . $fileName,
                    'file_path' => 'public/geojson/' . $fileId . '_' . $fileName,
                    'file_type' => 'geojson',
                    'uploaded_by' => $request->input('uploaded_by'),
                    'estate_name' => $estateName,
                    'description' => $request->input('description'),
                    'upload_date' => now()->toDateString(),
                ]);
            }

            // If we get here, commit all database changes
            DB::commit();

            $message = "File berhasil diproses. ";
            if ($imported > 0) $message .= "$imported data baru diimpor. ";
            if ($updated > 0) $message .= "$updated data diperbarui. ";
            if (!empty($errors)) {
                $message .= count($errors) . " error ditemukan.";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'updated' => $updated,
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            // Rollback any database changes
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            // Clean up files on error
            if (!empty($finalPath) && file_exists($finalPath)) {
                @unlink($finalPath);
            }
            
            // Clean up temp directory if it exists
            if (isset($tempDir) && is_dir($tempDir)) {
                array_map('unlink', glob("$tempDir/*"));
                @rmdir($tempDir);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage(),
                'error_details' => $e->getMessage()
            ], 500);
        }
    }
}
