<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisBarang extends Model
{
    protected $guarded = [];

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}
