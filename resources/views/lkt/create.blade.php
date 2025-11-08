@extends('layouts.master')

@section('page-title', 'Tambah Lembar Kerja Tebang (LKT)')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 42px;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="vendor-container">
    <h2 class="mb-4">Tambah Data LKT</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('lkt.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">Kode LKT</label>
                <input type="text" name="kode_lkt" value="{{ $kodeLKT }}" readonly class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block font-medium">Kode SPT</label>
                <select name="kode_spt" id="kode_spt" class="form-select w-full select2-spt" required>
                    <option value="">-- Pilih Kode SPT --</option>
                    @foreach($spts as $spt)
                        <option value="{{ $spt->kode_spt }}">{{ $spt->kode_spt }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium">Tanggal Tebang</label>
                <input type="date" name="tanggal_tebang" class="form-input w-full" required>
            </div>

            <div>
                <label class="block font-medium">Kode Petak</label>
                <input type="text" name="kode_petak" id="kode_petak" readonly class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block font-medium">Vendor Tebang</label>
                <input type="text" name="kode_vendor_tebang" id="kode_vendor_tebang" readonly class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block font-medium">Vendor Angkut</label>
                <select name="kode_vendor_angkut" id="kode_vendor_angkut" class="form-select w-full select2-vendor">
                    <option value="">-- Pilih Vendor Angkut --</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->kode_vendor }}" data-nama="{{ $vendor->nama_vendor }}">
                            {{ $vendor->kode_vendor }} / {{ $vendor->nama_vendor }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium">Kode Lambung</label>
                <select name="kode_driver" id="kode_driver" class="form-select w-full" required>
                    <option value="">-- Pilih Kode Lambung --</option>
                    <!-- Options will be populated by JavaScript -->
                </select>
            </div>

            <div>
                <label class="block font-medium">Tarif Zona Angkutan</label>
                <input type="number" name="tarif_zona_angkutan" id="tarif_zona_angkutan" readonly class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block font-medium">Jenis Tebangan</label>
                <input type="text" name="jenis_tebangan" id="jenis_tebangan" readonly class="form-input w-full bg-gray-100">
            </div>
        </div>

        <div class="mt-6">
            <label class="block font-medium">Catatan</label>
            <textarea name="catatan" class="form-textarea w-full" placeholder="Opsional..."></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div>
                <label class="block font-medium">Dibuat Oleh</label>
                <input type="text" name="dibuat_oleh" value="Mandor" readonly class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block font-medium">Diperiksa 1 Oleh</label>
                <input type="text" name="diperiksa_oleh" value="Asst. Divisi Plantation" readonly class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block font-medium">Diperiksa 2 Oleh</label>
                <input type="text" name="disetujui_oleh" value="Asst. Manager Plantation" readonly class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block font-medium">Ditimbang Oleh</label>
                <input type="text" name="ditimbang_oleh" value="Petugas Timbangan" readonly class="form-input w-full bg-gray-100">
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('lkt.index') }}" class="btn btn-secondary ml-2">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk dropdown Kode SPT
    var select2Spt = $('.select2-spt').select2({
        placeholder: 'Cari Kode SPT',
        allowClear: true,
        width: '100%',
        dropdownParent: $('.vendor-container')
    });

    // Inisialisasi Select2 untuk dropdown Vendor Angkut
    $('.select2-vendor').select2({
        placeholder: 'Cari Kode/Nama Vendor Angkut',
        allowClear: true,
        width: '100%',
        dropdownParent: $('.vendor-container'),
        matcher: function(params, data) {
            // If there are no search terms, return all of the data
            if ($.trim(params.term) === '') {
                return data;
            }

            // Check if the data element has text (it should always have it)
            if (typeof data.text === 'undefined') {
                return null;
            }

            // Convert search term to lowercase for case-insensitive search
            var searchTerm = params.term.toLowerCase();
            var text = data.text.toLowerCase();

            // Check if the text contains the search term
            if (text.indexOf(searchTerm) > -1) {
                return data;
            }

            // Also check the data-nama attribute if it exists
            if ($(data.element).data('nama')) {
                var nama = $(data.element).data('nama').toLowerCase();
                if (nama.indexOf(searchTerm) > -1) {
                    return data;
                }
            }

            // Return null if no match is found
            return null;
        },
        templateResult: formatVendor,
        templateSelection: formatVendorSelection
    });

    // Format tampilan hasil pencarian
    function formatVendor(vendor) {
        if (!vendor.id) {
            return vendor.text;
        }

        var $vendor = $(
            '<div class="flex items-center">' +
            '   <div class="font-medium">' + vendor.text.split(' / ')[0] + '</div>' +
            '   <div class="text-gray-500 text-sm ml-2">' + vendor.text.split(' / ')[1] + '</div>' +
            '</div>'
        );
        return $vendor;
    }

    // Format tampilan yang dipilih
    function formatVendorSelection(vendor) {
        return vendor.text;
    }

    // Inisialisasi Select2 untuk Kode Lambung
    var select2Driver = $('#kode_driver').select2({
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
            // Debug log untuk setiap driver
            console.log('Checking driver:', driver, 'kode_vendor:', driver.kode_vendor, 'vs selected:', vendorKode);
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

        // Jika hanya ada 1 driver, pilih otomatis
        if (filteredDrivers.length === 1) {
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

    // Event handler untuk perubahan select2
    select2Spt.on('change', function() {
        const kodeSPT = $(this).val();
        fetchSptData(kodeSPT);
    });

    // Inisialisasi data jika ada nilai yang sudah dipilih
    const initialKodeSPT = $('#kode_spt').val();
    if (initialKodeSPT) {
        fetchSptData(initialKodeSPT);
    }
});
</script>
@endpush
