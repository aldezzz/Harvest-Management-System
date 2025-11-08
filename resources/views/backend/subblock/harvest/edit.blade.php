@extends('layouts.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* Form Styles */
    .form-container {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        padding: 1.5rem;
    }

    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
        border: 1px solid #3b82f6;
    }

    .btn-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
    }

    .btn-outline-secondary {
        background-color: white;
        color: #6b7280;
        border: 1px solid #d1d5db;
    }

    .btn-outline-secondary:hover {
        background-color: #f9fafb;
        color: #374151;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .is-invalid ~ .invalid-feedback {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Harvest Data</h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-container">
                <form action="{{ route('harvest-sub-blocks.update', $harvestSubBlock->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode_petak" class="form-label">Sub-block Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{ $harvestSubBlock->kode_petak }}" readonly>
                                <input type="hidden" name="kode_petak" value="{{ $harvestSubBlock->kode_petak }}">
                                @error('kode_petak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harvest_season" class="form-label">Harvest Season <span class="text-danger">*</span></label>
                                <input type="text" name="harvest_season" id="harvest_season" class="form-control @error('harvest_season') is-invalid @enderror" value="{{ old('harvest_season', $harvestSubBlock->harvest_season) }}" readonly>
                                @error('harvest_season')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="age_months" class="form-label">Age (Months) <span class="text-danger">*</span></label>
                                <input type="number" name="age_months" id="age_months" class="form-control @error('age_months') is-invalid @enderror" value="{{ old('age_months', $harvestSubBlock->age_months) }}" min="1" readonly>
                                @error('age_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="estate" class="form-label">Estate <span class="text-danger">*</span></label>
                                <input type="text" name="estate" id="estate" class="form-control @error('estate') is-invalid @enderror" value="{{ old('estate', $harvestSubBlock->estate) }}" readonly>
                                @error('estate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="divisi" class="form-label">Divisi <span class="text-danger">*</span></label>
                                <input type="text" name="divisi" id="divisi" class="form-control @error('divisi') is-invalid @enderror" value="{{ old('divisi', $harvestSubBlock->divisi) }}" readonly>
                                @error('divisi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="luas_area" class="form-label">Luas Area (ha) <span class="text-danger">*</span></label>
                                @php
                                    $luasArea = old('luas_area', $harvestSubBlock->luas_area);
                                    $formattedLuasArea = $luasArea !== null ? number_format((float)$luasArea, 2, '.', '') : '';
                                @endphp
                                <input type="number" step="0.01" name="luas_area" id="luas_area" class="form-control @error('luas_area') is-invalid @enderror" value="{{ $formattedLuasArea }}" min="0" readonly>
                                @error('luas_area')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="yield_estimate_tph" class="form-label">Yield Estimate (ton/ha) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="yield_estimate_tph" id="yield_estimate_tph" class="form-control @error('yield_estimate_tph') is-invalid @enderror" value="{{ old('yield_estimate_tph', $harvestSubBlock->yield_estimate_tph) }}" min="0" readonly>
                                @error('yield_estimate_tph')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="planned_harvest_date" class="form-label">Planned Harvest Date <span class="text-danger">*</span></label>
                                @php
                                    $plannedDate = old('planned_harvest_date', $harvestSubBlock->planned_harvest_date);
                                    $formattedDate = $plannedDate ? \Carbon\Carbon::parse($plannedDate)->format('Y-m-d') : '';
                                @endphp
                                <input type="date" name="planned_harvest_date" id="planned_harvest_date" class="form-control @error('planned_harvest_date') is-invalid @enderror" value="{{ $formattedDate }}" required>
                                @error('planned_harvest_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="priority_level" class="form-label">Prioritas <span class="text-danger">*</span></label>
                                <select name="priority_level" id="priority_level" class="form-select @error('priority_level') is-invalid @enderror" required>
                                    <option value="">-- Pilih Prioritas --</option>
                                    <option value="1" {{ old('priority_level', $harvestSubBlock->priority_level) == 1 ? 'selected' : '' }}>1 - Prioritas Tertinggi</option>
                                    <option value="2" {{ old('priority_level', $harvestSubBlock->priority_level) == 2 ? 'selected' : '' }}>2 - Prioritas Menengah</option>
                                    <option value="3" {{ old('priority_level', $harvestSubBlock->priority_level) == 3 ? 'selected' : '' }}>3 - Prioritas Standar</option>
                                </select>
                                @error('priority_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>



                        <div class="col-12">
                            <div class="form-group">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="3">{{ old('remarks', $harvestSubBlock->remarks) }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('harvest-sub-blocks.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                        @can('edit-harvest-sub-block')
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Harvest Data
                        </button>
                        @endcan
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
    document.addEventListener('DOMContentLoaded', function() {
        // Update harvest season when planned date changes
        document.getElementById('planned_harvest_date').addEventListener('change', function() {
            const date = new Date(this.value);
            const year = date.getFullYear();
            document.getElementById('harvest_season').value = year;
        });

        // Form validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(event) {
                // Prevent default form submission
                event.preventDefault();
                
                // Remove previous error highlights
                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                
                // Validate the form
                let isValid = true;
                const errors = [];
                
                // Check required fields
                const requiredFields = [
                    { id: 'planned_harvest_date', name: 'Rencana Panen' },
                    { id: 'priority_level', name: 'Tingkat Prioritas' }
                ];
                
                requiredFields.forEach(field => {
                    const element = document.getElementById(field.id);
                    if (!element.value.trim()) {
                        element.classList.add('is-invalid');
                        errors.push(`❌ ${field.name} harus diisi`);
                        isValid = false;
                    }
                });
                
                // Show errors if any
                if (!isValid) {
                    const errorMessage = errors.join('\n');
                    Swal.fire({
                        title: 'Perhatian',
                        text: errorMessage,
                        icon: 'warning',
                        confirmButtonText: 'Mengerti',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    
                    // Scroll to first error
                    const firstError = document.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                    return false;
                }
                
                // If all valid, submit the form
                form.submit();
            });
        }
        // Initialize date picker
        const plannedHarvestDate = document.getElementById('planned_harvest_date');
        if (plannedHarvestDate) {
            // Set min date to today
            const today = new Date().toISOString().split('T')[0];
            plannedHarvestDate.min = today;
        }
    });
</script>
@endpush

@endsection
