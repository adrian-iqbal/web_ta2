<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Filament\Resources\TransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions\Action;


class ListTransaksis extends ListRecords
{
    protected static string $resource = TransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->label('Transaksi Baru'),
        ];
    }
}
