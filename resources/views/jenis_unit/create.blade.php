@extends('layouts.master')

@php
    $header = 'Tambah Jenis Unit';
    $breadcrumb = [
        ['title' => 'List Jenis Unit', 'url' => route('jenis-unit.index')],
        ['title' => 'Tambah Jenis Unit']
    ];
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor-angkut.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .vendor-container {
        background: #fff;
        padding: 2.5rem 3rem;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        max-width: 1200px;
        width: 100%;
        margin: 0 auto;
        box-sizing: border-box;
    }
    
    .form-group {
        margin-bottom: 1.25rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #374151;
        font-size: 0.875rem;
    }
    
    .form-control {
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.5;
        color: #4b5563;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .btn-submit {
        background-color: #2563eb;
        color: white;
        padding: 0.5rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        border-radius: 0.375rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-submit:hover {
        background-color: #1d4ed8;
    }
    
    .btn-back {
        background-color: #f3f4f6;
        color: #4b5563;
        padding: 0.5rem 1.25rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        margin-right: 0.75rem;
    }
    
    .btn-back:hover {
        background-color: #e5e7eb;
        border-color: #9ca3af;
    }
    
    .form-actions {
        display: flex;
        justify-content: flex-start;
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .is-invalid {
        border-color: #ef4444 !important;
    }
    
    .invalid-feedback {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }
    
    .required-field::after {
        content: ' *';
        color: #ef4444;
    }
</style>
@endpush

@php
    $header = 'Tambah Jenis Unit Baru';
    $breadcrumb = [
        ['title' => 'Dashboard', 'url' => route('dashboard')],
        ['title' => 'List Jenis Unit', 'url' => route('jenis-unit.index')],
        ['title' => 'Tambah Jenis Unit Baru']
    ];
@endphp

@section('content')
<div class="vendor-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Tambah Jenis Unit</h2>
        <a href="{{ route('jenis-unit.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    
    <form action="{{ route('jenis-unit.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="jenis_unit" class="form-label required-field">Jenis Unit</label>
            <input type="text" name="jenis_unit" id="jenis_unit" class="form-control @error('jenis_unit') is-invalid @enderror" 
                   value="{{ old('jenis_unit') }}" required>
            @error('jenis_unit')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <i class="fas fa-save mr-2"></i> Simpan
            </button>
            <a href="{{ route('jenis-unit.index') }}" class="btn-back">
                <i class="fas fa-times mr-2"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Show error message if there are validation errors
    @if($errors->any())
        const errorMessages = [];
        @foreach($errors->all() as $error)
            errorMessages.push('{{ $error }}');
        @endforeach
        
        Swal.fire({
            icon: 'error',
            title: 'Gagal Menyimpan',
            html: errorMessages.join('<br>'),
            confirmButtonText: 'Mengerti'
        });
    @endif
    
    // Show success message if exists
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif
    
    // Show error message if exists
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: true
        });
    @endif
</script>
@endpush
