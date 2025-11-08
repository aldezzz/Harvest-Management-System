<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor;

class Vehicle extends Model
{
    protected $table = 'vehicle';

    protected $fillable = [
        'kode_lambung',
        'plat_nomor',
        'id_jenis_unit',
        'kode_vendor',
        'nama_vendor'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'kode_vendor', 'kode_vendor');
    }

    // Keep the old relationship for backward compatibility
    public function vendorAngkut()
    {
        return $this->belongsTo(Vendor::class, 'kode_vendor', 'kode_vendor');
    }

    public function jenisUnit()
    {
        return $this->belongsTo(JenisUnit::class, 'id_jenis_unit');
    }

}
