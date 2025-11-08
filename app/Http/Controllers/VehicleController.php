<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Vendor;
use App\Models\JenisUnit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exports\VehicleExport;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $vehicles = Vehicle::with('vendor')
            ->when($search, function($query) use ($search) {
                $query->where('kode_lambung', 'like', "%{$search}%")
                      ->orWhere('plat_nomor', 'like', "%{$search}%")
                      ->orWhereHas('vendor', function($q) use ($search) {
                          $q->where('nama_vendor', 'like', "%{$search}%");
                      });
            })
            ->orderBy('kode_vendor')
            ->orderBy('kode_lambung')
            ->paginate(15)
            ->withQueryString();

        // Set breadcrumb for vehicle list page
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => url('/')],
            ['title' => 'List Kendaraan Vendor']
        ];

        return view('vehicles.vehicle-list', compact('vehicles', 'breadcrumb'));
    }

    public function vendorVehicleList(Request $request)
    {
        $search = $request->input('search');

        $vehicles = Vehicle::with('vendor')
            ->when($search, function($query) use ($search) {
                $query->where('kode_lambung', 'like', "%{$search}%")
                      ->orWhere('plat_nomor', 'like', "%{$search}%")
                      ->orWhereHas('vendor', function($q) use ($search) {
                          $q->where('nama_vendor', 'like', "%{$search}%");
                      });
            })
            ->orderBy('kode_vendor')
            ->orderBy('kode_lambung')
            ->paginate(15)
            ->withQueryString();

        // Set breadcrumb for vendor vehicle list page
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => url('/')],
            ['title' => 'List Kendaraan Vendor']
        ];

        return view('vehicles.vehicle-list', compact('vehicles', 'breadcrumb'));
    }

    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        return Excel::download(new \App\Exports\VehicleExport($search), 'kendaraan-vendor-' . date('Ymd_His') . '.xlsx');
    }

    public function create()
    {
        // Get only vendors with kode_vendor starting with 'VA' (Vendor Angkut)
        $vendors = Vendor::where('kode_vendor', 'LIKE', 'VA%')
            ->select('kode_vendor', 'nama_vendor')
            ->orderBy('kode_vendor')
            ->get();

        // Get all jenis_units for the dropdown
        $jenisUnits = JenisUnit::orderBy('jenis_unit')->get();

        // Add default vendor for testing if no vendors found
        if ($vendors->isEmpty()) {
            $vendors = collect([
                ['kode_vendor' => 'VA00001', 'nama_vendor' => 'Vendor Angkut 1'],
                ['kode_vendor' => 'VA00002', 'nama_vendor' => 'Vendor Angkut 2'],
                ['kode_vendor' => 'VA00003', 'nama_vendor' => 'Vendor Angkut 3']
            ]);
        }

        // Generate next kode_lambung
        $year = date('y'); // Get last 2 digits of current year
        $lastVehicle = Vehicle::where('kode_lambung', 'LIKE', 'JBM-' . $year . '-V%')
            ->orderBy('kode_lambung', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastVehicle) {
            // Extract the number from the last kode_lambung
            preg_match('/V(\d+)$/', $lastVehicle->kode_lambung, $matches);
            if (isset($matches[1])) {
                $nextNumber = (int)$matches[1] + 1;
            }
        }

        $kodeLambung = 'JBM-' . $year . '-V' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Set breadcrumb for create vehicle page
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => url('/')],
            ['title' => 'Kendaraan', 'url' => route('vehicles.index')],
            ['title' => 'Tambah Kendaraan']
        ];

        return view('vehicles.vehicle-create', compact('vendors', 'jenisUnits', 'kodeLambung', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'kode_vendor' => 'required',
            'kode_lambung' => 'nullable|unique:vehicle,kode_lambung',
            'plat_nomor' => [
                'nullable',
                'unique:vehicle,plat_nomor',
                function ($attribute, $value, $fail) {
                    if (is_numeric($value)) {
                        $fail('Nomor polisi tidak boleh hanya berisi angka.');
                    }
                },
            ],
            'id_jenis_unit' => 'required|exists:jenis_units,id'
        ], [
            'kode_lambung.unique' => 'Kode lambung sudah digunakan oleh kendaraan lain',
            'plat_nomor.unique' => 'Nomor polisi sudah digunakan oleh kendaraan lain',
            'id_jenis_unit.required' => 'Jenis unit wajib dipilih',
            'id_jenis_unit.exists' => 'Jenis unit tidak valid',
            'required' => 'Field ini wajib diisi'
        ]);

        // Get vendor data
        $vendor = Vendor::where('kode_vendor', $validated['kode_vendor'])->first();

        if (!$vendor) {
            return back()->withInput()->withErrors([
                'kode_vendor' => 'Kode vendor tidak ditemukan'
            ]);
        }

        // Get jenis unit data
        $jenisUnit = JenisUnit::find($validated['id_jenis_unit']);

        if (!$jenisUnit) {
            return back()->withInput()->withErrors([
                'id_jenis_unit' => 'Jenis unit tidak ditemukan'
            ]);
        }

        // Prepare data
        $vehicleData = [
            'kode_lambung' => $validated['kode_lambung'],
            'plat_nomor' => $validated['plat_nomor'],
            'id_jenis_unit' => $validated['id_jenis_unit'],
            'kode_vendor' => $validated['kode_vendor'],
            'nama_vendor' => $vendor->nama_vendor
        ];

        try {
            // Create vehicle
            $vehicle = Vehicle::create($vehicleData);

            // Redirect with success message
            return redirect()->route('vendor.vehicle.list')
                ->with('success', 'Vehicle berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('Error saving vehicle: ' . $e->getMessage());

            // Handle specific database errors
            if ($e instanceof \Illuminate\Database\QueryException) {
                // Handle unique constraint violation
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $field = str_contains($e->getMessage(), 'kode_lambung') ? 'kode_lambung' : 'plat_nomor';
                    return back()->withInput()->withErrors([
                        $field => 'Kode lambung atau nomor polisi sudah digunakan oleh kendaraan lain'
                    ]);
                }
            }

            // Return with error message
            return back()->withInput()->withErrors([
                'error' => 'Terjadi kesalahan saat menyimpan data'
            ]);
        }
    }

    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // Get the next available kode_lambung for reference
        $lastVehicle = Vehicle::where('kode_lambung', 'LIKE', 'JBM-__-V%')
            ->orderBy('kode_lambung', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastVehicle && preg_match('/V(\d+)$/', $lastVehicle->kode_lambung, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }

        $year = date('y');
        $nextKodeLambung = 'JBM-' . $year . '-V' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Get only vendors with kode_vendor starting with 'VA' (Vendor Angkut)
        $vendors = Vendor::where('kode_vendor', 'LIKE', 'VA%')
            ->orderBy('kode_vendor')
            ->get();

        // Get all jenis_units for the dropdown
        $jenisUnits = JenisUnit::orderBy('jenis_unit')->get();

        // Set breadcrumb for edit vehicle page
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => url('/')],
            ['title' => 'Kendaraan', 'url' => route('vehicles.index')],
            ['title' => 'Edit Kendaraan']
        ];

        return view('vehicles.vehicle-edit', compact('vehicle', 'vendors', 'jenisUnits', 'breadcrumb'));
    }

    public function update(Request $request, $id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            // Validate input
            $validated = $request->validate([
                'kode_vendor' => 'required',
                'kode_lambung' => 'nullable|unique:vehicle,kode_lambung,' . $id,
                'plat_nomor' => [
                    'nullable',
                    'unique:vehicle,plat_nomor,' . $id,
                    function ($attribute, $value, $fail) {
                        if (is_numeric($value)) {
                            $fail('Nomor polisi tidak boleh hanya berisi angka.');
                        }
                    },
                ],
                'id_jenis_unit' => 'required|exists:jenis_units,id'
            ], [
                'kode_lambung.unique' => 'Kode lambung sudah digunakan oleh kendaraan lain',
                'plat_nomor.unique' => 'Nomor polisi sudah digunakan oleh kendaraan lain',
                'id_jenis_unit.required' => 'Jenis unit wajib dipilih',
                'id_jenis_unit.exists' => 'Jenis unit tidak valid',
                'required' => 'Field ini wajib diisi'
            ]);

            // Get vendor data
            $vendor = Vendor::where('kode_vendor', $validated['kode_vendor'])->first();

            if (!$vendor) {
                return back()->withInput()->withErrors([
                    'kode_vendor' => 'Kode vendor tidak ditemukan'
                ]);
            }

            // Get jenis unit data
            $jenisUnit = JenisUnit::find($validated['id_jenis_unit']);

            if (!$jenisUnit) {
                return back()->withInput()->withErrors([
                    'id_jenis_unit' => 'Jenis unit tidak ditemukan'
                ]);
            }

            // Update vehicle data
            $vehicle->update([
                'kode_vendor' => $validated['kode_vendor'],
                'nama_vendor' => $vendor->nama_vendor,
                'kode_lambung' => $validated['kode_lambung'],
                'plat_nomor' => $validated['plat_nomor'],
                'id_jenis_unit' => $validated['id_jenis_unit']
            ]);

            return redirect()->route('vendor.vehicle.list')
                ->with('success', 'Vehicle berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('Error updating vehicle: ' . $e->getMessage());

            // Handle specific database errors
            if ($e instanceof \Illuminate\Database\QueryException) {
                // Handle unique constraint violation
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $field = str_contains($e->getMessage(), 'kode_lambung') ? 'kode_lambung' : 'plat_nomor';
                    return back()->withInput()->withErrors([
                        $field => 'Kode lambung atau nomor polisi sudah digunakan oleh kendaraan lain'
                    ]);
                }
            }

            // Return with error message
            return back()->withInput()->withErrors([
                'error' => 'Terjadi kesalahan saat memperbarui data'
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            // Check if this vehicle is being used in LKT or other related tables
            $isUsedInLkt = \DB::table('lkt')
                ->where('kode_driver', $vehicle->kode_lambung)
                ->exists();

            if ($isUsedInLkt) {
                $errorMessage = 'Tidak dapat menghapus kendaraan karena data kendaraan ini masih terdaftar dalam sistem LKT.';
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 422);
                }
                return back()->with('error', $errorMessage);
            }

            $vehicle->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data kendaraan berhasil dihapus!'
                ]);
            }

            return redirect()->route('vendor.vehicle.list')
                ->with('success', 'Data kendaraan berhasil dihapus!');

        } catch (\Exception $e) {
            // More specific error message for database errors
            $errorMessage = 'Tidak dapat menghapus data kendaraan. ';

            // Check for specific database errors and provide more helpful messages
            if (str_contains($e->getMessage(), 'Unknown column')) {
                if (str_contains($e->getMessage(), 'kode_lambung')) {
                    $errorMessage = 'Terdapat masalah dengan struktur database. Kolom kode_lambung tidak ditemukan.';
                } else {
                    $errorMessage .= 'Terdapat masalah dengan struktur database. ';
                }
                $errorMessage .= 'Mohon hubungi tim IT untuk bantuan lebih lanjut.';
            } elseif (str_contains($e->getMessage(), 'SQLSTATE')) {
                $errorMessage = 'Terjadi kesalahan pada database. ';
                if (str_contains($e->getMessage(), 'foreign key constraint')) {
                    $errorMessage = 'Tidak dapat menghapus data kendaraan karena masih ada data terkait di sistem.';
                }
            } else {
                $errorMessage .= $e->getMessage();
            }

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return back()->with('error', $errorMessage);
        }
    }
}
