@extends('layouts.master')

@push('styles')
<link href="{{ asset('css/user-management.css') }}" rel="stylesheet">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('content')

@if(session('success'))
    <div class="mb-4 px-4 py-2 bg-green-100 border border-green-300 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 px-4 py-2 bg-red-100 border border-red-300 text-red-800 rounded">
        {{ session('error') }}
    </div>
@endif

<div class="container user-management-container">
    <div class="mb-4">
        <h2>Tambah User Baru</h2>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Informasi User</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}" autocomplete="off" class="user-form" id="userForm" onsubmit="return validateForm()">
                @csrf

                <div class="form-group">
                    <label for="role_name" class="form-label">Role</label>
                    <div class="input-group mb-2">
                        <select class="form-select @error('role_name') is-invalid @enderror" id="role_name" name="role_name" required>
                            <option value="" selected disabled>Pilih Role</option>
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}" {{ old('role_name') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                            <option value="new_role">+ Tambah Role Baru</option>
                        </select>
                    </div>

                    <div class="input-group new-role-field" style="display: none; margin-top: 10px;">
                        <input type="text" class="form-control @error('new_role_name') is-invalid @enderror"
                               id="new_role_name" name="new_role_name"
                               placeholder="Masukkan nama role baru" required>
                        <button class="btn btn-primary" type="button" id="saveRoleBtn">Tambah</button>
                        @error('new_role_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    @error('role_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Field Vendor (hanya muncul untuk role vendor) -->
                <div class="form-group vendor-field" style="display: none;">
                    <label for="vendor_id" class="form-label">Username</label>
                    <select class="form-select select2-vendor @error('vendor_id') is-invalid @enderror" id="vendor_id" name="vendor_id">
                        <option value="" disabled selected>Pilih Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" data-nama="{{ $vendor->nama_vendor }}" data-hp="{{ $vendor->no_hp }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->nama_vendor }} ({{ $vendor->no_hp }})
                            </option>
                        @endforeach
                    </select>
                    @error('vendor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Username akan diambil dari nomor HP vendor</small>
                </div>

                <!-- Field Foreman (hanya muncul untuk role mandor) -->
                <div class="form-group foreman-field" style="display: none;">
                    <label for="foreman_id" class="form-label">Username</label>
                    <select class="form-select select2-foreman @error('foreman_id') is-invalid @enderror" id="foreman_id" name="foreman_id">
                        <option value="" disabled selected>Pilih Mandor</option>
                        @foreach($foremen as $foreman)
                            <option value="{{ $foreman->id }}" data-nama="{{ $foreman->nama_mandor }}" data-email="{{ $foreman->email }}" {{ old('foreman_id') == $foreman->id ? 'selected' : '' }}>
                                {{ $foreman->nama_mandor }} ({{ $foreman->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('foreman_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Email mandor akan digunakan sebagai username</small>
                </div>

                <!-- Field Email (untuk selain vendor dan mandor) -->
                <div class="form-group email-field" style="display: none;">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Email akan digunakan sebagai username</small>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-input-group">
                        <input type="text" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required minlength="8" pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$" oninput="validatePassword()">
                        <button type="button" class="toggle-password" data-target="password">
                            <i class="fas fa-eye-slash"></i>
                        </button>

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>
                    <div id="passwordHelp" class="form-text text-muted">
                            Minimal 8 karakter, terdiri dari huruf dan angka
                        </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <div class="password-input-group">
                        <input type="text" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required oninput="validatePasswordConfirmation()">
                        <button type="button" class="toggle-password" data-target="password_confirmation">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="passwordConfirmationHelp" class="form-text text-muted">
                        Harus sama dengan password
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="btn-text">Simpan</span>
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
    </div>
</div>

@push('scripts')
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role_name');
        const newRoleField = document.querySelector('.new-role-field');
        const newRoleInput = document.getElementById('new_role_name');
        const vendorField = document.querySelector('.vendor-field');
        const emailField = document.querySelector('.email-field');
        const foremanField = document.querySelector('.foreman-field');

        // Tampilkan/sembunyikan field role baru
        function toggleNewRoleField() {
            if (roleSelect.value === 'new_role') {
                newRoleField.style.display = 'block';
                newRoleInput.setAttribute('required', 'required');
                // Sembunyikan field lain yang tidak diperlukan
                if (vendorField) vendorField.style.display = 'none';
                if (emailField) emailField.style.display = 'none';
                if (foremanField) foremanField.style.display = 'none';
            } else {
                newRoleField.style.display = 'none';
                newRoleInput.removeAttribute('required');
            }
        }

        // Inisialisasi saat halaman dimuat
        toggleNewRoleField();

        // Event listener untuk perubahan role
        roleSelect.addEventListener('change', function() {
            toggleNewRoleField();
            // Kosongkan field role baru saat ganti ke role yang lain
            if (this.value !== 'new_role') {
                newRoleInput.value = '';
            }
            // Panggil fungsi toggleFieldsByRole yang sudah ada
            toggleFieldsByRole();
        });

        // Handle role selection change
        roleSelect.addEventListener('change', function() {
            const newRoleField = document.querySelector('.new-role-field');
            const addRoleBtn = document.getElementById('addRoleBtn');

            if (this.value === 'new_role') {
                // Show new role input field
                newRoleField.style.display = 'flex';
                addRoleBtn.style.display = 'none';
                // Hide other role-dependent fields
                toggleFieldsByRole();
            } else {
                // Hide new role input field
                newRoleField.style.display = 'none';
                addRoleBtn.style.display = 'none';
                toggleFieldsByRole();
            }
        });

        // Simple function to save role to localStorage
        function saveRole(roleName) {
            // Get existing roles or initialize empty array
            let roles = [];
            try {
                roles = JSON.parse(localStorage.getItem('customRoles') || '[]');
            } catch (e) {
                console.log('No existing roles found, initializing...');
            }

            // Check if role already exists (case insensitive)
            const roleExists = roles.some(r => r.toLowerCase() === roleName.toLowerCase());

            if (!roleExists) {
                roles.push(roleName);
                localStorage.setItem('customRoles', JSON.stringify(roles));
                console.log('Saved roles:', roles);
            }

            return roles;
        }

        // Simple function to load roles into select
        function loadRoles() {
            const roleSelect = document.getElementById('role_name');
            if (!roleSelect) return;

            // Get default roles from server
            const defaultRoles = @json(array_keys($roles));

            // Get custom roles from localStorage
            let customRoles = [];
            try {
                customRoles = JSON.parse(localStorage.getItem('customRoles') || '[]');
                console.log('Loaded custom roles:', customRoles);
            } catch (e) {
                console.log('No custom roles found');
            }

            // Clear all options except the first one and 'new_role'
            const optionsToKeep = Array.from(roleSelect.options).filter(opt =>
                opt.value === '' || opt.value === 'new_role' || defaultRoles.includes(opt.value)
            );

            // Clear and rebuild options
            roleSelect.innerHTML = '';
            optionsToKeep.forEach(opt => roleSelect.add(opt));

            // Add custom roles before 'new_role' option
            customRoles.forEach(role => {
                if (!defaultRoles.includes(role)) {
                    const option = new Option(role, role);
                    roleSelect.insertBefore(option, roleSelect.lastElementChild);
                }
            });
        }

        // Load roles when page loads
        document.addEventListener('DOMContentLoaded', loadRoles);

        // Handle add role button click
        document.getElementById('saveRoleBtn')?.addEventListener('click', function(e) {
            e.preventDefault();
            const newRoleName = document.getElementById('new_role_name').value.trim();
            const roleSelect = document.getElementById('role_name');

            if (!newRoleName) {
                alert('Silakan masukkan nama role baru');
                return;
            }

            // Save the new role to localStorage
            saveRole(newRoleName);

            // Reload roles to update the dropdown
            loadRoles();

            // Select the new role
            roleSelect.value = newRoleName;

            // Clear and hide the new role input
            document.getElementById('new_role_name').value = '';
            document.querySelector('.new-role-field').style.display = 'none';

            console.log('Role added and saved:', newRoleName);
        });

        // Handle role selection change
        roleSelect.addEventListener('change', function() {
            const newRoleField = document.querySelector('.new-role-field');

            if (this.value === 'new_role') {
                // Show new role input field
                newRoleField.style.display = 'flex';
            } else if (this.value) {
                // Hide new role field if an existing role is selected
                newRoleField.style.display = 'none';
            } else {
                // Hide if no role is selected
                newRoleField.style.display = 'none';
            }
        });

        // Handle form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const roleSelect = document.getElementById('role_name');

            // If 'Tambah Role Baru' is selected but no role was added
            if (roleSelect.value === 'new_role') {
                e.preventDefault();
                alert('Silakan tambahkan role baru terlebih dahulu');
                return false;
            }

            // If we have a valid role, allow form submission
            return true;
        });
        const nameInput = document.getElementById('name');

        // Data vendor untuk autofill
        const vendorData = @json($vendors->mapWithKeys(function($vendor) {
            return [$vendor->id => $vendor->nama_vendor];
        }));

        // Data foreman untuk autofill
        const foremanData = @json($foremen->mapWithKeys(function($foreman) {
            return [$foreman->id => $foreman->nama_mandor];
        }));

        // Fungsi untuk mengisi nama berdasarkan pilihan
        function fillName() {
            const selectedRole = roleSelect.value;

            if (selectedRole === 'vendor') {
                const vendorId = document.getElementById('vendor_id').value;
                if (vendorId && vendorData[vendorId]) {
                    nameInput.value = vendorData[vendorId];
                }
            } else if (selectedRole === 'mandor') {
                const foremanId = document.getElementById('foreman_id').value;
                if (foremanId && foremanData[foremanId]) {
                    nameInput.value = foremanData[foremanId];
                }
            }
        }

        // Event listener untuk perubahan pilihan vendor
        document.getElementById('vendor_id')?.addEventListener('change', function() {
            if (roleSelect.value === 'vendor') {
                fillName();
            }
        });

        // Event listener untuk perubahan pilihan foreman
        document.getElementById('foreman_id')?.addEventListener('change', function() {
            if (roleSelect.value === 'mandor') {
                fillName();
            }
        });

        // Event listener untuk perubahan role
        roleSelect.addEventListener('change', function() {
            // Kosongkan nama saat ganti role
            nameInput.value = '';
            // Isi nama jika sudah ada pilihan
            if (this.value === 'vendor' && document.getElementById('vendor_id')?.value) {
                fillName();
            } else if (this.value === 'mandor' && document.getElementById('foreman_id')?.value) {
                fillName();
            }
        });

        // Fungsi untuk menampilkan/menyembunyikan field berdasarkan role
        function toggleFieldsByRole() {
            const selectedRole = roleSelect.value;

            // Sembunyikan semua field terlebih dahulu
            vendorField.style.display = 'none';
            foremanField.style.display = 'none';
            emailField.style.display = 'none';

            // Reset required attributes
            document.getElementById('vendor_id').required = false;
            document.getElementById('foreman_id').required = false;
            document.getElementById('email').required = false;

            if (selectedRole === 'vendor') {
                vendorField.style.display = 'block';
                document.getElementById('vendor_id').required = true;
            } else if (selectedRole === 'mandor') {
                foremanField.style.display = 'block';
                document.getElementById('foreman_id').required = true;
            } else if (selectedRole) {
                emailField.style.display = 'block';
                document.getElementById('email').required = true;
            }
        }

        // Panggil fungsi saat halaman dimuat
        toggleFieldsByRole();

        // Panggil fungsi saat role berubah
        roleSelect.addEventListener('change', toggleFieldsByRole);

        // Function to toggle password visibility
        function togglePasswordVisibility(targetId) {
            const input = document.getElementById(targetId);
            const icon = document.querySelector(`button[data-target="${targetId}"] i`);

            if (input.type === 'text') {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Set role yang dipilih sebelumnya jika ada error
        @if(old('role_name'))
            const roleSelect = document.getElementById('role_name');
            roleSelect.value = '{{ old('role_name') }}';
            toggleFieldsByRole();
        @endif

        // Set password fields to be visible by default
        document.querySelectorAll('.password-input-group input').forEach(input => {
            input.type = 'text';
        });

        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                togglePasswordVisibility(targetId);
            });
        });

        // Password validation function
        function validatePassword() {
            const passwordInput = document.getElementById('password');
            const passwordHelp = document.getElementById('passwordHelp');
            const password = passwordInput.value;

            // Reset styles
            passwordInput.classList.remove('is-invalid');
            passwordHelp.classList.remove('text-danger');
            passwordHelp.classList.add('text-muted');

            // Check password requirements
            if (password.length > 0 && password.length < 8) {
                passwordInput.classList.add('is-invalid');
                passwordHelp.textContent = 'Password minimal 8 karakter';
                passwordHelp.classList.add('text-danger');
                passwordHelp.classList.remove('text-muted');
            } else if (password.length >= 8 && !/(?=.*[A-Za-z])(?=.*\d)/.test(password)) {
                passwordInput.classList.add('is-invalid');
                passwordHelp.textContent = 'Password harus mengandung huruf dan angka';
                passwordHelp.classList.add('text-danger');
                passwordHelp.classList.remove('text-muted');
            } else if (password.length >= 8) {
                passwordHelp.textContent = 'Password valid';
                passwordHelp.classList.add('text-success');
                passwordHelp.classList.remove('text-muted', 'text-danger');
            } else {
                passwordHelp.textContent = 'Minimal 8 karakter, terdiri dari huruf dan angka';
                passwordHelp.classList.remove('text-danger', 'text-success');
                passwordHelp.classList.add('text-muted');
            }
        }

        // Password confirmation validation function
        function validatePasswordConfirmation() {
            const password = document.getElementById('password').value;
            const confirmInput = document.getElementById('password_confirmation');
            const confirmPassword = confirmInput.value;
            const passwordConfirmationHelp = document.getElementById('passwordConfirmationHelp');
            
            // Reset styles
            confirmInput.classList.remove('is-invalid');
            passwordConfirmationHelp.classList.remove('text-danger');
            passwordConfirmationHelp.classList.add('text-muted');
            
            // Check if passwords match
            if (confirmPassword.length > 0 && password !== confirmPassword) {
                confirmInput.classList.add('is-invalid');
                passwordConfirmationHelp.textContent = 'Konfirmasi password tidak sesuai';
                passwordConfirmationHelp.classList.add('text-danger');
                passwordConfirmationHelp.classList.remove('text-muted');
                return false;
            } else if (confirmPassword.length > 0) {
                passwordConfirmationHelp.textContent = 'Password cocok';
                passwordConfirmationHelp.classList.remove('text-danger');
                passwordConfirmationHelp.classList.add('text-success');
                return true;
            } else {
                passwordConfirmationHelp.textContent = 'Harus sama dengan password';
                passwordConfirmationHelp.classList.remove('text-danger', 'text-success');
                passwordConfirmationHelp.classList.add('text-muted');
                return false;
            }
        }

        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            let isValid = true;

            // Reset all error states
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.text-danger').forEach(el => {
                el.classList.remove('text-danger');
                el.classList.add('text-muted');
            });

            // Validate password requirements
            if (password.length < 8 || !/(?=.*[A-Za-z])(?=.*\d)/.test(password)) {
                const passwordInput = document.getElementById('password');
                passwordInput.classList.add('is-invalid');
                const passwordHelp = document.getElementById('passwordHelp');
                passwordHelp.textContent = 'Password harus minimal 8 karakter dan terdiri dari huruf dan angka';
                passwordHelp.classList.add('text-danger');
                passwordHelp.classList.remove('text-muted');
                isValid = false;
            }

            // Validate password confirmation
            if (password !== confirmPassword) {
                const confirmInput = document.getElementById('password_confirmation');
                confirmInput.classList.add('is-invalid');
                const passwordConfirmationHelp = document.getElementById('passwordConfirmationHelp');
                passwordConfirmationHelp.textContent = 'Konfirmasi password tidak sesuai';
                passwordConfirmationHelp.classList.add('text-danger');
                passwordConfirmationHelp.classList.remove('text-muted');
                isValid = false;
            }
            
            return isValid;
        }

        // Handle form submission with AJAX for better UX
        document.getElementById('userForm').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            
            // Continue with form submission
            return true;
        });

        // Initialize Select2 for vendor dropdown
        function initVendorSelect2() {
            $('.select2-vendor').select2({
                theme: 'bootstrap-5',
                placeholder: 'Cari vendor...',
                allowClear: true,
                width: '100%',
                matcher: function(params, data) {
                    // If there are no search terms, return all of the data
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    // Do not display the 'No results found' message
                    if (data.text === undefined) {
                        return null;
                    }

                    const term = params.term.toLowerCase();
                    const nama = $(data.element).data('nama')?.toLowerCase() || '';
                    const hp = $(data.element).data('hp')?.toLowerCase() || '';

                    // Check if the search term matches in nama or hp
                    if (nama.includes(term) || hp.includes(term)) {
                        return data;
                    }

                    return null;
                }
            });
        }

        // Initialize Select2 for foreman dropdown
        function initForemanSelect2() {
            $('.select2-foreman').select2({
                theme: 'bootstrap-5',
                placeholder: 'Cari mandor...',
                allowClear: true,
                width: '100%',
                matcher: function(params, data) {
                    // If there are no search terms, return all of the data
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    // Do not display the 'No results found' message
                    if (data.text === undefined) {
                        return null;
                    }

                    const term = params.term.toLowerCase();
                    const nama = $(data.element).data('nama')?.toLowerCase() || '';
                    const email = $(data.element).data('email')?.toLowerCase() || '';

                    // Check if the search term matches in nama or email
                    if (nama.includes(term) || email.includes(term)) {
                        return data;
                    }

                    return null;
                }
            });
        }

        // Initialize Select2 when fields are shown
        function toggleFieldsByRole() {
            const selectedRole = roleSelect.value;

            // Hide all fields first
            vendorField.style.display = 'none';
            foremanField.style.display = 'none';
            emailField.style.display = 'none';

            // Show relevant fields based on role
            if (selectedRole === 'vendor' && vendorField) {
                vendorField.style.display = 'block';
                // Initialize Select2 for vendor
                setTimeout(() => {
                    initVendorSelect2();
                }, 100);
            } else if (selectedRole === 'mandor' && foremanField) {
                foremanField.style.display = 'block';
                // Initialize Select2 for foreman
                setTimeout(() => {
                    initForemanSelect2();
                }, 100);
            } else if (['admin', 'gis_department', 'finance'].includes(selectedRole) && emailField) {
                emailField.style.display = 'block';
            }
        }

        // Initialize on page load if vendor or foreman is already selected
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('vendor_id')) {
                initVendorSelect2();
            }
            if (document.getElementById('foreman_id')) {
                initForemanSelect2();
            }
        });
    });

    // Initialize Select2 for vendor dropdown
    function initVendorSelect2() {
        $('.select2-vendor').select2({
            theme: 'bootstrap-5',
            placeholder: 'Cari vendor...',
            allowClear: true,
            width: '100%',
            matcher: function(params, data) {
                // If there are no search terms, return all of the data
                if ($.trim(params.term) === '') {
                    return data;
                }

                // Do not display the 'No results found' message
                if (data.text === undefined) {
                    return null;
                }

                const term = params.term.toLowerCase();
                const nama = $(data.element).data('nama')?.toLowerCase() || '';
                const hp = $(data.element).data('hp')?.toLowerCase() || '';

                // Check if the search term matches in nama or hp
                if (nama.includes(term) || hp.includes(term)) {
                    return data;
                }

                return null;
            }
        });
    }

    // Initialize Select2 for foreman dropdown
    function initForemanSelect2() {
        $('.select2-foreman').select2({
            theme: 'bootstrap-5',
            placeholder: 'Cari mandor...',
            allowClear: true,
            width: '100%',
            matcher: function(params, data) {
                // If there are no search terms, return all of the data
                if ($.trim(params.term) === '') {
                    return data;
                }

                // Do not display the 'No results found' message
                if (data.text === undefined) {
                    return null;
                }

                const term = params.term.toLowerCase();
                const nama = $(data.element).data('nama')?.toLowerCase() || '';
                const email = $(data.element).data('email')?.toLowerCase() || '';

                // Check if the search term matches in nama or email
                if (nama.includes(term) || email.includes(term)) {
                    return data;
                }

                return null;
            }
        });
    }

    // Initialize Select2 when fields are shown
    function toggleFieldsByRole() {
        const selectedRole = roleSelect.value;

        // Hide all fields first
        vendorField.style.display = 'none';
        foremanField.style.display = 'none';
        emailField.style.display = 'none';

        // Show relevant fields based on role
        if (selectedRole === 'vendor' && vendorField) {
            vendorField.style.display = 'block';
            // Initialize Select2 for vendor
            setTimeout(() => {
                initVendorSelect2();
            }, 100);
        } else if (selectedRole === 'mandor' && foremanField) {
            foremanField.style.display = 'block';
            // Initialize Select2 for foreman
            setTimeout(() => {
                initForemanSelect2();
            }, 100);
        } else if (['admin', 'gis_department', 'finance'].includes(selectedRole) && emailField) {
            emailField.style.display = 'block';
        }
    }

    // Initialize on page load if vendor or foreman is already selected
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('vendor_id')) {
            initVendorSelect2();
        }
        if (document.getElementById('foreman_id')) {
            initForemanSelect2();
        }
    });
</script>
@endpush

@endsection
