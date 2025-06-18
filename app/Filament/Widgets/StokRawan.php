<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;

use App\Models\Barang;


class StokRawan extends BaseWidget
{
    protected static ?string $heading = 'Barang Yang Harus Restock';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Barang::query()->where('stok', '<=', 10)
            )
            ->columns([
                TextColumn::make('kode_barang')
                    ->label('Kode Barang'),

                TextColumn::make('nama_barang')
                    ->label('Nama Barang'),

                TextColumn::make('stok')
                    ->label('Stok')
                    ->badge()
                    ->color(fn ($state) => $state <= 10 ? 'danger' : 'primary'),
                TextColumn::make('satuan.nama_satuan')
                    ->label('Satuan')
            ]);
    }
}
