<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Foreman;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
// Role management without Spatie package

class UserController extends Controller
{
    /**
     * Get available roles
     *
     * @return array
     */
    protected function getRoles()
    {
        return [
            'vendor' => 'Vendor',
            'mandor' => 'Mandor',
            // 'finance' => 'Finance',
            // 'cdr' => 'CDR',
            // 'plantation' => 'Plantation',
            // 'gis' => 'GIS',
            // 'pt_pag' => 'PT PAG',
            // 'qa' => 'QA',
            'admin' => 'Admin'
        ];
    }
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');

        // Log untuk debugging
        Log::info('Filter parameters:', [
            'search' => $search,
            'role' => $role
        ]);

        $query = User::query();

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if ($role) {
            // Normalize role name to match database values
            $normalizedRole = strtolower(trim($role));
            $query->whereRaw('LOWER(role_name) = ?', [$normalizedRole]);

            // Log the query being executed
            Log::info('Role filter query:', [
                'role' => $role,
                'normalized_role' => $normalizedRole,
                'query' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        // Get all existing roles from the database for the filter dropdown
        $existingRoles = User::select('role_name')
            ->distinct()
            ->pluck('role_name')
            ->filter()
            ->mapWithKeys(function($role) {
                return [$role => ucfirst($role)];
            })
            ->toArray();

        // Merge with default roles to ensure all options are available
        $allRoles = array_merge($this->getRoles(), $existingRoles);
        $roles = ['' => 'Semua Role'] + $allRoles;

        // Log available roles for debugging
        Log::info('Available roles:', [
            'default_roles' => $this->getRoles(),
            'existing_roles' => $existingRoles,
            'all_roles' => $allRoles
        ]);

        // Set breadcrumb data
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => url('/')],
            ['title' => 'User Account Registration']
        ];

        return view('users.index', [
            'users' => $users,
            'breadcrumb' => $breadcrumb,
            'roles' => $roles,
            'selectedRole' => $role
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = $this->getRoles();

        // Dapatkan daftar vendor yang belum memiliki akun
        $existingVendorPhones = User::where('role_name', 'vendor')
            ->pluck('username')
            ->map(function($username) {
                // Hapus semua karakter non-angka dari username
                return preg_replace('/[^0-9]/', '', $username);
            })
            ->toArray();

        // Get all vendors first, ordered by name
        $allVendors = \App\Models\Vendor::select('id', 'nama_vendor', 'no_hp', 'kode_vendor')
            ->orderBy('nama_vendor')
            ->get();

        // Filter out vendors that already have accounts and remove duplicates by vendor name
        $uniqueVendorNames = [];
        $vendors = $allVendors->reject(function ($vendor) use ($existingVendorPhones, &$uniqueVendorNames) {
            $phone = preg_replace('/[^0-9]/', '', $vendor->no_hp);
            $isDuplicate = in_array(strtolower(trim($vendor->nama_vendor)), $uniqueVendorNames);

            if (!$isDuplicate) {
                $uniqueVendorNames[] = strtolower(trim($vendor->nama_vendor));
            }

            return $isDuplicate || in_array($phone, $existingVendorPhones);
        })->values();

        // Get all mandor accounts
        $existingAccounts = User::where('role_name', 'mandor')
            ->get(['username', 'name']);

        // Create array of existing emails and names (case-insensitive)
        $existingEmails = $existingAccounts->map(function($user) {
            return strtolower(trim($user->username));
        })->toArray();

        $existingNames = $existingAccounts->map(function($user) {
            return strtolower(trim($user->name));
        })->toArray();

        // Get all foremen and filter out those with existing accounts
        $foremen = \App\Models\Foreman::orderBy('nama_mandor')
            ->get()
            ->filter(function($foreman) use ($existingEmails, $existingNames) {
                $foremanEmail = strtolower(trim($foreman->email));
                $foremanName = strtolower(trim($foreman->nama_mandor));

                // Exclude if email matches username OR name matches user's display name
                return !in_array($foremanEmail, $existingEmails) &&
                       !in_array($foremanName, $existingNames);
            })
            ->values();

        // Set breadcrumb data
        $breadcrumb = [
            ['title' => 'User Account Registration', 'url' => route('users.index')],
            ['title' => 'Tambah User', 'url' => route('users.create')],
        ];

        // Available roles
        $roles = [
            'vendor' => 'Vendor',
            'mandor' => 'Mandor',
            // 'gis_department' => 'GIS Department',
            'admin' => 'Admin',
            // 'finance' => 'Finance'
        ];

        return view('users.create', compact('breadcrumb', 'roles', 'vendors', 'foremen'));
    }

    /**
     * Show the form for editing the specified user's password.
     */
    public function editPassword($id)
    {
        $user = User::findOrFail($id);

        // Set breadcrumb data
        $breadcrumb = [
            ['title' => 'User Account Registration', 'url' => route('users.index')],
            ['title' => 'Ubah Password', 'url' => route('users.edit-password', $user->id)],
        ];

        return view('users.edit', compact('user', 'breadcrumb'));
    }

    /**
     * Update the specified user's password.
     */
    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Password saat ini tidak sesuai.');
                }
            }],
            'new_password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/'],
            'new_password_confirmation' => ['required', 'same:new_password'],
        ], [
            'current_password.required' => 'Password saat ini harus diisi',
            'new_password.required' => 'Password baru harus diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.regex' => 'Password harus terdiri dari huruf dan angka',
            'new_password_confirmation.required' => 'Konfirmasi password harus diisi',
            'new_password_confirmation.same' => 'Konfirmasi password tidak sesuai',
        ]);

        try {
            // Update the password
            $user->password = Hash::make($validated['new_password']);
            $user->save();

            return redirect()->route('users.index')
                ->with('success', 'Password berhasil diperbarui');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui password: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Manual password confirmation check
        if ($request->password !== $request->password_confirmation) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Konfirmasi password tidak sesuai');
        }
        
        $rules = [
            'role_name' => 'required|string',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ];
        
        $messages = [
            'password.min' => 'Password minimal 8 karakter',
            'password.required' => 'Password harus diisi',
        ];

        // Get available roles from the roles array
        $availableRoles = array_keys($this->getRoles());

        // Add validation for role name
        $rules['role_name'] = 'required|string|max:50';

        // Add role-specific validations
        if ($request->role_name === 'vendor') {
            $rules['vendor_id'] = 'required|exists:vendors,id';
        } elseif ($request->role_name === 'mandor') {
            $rules['foreman_id'] = 'required|exists:foreman,id';
        } else {
            $rules['email'] = 'required|email|max:255|unique:users,username';
        }

        $validated = $request->validate($rules, $messages);
        $roleName = $validated['role_name'];
        
        // Prepare user data
        $userData = [
            'name' => $validated['name'],
            'password' => Hash::make($validated['password']),
            'role_name' => $roleName,
        ];
        
        // Set username based on role
        if ($roleName === 'vendor') {
            $vendor = Vendor::find($validated['vendor_id']);
            $username = $vendor ? preg_replace('/[^0-9]/', '', $vendor->no_hp) : '';
        } elseif ($roleName === 'mandor') {
            $foreman = Foreman::find($validated['foreman_id']);
            $username = $foreman ? $foreman->email : '';
        } else {
            $username = $validated['email'];
        }
        
        // Check if username already exists
        if (User::where('username', $username)->exists()) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Akun dengan username ini sudah ada.');
        }
        
        $userData['username'] = $username;
        
        // Create the user
        try {
            User::create($userData);
            return redirect()->route('users.index')
                ->with('success', 'User berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = ['vendor' => 'Vendor', 'mandor' => 'Mandor', 'gis_department' => 'GIS Department', 'admin' => 'Admin', 'finance' => 'Finance'];

        // Get all vendors for dropdown
        $vendors = VendorAngkut::all();

        // Get all foremen for dropdown
        $foremen = \App\Models\Foreman::all();

        // Set breadcrumb data
        $breadcrumb = [
            ['title' => 'Home', 'url' => url('/')],
            ['title' => 'User Account Registration', 'url' => route('users.index')],
            ['title' => 'Edit User']
        ];

        return view('users.edit', [
            'user' => $user,
            'roles' => $roles,
            'vendors' => $vendors,
            'foremen' => $foremen,
            'breadcrumb' => $breadcrumb
        ]);
    }

    /**
     * Check if the provided password matches the authenticated user's password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPassword(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'current_password' => 'required|string|min:1',
            ], [
                'current_password.required' => 'Password saat ini harus diisi'
            ]);

            // Get authenticated user
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'valid' => false,
                    'message' => 'Sesi Anda telah berakhir. Silakan login kembali.'
                ], 401);
            }

            // Get and trim password
            $password = trim($request->current_password);
            if (empty($password)) {
                return response()->json([
                    'success' => false,
                    'valid' => false,
                    'message' => 'Password tidak boleh kosong'
                ]);
            }
            
            // Log the password verification attempt
            \Log::info('Password check attempt', [
                'user_id' => $user->id,
                'username' => $user->username,
                'input_length' => strlen($password),
                'stored_hash' => $user->password,
                'is_hashed' => password_get_info($user->password)['algoName'] ?? 'not_hashed',
                'bcrypt_verify' => password_verify($password, $user->password) ? 'MATCH' : 'NO_MATCH',
                'hash_check' => Hash::check($password, $user->password) ? 'MATCH' : 'NO_MATCH',
                'direct_comparison' => $password === $user->password ? 'EQUAL' : 'NOT_EQUAL'
            ]);
            
            // Check password using both methods to be thorough
            $isValid = Hash::check($password, $user->password) || 
                       password_verify($password, $user->password) ||
                       $password === $user->password; // Fallback for plain text (not recommended)
            
            if ($isValid) {
                return response()->json([
                    'success' => true,
                    'valid' => true,
                    'message' => 'Password valid',
                    'debug' => [
                        'method' => 'password_verified',
                        'hash_algorithm' => password_get_info($user->password)['algoName'] ?? 'unknown'
                    ]
                ]);
            } else {
                // Additional check: if password is not hashed in database
                if ($password === $user->password) {
                    // If we get here, the password was stored in plain text (insecure)
                    // Hash it now for security
                    $user->password = Hash::make($password);
                    $user->save();
                    
                    return response()->json([
                        'success' => true,
                        'valid' => true,
                        'message' => 'Password valid (upgraded security)',
                        'debug' => 'password_upgraded_to_hash'
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'valid' => false,
                    'message' => 'Password yang Anda masukkan salah',
                    'debug' => 'password_mismatch'
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('Password check error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting own account
            if (auth()->id() === $user->id) {
                return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
            }

            $user->delete();

            return redirect()->route('users.index')
                             ->with('success', 'Data user berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}
