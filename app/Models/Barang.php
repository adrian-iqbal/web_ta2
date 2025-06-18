<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $guarded = [];

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }

    public function jenisBarang()
    {
        return $this->belongsTo(JenisBarang::class);
    }

    public function transaksiItems()
    {
        return $this->hasMany(TransaksiItem::class);
    }
}
