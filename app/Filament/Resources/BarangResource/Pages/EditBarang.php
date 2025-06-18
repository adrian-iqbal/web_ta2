<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;


class EditBarang extends EditRecord
{
    protected static string $resource = BarangResource::class;

    protected static ?string $title = 'Mengubah Data Barang';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
