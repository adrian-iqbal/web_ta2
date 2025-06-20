<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    protected $guarded = [];

    public function transaksiItems(): HasMany
    {
        return $this->hasMany(TransaksiItem::class);
    }
}
