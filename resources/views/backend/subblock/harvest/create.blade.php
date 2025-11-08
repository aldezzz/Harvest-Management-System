@extends('layouts.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* Validation alert box */
    .validation-alert {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1100;
        width: 90%;
        max-width: 800px;
        display: none;
        animation: slideDown 0.5s ease-out;
    }
    
    @keyframes slideDown {
        from { transform: translate(-50%, -100%); opacity: 0; }
        to { transform: translate(-50%, 0); opacity: 1; }
    }
    
    .validation-alert.show {
        display: block;
    }
    
    .form-container {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 2rem;
        margin: 2rem auto;
        max-width: 1200px;
        position: relative;
        z-index: 1;
    }

    .form-container h1 {
        color: #2d3748;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-label {
        font-weight: 500;
        color: #4a5568;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.875rem;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        transition: all 0.2s;
        font-size: 0.875rem;
        height: calc(1.5em + 0.75rem + 2px);
    }

    .form-control:focus, .form-select:focus {
        border-color: #4299e1;
        box-shadow: 0 0 0 0.2rem rgba(66, 153, 225, 0.25);
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .btn {
        font-weight: 500;
        padding: 0.5rem 1.25rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .btn-primary {
        background-color: #4299e1;
        border-color: #4299e1;
    }

    .btn-primary:hover {
        background-color: #3182ce;
        border-color: #2c5282;
    }

    .btn-outline-secondary {
        color: #4a5568;
        border-color: #cbd5e0;
    }

    .btn-outline-secondary:hover {
        background-color: #f7fafc;
        border-color: #a0aec0;
    }

    .invalid-feedback {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        color: #e53e3e;
    }

    .is-invalid {
        border-color: #fc8181;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }

    .text-danger {
        color: #e53e3e;
    }

    textarea.form-control {
        min-height: 100px;
    }
</style>
@endpush

@section('content')
<!-- Validation Alert Box -->
<div id="validationAlert" class="alert alert-danger validation-alert" role="alert">
    <div class="d-flex align-items-center">
        <i class="fas fa-exclamation-triangle me-3" style="font-size: 1.5rem;"></i>
        <div>
            <h5 class="alert-heading mb-1">Terdapat kesalahan pada form</h5>
            <div id="validationErrors" class="mb-0"></div>
        </div>
        <button type="button" class="btn-close ms-auto" onclick="hideValidationAlert()" aria-label="Close"></button>
    </div>
</div>

<div class="container-fluid py-4">
    <div class="form-container">
        <h1>Tambah Data Panen</h1>
        <form action="{{ route('harvest-sub-blocks.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="kode_petak" class="form-label">Kode Petak <span class="text-danger">*</span></label>
                                <select name="kode_petak" id="kode_petak" class="form-select @error('kode_petak') is-invalid @enderror" required style="width: 100%;">
                                    <option value="">-- Pilih Sub-block --</option>
                                    @foreach($subBlocks as $subBlock)
                                        <option value="{{ $subBlock->kode_petak }}"
                                                data-estate="{{ $subBlock->estate }}"
                                                data-divisi="{{ $subBlock->divisi }}"
                                                data-age-months="{{ $subBlock->age_months }}"
                                                data-zona="{{ $subBlock->zona }}"
                                                data-luas-area="{{ $subBlock->luas_area }}"
                                                {{ old('kode_petak') == $subBlock->kode_petak ? 'selected' : '' }}>
                                            {{ $subBlock->kode_petak }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kode_petak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Estate</label>
                                <input type="text" id="estate_display" class="form-control" readonly>
                                <input type="hidden" name="estate" id="estate">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Divisi</label>
                                <input type="text" id="divisi_display" class="form-control" readonly>
                                <input type="hidden" name="divisi" id="divisi">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="age_months" class="form-label">Umur (Bulan) <span class="text-danger">*</span></label>
                                <input type="number" name="age_months" id="age_months" class="form-control @error('age_months') is-invalid @enderror" value="{{ old('age_months') }}" min="1" required readonly>
                                @error('age_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="yield_estimate_tph" class="form-label">Estimasi (ton/ha) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="yield_estimate_tph" id="yield_estimate_tph" class="form-control @error('yield_estimate_tph') is-invalid @enderror" value="{{ old('yield_estimate_tph') }}" min="0" required readonly>
                                @error('yield_estimate_tph')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="planned_harvest_date" class="form-label">Rencana Panen <span class="text-danger">*</span></label>
                                <input type="date" name="planned_harvest_date" id="planned_harvest_date" class="form-control @error('planned_harvest_date') is-invalid @enderror" value="{{ old('planned_harvest_date') }}" required>
                                @error('planned_harvest_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="harvest_season" class="form-label">Musim Panen <span class="text-danger">*</span></label>
                                <input type="text" name="harvest_season" id="harvest_season" class="form-control @error('harvest_season') is-invalid @enderror" value="{{ old('harvest_season') }}" required>
                                @error('harvest_season')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="luas_area" class="form-label">Luas Area (ha) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="luas_area" id="luas_area" class="form-control @error('luas_area') is-invalid @enderror" value="{{ old('luas_area') }}" min="0" required>
                                @error('luas_area')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="priority_level" class="form-label">Prioritas <span class="text-danger">*</span></label>
                                <select name="priority_level" id="priority_level" class="form-select @error('priority_level') is-invalid @enderror" required>
                                    <option value="">-- Pilih Prioritas --</option>
                                    <option value="1" {{ old('priority_level') == 1 ? 'selected' : '' }}>1 - Prioritas Tertinggi</option>
                                    <option value="2" {{ old('priority_level') == 2 ? 'selected' : '' }}>2 - Prioritas Menengah</option>
                                    <option value="3" {{ old('priority_level') == 3 ? 'selected' : '' }}>3 - Prioritas Standar</option>
                                </select>
                                @error('priority_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="remarks" class="form-label">Keterangan</label>
                                <input type="text" name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror" value="{{ old('remarks') }}">
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="action-buttons">
                            <a href="{{ route('harvest-sub-blocks.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            @can('create-harvest-sub-block')
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Data
                            </button>
                            @endcan
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for kode_petak dropdown
        const $kodePetakSelect = $('#kode_petak');

        $kodePetakSelect.select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Sub-block --',
            allowClear: true,
            width: '100%',
            dropdownParent: $kodePetakSelect.parent()
        });

        // Handle estate and division display when sub-block is selected
        const estateDisplay = document.getElementById('estate_display');
        const estateInput = document.getElementById('estate');
        const divisiDisplay = document.getElementById('divisi_display');
        const divisiInput = document.getElementById('divisi');

        function updateEstateAndDivision() {
            const selectedOption = $kodePetakSelect.find('option:selected');
            const estate = selectedOption.data('estate');
            const divisi = selectedOption.data('divisi');
            const ageMonths = selectedOption.data('age-months') || '';
            const zona = selectedOption.data('zona');
            const luasArea = selectedOption.data('luas-area') || '';

            if (estate) {
                // Update estate and division
                estateDisplay.value = estate;
                estateInput.value = estate;
                divisiDisplay.value = divisi;
                divisiInput.value = divisi;

                // Update age months
                document.getElementById('age_months').value = ageMonths;

                // Set yield estimate based on age
                if (parseInt(ageMonths) === 10) {
                    document.getElementById('yield_estimate_tph').value = '50';
                } else {
                    // Default yield estimate if age is not 10
                    document.getElementById('yield_estimate_tph').value = '30'; // Default value, adjust as needed
                }

                // Set priority based on zona (1-3)
                if (zona && zona >= 1 && zona <= 3) {
                    document.getElementById('priority_level').value = zona;
                }

                // Update luas area with 2 decimal places
                if (luasArea) {
                    // Convert to number and format to 2 decimal places
                    const formattedLuasArea = parseFloat(luasArea).toFixed(2);
                    document.getElementById('luas_area').value = formattedLuasArea;
                }
            } else {
                // Clear all fields if no selection
                estateDisplay.value = '';
                estateInput.value = '';
                divisiDisplay.value = '';
                divisiInput.value = '';
                document.getElementById('age_months').value = '';
            }
        }

        // Initial update
        updateEstateAndDivision();

        // Update when selection changes
        $kodePetakSelect.on('change', updateEstateAndDivision);

        // Function to update harvest season based on selected date
        function updateHarvestSeason(selectedDate) {
            const harvestDate = new Date(selectedDate);
            if (!isNaN(harvestDate.getTime())) {
                // Format: "Tahun [YEAR]" (e.g., "Tahun 2025")
                document.getElementById('harvest_season').value = harvestDate.getFullYear();
            } else {
                document.getElementById('harvest_season').value = '';
            }
        }

        // Auto-fill harvest season when planned harvest date changes
        const plannedHarvestDateInput = document.getElementById('planned_harvest_date');
        plannedHarvestDateInput.addEventListener('change', function() {
            updateHarvestSeason(this.value);
        });

        // Also update on page load if there's already a value
        if (plannedHarvestDateInput.value) {
            updateHarvestSeason(plannedHarvestDateInput.value);
        }

        // Function to show validation errors in a beautiful alert
        function showValidationAlert(errors) {
            // Create a more structured error message
            const errorMessages = errors.map(error => ({
                text: error,
                icon: '❌'  // Using emoji for better visibility
            }));
            
            // Group similar errors
            const groupedErrors = [];
            const errorCounts = {};
            
            errors.forEach(error => {
                errorCounts[error] = (errorCounts[error] || 0) + 1;
            });
            
            for (const [error, count] of Object.entries(errorCounts)) {
                if (count > 1) {
                    groupedErrors.push(`❌ ${error} (${count}x)`);
                } else {
                    groupedErrors.push(`❌ ${error}`);
                }
            }
            
            // Show the alert with better formatting
            Swal.fire({
                title: '<strong>Perhatian: Data Belum Lengkap</strong>',
                html: `
                    <div class="text-start">
                        <p class="mb-3">Mohon periksa kembali form Anda:</p>
                        <div class="alert alert-warning text-start p-3" style="max-height: 300px; overflow-y: auto;">
                            ${groupedErrors.join('<br>')}
                        </div>
                        <p class="mt-3 text-muted small">
                            <i class="fas fa-lightbulb me-1"></i> 
                            Klik pada pesan error untuk langsung ke kolom yang bermasalah
                        </p>
                    </div>
                `,
                icon: 'warning',
                confirmButtonText: 'Saya Mengerti',
                confirmButtonColor: '#3085d6',
                customClass: {
                    popup: 'animate__animated animate__fadeInDown',
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown',
                },
                showCloseButton: true,
                focusConfirm: false,
                allowOutsideClick: false,
                didOpen: () => {
                    // Make error messages clickable to focus on the field
                    document.querySelectorAll('.swal2-html-container .alert div').forEach((el, index) => {
                        el.style.cursor = 'pointer';
                        el.onclick = () => {
                            const errorField = document.querySelectorAll('.is-invalid')[index];
                            if (errorField) {
                                errorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                errorField.focus();
                            }
                            Swal.close();
                        };
                    });
                }
            });
            
            // Scroll to first error field if exists
            const firstErrorField = document.querySelector('.is-invalid');
            if (firstErrorField) {
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstErrorField.focus();
            }
        }

        // Function to validate and format number with 2 decimal places
        function formatNumberWithTwoDecimals(input) {
            if (!input.value) return;
            
            // Replace comma with dot if needed
            input.value = input.value.replace(',', '.');
            
            // Ensure it's a valid number
            const num = parseFloat(input.value);
            if (isNaN(num)) {
                input.value = '';
                return false;
            }
            
            // Format to 2 decimal places
            input.value = num.toFixed(2);
            return true;
        }

        // Function to validate form fields and show all missing/incorrect fields
        function validateForm() {
            const errors = [];
            const fieldsToHighlight = [];
            
            // Field configurations with custom error messages
            const fieldConfigs = [
                { 
                    id: 'kode_petak', 
                    name: 'Kode Petak',
                    required: true,
                    errorMessage: 'Silakan pilih Kode Petak dari daftar yang tersedia'
                },
                { 
                    id: 'planned_harvest_date', 
                    name: 'Rencana Panen',
                    required: true,
                    errorMessage: 'Silakan pilih tanggal Rencana Panen'
                },
                { 
                    id: 'age_months', 
                    name: 'Umur (Bulan)',
                    required: true,
                    type: 'number',
                    min: 0,
                    integerOnly: true,
                    errorMessage: 'Umur harus berupa angka bulat (contoh: 12)'
                },
                { 
                    id: 'yield_estimate_tph', 
                    name: 'Estimasi (ton/ha)',
                    required: true,
                    type: 'number',
                    min: 0.01,
                    errorMessage: 'Estimasi harus berupa angka (contoh: 12.5)'
                },
                { 
                    id: 'luas_area', 
                    name: 'Luas Area (ha)',
                    required: true,
                    type: 'number',
                    min: 0.01,
                    requireTwoDecimals: true,
                    errorMessage: 'Luas area harus berupa angka dengan 2 angka di belakang koma (contoh: 2.50)'
                },
                { 
                    id: 'priority_level', 
                    name: 'Tingkat Prioritas',
                    required: true,
                    errorMessage: 'Silakan pilih Tingkat Prioritas'
                }
            ];

            // Validate each field
            fieldConfigs.forEach(field => {
                const element = document.getElementById(field.id);
                const value = element ? element.value.trim() : '';
                
                // Check required fields
                if (field.required && !value) {
                    errors.push(field.errorMessage || `Kolom ${field.name} wajib diisi`);
                    fieldsToHighlight.push(field.id);
                    return;
                }
                
                // Skip further validation if no value (for non-required fields)
                if (!value) return;
                
                // Number validation
                if (field.type === 'number') {
                    const num = parseFloat(value.replace(',', '.'));
                    
                    // Check if valid number
                    if (isNaN(num)) {
                        errors.push(field.errorMessage || `Format ${field.name} tidak valid`);
                        fieldsToHighlight.push(field.id);
                        return;
                    }
                    
                    // Check minimum value
                    if (field.min !== undefined && num < field.min) {
                        errors.push(`${field.name} tidak boleh kurang dari ${field.min}`);
                        fieldsToHighlight.push(field.id);
                    }
                    
                    // Check for integer only
                    if (field.integerOnly && !Number.isInteger(num)) {
                        errors.push(`${field.name} harus berupa bilangan bulat`);
                        fieldsToHighlight.push(field.id);
                    }
                    
                    // Format to 2 decimal places if required
                    if (field.requireTwoDecimals) {
                        const formatted = parseFloat(num.toFixed(2));
                        if (formatted !== num) {
                            errors.push(`${field.name} harus memiliki 2 angka di belakang koma (contoh: ${formatted.toFixed(2)})`);
                            fieldsToHighlight.push(field.id);
                        }
                        element.value = formatted.toFixed(2);
                    }
                }
            });
            
                // If there are errors, show them all at once
            if (errors.length > 0) {
                // Highlight all problematic fields
                fieldsToHighlight.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.classList.add('is-invalid');
                        
                        // Add event to remove error state when user starts typing/selecting
                        const eventType = element.tagName === 'SELECT' ? 'change' : 'input';
                        const clearError = function() {
                            this.classList.remove('is-invalid');
                            this.removeEventListener(eventType, clearError);
                        };
                        element.addEventListener(eventType, clearError, { once: true });
                        
                        // Add invalid feedback element if it doesn't exist
                        if (!element.nextElementSibling || !element.nextElementSibling.classList.contains('invalid-feedback')) {
                            const invalidFeedback = document.createElement('div');
                            invalidFeedback.className = 'invalid-feedback';
                            invalidFeedback.textContent = errors.find(e => e.includes(element.name) || e.includes(element.id)) || 'Nilai tidak valid';
                            element.parentNode.insertBefore(invalidFeedback, element.nextSibling);
                        }
                    }
                });
                
                // Show all errors in a beautiful alert
                showValidationAlert(errors);
                
                // Scroll to first error field
                const firstErrorField = document.querySelector('.is-invalid');
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstErrorField.focus();
                }
                
                return false;
            }
            
            // If we got here, all validations passed
            return true;
            
            return true;
        }

        // Form submission handler
        const form = document.querySelector('form.needs-validation');
        if (form) {
            form.addEventListener('submit', function(event) {
                // Prevent default form submission
                event.preventDefault();
                
                // Remove previous error highlights
                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                
                // Validate the form
                const isValid = validateForm();
                
                // If form is valid, submit it
                if (isValid) {
                    // You can add a loading state here if needed
                    // form.querySelector('button[type="submit"]').disabled = true;
                    
                    // Submit the form
                    form.submit();
                }
                
                // Always add was-validated to show validation states
                form.classList.add('was-validated');
            });
        }

        // Add input validation and formatting for number fields
        document.querySelectorAll('input[type="number"]').forEach(input => {
            // Format on blur
            input.addEventListener('blur', function() {
                if (this.value) {
                    // Format number with 2 decimal places
                    const formatted = parseFloat(this.value).toFixed(2);
                    if (!isNaN(formatted)) {
                        this.value = formatted;
                    }
                    
                    // Validate min value
                    if (this.min && parseFloat(this.value) < parseFloat(this.min)) {
                        showSweetAlert('warning', 'Nilai Terlalu Kecil', `Nilai minimal untuk ${this.labels[0]?.textContent?.trim() || 'field ini'} adalah ${this.min}`);
                        this.value = this.min;
                        this.focus();
                    }
                }
            });
            
            // Prevent invalid characters
            input.addEventListener('input', function(e) {
                // Allow numbers, single dot, and minus (if allowed)
                if (this.min >= 0) {
                    this.value = this.value.replace(/[^0-9.]/g, '');
                    // Ensure only one decimal point
                    if ((this.value.match(/\./g) || []).length > 1) {
                        this.value = this.value.replace(/\.+$/, '');
                    }
                } else {
                    this.value = this.value.replace(/[^0-9.-]/g, '');
                }
            });
        });
        
        // Special handling for luas_area to ensure 2 decimal places
        const luasAreaInput = document.getElementById('luas_area');
        if (luasAreaInput) {
            luasAreaInput.addEventListener('blur', function() {
                if (this.value) {
                    const num = parseFloat(this.value.replace(',', '.'));
                    if (isNaN(num)) {
                        showSweetAlert('error', 'Format Tidak Valid', 'Luas Area harus berupa angka');
                        this.value = '';
                        this.focus();
                    } else {
                        this.value = num.toFixed(2);
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
