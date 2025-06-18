<?php

namespace App\Filament\Resources\JenisBarangResource\Pages;

use App\Filament\Resources\JenisBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenisBarangs extends ListRecords
{
    protected static string $resource = JenisBarangResource::class;

    protected static ?string $title = 'Kelola Jenis Barang';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->icon('heroicon-m-plus')
            ->label('Tambah Jenis')
        ];
    }
}
