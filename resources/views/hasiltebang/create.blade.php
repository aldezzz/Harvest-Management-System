@extends('layouts.master')

@section('page-title', 'Tambah Data Hasil Tebang')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hasil-tebang.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        height: auto;
        line-height: 1.5;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Tambah Data Hasil Tebang</h2>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="hasilTebangForm" action="{{ route('hasil-tebang.store') }}" method="POST">
                        @csrf

                        <!-- Informasi Umum -->
                        <div class="form-section">
                            <h3 class="section-title">Informasi Umum</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Kode Hasil Tebang</label>
                                    <input type="text" class="form-control" name="kode_hasil_tebang" value="{{ $kodeHasilTebang }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label>Tanggal Timbang</label>
                                    <input type="date" class="form-control" name="tanggal_timbang" value="{{ old('tanggal_timbang', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label>No LKT</label>
                                <select class="form-control select2" name="kode_lkt" id="kode_lkt" required style="width: 100%;">
                                    <option value="">-- Pilih No LKT --</option>
                                    @foreach($lkts as $lkt)
                                        <option value="{{ $lkt['kode_lkt'] }}"
                                            data-kode-spt="{{ $lkt['kode_spt'] }}"
                                            data-kode-petak="{{ $lkt['kode_petak'] }}"
                                            data-divisi="{{ $lkt['divisi'] }}"
                                            data-vendor-tebang="{{ $lkt['vendor_tebang'] }}"
                                            data-vendor-angkut="{{ $lkt['vendor_angkut'] }}"
                                            data-zonasi="{{ $lkt['zonasi'] }}"
                                            data-jenis-tebang="{{ $lkt['jenis_tebangan'] }}"
                                            data-kode-lambung="{{ $lkt['kode_lambung'] }}">
                                            {{ $lkt['kode_lkt'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Data LKT -->
                        <div class="form-section">
                            <h3 class="section-title">Data LKT</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>No SPT</label>
                                    <input type="text" class="form-control" name="kode_spt" id="kode_spt" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label>Kode Petak</label>
                                    <input type="text" class="form-control" name="kode_petak" id="kode_petak" readonly>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <label>Divisi</label>
                                    <input type="text" class="form-control" name="divisi" id="divisi" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>Vendor Tebang</label>
                                    <input type="text" class="form-control" id="vendor_tebang_display" readonly>
                                    <input type="hidden" name="vendor_tebang" id="vendor_tebang">
                                </div>
                                <div class="col-md-4">
                                    <label>Vendor Angkut</label>
                                    <input type="text" class="form-control" id="vendor_angkut_display" readonly>
                                    <input type="hidden" name="vendor_angkut" id="vendor_angkut">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label>Zonasi</label>
                                    <input type="text" class="form-control" name="zonasi" id="zonasi" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label>Jenis Tebang</label>
                                    <input type="text" class="form-control" name="jenis_tebang" id="jenis_tebang" readonly>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label>Kode Lambung</label>
                                <input type="text" class="form-control" id="kode_lambung_display" readonly>
                                <input type="hidden" name="kode_lambung" id="kode_lambung">

                            </div>
                        </div>

                        <!-- Data Timbangan -->
                        <div class="form-section">
                            <h3 class="section-title">Data Timbangan</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Total Bruto (ton) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="bruto" id="bruto" required>
                                    <small class="form-text text-muted">Masukkan berat kotor dalam ton</small>
                                    <div id="bruto-error" class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6">
                                    <label>Tanggal & Jam Bruto <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" name="tanggal_bruto" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label>Total Tarra (ton) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="tarra" id="tarra" required>
                                    <small class="form-text text-muted">Masukkan berat kendaraan kosong dalam ton</small>
                                    <div id="tarra-error" class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6">
                                    <label>Tanggal & Jam Tarra <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" name="tanggal_tarra" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <label>Netto 1 (ton)</label>
                                    <input type="number" step="0.01" class="form-control bg-light" name="netto1" id="netto1" readonly>
                                    <small class="form-text text-muted">Bruto - Tarra</small>
                                </div>
                                <div class="col-md-4">
                                    <label>Sortase (ton) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="sortase" id="sortase" required>
                                </div>
                                <div class="col-md-4">
                                    <label>Netto 2 (ton)</label>
                                    <input type="number" step="0.01" class="form-control bg-light" name="netto2" id="netto2" readonly>
                                    <small class="form-text text-muted">Netto 1 - Sortase</small>
                                </div>
                            </div>

                        <div class="form-group mt-4">
                            <button type="button" id="btnSimpan" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Data
                            </button>
                            <a href="{{ route('hasil-tebang.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for LKT field
        $('#kode_lkt').select2({
            placeholder: '-- Pilih No LKT --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#kode_lkt').parent()
        });

        // Handle LKT change event
        $('#kode_lkt').on('change', function () {
            const opt = $(this).find(':selected');
            $('#kode_spt').val(opt.data('kode-spt'));
            $('#kode_petak').val(opt.data('kode-petak'));
            $('#divisi').val(opt.data('divisi'));
            $('#vendor_tebang_display').val(opt.data('vendor-tebang'));
            $('#vendor_tebang').val(opt.data('vendor-tebang'));
            $('#vendor_angkut_display').val(opt.data('vendor-angkut'));
            $('#vendor_angkut').val(opt.data('vendor-angkut'));
            $('#zonasi').val(opt.data('zonasi'));
            $('#jenis_tebang').val(opt.data('jenis-tebang'));
            $('#kode_lambung_display').val(opt.data('kode-lambung'));
            $('#kode_lambung').val(opt.data('kode-lambung'));

        });

        function validateWeights() {
            const bruto = parseFloat($('#bruto').val()) || 0;
            const tarra = parseFloat($('#tarra').val()) || 0;
            const sortase = parseFloat($('#sortase').val()) || 0;
            const kodeLambung = $('#kode_lambung').val() || '';
            const isPickup = kodeLambung.toLowerCase().includes('pickup');
            let isValid = true;
            
            // Reset error states
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            
            // Validate bruto and tarra
            if (isNaN(bruto) || bruto <= 0) {
                showError('bruto', 'Masukkan nilai bruto yang valid (harus lebih dari 0)');
                isValid = false;
            }
            
            if (isNaN(tarra) || tarra < 0) {
                showError('tarra', 'Masukkan nilai tarra yang valid (tidak boleh negatif)');
                isValid = false;
            }
            
            if (bruto <= tarra) {
                showError('tarra', 'Nilai tarra tidak boleh melebihi atau sama dengan bruto');
                isValid = false;
            }
            
            // Check maximum weight based on vehicle type
            const maxWeight = isPickup ? 5 : 15;
            if (bruto > maxWeight) {
                showError('bruto', `Berat maksimum untuk kendaraan ${isPickup ? 'pickup' : 'truk'} adalah ${maxWeight} ton`);
                isValid = false;
            }
            
            // Validate sortase
            const netto1 = bruto - tarra;
            if (isNaN(sortase) || sortase < 0) {
                showError('sortase', 'Nilai sortase tidak boleh negatif');
                isValid = false;
            } else if (sortase > netto1) {
                showError('sortase', `Nilai sortase tidak boleh melebihi Netto 1 (${netto1.toFixed(2)} ton)`);
                isValid = false;
            }
            
            return isValid;
        }
        
        function showError(field, message) {
            $(`#${field}`).addClass('is-invalid');
            $(`<div class="invalid-feedback">${message}</div>`).insertAfter(`#${field}`);
        }

        function calculateNetto() {
            const bruto = parseFloat($('#bruto').val()) || 0;
            const tarra = parseFloat($('#tarra').val()) || 0;
            const sortase = parseFloat($('#sortase').val()) || 0;
            
            // Calculate netto1 (bruto - tarra)
            const netto1 = Math.max(0, bruto - tarra);
            $('#netto1').val(netto1.toFixed(2));
            
            // Calculate netto2 (netto1 - sortase)
            const netto2 = Math.max(0, netto1 - sortase);
            $('#netto2').val(netto2.toFixed(2));
        }
        
        // Event listeners for weight calculations
        $('#bruto, #tarra, #sortase').on('input', function() {
            // Only validate weights if the input is not empty
            if ($(this).val() !== '') {
                validateWeights();
                calculateNetto();
            }
        });
        
        // Additional validation on blur to ensure empty fields are validated
        $('#bruto, #tarra, #sortase').on('blur', function() {
            validateWeights();
        });

        // Handle tombol simpan
        $('#btnSimpan').click(function(e) {
            e.preventDefault();

            // Validate form
            if (!validateWeights()) {
                // Get all error messages
                const errorMessages = [];
                $('.is-invalid').each(function() {
                    const errorMsg = $(this).next('.invalid-feedback').text();
                    if (errorMsg && !errorMessages.includes(errorMsg)) {
                        errorMessages.push(errorMsg);
                    }
                });
                
                // Show error message in Swal
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: 'Terdapat kesalahan pada form:<br><br>' + 
                          errorMessages.map(msg => `• ${msg}`).join('<br>'),
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#3085d6',
                }).then(() => {
                    // Scroll to first error
                    $('html, body').animate({
                        scrollTop: $('.is-invalid').first().offset().top - 100
                    }, 500);
                    // Focus on first error field
                    $('.is-invalid').first().focus();
                });
                return;
            }
            
            // Check form validity
            const form = document.getElementById('hasilTebangForm');
            if (!form.checkValidity()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Form Belum Lengkap',
                    text: 'Harap lengkapi semua field yang wajib diisi',
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#3085d6',
                });
                form.reportValidity();
                return;
            }

            // Tampilkan konfirmasi
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah data yang Anda input sudah benar?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Periksa Kembali',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika dikonfirmasi, submit form
                    $('#hasilTebangForm').submit();
                }
            });
        });

        // Handle submit form dengan tombol Enter
        $('#hasilTebangForm').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#btnSimpan').click();
            }
        });
    });
</script>
@endpush
