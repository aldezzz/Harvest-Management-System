@extends('layouts.master')

@php
    $header = 'Edit Vendor';
    $breadcrumb = [
        ['title' => 'List Vendor', 'url' => route('vendor.index')],
        ['title' => 'Edit Vendor']
    ];
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor-angkut.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
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

    .form-control:focus, .form-select:focus, .select2-selection:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #374151;
    }

    .form-input, .form-select {
        display: block;
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .btn {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        user-select: none;
        border: 1px solid transparent;
        padding: 0.5rem 1rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out,
                    border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0069d9;
        border-color: #0062cc;
    }

    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    .text-danger {
        color: #dc3545;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .hidden {
        display: none !important;
    }

    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding: 0;
        line-height: 1.5;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@section('content')
<div class="vendor-container">
    <h2>Edit Vendor</h2>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vendor.update', $vendor->id) }}" method="POST" id="vendorForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="vendor_type" id="vendorType" value="{{ $vendor->jenis_vendor }}">

        <!-- Kode Vendor Fields -->
        <div id="vendorCodesContainer">
            <!-- Kode Vendor Angkut -->
            <div id="angkutCodeGroup" class="code-group" style="display: none;">
                <label for="kode_vendor_angkut" class="form-label">Kode Vendor Angkut</label>
                <input type="text" name="kode_vendor_angkut" id="kode_vendor_angkut" class="form-input"
                    value="{{ old('kode_vendor_angkut', $vendor->kode_vendor_angkut) }}" readonly>
            </div>

            <!-- Kode Vendor Tebang -->
            <div id="tebangCodeGroup" class="code-group" style="display: none;">
                <label for="kode_vendor_tebang" class="form-label">Kode Vendor Tebang</label>
                <input type="text" name="kode_vendor_tebang" id="kode_vendor_tebang" class="form-input"
                    value="{{ old('kode_vendor_tebang', $vendor->kode_vendor_tebang) }}" readonly>
            </div>
        </div>

        <!-- Nama Vendor -->
        <div class="form-group">
            <label for="nama_vendor" class="form-label">Nama Vendor <span class="text-danger">*</span></label>
            <input type="text" name="nama_vendor" id="nama_vendor"
                   class="form-input @error('nama_vendor') is-invalid @enderror"
                   value="{{ old('nama_vendor', $vendor->nama_vendor) }}"
                   required
                   maxlength="100"
                   placeholder="Masukkan nama vendor">
            @error('nama_vendor')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- No HP -->
        <div class="form-group">
            <label for="no_hp" class="form-label">No HP <span class="text-danger">*</span></label>
            <input type="text" name="no_hp" id="no_hp"
                   class="form-input @error('no_hp') is-invalid @enderror"
                   value="{{ old('no_hp', $vendor->no_hp) }}"
                   required
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                   minlength="10"
                   maxlength="13"
                   pattern="08[0-9]{8,11}"
                   placeholder="Contoh: 081234567890"
                   title="Nomor HP harus dimulai dengan 08 dan terdiri dari 10-13 angka">
            @error('no_hp')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Format: 08xxxxxxxx (10-13 digit)</small>
        </div>

        <!-- Jenis Vendor -->
        <div class="form-group">
            <label for="jenis_vendor" class="form-label">Jenis Vendor <span class="text-danger">*</span></label>
            <select name="jenis_vendor" id="jenis_vendor"
                    class="form-select @error('jenis_vendor') is-invalid @enderror"
                    required>
                <option value="" disabled>Pilih Jenis Vendor</option>
                <option value="angkut" {{ old('jenis_vendor', $vendor->jenis_vendor) == 'angkut' ? 'selected' : '' }}>Vendor Angkut</option>
                <option value="tebang" {{ old('jenis_vendor', $vendor->jenis_vendor) == 'tebang' ? 'selected' : '' }}>Vendor Tebang</option>
                <option value="both" {{ old('jenis_vendor', $vendor->jenis_vendor) == 'both' ? 'selected' : '' }}>Vendor Angkut & Tebang</option>
            </select>
            @error('jenis_vendor')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="status"
                    class="form-select @error('status') is-invalid @enderror"
                    required>
                <option value="" disabled>Pilih Status</option>
                <option value="Aktif" {{ old('status', $vendor->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ old('status', $vendor->status) == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Jumlah Tenaga Kerja -->
        <div class="form-group">
            <label for="jumlah_tenaga_kerja" class="form-label">Jumlah Tenaga Kerja <span class="text-danger">*</span></label>
            <input type="number"
                   name="jumlah_tenaga_kerja"
                   id="jumlah_tenaga_kerja"
                   class="form-input @error('jumlah_tenaga_kerja') is-invalid @enderror"
                   value="{{ old('jumlah_tenaga_kerja', $vendor->jumlah_tenaga_kerja ?? 15) }}"
                   min="15"
                   max="999"
                   required
                   oninput="validateWorkerCount(this)"
                   placeholder="Masukkan jumlah tenaga kerja">
            @error('jumlah_tenaga_kerja')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Minimal 15 tenaga kerja</small>
            <div id="worker_count_error" class="invalid-feedback">Jumlah tenaga kerja minimal 15 orang</div>
        </div>

        <!-- Nomor Rekening -->
        <div class="form-group">
            <label for="nomor_rekening" class="form-label">Nomor Rekening <span class="text-danger">*</span></label>
            <input type="text"
                   name="nomor_rekening"
                   id="nomor_rekening"
                   class="form-input @error('nomor_rekening') is-invalid @enderror"
                   value="{{ old('nomor_rekening', $vendor->nomor_rekening) }}"
                   required
                   oninput="this.value = this.value.replace(/[^0-9]/g, ''); validateBankAccount(this);"
                   minlength="5"
                   maxlength="20"
                   pattern="\d{5,20}"
                   placeholder="Masukkan nomor rekening (5-20 digit)"
                   title="Masukkan 5-20 digit nomor rekening (hanya angka)">
            @error('nomor_rekening')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Masukkan 5-20 digit nomor rekening (hanya angka)</small>
        </div>

        <!-- Nama Bank -->
        <div class="form-group">
            <label for="nama_bank" class="form-label">Nama Bank <span class="text-danger">*</span></label>
            @php
                $selectedBank = old('nama_bank', $vendor->nama_bank);
                $banks = [
                    'Bank Central Asia (BCA)' => 'Bank Central Asia (BCA)',
                    'Bank Rakyat Indonesia (BRI)' => 'Bank Rakyat Indonesia (BRI)',
                    'Bank Negara Indonesia (BNI)' => 'Bank Negara Indonesia (BNI)',
                    'Bank Mandiri' => 'Bank Mandiri',
                    'CIMB Niaga' => 'CIMB Niaga',
                    'Bank Danamon' => 'Bank Danamon',
                    'Bank Panin' => 'Bank Panin',
                    'Bank Permata' => 'Bank Permata',
                    'Bank Bukopin' => 'Bank Bukopin',
                    'BTPN' => 'BTPN',
                    'Maybank Indonesia' => 'Maybank Indonesia',
                    'OCBC NISP' => 'OCBC NISP',
                    'HSBC Indonesia' => 'HSBC Indonesia',
                    'Citibank' => 'Citibank',
                    'DBS Indonesia' => 'DBS Indonesia',
                    'Bank Tabungan Negara (BTN)' => 'Bank Tabungan Negara (BTN)',
                    'Bank Jabar Banten' => 'Bank Jabar Banten',
                    'Bank Pembangunan Daerah Lainnya' => 'Bank Pembangunan Daerah Lainnya',
                    'Bank Lainnya' => 'Bank Lainnya'
                ];
            @endphp
            <select name="nama_bank" id="nama_bank"
                    class="form-select @error('nama_bank') is-invalid @enderror"
                    required>
                <option value="">Pilih Bank</option>
                @foreach($banks as $code => $bank)
                    <option value="{{ $bank }}" {{ $selectedBank == $bank ? 'selected' : '' }}>{{ $bank }}</option>
                @endforeach
            </select>
            @error('nama_bank')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Pilih bank sesuai dengan nomor rekening</small>
        </div>

        <div class="form-group d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Simpan Perubahan
            </button>
            <a href="{{ route('vendor.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Batal
            </a>
        </div>
    </form>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor-angkut.css') }}">
<style>
    .is-invalid {
        border-color: #dc3545 !important;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
        transition: all 0.3s ease;
    }

    .is-valid {
        border-color: #198754 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right calc(0.375em + 0.1875rem) center !important;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
    }

    .was-validated .form-control:invalid ~ .invalid-feedback,
    .was-validated .form-control:invalid ~ .invalid-tooltip,
    .form-control.is-invalid ~ .invalid-feedback,
    .form-control.is-invalid ~ .invalid-tooltip {
        display: block;
    }

    .select2-container--bootstrap-5 .select2-selection {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding: 0;
        line-height: 1.5;
    }
    .select2-container--bootstrap-5 .select2-selection--single {
        padding: 0.375rem 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Form validation and submission
    function validateAndSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const noRekening = document.getElementById('nomor_rekening');
        let isValid = true;
        const errorMessages = [];

        // Clear previous errors
        if (noRekening) {
            const errorDiv = noRekening.nextElementSibling;
            if (errorDiv && errorDiv.classList.contains('error-message')) {
                errorDiv.remove();
            }
            noRekening.classList.remove('border-red-500');
        }

        // Validate bank account number
        if (noRekening) {
            const value = noRekening.value.trim();

            if (value === '') {
                showError(noRekening, 'Nomor rekening harus diisi');
                errorMessages.push('Nomor rekening harus diisi');
                isValid = false;
            } else if (/^0+$/.test(value)) {
                showError(noRekening, 'Nomor rekening tidak boleh semua angka nol');
                errorMessages.push('Nomor rekening tidak boleh semua angka nol');
                isValid = false;
            } else if (value.length < 5 || value.length > 20) {
                showError(noRekening, 'Nomor rekening harus 5-20 digit');
                errorMessages.push('Nomor rekening harus 5-20 digit');
                isValid = false;
            } else if (!/^\d+$/.test(value)) {
                showError(noRekening, 'Hanya boleh berisi angka');
                errorMessages.push('Nomor rekening hanya boleh berisi angka');
                isValid = false;
            }
        }

        // Show error message if validation fails
        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: errorMessages.map(msg => `<div>${msg}</div>`).join(''),
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#dc3545'
            });
            return false;
        }

        // If all validations pass, submit the form
        form.submit();
        return true;
    }
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                let fieldName = field.labels[0]?.textContent?.trim() ||
                              field.previousElementSibling?.textContent?.trim() ||
                              field.getAttribute('name') || 'Field';
                fieldName = fieldName.replace('*', '').trim();
                const errorMsg = field.getAttribute('data-error-required') || 'harus diisi';
                showError(field, errorMsg);
                errorFields.push({
                    name: fieldName,
                    message: errorMsg
                });
                isValid = false;
            }
        });

        // Validate phone number format
        const phoneField = document.getElementById('no_hp');
        if (phoneField.value) {
            if (!/^08[0-9]{8,11}$/.test(phoneField.value)) {
                phoneField.classList.add('is-invalid');
                const errorMsg = 'harus diawali 08 dan 10-13 digit angka';
                showError(phoneField, errorMsg);
                errorFields.push({
                    name: 'Nomor HP',
                    message: errorMsg
                });
                isValid = false;
            }
        } else if (phoneField.required) {
            phoneField.classList.add('is-invalid');
            const errorMsg = 'harus diisi';
            showError(phoneField, errorMsg);
            errorFields.push({
                name: 'Nomor HP',
                message: errorMsg
            });
            isValid = false;
        }

        // Validate account number format
        const rekeningField = document.getElementById('nomor_rekening');
        const noRekeningValue = rekeningField.value.trim();

        if (!noRekeningValue) {
            rekeningField.classList.add('is-invalid');
            const errorMsg = 'Nomor rekening harus diisi';
            showError(rekeningField, errorMsg);
            errorFields.push({
                name: 'Nomor Rekening',
                message: errorMsg
            });
            isValid = false;
        } else if (!/^\d+$/.test(noRekeningValue)) {
            rekeningField.classList.add('is-invalid');
            const errorMsg = 'Nomor rekening hanya boleh berisi angka';
            showError(rekeningField, errorMsg);
            errorFields.push({
                name: 'Nomor Rekening',
                message: errorMsg
            });
            isValid = false;
        } else if (noRekeningValue.length < 5 || noRekeningValue.length > 20) {
            rekeningField.classList.add('is-invalid');
            const errorMsg = 'Nomor rekening harus 5-20 digit';
            showError(rekeningField, errorMsg);
            errorFields.push({
                name: 'Nomor Rekening',
                message: errorMsg
            });
            isValid = false;
        } else if (/(\d)\1{4,}/.test(noRekeningValue)) {
            // Check for repeated numbers (5 or more of the same digit)
            rekeningField.classList.add('is-invalid');
            const errorMsg = 'Nomor rekening tidak valid. Tidak boleh berisi 5 atau lebih angka yang sama berurutan.';
            showError(rekeningField, errorMsg);
            errorFields.push({
                name: 'Nomor Rekening',
                message: errorMsg
            });
            isValid = false;
        } else if (/01234|12345|23456|34567|45678|56789|98765|87654|76543|65432|54321/.test(noRekeningValue)) {
            // Check for sequential numbers
            rekeningField.classList.add('is-invalid');
            const errorMsg = 'Nomor rekening tidak valid. Tidak boleh berurutan.';
            showError(rekeningField, errorMsg);
            errorFields.push({
                name: 'Nomor Rekening',
                message: errorMsg
            });
            isValid = false;
        }

        // If form is invalid, show error messages
        if (!isValid) {
            // Scroll to first error
            const firstError = document.querySelector('.is-invalid, .border-red-500');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            // Show error message with SweetAlert2 if available
            if (window.Swal && errorFields.length > 0) {
                let errorList = '';
                errorFields.forEach(error => {
                    errorList += `<li><strong>${error.name}:</strong> ${error.message}</li>`;
                });

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: `Terdapat kesalahan pada form:<ul class="text-left">${errorList}</ul>`,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#dc3545'
                });
            }
            return false;
        }
            });

            // Show error popup
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: `
                    <div style="text-align: left;">
                        <p style="margin-bottom: 15px; font-weight: bold;">Terdapat kesalahan pada form:</p>
                        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                            <ul style="margin: 0; padding-left: 20px;">
                                ${errorList}
                            </ul>
                        </div>
                        <p style="margin-top: 15px; color: #6c757d;">Silakan perbaiki field yang ditandai dengan warna merah.</p>
                    </div>
                `,
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#3085d6'
            });

            // Scroll to first error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }

            return false;
        }

        // If form is valid, show loading and submit
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        // Submit the form
        form.submit();
        return true;
    }

    // Validate bank account number
    function validateBankAccount(input) {
        if (!input) return true;

        const value = input.value.trim();
        let isValid = true;

        // Clear previous errors
        const existingError = input.nextElementSibling;
        if (existingError && (existingError.classList.contains('error-message') || existingError.classList.contains('invalid-feedback'))) {
            existingError.remove();
        }

        // Check validation rules
        if (value === '') {
            showError(input, 'Nomor rekening harus diisi');
            isValid = false;
        } else if (/^0+$/.test(value)) {
            showError(input, 'Nomor rekening tidak boleh semua angka nol');
            isValid = false;
            // Show alert for all zeros
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Nomor rekening tidak boleh semua angka nol',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#dc3545'
            });
        } else if (value.length < 5 || value.length > 20) {
            showError(input, 'Nomor rekening harus 5-20 digit');
            isValid = false;
        } else if (!/^\d+$/.test(value)) {
            showError(input, 'Hanya boleh berisi angka');
            isValid = false;
        } else if (/(\d)\1{4,}/.test(value)) {
            showError(input, 'Tidak boleh berisi 5 atau lebih angka yang sama berurutan');
            isValid = false;
        } else if (/01234|12345|23456|34567|45678|56789|98765|87654|76543|65432|54321/.test(value)) {
            showError(input, 'Nomor rekening tidak boleh berurutan');
            isValid = false;
        } else {
            // If valid, remove error styling
            input.classList.remove('border-red-500');
        }

        return isValid;
    }

    // Show error message under field
    function showError(field, message) {
        // Remove any existing error message
        const existingError = field.nextElementSibling;
        if (existingError && (existingError.classList.contains('error-message') || existingError.classList.contains('invalid-feedback'))) {
            existingError.remove();
        }

        const errorDiv = document.createElement('div');
        errorDiv.className = 'text-red-500 text-sm mt-1 error-message';
        errorDiv.textContent = message;
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
        field.classList.add('border-red-500');
    }

    // Initialize form validation on input change
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize bank select2
        initBankSelect2();

        const form = document.getElementById('vendorForm');
        if (form) {
            form.addEventListener('submit', validateAndSubmit);

            // Initialize bank account validation
            const bankAccountInput = document.getElementById('nomor_rekening');
            if (bankAccountInput) {
                bankAccountInput.addEventListener('input', function() {
                    validateBankAccount(this);
                });
            }

            // Add real-time validation for phone number
            const noHp = document.getElementById('no_hp');
            if (noHp) {
                noHp.addEventListener('input', function() {
                    // Remove non-numeric characters
                    this.value = this.value.replace(/[^0-9]/g, '');
                    // Validate length
                    if (this.value.length < 10) {
                        showError(this, 'Nomor HP minimal 10 digit');
                    } else {
                        const errorDiv = this.nextElementSibling;
                        if (errorDiv && errorDiv.classList.contains('error-message')) {
                            errorDiv.remove();
                        }
                        this.classList.remove('border-red-500');
                    }
                });
            }
                if (value && !/^08[0-9]{8,11}$/.test(value)) {
                    showError(input, 'Nomor HP harus dimulai dengan 08 dan 10-13 angka');
                } else {
                    clearError(input);
                }
            });
        }

        if (noRekening) {
            // Function to validate account number and show popup
            const validateAccountNumber = (input) => {
                const value = input.value.trim();

                // Clear previous errors
                clearError(input);
                input.classList.remove('is-invalid', 'is-valid');

                // Check for empty field
                if (!value) {
                    showError(input, 'Nomor rekening harus diisi');
                    return false;
                }

                // Check for non-numeric characters
                if (!/^\d+$/.test(value)) {
                    const errorMsg = 'Hanya boleh berisi angka';
                    showError(input, errorMsg);
                    showPopup('Nomor Rekening Tidak Valid', errorMsg);
                    return false;
                }

                // Check length
                if (value.length < 5 || value.length > 20) {
                    const errorMsg = 'Harus 5-20 digit';
                    showError(input, errorMsg);
                    showPopup('Nomor Rekening Tidak Valid', errorMsg);
                    return false;
                }

                // Check for repeated numbers (e.g., 00000, 11111, etc.)
                if (/(\d)\1{4,}/.test(value)) {
                    const errorMsg = 'Tidak boleh berisi 5 atau lebih angka yang sama berurutan (contoh: 00000, 11111, dll)';
                    showError(input, errorMsg);
                    showPopup('Nomor Rekening Tidak Valid', errorMsg);
                    return false;
                }

                // Check for sequential numbers (e.g., 12345, 98765, etc.)
                if (/01234|12345|23456|34567|45678|56789|98765|87654|76543|65432|54321/.test(value)) {
                    const errorMsg = 'Tidak boleh berisi angka berurutan (contoh: 12345, 98765, dll)';
                    showError(input, errorMsg);
                    showPopup('Nomor Rekening Tidak Valid', errorMsg);
                    return false;
                }

                // If all validations pass, show success state
                input.classList.add('is-valid');
                return true;
            };

            // Add input event for real-time validation
            noRekening.addEventListener('input', function() {
                validateAccountNumber(this);
            });

            // Add blur event for when user leaves the field
            noRekening.addEventListener('blur', function() {
                if (this.value.trim() !== '') {
                    validateAccountNumber(this);
                } else {
                    clearError(this);
                    this.classList.remove('is-valid');
                }
            });

            // Function to show popup
            function showPopup(title, message) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: title,
                        text: message,
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#3085d6',
                        allowOutsideClick: false
                    });
                } else {
                    alert(title + ': ' + message);
                }
            }

            // Also validate on blur in case user pastes and leaves the field
            noRekening.addEventListener('blur', function() {
                if (this.value.trim() !== '') {
                    this.dispatchEvent(new Event('input'));
                }
            });
        }

        // Function to show popup
        function showPopup(title, message) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: message,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#3085d6',
                    allowOutsideClick: false
                });
            } else {
                alert(title + ': ' + message);
            }
        }
            $('.select2-container--bootstrap-5 .select2-selection').css({
                'height': '38px',
                'display': 'flex',
                'align-items': 'center'
            });
            $('.select2-selection__rendered').css({
                'line-height': '1.25',
                'padding-top': '0',
                'padding-bottom': '0',
                'display': 'flex',
                'align-items': 'center'
            });
        });
    });

    // Show error popup with SweetAlert2
    function showErrorMessages(messages) {
        const errorList = messages.map(msg => `<li>${msg}</li>`).join('');
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            html: `<div class="text-left">
                     <p class="mb-3">Terdapat kesalahan pada form:</p>
                     <ul class="list-disc pl-5">${errorList}</ul>
                   </div>`,
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#3085d6',
            allowOutsideClick: false
        });
    }

    // Show error message under input
    function showError(input, message) {
        if (!input) return;

        const formGroup = input.closest('.form-group') || input.parentNode;
        input.classList.add('is-invalid');

        // Remove existing error message if any
        const existingError = formGroup.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }

        // Create and append new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        errorDiv.style.color = '#dc3545';
        errorDiv.style.fontSize = '0.875em';
        errorDiv.style.marginTop = '0.25rem';

        formGroup.appendChild(errorDiv);
    }

    // Clear error from input
    function clearError(input) {
        const formGroup = input.closest('.form-group') || input.parentNode;
        input.classList.remove('is-invalid');
        const errorDiv = formGroup.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    // Main form validation
    function validateForm(event) {
        // Prevent default form submission
        event.preventDefault();

        let isValid = true;
        const form = document.getElementById('vendorForm');
        const errorMessages = [];
        const submitBtn = form.querySelector('button[type="submit"]');

        // Reset all error states
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        // Get form elements
        const noHp = document.getElementById('no_hp');
        const noRekening = document.getElementById('nomor_rekening');
        const namaPemilik = document.getElementById('nama_pemilik_rekening');
        const tenagaKerja = document.getElementById('jumlah_tenaga_kerja');
        const kodeAngkutInput = document.getElementById('kode_vendor_angkut');
        const kodeTebangInput = document.getElementById('kode_vendor_tebang');
        const vendorTypeInput = document.getElementById('vendorType');

        // Trim all input values
        noHp.value = noHp.value.trim();
        noRekening.value = noRekening.value.trim();

        // Validate Phone Number (No HP)
        if (!noHp.value) {
            showError(noHp, 'Nomor HP harus diisi');
            errorMessages.push('Nomor HP harus diisi');
            isValid = false;
        } else if (!/^08[0-9]{8,11}$/.test(noHp.value)) {
            showError(noHp, 'Nomor HP harus diawali 08 dan 10-13 digit angka');
            errorMessages.push('Format nomor HP tidak valid (contoh: 081234567890)');
            isValid = false;
        }

        // Validate Account Number (No Rekening)
        if (!noRekening.value) {
            showError(noRekening, 'Nomor rekening harus diisi');
            errorMessages.push('Nomor rekening harus diisi');
            isValid = false;
        } else if (!/^[0-9]{5,20}$/.test(noRekening.value)) {
            showError(noRekening, 'Nomor rekening harus 5-20 digit angka');
            errorMessages.push('Format nomor rekening tidak valid (hanya angka 5-20 digit)');
            isValid = false;
        }

            isValid = false;
        } else if (!/^08[0-9]{8,11}$/.test(noHpValue)) {
            showError(noHp, 'Nomor HP harus dimulai dengan 08 dan 10-13 angka');
            errorMessages.push('Nomor HP harus dimulai dengan 08 dan 10-13 angka');
            isValid = false;
        }

        // Validate Nomor Rekening
        const noRekeningValue = noRekening.value.trim();
        if (!noRekeningValue) {
            showError(noRekening, 'Nomor rekening harus diisi');
            errorMessages.push('Nomor rekening harus diisi');
            isValid = false;
        } else if (!/^\d+$/.test(noRekeningValue)) {
            showError(noRekening, 'Nomor rekening hanya boleh berisi angka');
            errorMessages.push('Nomor rekening hanya boleh berisi angka');
            isValid = false;
        } else if (noRekeningValue.length < 5 || noRekeningValue.length > 20) {
            showError(noRekening, 'Nomor rekening harus 5-20 digit');
            errorMessages.push('Nomor rekening harus 5-20 digit');
            isValid = false;
        }

        // Validate Nama Bank
        if (!namaBank.value.trim()) {
            showError(namaBank, 'Nama bank harus diisi');
            errorMessages.push('Nama bank harus diisi');
            isValid = false;
        }

        // Validate Nama Pemilik Rekening
        if (!namaPemilik.value.trim()) {
            showError(namaPemilik, 'Nama pemilik rekening harus diisi');
            errorMessages.push('Nama pemilik rekening harus diisi');
            isValid = false;
        }

        // Validate Jumlah Tenaga Kerja
        if (parseInt(tenagaKerja.value) < 15) {
            showError(tenagaKerja, 'Jumlah tenaga kerja minimal 15 orang');
            errorMessages.push('Jumlah tenaga kerja minimal 15 orang');
            isValid = false;
        }

        if (!isValid) {
            // Show all error messages in a popup
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: `
                    <div class="text-left">
                        <p class="mb-3">Mohon perbaiki kesalahan berikut:</p>
                        <ul class="list-disc pl-5">
                            ${errorMessages.map(msg => `<li>${msg}</li>`).join('')}
                        </ul>
                    </div>
                `,
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#3085d6',
            });

            // Scroll to first error
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }

            return false;
        }

        // If we get here, all validations passed
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        // Submit the form
        form.submit();
    }
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonText: 'OK'
                    });
                    window.location.href = '{{ route("vendor.index") }}';
                    return;
                } else if (data.errors) {
                    // Handle server-side validation errors
                    const errorMessages = Object.values(data.errors).flat();
                    throw new Error(errorMessages.join('\n'));
                }
            }

            // If we get here, something went wrong
            const text = await response.text();
            throw new Error(text || 'Terjadi kesalahan saat menyimpan data');
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMessage = 'Gagal menyimpan data. Silakan coba lagi.';

            if (error.message) {
                errorMessage = error.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                html: errorMessage.replace(/\n/g, '<br>'),
                confirmButtonText: 'Mengerti'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Update';
        });

        return false;
    }

    // Validate jumlah tenaga kerja
    function validateWorkerCount(input) {
        const value = parseInt(input.value) || 0;
        if (value < 15) {
            showError(input, 'Jumlah tenaga kerja minimal 15 orang');
            return false;
        }
        clearError(input);
        return true;
    }

    // Validate phone number
    function validatePhoneNumber(input) {
        const phoneNumber = input.value.trim();
        const phoneRegex = /^08[0-9]{8,11}$/;

        if (phoneNumber === '') {
            showError(input, 'Nomor HP harus diisi');
            return false;
        } else if (!phoneRegex.test(phoneNumber)) {
            showError(input, 'Nomor HP harus dimulai dengan 08 dan terdiri dari 10-13 angka');
            return false;
        }

        clearError(input);
        return true;
    }

    // Validate worker count
    function validateWorkerCount(input) {
        const value = parseInt(input.value) || 0;
        if (value < 15) {
            showError(input, 'Jumlah tenaga kerja minimal 15 orang');
            return false;
        }
        clearError(input);
        return true;
    }

    // Validate bank account number
    function validateAccountNumber(input) {
        const value = input.value.trim();

        if (value === '') {
            showError(input, 'Nomor rekening harus diisi');
            return false;
        } else if (!/^\d+$/.test(value)) {
            showError(input, 'Nomor rekening hanya boleh berisi angka');
            return false;
        } else if (value.length < 5 || value.length > 20) {
            showError(input, 'Nomor rekening harus terdiri dari 5-20 digit angka');
            return false;
        }

        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        return true;
    }

    // Validate required field
    function validateRequired(input, fieldName) {
        if (!input.value.trim()) {
            showError(input, `${fieldName} harus diisi`);
            return false;
        }
        return true;
    }

    // Show error popup
    function showErrorPopup(messages) {
        let errorMessage = '<div style="text-align: left; max-height: 300px; overflow-y: auto;">';
        errorMessage += '<p style="margin-bottom: 10px; font-weight: bold;">Mohon perbaiki kesalahan berikut:</p>';
        errorMessage += '<ul style="padding-left: 20px; margin: 0;">';

        if (Array.isArray(messages)) {
            messages.forEach(function(message) {
                errorMessage += '<li>' + message + '</li>';
            });
        } else if (typeof messages === 'string') {
            errorMessage += '<li>' + messages + '</li>';
        }

        errorMessage += '</ul></div>';

        Swal.fire({
            title: 'Validasi Gagal',
            html: errorMessage,
            icon: 'error',
            confirmButtonText: 'Mengerti',
            customClass: {
                confirmButton: 'btn btn-primary',
                title: 'swal-title',
                htmlContainer: 'swal-html'
            },
            buttonsStyling: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    }

    // Function to validate form fields
    function validateFormFields() {
        let isValid = true;
        const errorMessages = [];

        // Validate Nama Vendor
        const namaVendor = document.getElementById('nama_vendor');
        if (namaVendor && !namaVendor.value.trim()) {
            showError(namaVendor, 'Nama vendor harus diisi');
            errorMessages.push('Nama vendor harus diisi');
            isValid = false;
        } else if (namaVendor && /^\d+$/.test(namaVendor.value.trim())) {
            showError(namaVendor, 'Nama vendor tidak boleh hanya berisi angka');
            errorMessages.push('Nama vendor tidak boleh hanya berisi angka');
            isValid = false;
        }

        // Validate No HP
        const noHp = document.getElementById('no_hp');
        const noHpValue = noHp ? noHp.value.trim() : '';

        if (!noHpValue) {
            showError(noHp, 'Nomor HP harus diisi');
            errorMessages.push('Nomor HP harus diisi');
            isValid = false;
        } else if (!/^08[0-9]{8,11}$/.test(noHpValue)) {
            showError(noHp, 'Nomor HP harus dimulai dengan 08 dan terdiri dari 10-13 angka');
            errorMessages.push('Nomor HP harus dimulai dengan 08 dan terdiri dari 10-13 angka');
            isValid = false;
        }

        // Validate Nomor Rekening
        const noRekening = document.getElementById('nomor_rekening');
        const noRekeningValue = noRekening ? noRekening.value.trim() : '';

        if (!noRekeningValue) {
            showError(noRekening, 'Nomor rekening harus diisi');
            errorMessages.push('Nomor rekening harus diisi');
            isValid = false;
        } else if (!/^\d+$/.test(noRekeningValue)) {
            showError(noRekening, 'Nomor rekening hanya boleh berisi angka');
            errorMessages.push('Nomor rekening hanya boleh berisi angka');
            isValid = false;
        } else if (noRekeningValue.length < 5 || noRekeningValue.length > 20) {
            showError(noRekening, 'Nomor rekening harus terdiri dari 5-20 digit angka');
            errorMessages.push('Nomor rekening harus 5-20 digit');
            isValid = false;
        }

        // Validate Nama Bank
        const namaBank = document.getElementById('nama_bank');
        if (namaBank && !namaBank.value.trim()) {
            showError(namaBank, 'Nama bank harus diisi');
            errorMessages.push('Nama bank harus diisi');
            isValid = false;
        }

        // Validate Nama Pemilik Rekening
        const namaPemilik = document.getElementById('nama_pemilik_rekening');
        if (namaPemilik && !namaPemilik.value.trim()) {
            showError(namaPemilik, 'Nama pemilik rekening harus diisi');
            errorMessages.push('Nama pemilik rekening harus diisi');
            isValid = false;
        }

        // Validate Jumlah Tenaga Kerja
        const tenagaKerja = document.getElementById('jumlah_tenaga_kerja');
        if (tenagaKerja) {
            const tenagaKerjaValue = parseInt(tenagaKerja.value) || 0;
            if (tenagaKerjaValue < 15) {
                showError(tenagaKerja, 'Jumlah tenaga kerja minimal 15 orang');
                errorMessages.push('Jumlah tenaga kerja minimal 15 orang');
                isValid = false;
            }
        }

        return { isValid, errorMessages };
    }

    // Handle form submission
    function handleFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');

        // Reset previous errors
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        // Validate form fields
        const { isValid, errorMessages } = validateFormFields();

        // If form is invalid, show error popup
        if (!isValid) {
            // Show error messages inline
            errorMessages.forEach(msg => {
                // Find the first field that matches the error message
                let field = null;
                if (msg.includes('Nama vendor')) field = document.getElementById('nama_vendor');
                else if (msg.includes('Nomor HP')) field = document.getElementById('no_hp');
                else if (msg.includes('Nomor Rekening')) field = document.getElementById('nomor_rekening');
                else if (msg.includes('Nama bank')) field = document.getElementById('nama_bank');
                else if (msg.includes('Nama pemilik')) field = document.getElementById('nama_pemilik_rekening');
                else if (msg.includes('Jumlah tenaga kerja')) field = document.getElementById('jumlah_tenaga_kerja');

                if (field) showError(field, msg);
            });

            // Show error popup
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: 'Silakan periksa kembali form yang Anda isi. Pastikan semua field yang wajib diisi sudah terisi dengan benar.',
                confirmButtonText: 'Mengerti'
            });

            // Scroll to first error field
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }

            // Reset button state
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Update';
            }

            return false;
        }

        // If all validations pass, submit the form via AJAX
        const formData = new FormData(form);

        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        }

        // Submit form via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Terjadi kesalahan saat menyimpan data');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else if (data.message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'Data vendor berhasil diperbarui',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('vendor.index') }}';
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: error.message || 'Gagal menyimpan data. Silakan coba lagi.'
            });
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Update';
            }
        });

        return false;
    }

    // Initialize when DOM is fully loaded
    // Initialize Select2 for bank dropdown
    function initBankSelect2() {
        // First, destroy any existing Select2 instances
        if ($('#nama_bank').hasClass('select2-hidden-accessible')) {
            $('#nama_bank').select2('destroy');
        }

        // Store the current value before reinitializing
        const currentValue = $('#nama_bank').val();

        // Initialize Select2 with proper configuration
        $('#nama_bank').select2({
            theme: 'bootstrap-5',
            placeholder: 'Ketik untuk mencari bank...',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#nama_bank').closest('.form-group'),
            minimumInputLength: 0,
            matcher: function(params, data) {
                // Always return the option if there's no search term
                if ($.trim(params.term) === '') {
                    return data;
                }

                // Check if the option text contains the search term (case-insensitive)
                if (data.text.toLowerCase().includes(params.term.toLowerCase())) {
                    return data;
                }

                // Check if the option value contains the search term (case-insensitive)
                if (data.id && data.id.toLowerCase().includes(params.term.toLowerCase())) {
                    return data;
                }

                return null;
            }
        });

        // Restore the current value after initialization
        if (currentValue) {
            $('#nama_bank').val(currentValue).trigger('change');
        }
    }

    // Initialize on document ready
    $(document).ready(function() {
        initBankSelect2();
    });

    // Re-initialize if the form is inside a modal that gets shown
    $(document).on('shown.bs.modal', '.modal', function() {
        initBankSelect2();
    });

        // Initialize vendor type handling
        const tebangGroup = document.getElementById('tebangCodeGroup');
        const kodeAngkutInput = document.getElementById('kode_vendor_angkut');
        const kodeTebangInput = document.getElementById('kode_vendor_tebang');
        const vendorTypeInput = document.getElementById('vendorType');

        // Toggle vendor fields based on vendor type
        function toggleVendorFields() {
            const jenisVendor = vendorTypeInput.value;
            if (jenisVendor === 'tebang') {
                if (tebangGroup) tebangGroup.style.display = 'block';
                if (kodeAngkutInput) kodeAngkutInput.required = false;
                if (kodeTebangInput) kodeTebangInput.required = true;
            } else if (jenisVendor === 'angkut') {
                if (tebangGroup) tebangGroup.style.display = 'none';
                if (kodeAngkutInput) kodeAngkutInput.required = true;
                if (kodeTebangInput) kodeTebangInput.required = false;
            } else {
                if (tebangGroup) tebangGroup.style.display = 'none';
                if (kodeAngkutInput) kodeAngkutInput.required = false;
                if (kodeTebangInput) kodeTebangInput.required = false;
            }
        }

        // Call the function on page load and when vendor type changes
        if (vendorTypeInput) {
            toggleVendorFields();
            vendorTypeInput.addEventListener('change', toggleVendorFields);
        }
            }
        });

        // Format bank display in dropdown
        function formatBank(bank) {
            if (!bank.id) {
                return bank.text;
            }
            return $('<span>').text(bank.text);
        }

        // Format selected bank
        function formatBankSelection(bank) {
            return bank.text;
        }

        // Initialize validation on page load
        if (document.getElementById('jumlah_tenaga_kerja')) {
            validateWorkerCount(document.getElementById('jumlah_tenaga_kerja'));
        }
        const originalKodeAngkut = kodeAngkutInput.value;
        const originalKodeTebang = kodeTebangInput.value;
        const originalJenisVendor = '{{ $vendor->jenis_vendor }}';

        // Initialize the display based on current vendor type
        function initializeVendorType() {
            const currentType = '{{ $vendor->jenis_vendor }}';
            const angkutGroup = document.getElementById('angkutCodeGroup');

            // Show/hide code groups based on current type
            if (currentType === 'angkut' || currentType === 'both') {
                if (angkutGroup) angkutGroup.style.display = 'block';
            } else if (angkutGroup) {
                angkutGroup.style.display = 'none';
            }

            if (currentType === 'tebang' || currentType === 'both') {
                tebangGroup.style.display = 'block';
            } else {
                tebangGroup.style.display = 'none';
            }

            // Set the initial values
            vendorTypeInput.value = currentType;
        }

        // Helper function to show error messages
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: message,
                confirmButtonText: 'Mengerti'
            });
        }

        function toggleVendorCodes() {
            const selectedValue = select.value;

            // Show/hide relevant fields based on selection
            angkutGroup.style.display = (selectedValue === 'angkut' || selectedValue === 'both') ? 'block' : 'none';
            tebangGroup.style.display = (selectedValue === 'tebang' || selectedValue === 'both') ? 'block' : 'none';

            // Update the hidden vendor type field
            vendorTypeInput.value = selectedValue;

            // Handle code generation when type changes
            if (selectedValue === 'angkut') {
                // If switching to angkut, restore original or use current angkut code
                kodeAngkutInput.value = originalKodeAngkut || kodeAngkutInput.value;
            } else if (selectedValue === 'tebang') {
                // If switching to tebang, restore original or use current tebang code
                kodeTebangInput.value = originalKodeTebang || kodeTebangInput.value;
            } else if (selectedValue === 'both') {
                // If switching to both, ensure both codes are set
                kodeAngkutInput.value = originalKodeAngkut || kodeAngkutInput.value;
                kodeTebangInput.value = originalKodeTebang || kodeTebangInput.value;
            }
        }

        // Initialize vendor type
        initializeVendorType();

        // Add form submission handler
        document.getElementById('vendorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            validateForm(e);
        });

        // Add event listener for vendor type changes
        if (vendorTypeInput) {
            vendorTypeInput.addEventListener('change', toggleVendorFields);
        }

            // Show success notification if there's a success message in session
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'Tutup'
                });
            @endif
        });
</script>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 for bank dropdown
        $('#nama_bank').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih Bank',
            allowClear: true
        });

        // Show/hide vendor code fields based on vendor type
        toggleVendorCodeFields();

        // Handle vendor type change
        $('#jenis_vendor').on('change', function() {
            toggleVendorCodeFields();
        });

        // Form validation
        $('#vendorForm').on('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    });

    // Toggle vendor code fields based on selected vendor type
    function toggleVendorCodeFields() {
        const vendorType = $('#jenis_vendor').val();

        // Hide both fields first
        $('#angkutCodeGroup, #tebangCodeGroup').hide();

        // Show relevant fields based on vendor type
        if (vendorType === 'angkut' || vendorType === 'both') {
            $('#angkutCodeGroup').show();
        }
        if (vendorType === 'tebang' || vendorType === 'both') {
            $('#tebangCodeGroup').show();
        }
    }

    // Validate worker count
    function validateWorkerCount(input) {
        const minWorkers = 15;
        const workerCount = parseInt(input.value) || 0;
        const errorElement = $('#worker_count_error');

        if (workerCount < minWorkers) {
            $(input).addClass('is-invalid');
            errorElement.show();
            return false;
        } else {
            $(input).removeClass('is-invalid');
            errorElement.hide();
            return true;
        }
    }

    // Validate bank account number
    function validateBankAccount(input) {
        const value = input.value.trim();
        const isValid = /^\d{5,20}$/.test(value);

        if (value && !isValid) {
            $(input).addClass('is-invalid');
            return false;
        } else {
            $(input).removeClass('is-invalid');
            return true;
        }
    }

    // Main form validation
    function validateForm() {
        let isValid = true;
        const form = document.getElementById('vendorForm');

        // Reset all error states
        $('.is-invalid').removeClass('is-invalid');

        // Validate required fields
        $('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            }
        });

        // Validate phone number format
        const phoneInput = $('#no_hp');
        if (phoneInput.val() && !/^08\d{8,11}$/.test(phoneInput.val())) {
            phoneInput.addClass('is-invalid');
            isValid = false;
        }

        // Validate worker count
        const workerCountInput = $('#jumlah_tenaga_kerja');
        if (workerCountInput.val() && !validateWorkerCount(workerCountInput[0])) {
            isValid = false;
        }

        // Validate bank account
        const bankAccountInput = $('#nomor_rekening');
        if (bankAccountInput.val() && !validateBankAccount(bankAccountInput[0])) {
            isValid = false;
        }

        // If form is invalid, scroll to first error
        if (!isValid) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);

            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Mohon periksa kembali form Anda',
                confirmButtonText: 'Mengerti'
            });
        }

        return isValid;
    }
</script>
@endpush

@endsection
