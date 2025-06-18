<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;


class CreateBarang extends CreateRecord
{
    protected static string $resource = BarangResource::class;

    protected static ?string $title = 'Menambah Data Barang';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


        protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Ditambahkan')
            ->body('Barang Berhasil Ditambah');
    }
}
