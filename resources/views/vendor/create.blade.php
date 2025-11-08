@extends('layouts.master')

@php
    $header = 'Vendor Baru';
    $breadcrumb = [
        ['title' => 'List Vendor', 'url' => route('vendor.index')],
        ['title' => 'Vendor Baru']
    ];
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor-angkut.css') }}">
<style>
    #vendorCodesContainer {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .code-group {
        padding: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        background-color: #f9fafb;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    .invalid-feedback {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>
@endpush

@section('content')
<div class="vendor-container">
    <h2>Tambah Vendor Baru</h2>
    
    <!-- Error messages will be shown in popup -->
    <form action="{{ route('vendor.store') }}" method="POST" id="vendorForm" novalidate onsubmit="return validateForm()">
        @csrf
        <input type="hidden" name="vendor_type" id="vendorType" value="angkut">



        <!-- Kode Vendor Fields -->
        <div id="vendorCodesContainer">
            <!-- Kode Vendor Angkut -->
            <div id="angkutCodeGroup" class="code-group">
                <label for="kode_vendor_angkut" class="form-label">Kode Vendor Angkut</label>
                <input type="text" name="kode_vendor_angkut" id="kode_vendor_angkut" class="form-input" value="{{ $newKode }}" readonly>
                @error('kode_vendor_angkut')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kode Vendor Tebang -->
            <div id="tebangCodeGroup" class="code-group" style="display: none;">
                <label for="kode_vendor_tebang" class="form-label">Kode Vendor Tebang</label>
                <input type="text" name="kode_vendor_tebang" id="kode_vendor_tebang" class="form-input" value="{{ $newKodeTebang }}" readonly>
                @error('kode_vendor_tebang')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Nama Vendor -->
        <div class="form-group">
            <label for="nama_vendor" class="form-label">Nama Vendor</label>
            <input type="text" name="nama_vendor" id="nama_vendor" class="form-input" value="{{ old('nama_vendor') }}">
            @error('nama_vendor')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- No HP -->
        <div class="form-group">
            <label for="no_hp" class="form-label">No HP </label>
            <input type="text" name="no_hp" id="no_hp" class="form-input @error('no_hp') is-invalid @enderror"
                   value="{{ old('no_hp') }}" required
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                   pattern="08[0-9]{8,11}"
                   title="Nomor HP harus dimulai dengan 08 dan terdiri dari 10-13 angka">
            @error('no_hp')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div id="no_hp_help" class="invalid-feedback">Nomor HP harus dimulai dengan 08 dan terdiri dari 10-13 angka</div>
        </div>

        <!-- Jenis Vendor -->
        <div class="form-group">
            <label for="jenis_vendor" class="form-label">Jenis Vendor</label>
            <select name="jenis_vendor" id="jenis_vendor" class="form-select" required>
                <option value="">Pilih Jenis Vendor</option>
                <option value="angkut" {{ old('jenis_vendor') == 'angkut' ? 'selected' : '' }}>Vendor Angkut</option>
                <option value="tebang" {{ old('jenis_vendor') == 'tebang' ? 'selected' : '' }}>Vendor Tebang</option>
                <option value="both" {{ old('jenis_vendor') == 'both' ? 'selected' : '' }}>Vendor Angkut & Tebang</option>
            </select>
            @error('jenis_vendor')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select @error('status') border-red-500 @enderror" required>
                <option value="" disabled selected>Pilih Status</option>
                <option value="Aktif" {{ old('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ old('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Jumlah Tenaga Kerja -->
        <div class="form-group">
            <label for="jumlah_tenaga_kerja" class="form-label">Jumlah Tenaga Kerja</label>
            <input type="number" name="jumlah_tenaga_kerja" id="jumlah_tenaga_kerja" 
                   class="form-input @error('jumlah_tenaga_kerja') is-invalid @enderror" 
                   value="{{ old('jumlah_tenaga_kerja', 15) }}" 
                   min="15" 
                   required>
            @error('jumlah_tenaga_kerja')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Nomor Rekening -->
        <div class="form-group">
            <label for="nomor_rekening" class="form-label">Nomor Rekening</label>
            <input type="text" 
                   name="nomor_rekening" 
                   id="nomor_rekening" 
                   class="form-input @error('nomor_rekening') is-invalid @enderror" 
                   value="{{ old('nomor_rekening') }}" 
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                   minlength="5"
                   maxlength="20"
                   pattern="\d{5,20}"
                   title="Nomor rekening harus terdiri dari 5-20 digit angka"
                   required>
            @error('nomor_rekening')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Masukkan 5-20 digit nomor rekening (hanya angka)</small>
        </div>

        <!-- Nama Bank -->
        <div class="form-group">
            <label for="nama_bank" class="form-label">Nama Bank</label>
            <select name="nama_bank" id="nama_bank" class="form-select @error('nama_bank') is-invalid @enderror" required>
                <option value="">Pilih Bank</option>
                @php
                    $banks = [
                        'BCA' => 'Bank Central Asia (BCA)',
                        'BRI' => 'Bank Rakyat Indonesia (BRI)',
                        'BNI' => 'Bank Negara Indonesia (BNI)',
                        'Mandiri' => 'Bank Mandiri',
                        'CIMB' => 'CIMB Niaga',
                        'Danamon' => 'Bank Danamon',
                        'Panin' => 'Bank Panin',
                        'Permata' => 'Bank Permata',
                        'Bukopin' => 'Bank Bukopin',
                        'BTPN' => 'BTPN',
                        'Maybank' => 'Maybank Indonesia',
                        'OCBC' => 'OCBC NISP',
                        'HSBC' => 'HSBC Indonesia',
                        'Citi' => 'Citibank',
                        'DBS' => 'DBS Indonesia',
                        'BTN' => 'Bank Tabungan Negara (BTN)',
                        'BJB' => 'Bank Jabar Banten',
                        'BPD' => 'Bank Pembangunan Daerah Lainnya',
                        'Lainnya' => 'Bank Lainnya'
                    ];
                @endphp
                @foreach($banks as $code => $bank)
                    <option value="{{ $bank }}" {{ old('nama_bank') == $bank ? 'selected' : '' }}>{{ $bank }}</option>
                @endforeach
            </select>
            @error('nama_bank')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Buttons -->
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('vendor.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
    // Show error popup if there are validation errors
    @if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        const errors = {!! json_encode($errors->all()) !!};
        let errorMessage = '<ul style="text-align: left; margin: 10px 0 0 20px; padding: 0;">';
        errors.forEach(error => {
            errorMessage += `<li>${error}</li>`;
        });
        errorMessage += '</ul>';
        
        Swal.fire({
            title: 'Terdapat Kesalahan!',
            html: errorMessage,
            icon: 'error',
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#3085d6',
            allowOutsideClick: false
        });
    });
    @endif
</script>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<script>
    // Inisialisasi Select2 untuk dropdown bank
    $(document).ready(function() {
        $('#nama_bank').select2({
            theme: 'bootstrap-5',
            placeholder: 'Cari nama bank...',
            allowClear: true,
            width: '100%'
        });
    });
    
    // Validasi form sebelum submit
    function validateForm() {
        let isValid = true;
        const form = document.getElementById('vendorForm');
        
        // Reset error states
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        // Validasi Nama Vendor (tidak boleh hanya angka)
        const namaVendor = document.getElementById('nama_vendor');
        if (/^\d+$/.test(namaVendor.value.trim())) {
            showError(namaVendor, 'Nama vendor tidak boleh hanya berisi angka');
            isValid = false;
        }
        
        // Validasi No HP
        const noHp = document.getElementById('no_hp');
        if (!/^08\d{8,11}$/.test(noHp.value.trim())) {
            showError(noHp, 'Nomor HP harus dimulai dengan 08 dan terdiri dari 10-13 angka');
            isValid = false;
        }
        
        // Validasi Jumlah Tenaga Kerja
        const tenagaKerja = document.getElementById('jumlah_tenaga_kerja');
        if (parseInt(tenagaKerja.value) < 15) {
            showError(tenagaKerja, 'Jumlah tenaga kerja minimal 15 orang');
            isValid = false;
        }
        
        // Validasi Nomor Rekening
        const noRekening = document.getElementById('nomor_rekening');
        if (!/^\d+$/.test(noRekening.value.trim())) {
            showError(noRekening, 'Nomor rekening hanya boleh berisi angka');
            isValid = false;
        }
        
        // Validasi Nama Bank
        const namaBank = document.getElementById('nama_bank');
        if (!namaBank.value.trim()) {
            showError(namaBank, 'Nama bank harus dipilih');
            isValid = false;
        }
        
        // Validasi Nama Pemilik Rekening
        const namaPemilik = document.getElementById('nama_pemilik_rekening');
        if (!namaPemilik.value.trim()) {
            showError(namaPemilik, 'Nama pemilik rekening harus diisi');
            isValid = false;
        }
        
        return isValid;
    }
    
    function showError(input, message) {
        input.classList.add('is-invalid');
        let errorDiv = input.nextElementSibling;
        
        // Cari div error yang sesuai
        while (errorDiv) {
            if (errorDiv.classList.contains('invalid-feedback')) {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
                break;
            }
            errorDiv = errorDiv.nextElementSibling;
        }
        
        // Scroll ke elemen yang error
        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        input.focus();
    }
    
    // Real-time validation for numeric fields
    document.getElementById('no_hp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    document.getElementById('nomor_rekening').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    function toggleKodeVendorFields() {
        const jenisVendor = document.getElementById('jenis_vendor').value;
        const angkutGroup = document.getElementById('angkutCodeGroup');
        const tebangGroup = document.getElementById('tebangCodeGroup');

        // Reset required attribute
        const kodeAngkut = document.getElementById('kode_vendor_angkut');
        const kodeTebang = document.getElementById('kode_vendor_tebang');

        kodeAngkut.required = false;
        kodeTebang.required = false;

        if (jenisVendor === 'angkut') {
            angkutGroup.style.display = 'block';
            tebangGroup.style.display = 'none';
            kodeAngkut.required = true;
        } else if (jenisVendor === 'tebang') {
            angkutGroup.style.display = 'none';
            tebangGroup.style.display = 'block';
            kodeTebang.required = true;
        } else if (jenisVendor === 'both') {
            angkutGroup.style.display = 'block';
            tebangGroup.style.display = 'block';
            kodeAngkut.required = true;
            kodeTebang.required = true;
        } else {
            angkutGroup.style.display = 'none';
            tebangGroup.style.display = 'none';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        toggleKodeVendorFields();

        // Add change event listener to jenis_vendor select
        document.getElementById('jenis_vendor').addEventListener('change', function() {
            toggleKodeVendorFields();

            // Reset validation messages
            const errorElements = document.querySelectorAll('.text-danger');
            errorElements.forEach(el => el.remove());
        });

        // Check if there's a new vendor code from the server
        @if(session('kode_vendor_angkut'))
            document.getElementById('kode_vendor_angkut').value = '{{ session('kode_vendor_angkut') }}';

            // Show warning message if exists
            @if(session('warning'))
                alert('{{ session('warning') }}');
            @endif
        @endif
    });
</script>
@endpush
