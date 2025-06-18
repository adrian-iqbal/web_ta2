<?php

namespace App\Filament\Resources\JenisBarangResource\Pages;

use App\Filament\Resources\JenisBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateJenisBarang extends CreateRecord
{
    protected static string $resource = JenisBarangResource::class;

    protected static ?string $title = 'Menambah Jenis Barang';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

        protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Ditambahkan')
            ->body('Jenis Barang Berhasil Ditambah');
    }
    
}
