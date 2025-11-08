@extends('layouts.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 6px 12px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .code-group {
        padding: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        background-color: #f9fafb;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="vendor-container">
    <h2>Tambah Sub Block Baru</h2>

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p class="font-bold">Validasi Gagal</p>
            <ul class="list-disc pl-5 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sub-blocks.store') }}" method="POST" id="subBlockForm">
        @csrf
        @method('POST')



        <!-- Estate Selection -->
        <div class="form-group">
            <label for="estate" class="form-label">Estate</label>
            <select name="estate" id="estate" class="form-select" required>
                <option value="">Pilih Estate</option>
                <option value="LKL" {{ old('estate') == 'LKL' ? 'selected' : '' }}>LKL</option>
                <option value="PLG" {{ old('estate') == 'PLG' ? 'selected' : '' }}>PLG</option>
                <option value="RST" {{ old('estate') == 'RST' ? 'selected' : '' }}>RST</option>
            </select>
            @error('estate')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Divisi Selection (initially disabled) -->
        <div class="form-group">
            <label for="divisi" class="form-label">Divisi</label>
            <select name="divisi" id="divisi" class="form-select select2-divisi" disabled required>
                <option value="">Pilih Divisi</option>
                @if(old('divisi') && old('estate'))
                    @foreach($estatesWithDivisions[old('estate')] ?? [] as $divisi)
                        <option value="{{ $divisi }}" {{ old('divisi') == $divisi ? 'selected' : '' }}>
                            {{ $divisi }}
                        </option>
                    @endforeach
                @endif
            </select>
            @error('divisi')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Blok Selection (initially disabled) -->
        <div class="form-group">
            <label for="blok" class="form-label">Blok</label>
            <select name="blok" id="blok" class="form-select select2-blok" disabled required>
                <option value="">Pilih Blok</option>
                @if(old('blok') && old('divisi'))
                    @foreach($blocks ?? [] as $block)
                        <option value="{{ $block }}" {{ old('blok') == $block ? 'selected' : '' }}>
                            {{ $block }}
                        </option>
                    @endforeach
                @endif
            </select>
            @error('blok')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Kode Petak Selection (initially disabled) -->
        <div class="form-group">
            <label for="kode_petak" class="form-label">Kode Petak</label>
            <select name="kode_petak" id="kode_petak" class="form-select select2-dropdown" disabled required>
                <option value="">Pilih Kode Petak</option>
                @if(old('kode_petak') && old('blok'))
                    <option value="{{ old('kode_petak') }}" selected>{{ old('kode_petak') }}</option>
                @endif
            </select>
            @error('kode_petak')
                <div class="text-danger mt-1 text-sm">{{ $message }}</div>
            @enderror
            <div id="kode_petak_loading" class="hidden mt-1 text-sm text-gray-500">
                <i class="fas fa-spinner fa-spin"></i> Memuat kode petak yang tersedia...
            </div>
        </div>

        <div class="form-group">
            <label for="luas_area" class="form-label">Luas Area (ha)</label>
            <div class="relative">
                <input type="number" step="0.01" name="luas_area" id="luas_area"
                       class="form-input w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('luas_area') }}" placeholder="Input Luas Area..." required>
            </div>
            @error('luas_area')
                <div class="text-danger mt-1 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="age_months" class="form-label">Umur (Bulan)</label>
            <div class="relative">
                <input type="number" name="age_months" id="age_months"
                       class="form-input w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('age_months') }}" placeholder="Input Umur dalam Bulan...">
            </div>
            @error('age_months')
                <div class="text-danger mt-1 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="zona" class="form-label">Zona</label>
            <select name="zona" id="zona" class="form-select" required>
                <option value="">Pilih Zona</option>
                <option value="1" {{ old('zona') == '1' ? 'selected' : '' }}>1</option>
                <option value="2" {{ old('zona') == '2' ? 'selected' : '' }}>2</option>
                <option value="3" {{ old('zona') == '3' ? 'selected' : '' }}>3</option>
                <option value="4" {{ old('zona') == '4' ? 'selected' : '' }}>4</option>
            </select>
            @error('zona')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="keterangan" class="form-label">Keterangan</label>
            <div class="relative">
                <textarea name="keterangan" id="keterangan" class="form-input w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="Input Keterangan...">{{ old('keterangan') }}</textarea>
            </div>
            @error('keterangan')
                <div class="text-danger mt-1 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="geom_json" class="form-label">Data Peta (GeoJSON Format)</label>
            <div class="relative">
                <textarea name="geom_json" id="geom_json" class="form-input w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="6" placeholder='{
  "type": "Polygon",
  "coordinates": [
    [
      [122.01, -4.55],
      [122.02, -4.55],
      [122.02, -4.56],
      [122.01, -4.56],
      [122.01, -4.55]
    ]
  ]
}'>{{ old('geom_json') }}</textarea>
            </div>
            <div class="text-sm text-gray-500 mt-1">
                Pastikan format JSON sesuai dengan contoh di atas. Harap gunakan tipe "Polygon" dan koordinat yang valid.
            </div>
            @error('geom_json')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <div class="flex items-center">
                <input type="hidden" name="aktif" value="0">
                <input type="checkbox" name="aktif" id="aktif" class="form-checkbox" value="1"
                       {{ old('aktif', '1') == '1' ? 'checked' : '' }}>
                <label for="aktif" class="ml-2">Aktif</label>
            </div>
            @error('aktif')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('sub-blocks.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Initialize Select2
    $(document).ready(function() {
        // Initialize Select2 for Kode Petak
        $('.select2-dropdown').select2({
            placeholder: 'Cari atau pilih kode petak',
            width: '100%'
        });
        
        // Initialize Select2 for Divisi
        $('.select2-divisi').select2({
            placeholder: 'Cari atau pilih divisi',
            width: '100%'
        });
        
        // Initialize Select2 for Blok
        $('.select2-blok').select2({
            placeholder: 'Cari atau pilih blok',
            width: '100%',
            allowClear: true,
            width: '100%',
            dropdownAutoWidth: true
        });
    });
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            var estateSelect = document.getElementById('estate');
            var divisiSelect = document.getElementById('divisi');
            var blokSelect = document.getElementById('blok');
            var form = document.getElementById('subBlockForm');
            var submitBtn = form.querySelector('button[type="submit"]');
            var originalSubmitBtnText = submitBtn.innerHTML;

            // Function to show loading state
            function setLoading(isLoading) {
                if (isLoading) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                } else {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalSubmitBtnText;
                }
            }

            // Function to show success message
            function showSuccessMessage(message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: message || 'Data berhasil disimpan',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('sub-blocks.index') }}';
                    }
                });
            }

            // Function to validate GeoJSON format
            function validateGeoJSON(geojson) {
                try {
                    const data = JSON.parse(geojson);
                    if (!data.type || data.type !== 'Polygon') {
                        return 'Tipe GeoJSON harus berupa "Polygon"';
                    }
                    if (!Array.isArray(data.coordinates) || data.coordinates.length === 0) {
                        return 'Koordinat tidak valid';
                    }
                    // Validate polygon coordinates
                    const coords = data.coordinates[0];
                    if (coords.length < 4) {
                        return 'Polygon membutuhkan minimal 4 titik koordinat';
                    }
                    // Check if first and last points are the same (closed polygon)
                    const first = coords[0];
                    const last = coords[coords.length - 1];
                    if (first[0] !== last[0] || first[1] !== last[1]) {
                        return 'Polygon harus tertutup (titik awal dan akhir harus sama)';
                    }
                    return null;
                } catch (e) {
                    return 'Format GeoJSON tidak valid: ' + e.message;
                }
            }

            // Function to show error messages
            function showErrorMessages(errors) {
                var errorMessage = '';
                Object.entries(errors).forEach(function(entry) {
                    var key = entry[0];
                    var value = entry[1];
                    errorMessage += value.join('<br>') + '<br>';
                });

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: errorMessage || 'Terjadi kesalahan saat menyimpan data',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }

            // Load divisions when estate is selected
            if (estateSelect) {
                estateSelect.addEventListener('change', function() {
                    var estate = this.value;

                    if (estate) {
                        divisiSelect.disabled = false;

                        // Clear existing options
                        divisiSelect.innerHTML = '<option value="">Pilih Divisi</option>';

                        // Add divisions for the selected estate
                        var divisions = @json($estatesWithDivisions);
                        if (divisions[estate]) {
                            divisions[estate].forEach(function(divisi) {
                                var option = new Option(divisi, divisi);
                                divisiSelect.add(option);
                            });
                        }
                    } else {
                        divisiSelect.disabled = true;
                        divisiSelect.innerHTML = '<option value="">Pilih Divisi</option>';
                        if (blokSelect) {
                            blokSelect.disabled = true;
                            blokSelect.innerHTML = '<option value="">Pilih Blok</option>';
                        }
                    }
                });
            }

            // Load blocks when division is selected
            if (divisiSelect) {
                $(divisiSelect).on('change', function() {
                    var divisi = this.value;

                    if (divisi && blokSelect) {
                        blokSelect.disabled = false;

                        // Fetch blocks via AJAX
                        fetch('{{ route("sub-blocks.get-blocks-by-division") }}?divisi=' + encodeURIComponent(divisi))
                            .then(function(response) {
                                return response.json();
                            })
                            .then(function(data) {
                                // Clear existing options
                                blokSelect.innerHTML = '<option value="">Pilih Blok</option>';

                                // Add blocks to the select
                                data.forEach(function(item) {
                                    var option = new Option(item.blok, item.blok);
                                    blokSelect.add(option);
                                });
                                $(blokSelect).trigger('change'); // Update Select2 with new options
                            })
                            .catch(function(error) {
                                console.error('Error loading blocks:', error);
                            });
                        // Enable and clear blok select
                        blokSelect.disabled = false;
                        $(blokSelect).empty().append('<option value="">Pilih Blok</option>');
                        $(blokSelect).trigger('change'); // Trigger change to update Select2
                    }
                });
            }

            // Function to load available kode_petak for selected blok
            function loadAvailableKodePetak(blok) {
                const kodePetakSelect = document.getElementById('kode_petak');
                const loadingIndicator = document.getElementById('kode_petak_loading');

                if (!blok) {
                    kodePetakSelect.disabled = true;
                    kodePetakSelect.innerHTML = '<option value="">Pilih Kode Petak</option>';
                    $(kodePetakSelect).select2({
                        placeholder: 'Pilih blok terlebih dahulu',
                        allowClear: true,
                        width: '100%',
                        dropdownAutoWidth: true
                    });
                    return;
                }

                // Show loading indicator
                loadingIndicator.classList.remove('hidden');
                kodePetakSelect.disabled = true;
                kodePetakSelect.innerHTML = '<option value="">Memuat kode petak...</option>';
                $(kodePetakSelect).select2('destroy');
                $(kodePetakSelect).select2({
                    placeholder: 'Memuat kode petak...',
                    allowClear: true,
                    width: '100%',
                    dropdownAutoWidth: true
                });

                // Fetch available kode_petak via AJAX
                fetch('/sub-blocks/get-available-kode-petak/' + encodeURIComponent(blok))
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Gagal memuat kode petak');
                        }
                        return response.json();
                    })
                    .then(function(result) {
                        if (result.success && result.data) {
                            // Clear existing options
                            kodePetakSelect.innerHTML = '<option value="">Pilih Kode Petak</option>';

                            // Add available kode_petak options
                            result.data.forEach(function(item) {
                                const option = new Option(item.kode_petak, item.kode_petak);
                                kodePetakSelect.add(option);
                            });

                            // Enable the select and reinitialize Select2
                            kodePetakSelect.disabled = false;
                            $(kodePetakSelect).select2('destroy');
                            $(kodePetakSelect).select2({
                                placeholder: 'Cari atau pilih kode petak',
                                allowClear: true,
                                width: '100%',
                                dropdownAutoWidth: true,
                                minimumResultsForSearch: 1
                            });

                            // If there's an old value, try to select it
                            const oldKodePetak = '{{ old("kode_petak") }}';
                            if (oldKodePetak) {
                                $(kodePetakSelect).val(oldKodePetak).trigger('change');
                            }
                        } else {
                            throw new Error(result.message || 'Gagal memuat kode petak');
                        }
                    })
                    .catch(function(error) {
                        console.error('Error loading kode_petak:', error);
                        kodePetakSelect.innerHTML = '<option value="">Gagal memuat kode petak</option>';
                        $(kodePetakSelect).select2('destroy');
                        $(kodePetakSelect).select2({
                            placeholder: 'Gagal memuat kode petak',
                            allowClear: true,
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                    })
                    .finally(function() {
                        loadingIndicator.classList.add('hidden');
                    });
            }

            // Handle blok selection change
            if (blokSelect) {
                $(blokSelect).on('change', function() {
                    const blok = this.value;
                    loadAvailableKodePetak(blok);
                });
            }

            // If editing and estate is already selected, trigger change event
            @if(old('estate'))
                if (estateSelect) {
                    estateSelect.dispatchEvent(new Event('change'));
                }
            @endif

            // If editing and divisi is already selected, trigger change event
            @if(old('divisi'))
                // Small timeout to ensure the divisions are loaded first
                setTimeout(function() {
                    if (divisiSelect) {
                        divisiSelect.value = '{{ old("divisi") }}';
                        divisiSelect.dispatchEvent(new Event('change'));

                        // If blok is also already selected, load its kode_petak
                        @if(old('blok'))
                            setTimeout(function() {
                                if (blokSelect) {
                                    blokSelect.value = '{{ old("blok") }}';
                                    loadAvailableKodePetak('{{ old("blok") }}');
                                }
                            }, 100);
                        @endif
                    }
                }, 300);
            @endif

            // Handle form submission with AJAX
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Validate GeoJSON before submission
                const geojsonInput = document.getElementById('geom_json');
                if (geojsonInput && geojsonInput.value.trim() !== '') {
                    const error = validateGeoJSON(geojsonInput.value);
                    if (error) {
                        showErrorMessages({geom_json: [error]});
                        setLoading(false);
                        return false;
                    }
                }

                setLoading(true);

                // Get form data
                var formData = new FormData(form);

                // Send AJAX request
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(function(response) {
                    return response.json().then(function(data) {
                        return {ok: response.ok, data: data};
                    });
                })
                .then(function(result) {
                    if (result.ok) {
                        // Show success message and redirect
                        showSuccessMessage('Data sub block berhasil disimpan');
                    } else {
                        // Show validation errors
                        if (result.data.errors) {
                            showErrorMessages(result.data.errors);
                        } else {
                            throw new Error(result.data.message || 'Terjadi kesalahan saat menyimpan data');
                        }
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.message || 'Terjadi kesalahan saat menyimpan data',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                })
                .finally(function() {
                    // Restore button state
                    setLoading(false);
                });

                return false;
            });
        });
    })();
</script>
@endpush
