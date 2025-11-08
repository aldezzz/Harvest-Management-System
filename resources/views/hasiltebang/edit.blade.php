@extends('layouts.master')

@section('page-title', 'Edit Data ')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h2 class="text-xl font-bold mb-4">Edit Data - {{ $hasil->kode_hasil_tebang }}</h2>

    <form action="{{ route('hasil-tebang.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Informasi Umum -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-3 border-b pb-1">Informasi Umum</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="font-medium">Kode Hasil Tebang</label>
                    <input type="text" class="form-input bg-gray-100" value="{{ $hasil->kode_hasil_tebang }}" readonly>
                </div>
                <div>
                    <label class="font-medium">Tanggal Timbang</label>
                    <input type="date" class="form-input bg-gray-100" value="{{ $hasil->tanggal_timbang }}" readonly>
                </div>
                <div>
                    <label class="font-medium">No LKT</label>
                    <input type="text" class="form-input bg-gray-100" value="{{ $hasil->kode_lkt }}" readonly>
                </div>
            </div>
        </div>

        <!-- Data LKT -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-3 border-b pb-1">Data LKT</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="font-medium">No SPT</label>
                    <input type="text" class="form-input bg-gray-100" value="{{ $hasil->kode_spt }}" readonly>
                </div>
                <div>
                    <label class="font-medium">Kode Petak</label>
                    <input type="text" class="form-input bg-gray-100" value="{{ $hasil->kode_petak }}" readonly>
                </div>
                <div>
                    <label class="font-medium">Vendor Tebang</label>
                    <input type="text" class="form-input bg-gray-100" value="{{ $hasil->vendor_tebang }}" readonly>
                </div>
                <div>
                    <label class="font-medium">Vendor Angkut</label>
                    <input type="text" class="form-input bg-gray-100" value="{{ $hasil->vendor_angkut }}" readonly>
                </div>
                <div>
                    <label class="font-medium">Kode Lambung</label>
                    <input type="text" class="form-input bg-gray-100" value="{{ $hasil->kode_lambung }}" readonly>
                </div>
            </div>
        </div>

        <!-- Data Timbangan -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-3 border-b pb-1">Data Timbangan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="bruto" class="font-medium">Total Bruto (ton) <span class="text-red-500">*</span></label>
                    <input type="number" name="bruto" id="bruto" step="0.01" min="0" class="form-input" 
                           value="{{ number_format($hasil->bruto, 2, '.', '') }}" required>
                    <small class="text-gray-500 text-sm">Masukkan berat kotor dalam ton</small>
                    <div id="bruto-error" class="text-red-500 text-sm hidden"></div>
                </div>
                <div>
                    <label for="tanggal_bruto" class="font-medium">Tanggal & Jam Bruto <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tanggal_bruto" id="tanggal_bruto" class="form-input"
                        value="{{ \Carbon\Carbon::parse($hasil->tanggal_bruto)->format('Y-m-d\TH:i') }}" required>
                </div>
                <div>
                    <label for="tarra" class="font-medium">Total Tarra (ton) <span class="text-red-500">*</span></label>
                    <input type="number" name="tarra" id="tarra" step="0.01" min="0" class="form-input" 
                           value="{{ number_format($hasil->tarra, 2, '.', '') }}" required>
                    <small class="text-gray-500 text-sm">Masukkan berat kendaraan kosong dalam ton</small>
                    <div id="tarra-error" class="text-red-500 text-sm hidden"></div>
                </div>
                <div>
                    <label for="tanggal_tarra" class="font-medium">Tanggal & Jam Tarra <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="tanggal_tarra" id="tanggal_tarra" class="form-input"
                        value="{{ \Carbon\Carbon::parse($hasil->tanggal_tarra)->format('Y-m-d\TH:i') }}" required>
                </div>
                <div>
                    <label for="netto1" class="font-medium">Netto 1 (ton)</label>
                    <input type="number" name="netto1" id="netto1" step="0.01" class="form-input bg-gray-100" 
                           value="{{ number_format($hasil->netto1, 2, '.', '') }}" readonly>
                    <small class="text-gray-500 text-sm">Bruto - Tarra</small>
                </div>
                <div>
                    <label for="sortase" class="font-medium">Sortase (ton) <span class="text-red-500">*</span></label>
                    <input type="number" name="sortase" id="sortase" step="0.01" min="0" class="form-input" 
                           value="{{ number_format($hasil->sortase, 2, '.', '') }}" required>
                </div>
                <div>
                    <label for="netto2" class="font-medium">Netto 2 (ton)</label>
                    <input type="number" name="netto2" id="netto2" step="0.01" class="form-input bg-gray-100" 
                           value="{{ number_format($hasil->netto2, 2, '.', '') }}" readonly>
                    <small class="text-gray-500 text-sm">Netto 1 - Sortase</small>
                </div>
            </div>
        </div>

        <!-- Tombol Submit -->
        <div class="flex justify-between mt-6">
            <a href="{{ route('hasil-tebang.index') }}" class="btn btn-secondary">← Kembali ke Daftar</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const brutoInput = document.getElementById('bruto');
        const tarraInput = document.getElementById('tarra');
        const netto1Input = document.getElementById('netto1');
        const sortaseInput = document.getElementById('sortase');
        const netto2Input = document.getElementById('netto2');
        const form = document.querySelector('form');
        const kodeLambung = '{{ $hasil->kode_lambung }}';
        const isPickup = kodeLambung.toLowerCase().includes('pickup');
        const maxWeight = isPickup ? 5 : 15;

        // Function to show error message
        function showError(input, message) {
            const formControl = input.parentElement;
            const errorDiv = formControl.querySelector('.error-message') || document.createElement('div');
            errorDiv.className = 'text-red-500 text-sm mt-1 error-message';
            errorDiv.textContent = message;
            
            if (!formControl.querySelector('.error-message')) {
                formControl.appendChild(errorDiv);
            }
            
            input.classList.add('border-red-500');
            return false;
        }

        // Function to remove error
        function removeError(input) {
            const formControl = input.parentElement;
            const errorDiv = formControl.querySelector('.error-message');
            if (errorDiv) {
                errorDiv.remove();
            }
            input.classList.remove('border-red-500');
            return true;
        }

        // Function to validate weights
        function validateWeights() {
            let isValid = true;
            const bruto = parseFloat(brutoInput.value) || 0;
            const tarra = parseFloat(tarraInput.value) || 0;
            const sortase = parseFloat(sortaseInput.value) || 0;
            const netto1 = Math.max(0, bruto - tarra);

            // Reset all errors
            [brutoInput, tarraInput, sortaseInput].forEach(input => removeError(input));

            // Validate Bruto
            if (isNaN(bruto) || bruto <= 0) {
                showError(brutoInput, 'Masukkan nilai bruto yang valid (harus lebih dari 0)');
                isValid = false;
            } else if (bruto > maxWeight) {
                showError(brutoInput, `Berat maksimum untuk kendaraan ${isPickup ? 'pickup' : 'truk'} adalah ${maxWeight} ton`);
                isValid = false;
            }

            // Validate Tarra
            if (isNaN(tarra) || tarra < 0) {
                showError(tarraInput, 'Masukkan nilai tarra yang valid (tidak boleh negatif)');
                isValid = false;
            } else if (bruto > 0 && tarra >= bruto) {
                showError(tarraInput, 'Nilai tarra tidak boleh melebihi atau sama dengan bruto');
                isValid = false;
            }

            // Validate Sortase
            if (isNaN(sortase) || sortase < 0) {
                showError(sortaseInput, 'Nilai sortase tidak boleh negatif');
                isValid = false;
            } else if (sortase > netto1) {
                showError(sortaseInput, `Nilai sortase tidak boleh melebihi Netto 1 (${netto1.toFixed(2)} ton)`);
                isValid = false;
            }

            return isValid;
        }

        // Function to calculate netto values
        function calculateNetto() {
            const bruto = parseFloat(brutoInput.value) || 0;
            const tarra = parseFloat(tarraInput.value) || 0;
            const sortase = parseFloat(sortaseInput.value) || 0;
            
            // Calculate netto1 (bruto - tarra)
            const netto1 = Math.max(0, bruto - tarra);
            netto1Input.value = netto1.toFixed(2);
            
            // Calculate netto2 (netto1 - sortase)
            const netto2 = Math.max(0, netto1 - sortase);
            netto2Input.value = netto2.toFixed(2);
        }

        // Event listeners for input changes
        [brutoInput, tarraInput, sortaseInput].forEach(input => {
            // Validate on input
            input.addEventListener('input', function() {
                removeError(input);
                calculateNetto();
                if (this.value !== '') {
                    validateWeights();
                }
            });
            
            // Additional validation on blur
            input.addEventListener('blur', function() {
                validateWeights();
            });
        });

        // Form submission validation
        form.addEventListener('submit', function(e) {
            if (!validateWeights()) {
                e.preventDefault();
                
                // Get all error messages
                const errorMessages = [];
                document.querySelectorAll('.border-red-500').forEach(input => {
                    const errorDiv = input.parentElement.querySelector('.error-message');
                    if (errorDiv && errorDiv.textContent && !errorMessages.includes(errorDiv.textContent)) {
                        errorMessages.push(errorDiv.textContent);
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
                    const firstError = document.querySelector('.border-red-500');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                });
                
                return false;
            }
            
            // Check form validity
            if (!form.checkValidity()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Form Belum Lengkap',
                    text: 'Harap lengkapi semua field yang wajib diisi',
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#3085d6',
                });
                form.reportValidity();
                return false;
            }
            
            // Ensure netto values are calculated before submission
            calculateNetto();
            
            // Show confirmation dialog
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah data yang Anda input sudah benar?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Periksa Kembali',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            
            return false;
        });

        // Initial calculation
        calculateNetto();
    });
</script>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function calculateNetto() {
            const bruto = parseFloat(document.getElementById('bruto').value) || 0;
            const tarra = parseFloat(document.getElementById('tarra').value) || 0;
            const sortase = parseFloat(document.getElementById('sortase').value) || 0;

            const netto1 = bruto - tarra;
            const netto2 = netto1 - sortase;

            document.getElementById('netto1').value = netto1.toFixed(2);
            document.getElementById('netto2').value = netto2.toFixed(2);
        }

        ['bruto', 'tarra', 'sortase'].forEach(id => {
            document.getElementById(id).addEventListener('input', calculateNetto);
        });

        // Initialize on page load
        calculateNetto();
    });
</script>
@endpush
