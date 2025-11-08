@extends('layouts.master')

@push('styles')
<link href="{{ asset('css/user-management.css') }}" rel="stylesheet">
<link href="{{ asset('css/vendor-angkut.css') }}" rel="stylesheet">
<style>
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1100;
    }
    .toast {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .toast-header {
        border-bottom: none;
        font-weight: 600;
    }
    .toast-body {
        padding: 1rem;
    }
    .toast-danger {
        background-color: #fff5f5;
        border-left: 4px solid #dc3545;
    }
    .toast-success {
        background-color: #f0fff4;
        border-left: 4px solid #28a745;
    }
    .toast-warning {
        background-color: #fffaf0;
        border-left: 4px solid #ffc107;
    }
</style>
@endpush

@section('content')
<!-- Toast Notification -->
<div class="toast-container">
    <div id="validationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fas fa-exclamation-circle text-danger me-2"></i>
            <strong class="me-auto">Perhatian</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
            <!-- Message will be inserted here -->
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container user-management-container">
    <div class="mb-4">
        <h2>Ubah Password</h2>
    </div>

    <div class="card">
        <div class="card-body">

            <form action="{{ route('users.update-password', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                </div>

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" value="{{ $user->username }}" readonly>
                </div>

                <div class="form-group">
                    <label for="role_name" class="form-label">Role</label>
                    <input type="text" class="form-control" value="{{ ucfirst($user->role_name) }}" readonly>
                </div>

                <div class="form-group">
                    <label for="current_password" class="form-label">Password Saat Ini</label>
                    <div class="password-input-group">
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        <button type="button" class="toggle-password" data-target="current_password">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                        @error('current_password')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div id="currentPasswordHelp" class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Masukkan password saat ini
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_password" class="form-label">Password Baru</label>
                    <div class="password-input-group">
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                               id="new_password" name="new_password" required minlength="8"
                               pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$" oninput="validatePassword()">
                        <button type="button" class="toggle-password" data-target="new_password">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="passwordHelp" class="form-text text-muted">
                        Minimal 8 karakter, terdiri dari huruf dan angka
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <div class="password-input-group">
                        <input type="password" class="form-control" id="new_password_confirmation"
                               name="new_password_confirmation" required oninput="validatePasswordConfirmation()">
                        <button type="button" class="toggle-password" data-target="new_password_confirmation">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                    <div id="passwordConfirmationHelp" class="form-text text-muted">
                        Harus sama dengan password baru
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="btn-text">Simpan Perubahan</span>
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                </div>

                <style>
                .form-actions {
                    display: flex;
                    justify-content: flex-start;
                    gap: 0.5rem;
                    margin-top: 1.5rem;
                }
                .btn {
                    padding: 0.5rem 1rem;
                    border-radius: 0.25rem;
                    font-weight: 500;
                    cursor: pointer;
                    transition: all 0.2s;
                    border: 1px solid transparent;
                }
                .btn-primary {
                    background-color: #2563eb;
                    color: white;
                }
                .btn-primary:hover {
                    background-color: #1d4ed8;
                }
                .btn-secondary {
                    background-color: #e0e7ff;
                    color: #1e40af;
                }
                .btn-secondary:hover {
                    background-color: #c7d2fe;
                }
                .password-strength {
                    font-size: 0.875rem;
                    margin-top: 0.25rem;
                }
                </style>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .password-input-group {
        position: relative;
    }
    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #6c757d;
    }
    .toggle-password:focus {
        outline: none;
        box-shadow: none;
    }
</style>
@endpush

@push('scripts')
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
    // Toggle password visibility
    function togglePasswordVisibility(targetId) {
        const input = document.getElementById(targetId);
        const icon = document.querySelector(`button[data-target="${targetId}"] i`);

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }

    async function validateCurrentPassword() {
        const currentPassword = document.getElementById('current_password');
        const currentPasswordHelp = document.getElementById('currentPasswordHelp');
        const submitBtn = document.getElementById('submitBtn');

        // Reset state
        currentPassword.classList.remove('is-invalid', 'is-valid');
        currentPasswordHelp.classList.remove('text-danger', 'text-success', 'text-muted');
        currentPasswordHelp.innerHTML = '';

        const password = currentPassword.value.trim();
        
        if (!password) {
            currentPassword.classList.add('is-invalid');
            currentPasswordHelp.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2 text-danger"></i>
                    <div class="text-danger">Password tidak boleh kosong</div>
                </div>`;
            return false;
        }

        // Show checking state
        currentPasswordHelp.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                <div>Memeriksa password...</div>
            </div>`;
        submitBtn.disabled = true;

        try {
            const response = await fetch('{{ route("users.check-password") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    current_password: password
                })
            });

            const data = await response.json();
            
            if (response.status === 401) {
                // Session expired
                window.location.reload();
                return false;
            }

            if (!response.ok) {
                const errorData = await response.json();
                if (errorData.errors && errorData.errors.current_password) {
                    const toastMessage = document.getElementById('toastMessage');
                    toastMessage.textContent = 'Password yang Anda masukkan salah';
                    const toast = new bootstrap.Toast(document.getElementById('validationToast'));
                    toast.show();
                }
                throw new Error(errorData.message || 'Terjadi kesalahan');
            }

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Gagal memverifikasi password');
            }

            if (data.valid) {
                // Password is valid
                currentPassword.classList.add('is-valid');
                currentPasswordHelp.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2 text-success"></i>
                        <div class="text-success">Password valid</div>
                    </div>`;
                submitBtn.disabled = false;
                return true;
            } else {
                // Password is invalid
                currentPassword.classList.add('is-invalid');
                currentPasswordHelp.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-times-circle me-2 text-danger"></i>
                        <div class="text-danger">Password yang Anda masukkan salah</div>
                    </div>`;
                submitBtn.disabled = false;
                return false;
            }

        } catch (error) {
            console.error('Error:', error);
            currentPassword.classList.add('is-invalid');
            currentPasswordHelp.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                    <div class="text-danger">${error.message || 'Terjadi kesalahan. Silakan coba lagi.'}</div>
                </div>`;
            submitBtn.disabled = false;
            return false;
        }
    }

    function validatePassword() {
        const password = document.getElementById('new_password');
        const passwordHelp = document.getElementById('passwordHelp');
        const regex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;

        if (!regex.test(password.value)) {
            password.classList.add('is-invalid');
            passwordHelp.innerHTML = '<i class="fas fa-exclamation-circle"></i> Password harus terdiri dari huruf dan angka (min 8 karakter)';
            passwordHelp.classList.remove('text-muted', 'text-success');
            passwordHelp.classList.add('text-danger');
            return false;
        } else {
            password.classList.remove('is-invalid');
            passwordHelp.innerHTML = '<i class="fas fa-check-circle"></i> Password valid';
            passwordHelp.classList.remove('text-danger', 'text-muted');
            passwordHelp.classList.add('text-success');
            return true;
        }
    }

    function validatePasswordConfirmation() {
        const password = document.getElementById('new_password').value;
        const confirmInput = document.getElementById('new_password_confirmation');
        const confirmPassword = confirmInput.value;
        const passwordConfirmationHelp = document.getElementById('passwordConfirmationHelp');

        if (!confirmPassword) {
            confirmInput.classList.add('is-invalid');
            passwordConfirmationHelp.innerHTML = '<i class="fas fa-exclamation-circle"></i> Konfirmasi password harus diisi';
            passwordConfirmationHelp.classList.remove('text-muted', 'text-success');
            passwordConfirmationHelp.classList.add('text-danger');
            return false;
        }

        if (password !== confirmPassword) {
            confirmInput.classList.add('is-invalid');
            passwordConfirmationHelp.innerHTML = '<i class="fas fa-exclamation-circle"></i> Konfirmasi password tidak sesuai';
            passwordConfirmationHelp.classList.remove('text-muted', 'text-success');
            passwordConfirmationHelp.classList.add('text-danger');
            return false;
        } else {
            confirmInput.classList.remove('is-invalid');
            passwordConfirmationHelp.innerHTML = '<i class="fas fa-check-circle"></i> Konfirmasi password sesuai';
            passwordConfirmationHelp.classList.remove('text-danger', 'text-muted');
            passwordConfirmationHelp.classList.add('text-success');
            return true;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Close alert buttons
        document.querySelectorAll('.close-alert').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.alert-message').remove();
            });
        });

        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                togglePasswordVisibility(targetId);
            });
        });

        // Show toast notification
        function showToast(message, type = 'error') {
            const toastEl = document.getElementById('validationToast');
            const toastBody = document.getElementById('toastMessage');
            const toastHeaderIcon = toastEl.querySelector('.toast-header i');

            // Update toast content
            toastBody.innerHTML = message;

            // Update toast style based on type
            toastEl.className = 'toast';
            toastEl.classList.add(`toast-${type}`);

            // Update icon
            const icons = {
                'error': 'exclamation-circle',
                'success': 'check-circle',
                'warning': 'exclamation-triangle'
            };

            toastHeaderIcon.className = `fas fa-${icons[type] || 'info-circle'} text-${type} me-2`;

            // Show toast
            const toast = new bootstrap.Toast(toastEl);
            toast.show();

            return toast;
        }

        // Handle form submission
        // Form submission handler
        document.querySelector('form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Reset states
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            
            // Show loading
            const submitBtn = document.getElementById('submitBtn');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
            
            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Success - redirect to users list
                    window.location.href = '{{ route("users.index") }}';
                } else if (response.status === 422) {
                    // Validation errors
                    if (data.errors) {
                        // Let Laravel handle the validation errors
                        location.reload();
                    }
                } else {
                    // Other errors
                    alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    });
</script>
@endpush
@endsection
