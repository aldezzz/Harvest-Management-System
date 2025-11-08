@extends('layouts.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor-angkut.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="vendor-container">
    <h2>Tambah Kendaraan</h2>

    @if(session('success'))
        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('vehicles.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="kode_vendor" class="form-label">Kode Vendor</label>
            <select name="kode_vendor" id="kode_vendor" class="form-select select2 @error('kode_vendor') is-invalid @enderror" required onchange="updateNamaVendor(this)" style="width: 100%;">
                <option value="">Pilih Kode Vendor</option>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->kode_vendor }}"
                            data-nama="{{ $vendor->nama_vendor }}"
                            {{ old('kode_vendor') == $vendor->kode_vendor ? 'selected' : '' }}>
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
            <input type="text" name="nama_vendor" id="nama_vendor" class="form-input @error('nama_vendor') is-invalid @enderror"
                   value="" readonly style="background-color: #f8fafc; color: #1f2937;">
            @error('nama_vendor')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="kode_lambung" class="form-label">Kode Lambung</label>
            <input type="text" name="kode_lambung" id="kode_lambung" class="form-input"
                   value="{{ $kodeLambung }}" readonly>
            <input type="hidden" name="kode_lambung" value="{{ $kodeLambung }}">
            <small class="text-muted">Kode lambung akan otomatis terisi dengan format JBM-YY-VXXX</small>
            @error('kode_lambung')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="plat_nomor" class="form-label">No Polisi</label>
            <input type="text" name="plat_nomor" id="plat_nomor" class="form-input @error('plat_nomor') is-invalid @enderror" value="{{ old('plat_nomor') }}">
            @error('plat_nomor')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="id_jenis_unit" class="form-label">Jenis Unit</label>
            <select name="id_jenis_unit" id="id_jenis_unit" class="form-select select2 @error('id_jenis_unit') is-invalid @enderror" required>
                <option value="">Pilih Jenis Unit</option>
                @foreach($jenisUnits as $jenisUnit)
                    <option value="{{ $jenisUnit->id }}" {{ old('id_jenis_unit') == $jenisUnit->id ? 'selected' : '' }}>
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
    // Initialize Select2 for Kode Vendor
    $('#kode_vendor').select2({
        placeholder: 'Cari kode atau nama vendor...',
        allowClear: true,
        width: '100%'
    });
    
    // Initialize Select2 for Jenis Unit
    $('#id_jenis_unit').select2({
        placeholder: 'Pilih jenis unit...',
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

// Inisialisasi Select2 untuk kode_vendor
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Select2
    $('#kode_vendor').select2({
        placeholder: 'Cari kode atau nama vendor',
        allowClear: true,
        width: '100%',
        matcher: function(params, data) {
            // Jika tidak ada pencarian, tampilkan semua
            if ($.trim(params.term) === '') {
                return data;
            }

            // Cari di kode_vendor atau nama_vendor
            const searchTerm = params.term.toLowerCase();
            const text = $(data.element).text().toLowerCase();

            if (text.indexOf(searchTerm) > -1) {
                return data;
            }

            return null;
        }
    });

    // Tetap panggil updateNamaVendor saat nilai berubah
    const kodeVendorSelect = document.getElementById('kode_vendor');
    if (kodeVendorSelect) {
        updateNamaVendor(kodeVendorSelect);
    }
});
</script>
@endpush
@endsection
