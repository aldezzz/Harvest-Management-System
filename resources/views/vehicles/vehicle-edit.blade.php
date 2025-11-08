@extends('layouts.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor-angkut.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="vendor-container">
    <h2>Edit Kendaraan</h2>
    
    @if(session('success'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('vehicles.update', $vehicle->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="kode_vendor" class="form-label">Kode Vendor</label>
            <select name="kode_vendor" id="kode_vendor" class="form-select" required onchange="updateNamaVendor(this)">
                <option value="">Pilih Kode Vendor</option>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->kode_vendor }}" 
                            data-nama="{{ $vendor->nama_vendor }}" 
                            {{ old('kode_vendor', $vehicle->kode_vendor) == $vendor->kode_vendor ? 'selected' : '' }}>
                        {{ $vendor->kode_vendor }} - {{ $vendor->nama_vendor }}
                    </option>
                @endforeach
            </select>
            @error('kode_vendor')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="nama_vendor" class="form-label">Nama Vendor</label>
            <input type="text" name="nama_vendor" id="nama_vendor" class="form-input" 
                   value="{{ old('nama_vendor', $vehicle->nama_vendor) }}" readonly style="background-color: #f8fafc; color: #1f2937;">
            @error('nama_vendor')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="kode_lambung" class="form-label">Kode Lambung</label>
            <input type="text" name="kode_lambung" id="kode_lambung" class="form-input" 
                   value="{{ $vehicle->kode_lambung }}" readonly>
            @error('kode_lambung')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            <small class="text-muted">Kode lambung tidak dapat diubah</small>
        </div>

        <div class="form-group">
            <label for="plat_nomor" class="form-label">No Polisi</label>
            <input type="text" name="plat_nomor" id="plat_nomor" class="form-input" value="{{ old('plat_nomor', $vehicle->plat_nomor) }}">
            @error('plat_nomor')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="id_jenis_unit" class="form-label">Jenis Unit</label>
            <select name="id_jenis_unit" id="id_jenis_unit" class="form-select select2 @error('id_jenis_unit') is-invalid @enderror" required>
                <option value="">Pilih Jenis Unit</option>
                @foreach($jenisUnits as $jenisUnit)
                    <option value="{{ $jenisUnit->id }}" {{ old('id_jenis_unit', $vehicle->id_jenis_unit) == $jenisUnit->id ? 'selected' : '' }}>
                        {{ $jenisUnit->jenis_unit }}
                    </option>
                @endforeach
            </select>
            @error('id_jenis_unit')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Cari jenis unit...',
        allowClear: true,
        width: '100%'
    });
});

// Inisialisasi nama vendor saat page load
function updateNamaVendor(selectElement) {
    const namaVendorInput = document.getElementById('nama_vendor');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const namaVendor = selectedOption.getAttribute('data-nama');
    
    if (namaVendor) {
        namaVendorInput.value = namaVendor;
        namaVendorInput.style.color = '#1f2937';
    } else {
        namaVendorInput.value = '';
        namaVendorInput.style.color = '#6b7280';
    }
}

// Inisialisasi nama vendor jika ada old value
document.addEventListener('DOMContentLoaded', function() {
    const kodeVendorSelect = document.getElementById('kode_vendor');
    if (kodeVendorSelect) {
        updateNamaVendor(kodeVendorSelect);
    }
});
</script>
@endpush
</script>
@endsection
