@extends('layouts.master')

@section('page-title', 'Edit Lembar Kerja Tebang (LKT)')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/vendor-angkut.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 42px;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
</style>
@endpush

@section('content')
<div class="vendor-container">
    <h2 class="mb-4">Edit Data LKT</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('lkt.update', $lkt->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">Kode LKT</label>
                <input type="text" name="kode_lkt" value="{{ $lkt->kode_lkt }}" readonly class="form-input w-full">
            </div>

            <div>
                <label class="block font-medium">Kode SPT</label>
                <select name="kode_spt" id="kode_spt" class="form-select w-full select2-spt" required>
                    <option value="">-- Pilih Kode SPT --</option>
                    @foreach($spts as $spt)
                        <option value="{{ $spt->kode_spt }}" 
                                data-vendor-tebang="{{ $spt->kode_vendor_tebang }}"
                                data-kode-petak="{{ $spt->kode_petak }}"
                                data-tarif-zona="{{ $spt->tarif_zona_angkutan }}"
                                data-jenis-tebangan="{{ $spt->jenis_tebangan }}"
                                {{ $lkt->kode_spt == $spt->kode_spt ? 'selected' : '' }}>
                            {{ $spt->kode_spt }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium">Tanggal Tebang</label>
                <input type="date" name="tanggal_tebang" class="form-input w-full" value="{{ $lkt->tanggal_tebang }}" required>
            </div>

            <div>
                <label class="block font-medium">Kode Petak</label>
                <input type="text" name="kode_petak" id="kode_petak" class="form-input w-full" value="{{ $lkt->kode_petak }}" readonly>
            </div>

            <div>
                <label class="block font-medium">Vendor Tebang</label>
                <input type="text" name="kode_vendor_tebang" id="kode_vendor_tebang" class="form-input w-full" value="{{ $lkt->kode_vendor_tebang }}" readonly>
            </div>

            <div>
                <label class="block font-medium">Vendor Angkut</label>
                <select name="kode_vendor_angkut" id="kode_vendor_angkut" class="form-select w-full select2-vendor">
                    <option value="">-- Pilih Vendor Angkut --</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->kode_vendor }}" 
                                data-nama="{{ $vendor->nama_vendor }}"
                                {{ $vendor->kode_vendor == $lkt->kode_vendor_angkut ? 'selected' : '' }}>
                            {{ $vendor->kode_vendor }} / {{ $vendor->nama_vendor }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium">Kode Lambung</label>
                <select name="kode_driver" id="kode_driver" class="form-select w-full" required>
                    <option value="">-- Pilih Kode Lambung --</option>
                    @foreach($drivers as $driver)
                        @if($driver->kode_vendor == $lkt->kode_vendor_angkut)
                            <option value="{{ $driver->kode_lambung }}" 
                                    data-kode-vendor="{{ $driver->kode_vendor }}"
                                    {{ $driver->kode_lambung == $lkt->kode_driver ? 'selected' : '' }}>
                                {{ $driver->kode_lambung }} / {{ $driver->plat_nomor }} / {{ $driver->nama_vendor }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium">Tarif Zona Angkutan</label>
                <input type="text" name="tarif_zona_angkutan" id="tarif_zona_angkutan" class="form-input w-full" value="{{ $lkt->tarif_zona_angkutan }}" readonly>
            </div>

            <div>
                <label class="block font-medium">Jenis Tebangan</label>
                <input type="text" name="jenis_tebangan" id="jenis_tebangan" class="form-input w-full" value="{{ $lkt->jenis_tebangan }}" readonly>
            </div>
        </div>

        <div class="mt-6">
            <label class="block font-medium">Catatan</label>
            <textarea name="catatan" class="form-textarea w-full">{{ $lkt->catatan }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div>
                <label class="block font-medium">Dibuat Oleh</label>
                <input type="text" name="dibuat_oleh" value="{{ $lkt->dibuat_oleh ?? 'Mandor' }}" readonly class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block font-medium">Diperiksa 1 Oleh</label>
                <input type="text" name="diperiksa_oleh" value="{{ $lkt->diperiksa_oleh ?? 'Asst. Divisi Plantation' }}" readonly class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block font-medium">Diperiksa 2 Oleh</label>
                <input type="text" name="disetujui_oleh" value="{{ $lkt->disetujui_oleh ?? 'Asst. Manager Plantation' }}" readonly class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block font-medium">Ditimbang Oleh</label>
                <input type="text" name="ditimbang_oleh" value="{{ $lkt->ditimbang_oleh ?? 'Petugas Timbangan' }}" readonly class="form-input w-full bg-gray-100">
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('lkt.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk Kode SPT
        $('.select2-spt').select2({
            placeholder: 'Cari Kode SPT',
            allowClear: true,
            width: '100%',
            dropdownParent: $('.vendor-container')
        });

        // Inisialisasi Select2 untuk Vendor Angkut
        $('.select2-vendor').select2({
            placeholder: 'Cari Vendor Angkut',
            allowClear: true,
            width: '100%',
            dropdownParent: $('.vendor-container'),
            matcher: function(params, data) {
                // Jika tidak ada pencarian, tampilkan semua
                if ($.trim(params.term) === '') {
                    return data;
                }
                
                // Cari di kode vendor dan nama vendor
                var searchTerm = params.term.toLowerCase();
                var text = data.text.toLowerCase();
                if (text.indexOf(searchTerm) > -1) {
                    return data;
                }
                
                // Cek di atribut data-nama
                var nama = $(data.element).data('nama').toLowerCase();
                if (nama.indexOf(searchTerm) > -1) {
                    return data;
                }
                
                return null;
            },
            templateResult: function(data) {
                if (!data.id) { return data.text; }
                return $('<span>').text(data.text).addClass('block truncate');
            },
            templateSelection: function(data) {
                if (!data.id) { return data.text; }
                return $('<span>').text(data.text).addClass('block truncate');
            }
        });

        // Inisialisasi Select2 untuk Kode Lambung
        $('#kode_driver').select2({
            placeholder: 'Pilih Kode Lambung',
            allowClear: true,
            width: '100%',
            dropdownParent: $('.vendor-container')
        });

        // Simpan data driver untuk referensi
        var allDrivers = [];
        @foreach($drivers as $driver)
            allDrivers.push({
                'kode_lambung': '{{ addslashes($driver->kode_lambung) }}',
                'plat_nomor': '{{ addslashes($driver->plat_nomor) }}',
                'kode_vendor': '{{ addslashes($driver->kode_vendor) }}',
                'nama_vendor': '{{ addslashes($driver->nama_vendor) }}'
            });
        @endforeach

        // Fungsi untuk memperbarui opsi kode lambung berdasarkan vendor yang dipilih
        function updateDriverOptions(vendorKode) {
            console.log('Updating driver options for vendor:', vendorKode);
            console.log('All drivers:', allDrivers);
            
            var driverSelect = $('#kode_driver');
            
            // Kosongkan opsi yang ada
            driverSelect.empty();
            driverSelect.append('<option value="">-- Pilih Kode Lambung --</option>');
            
            if (!vendorKode) {
                console.log('No vendor selected, showing empty driver list');
                driverSelect.trigger('change');
                return;
            }
            
            // Filter driver berdasarkan vendor yang dipilih (case insensitive)
            var filteredDrivers = allDrivers.filter(function(driver) {
                return driver.kode_vendor && driver.kode_vendor.toString().toLowerCase() === vendorKode.toString().toLowerCase();
            });
            
            console.log('Filtered drivers:', filteredDrivers);
            
            // Tambahkan opsi yang sesuai
            filteredDrivers.forEach(function(driver) {
                var optionText = [];
                if (driver.kode_lambung) optionText.push(driver.kode_lambung);
                if (driver.plat_nomor) optionText.push(driver.plat_nomor);
                if (driver.nama_vendor) optionText.push(driver.nama_vendor);
                
                driverSelect.append(
                    $('<option></option>')
                        .attr('value', driver.kode_lambung || '')
                        .text(optionText.join(' / '))
                );
            });
            
            // Trigger change untuk memperbarui tampilan
            driverSelect.trigger('change');
            
            // Pilih opsi yang sesuai dengan data yang ada
            var currentDriver = '{{ $lkt->kode_driver }}';
            if (currentDriver) {
                driverSelect.val(currentDriver).trigger('change');
            } else if (filteredDrivers.length === 1) {
                driverSelect.val(filteredDrivers[0].kode_lambung).trigger('change');
            }
        }

        // Event handler untuk perubahan vendor angkut
        $('#kode_vendor_angkut').on('change', function() {
            var selectedVendor = $(this).val();
            updateDriverOptions(selectedVendor);
        });

        // Inisialisasi awal
        updateDriverOptions($('#kode_vendor_angkut').val());

        // Fungsi untuk mengambil data SPT
        function fetchSptData(kodeSPT) {
            if (!kodeSPT) {
                clearFields();
                return;
            }

            console.log('Mengambil data SPT dengan kode:', kodeSPT);

            fetch(`/lkt/get-spt-data/${kodeSPT}`)
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal mengambil data SPT');
                    }
                    return data;
                })
                .then(data => {
                    console.log('Data SPT:', data);
                    if (data.success) {
                        // Update vendor tebang field
                        $('#kode_vendor_tebang').val(`${data.kode_vendor_tebang} / ${data.nama_vendor_tebang}`);

                        // Update kode petak field
                        $('#kode_petak').val(data.kode_petak || '-');

                        // Update tarif zona angkutan field
                        $('#tarif_zona_angkutan').val(data.tarif_zona_angkutan || '1');

                        // Update jenis tebangan field
                        $('#jenis_tebangan').val(data.jenis_tebangan || 'Tebang Habis');
                    } else {
                        throw new Error(data.message || 'Gagal memuat data SPT');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'Terjadi kesalahan saat memuat data SPT');
                    clearFields();
                });
        }

        // Fungsi untuk mengosongkan field
        function clearFields() {
            $('#kode_vendor_tebang').val('');
            $('#kode_petak').val('');
            $('#tarif_zona_angkutan').val('');
            $('#jenis_tebangan').val('');
        }

        // Event handler untuk perubahan Kode SPT
        $('#kode_spt').on('change', function() {
            const kodeSPT = $(this).val();
            fetchSptData(kodeSPT);
        });

        // Inisialisasi data jika ada nilai yang sudah dipilih
        const initialKodeSPT = $('#kode_spt').val();
        if (initialKodeSPT) {
            fetchSptData(initialKodeSPT);
        }
    });
document.addEventListener('DOMContentLoaded', function () {
    const kodeSPTSelect = document.getElementById('kode_spt');
    const kodePetakInput = document.getElementById('kode_petak');
    const vendorTebangInput = document.getElementById('kode_vendor_tebang');
    const tarifZonaInput = document.getElementById('tarif_zona_angkutan');
    const jenisTebanganInput = document.getElementById('jenis_tebangan');

    kodeSPTSelect.addEventListener('change', function () {
        const kodeSPT = this.value;
        if (kodeSPT) {
            fetch(`/lkt/get-spt-data/${kodeSPT}`)
                .then(res => res.json())
                .then(data => {
                    vendorTebangInput.value = data.kode_vendor_tebang || '-';
                    kodePetakInput.value = data.kode_petak || '-';
                    tarifZonaInput.value = data.tarif_zona_angkutan || '-';
                    jenisTebanganInput.value = data.jenis_tebangan || '-';
                });
        } else {
            vendorTebangInput.value = '';
            kodePetakInput.value = '';
            tarifZonaInput.value = '';
            jenisTebanganInput.value = '';
        }
    });
});
</script>
@endpush
