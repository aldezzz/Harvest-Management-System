@extends('layouts.master')

@section('title', 'List Jenis Unit')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor-angkut.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    /* Custom styles for Jenis Unit table */
    .vendor-table {
        width: 100%;
        font-size: 0.875rem;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    
    .vendor-table thead {
        background-color: #bfdbfe;
        color: #1e40af;
    }
    
    .vendor-table th,
    .vendor-table td {
        padding: 0.6rem 0.75rem;
        border: 1px solid #e5e7eb;
        text-align: left;
    }
    
    .vendor-table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }
    
    .vendor-table tbody tr:hover {
        background-color: #f0f9ff;
    }
    
    .vendor-table th:first-child,
    .vendor-table td:first-child {
        width: 1%;
        white-space: nowrap;
    }
    
    .vendor-table th:last-child,
    .vendor-table td:last-child {
        width: 250px;
        white-space: nowrap;
        text-align: center;
    }
    
    /* Button Styles */
    .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.375rem;
        cursor: pointer;
        border: none;
        transition: background-color 0.2s ease-in-out;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        text-decoration: none;
    }
    
    .btn i {
        margin-right: 5px;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .tambah-jenis-unit-btn {
        background-color: #2563eb;
        color: white;
        border: none;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: background-color 0.2s;
    }
    
    .tambah-jenis-unit-btn:hover {
        background-color: #1d4ed8;
        color: white;
        text-decoration: none;
    }
    
    .btn-edit {
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 0.25rem;
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        min-width: 80px;
        justify-content: center;
    }
    
    .btn-edit:hover {
        background-color: #2563eb;
    }
    
    .btn-delete {
        background-color: #ef4444;
        color: white;
        border: none;
        border-radius: 0.25rem;
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        min-width: 80px;
        justify-content: center;
    }
    
    .btn-delete:hover {
        background-color: #dc2626;
    }
    
    .btn-danger {
        background-color: #dc2626;
        color: white;
    }
    
    .btn-danger:hover {
        background-color: #b91c1c;
    }
    
    .btn-group {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }
    
    .pagination .page-item {
        list-style: none;
    }
    
    .pagination .page-link {
        padding: 8px 12px;
        border: 1px solid #ddd;
        color: #4e73df;
        text-decoration: none;
        border-radius: 4px;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #4e73df;
        color: white;
        border-color: #4e73df;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #b7b9cc;
        pointer-events: none;
    }
    
    .no-data {
        text-align: center;
        padding: 30px;
        color: #6c757d;
    }
    
    .reset-search-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        font-size: 16px;
    }
    
    .search-container {
        position: relative;
        flex: 1;
    }
</style>
@endpush

@php
    $header = 'List Jenis Unit';
    $breadcrumb = [
        ['title' => 'Dashboard', 'url' => route('dashboard')],
        ['title' => 'List Jenis Unit', 'url' => route('jenis-unit.index')]
    ];
@endphp

@section('content')
<div class="vendor-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-2xl font-bold">List Jenis Unit</h2>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <form action="{{ route('jenis-unit.index') }}" method="GET" class="search-form" id="search-form" style="flex-grow: 1; margin-right: 15px;">
            <div class="filter-group" style="display: flex; align-items: center; gap: 10px;">
                <div style="display: flex; gap: 10px; align-items: center; width: 100%;">
                    <div style="position: relative;">
                        <input type="text" name="search" id="search-input" 
                               class="search-input" 
                               placeholder="Cari jenis unit..."
                               value="{{ request('search') }}">
                        @if(request('search'))
                            <button type="button" onclick="resetSearch()" class="reset-search-btn" title="Reset Pencarian">
                                &times;
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
        
        @can('create-jenis-unit')
            <div class="btn-group" style="margin-left: 15px;">
                <a href="{{ route('jenis-unit.create') }}" class="btn btn-primary tambah-jenis-unit-btn">
                    <i class="fas fa-plus"></i> Tambah Jenis Unit
                </a>
            </div>
        @endcan
    </div>

    <div class="table-responsive">
        <table class="vendor-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Unit</th>
                    <th>Dibuat Pada</th>
                    <th>Diperbarui Pada</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jenisUnits as $index => $jenisUnit)
                    <tr>
                        <td>{{ $jenisUnits->firstItem() + $index }}</td>
                        <td>{{ $jenisUnit->jenis_unit }}</td>
                        <td>{{ $jenisUnit->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $jenisUnit->updated_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('edit-jenis-unit')
                                <a href="{{ route('jenis-unit.edit', $jenisUnit->id) }}" class="btn btn-sm btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endcan
                                @can('delete-jenis-unit')
                                <form action="{{ route('jenis-unit.destroy', $jenisUnit->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data jenis unit</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="dataTables_info">
            Menampilkan {{ $jenisUnits->firstItem() }} sampai {{ $jenisUnits->lastItem() }} dari {{ $jenisUnits->total() }} entri
        </div>
        <div class="dataTables_paginate">
            {{ $jenisUnits->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Function to reset search
    function resetSearch() {
        const url = new URL(window.location.href);
        url.searchParams.delete('search');
        window.location.href = url.toString();
    }

    // Handle delete with SweetAlert2 confirmation
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.delete-form');
        
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
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
                        const form = e.target;
                        const url = form.getAttribute('action');
                        const token = form.querySelector('input[name="_token"]').value;
                        
                        fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Gagal!', data.message || 'Terjadi kesalahan saat menghapus data', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Terjadi kesalahan saat menghubungi server', 'error');
                        });
                    }
                });
            });
        });
    });
    
    // Handle search on enter key
    document.getElementById('search-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('search-form').submit();
        }
    });
</script>
@endpush
