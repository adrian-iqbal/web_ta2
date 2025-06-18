<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class CreateTransaksi extends CreateRecord
{
    protected static string $resource = TransaksiResource::class;

    protected static ?string $title = 'Halaman Transaksi';

    protected function getRedirectUrl(): string
    {
        return static::getUrl(); // Akan redirect ke halaman create lagi
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        foreach ($record->transaksiItems as $item) {
            $barang = $item->barang;
            if ($barang && $barang->stok >= $item->quantity) {
                $barang->decrement('stok', $item->quantity);
            } else {
                $this->notify('danger', "Stok barang {$barang->nama_barang} tidak cukup!");
                // Bisa juga throw exception supaya transaksi batal
            }
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Terjual')
            ->body('Barang Berhasil Terjual');
    }
}
