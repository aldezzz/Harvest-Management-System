@extends('layouts.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor-angkut.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .jenis-unit-detail {
        background: #fff;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        max-width: 800px;
        margin: 0 auto;
    }
    
    .detail-item {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .detail-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .detail-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .detail-value {
        font-size: 1.125rem;
        color: #2d3748;
    }
    
    .btn-back {
        display: inline-flex;
        align-items: center;
        color: #4a5568;
        text-decoration: none;
        margin-bottom: 1.5rem;
    }
    
    .btn-back:hover {
        color: #2d3748;
    }
    
    .btn-back i {
        margin-right: 0.5rem;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.75rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .btn-edit {
        background-color: #f6e05e;
        color: #744210;
    }
    
    .btn-edit:hover {
        background-color: #ecc94b;
        color: #5f370e;
    }
    
    .btn-delete {
        background-color: #feb2b2;
        color: #9b2c2c;
        border: none;
        cursor: pointer;
    }
    
    .btn-delete:hover {
        background-color: #fc8181;
        color: #742a2a;
    }
    
    .btn-secondary {
        background-color: #e2e8f0;
        color: #4a5568;
    }
    
    .btn-secondary:hover {
        background-color: #cbd5e0;
        color: #2d3748;
    }
</style>
@endpush

@php
    $header = 'Detail Jenis Unit';
    $breadcrumb = [
        ['title' => 'Dashboard', 'url' => route('dashboard')],
        ['title' => 'List Jenis Unit', 'url' => route('jenis-unit.index')],
        ['title' => 'Detail Jenis Unit']
    ];
@endphp

@section('content')
<div class="jenis-unit-detail">
    <a href="{{ route('jenis-unit.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali ke List
    </a>
    
    <h2 style="margin-bottom: 2rem;">Detail Jenis Unit</h2>
    
    <div class="detail-item">
        <span class="detail-label">ID</span>
        <div class="detail-value">{{ $jenisUnit->id }}</div>
    </div>
    
    <div class="detail-item">
        <span class="detail-label">Jenis Unit</span>
        <div class="detail-value">{{ $jenisUnit->jenis_unit }}</div>
    </div>
    
    <div class="detail-item">
        <span class="detail-label">Dibuat Pada</span>
        <div class="detail-value">{{ $jenisUnit->created_at->format('d/m/Y H:i') }}</div>
    </div>
    
    <div class="detail-item">
        <span class="detail-label">Diperbarui Pada</span>
        <div class="detail-value">{{ $jenisUnit->updated_at->format('d/m/Y H:i') }}</div>
    </div>
    
    <div class="action-buttons">
        @can('edit-jenis-unit')
            <a href="{{ route('jenis-unit.edit', $jenisUnit->id) }}" class="btn btn-edit">
                <i class="fas fa-edit"></i> Edit
            </a>
        @endcan
        
        @can('delete-jenis-unit')
            <form action="{{ route('jenis-unit.destroy', $jenisUnit->id) }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event)">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
        @endcan
        
        <a href="{{ route('jenis-unit.index') }}" class="btn btn-secondary">
            <i class="fas fa-list"></i> Kembali ke List
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Function to confirm delete
    function confirmDelete(event) {
        event.preventDefault();
        const form = event.target;
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
    
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
