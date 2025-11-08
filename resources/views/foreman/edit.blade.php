@extends('layouts.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor-angkut.css') }}">
@endpush

@push('scripts')
<script>
    function validateEmail(input) {
        const emailRegex = /^[a-z0-9._%+-]{2,}@(?:[a-z0-9-]+\.)+[a-z]{2,}$/i;
        const errorElement = document.getElementById('email-error');
        
        if (input.value === '') {
            errorElement.classList.add('hidden');
            return true;
        }
        
        if (!emailRegex.test(input.value)) {
            errorElement.classList.remove('hidden');
            input.setCustomValidity('Format email tidak valid');
            return false;
        } else {
            errorElement.classList.add('hidden');
            input.setCustomValidity('');
            return true;
        }
    }
    
    // Validate form before submission
    document.querySelector('form').addEventListener('submit', function(event) {
        let isValid = true;
        const form = event.target;
        
        // Validate email
        const emailInput = document.getElementById('email');
        if (!validateEmail(emailInput)) {
            isValid = false;
        }
        
        // Validate phone number
        const phoneInput = document.getElementById('no_hp');
        const phoneHelp = document.getElementById('no_hp_help');
        const phoneRegex = /^08[0-9]{8,11}$/;
        
        if (!phoneRegex.test(phoneInput.value.trim())) {
            phoneInput.classList.add('border-red-500');
            phoneHelp.classList.remove('hidden');
            isValid = false;
        } else {
            phoneInput.classList.remove('border-red-500');
            phoneHelp.classList.add('hidden');
        }
        
        if (!isValid) {
            event.preventDefault();
            if (!validateEmail(emailInput)) {
                emailInput.focus();
            } else {
                phoneInput.focus();
            }
        }
    });
    
    // Real-time validation for phone number
    document.getElementById('no_hp').addEventListener('input', function(e) {
        const phoneHelp = document.getElementById('no_hp_help');
        const phoneRegex = /^08[0-9]{8,11}$/;
        
        if (this.value.trim() === '') {
            this.classList.remove('border-red-500');
            phoneHelp.classList.add('hidden');
            return;
        }
        
        if (!phoneRegex.test(this.value.trim())) {
            this.classList.add('border-red-500');
            phoneHelp.classList.remove('hidden');
        } else {
            this.classList.remove('border-red-500');
            phoneHelp.classList.add('hidden');
        }
    });
</script>
@endpush

@section('content')
<div class="vendor-container">
    <h2>Edit Data Mandor</h2>
    
    <form action="{{ route('foreman.update', $foreman->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="kode_mandor" class="form-label">Kode Mandor <span class="text-red-500">*</span></label>
            <input type="text" id="kode_mandor" 
                   class="form-input bg-gray-100"
                   value="{{ $foreman->kode_mandor }}" readonly>
        </div>

        <div class="form-group">
            <label for="nama_mandor" class="form-label">Nama Mandor <span class="text-red-500">*</span></label>
            <input type="text" name="nama_mandor" id="nama_mandor" 
                   class="form-input @error('nama_mandor') border-red-500 @enderror"
                   value="{{ old('nama_mandor', $foreman->nama_mandor) }}" required>
            @error('nama_mandor')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email <span class="text-red-500">*</span></label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   class="form-input @error('email') border-red-500 @enderror"
                   value="{{ old('email', $foreman->email) }}" 
                   pattern="[a-z0-9._%+-]+@(?:[a-z0-9-]+\.)+[a-z]{2,}$"
                   title="Masukkan alamat email yang valid (contoh: nama@contoh.com atau name@jbm.co.id)"
                   oninput="validateEmail(this)"
                   required>
            <p id="email-error" class="text-red-500 text-sm mt-1 hidden">
                Format email tidak valid. Gunakan format: nama@contoh.com
            </p>
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="no_hp" class="form-label">No HP <span class="text-red-500">*</span></label>
            <input type="text" 
                   name="no_hp" 
                   id="no_hp" 
                   class="form-input @error('no_hp') border-red-500 @enderror"
                   value="{{ old('no_hp', $foreman->no_hp) }}" 
                   required
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                   minlength="10"
                   maxlength="13"
                   pattern="^08[0-9]{8,11}$"
                   placeholder="Contoh: 081234567890"
                   title="Nomor HP harus dimulai dengan 08 dan terdiri dari 10-13 angka">
            @error('no_hp')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p id="no_hp_help" class="text-red-500 text-sm mt-1 hidden">
                Nomor HP harus dimulai dengan 08 dan terdiri dari 10-13 angka
            </p>
        </div>

        <div class="form-group">
            <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
            <select name="status" id="status" 
                    class="form-select @error('status') border-red-500 @enderror" required>
                <option value="Aktif" {{ old('status', $foreman->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ old('status', $foreman->status) == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('foreman.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
