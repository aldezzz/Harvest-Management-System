<?php

namespace App\Http\Controllers;

use App\Models\JenisUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JenisUnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-jenis-units', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-jenis-unit', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-jenis-unit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-jenis-unit', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = JenisUnit::query();

        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->input('search');
            $query->where('jenis_unit', 'like', "%$search%");
        }

        $jenisUnits = $query->latest()->paginate(10);

        return view('jenis_unit.index', compact('jenisUnits'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('jenis_unit.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_unit' => [
                'required',
                'string',
                'max:255',
                'unique:jenis_units,jenis_unit',
                'regex:/^[^0-9]*[a-zA-Z][a-zA-Z0-9\s]*$/',
            ],
        ], [
            'jenis_unit.regex' => 'Jenis Unit tidak boleh hanya berisi angka dan harus mengandung setidaknya satu huruf.',
        ]);

        try {
            DB::beginTransaction();

            JenisUnit::create([
                'jenis_unit' => $request->jenis_unit,
            ]);

            DB::commit();
            return redirect()->route('jenis-unit.index')
                ->with('success', 'Jenis Unit berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Jenis Unit: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jenisUnit = JenisUnit::findOrFail($id);
        return view('jenis_unit.show', compact('jenisUnit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $jenisUnit = JenisUnit::findOrFail($id);
        return view('jenis_unit.edit', compact('jenisUnit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_unit' => [
                'required',
                'string',
                'max:255',
                'unique:jenis_units,jenis_unit,' . $id,
                'regex:/^[^0-9]*[a-zA-Z][a-zA-Z0-9\s]*$/',
            ],
        ], [
            'jenis_unit.regex' => 'Jenis Unit tidak boleh hanya berisi angka dan harus mengandung setidaknya satu huruf.',
        ]);

        try {
            DB::beginTransaction();

            $jenisUnit = JenisUnit::findOrFail($id);
            $jenisUnit->update([
                'jenis_unit' => $request->jenis_unit,
            ]);

            DB::commit();
            return redirect()->route('jenis-unit.index')
                ->with('success', 'Jenis Unit berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Jenis Unit: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $jenisUnit = JenisUnit::findOrFail($id);
            
            // Check if this jenis unit is being used in vehicles
            $isUsed = \App\Models\Vehicle::where('id_jenis_unit', $id)->exists();
            
            if ($isUsed) {
                $errorMessage = 'Tidak dapat menghapus Jenis Unit karena sudah digunakan di daftar kendaraan vendor';
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 422);
                }
                return redirect()->back()
                    ->with('error', $errorMessage);
            }
            
            $jenisUnit->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jenis Unit berhasil dihapus'
                ]);
            }

            return redirect()->route('jenis-unit.index')
                ->with('success', 'Jenis Unit berhasil dihapus');

        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus Jenis Unit: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus Jenis Unit: ' . $e->getMessage());
        }
    }
}
