<?php

namespace App\Filament\Resources\JenisBarangResource\Pages;

use App\Filament\Resources\JenisBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisBarang extends EditRecord
{
    protected static string $resource = JenisBarangResource::class;

    protected static ?string $title = 'Mengubah Jenis Barang';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
