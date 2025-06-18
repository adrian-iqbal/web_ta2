<?php

namespace App\Filament\Resources\SatuanResource\Pages;

use App\Filament\Resources\SatuanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateSatuan extends CreateRecord
{
    protected static string $resource = SatuanResource::class;

    protected static ?string $title = 'Menambah Satuan Barang';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Ditambahkan')
            ->body('Satuan Barang Berhasil Ditambah');
    }
}
