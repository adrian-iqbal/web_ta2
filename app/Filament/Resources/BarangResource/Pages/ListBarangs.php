<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab as ListRecordsTab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\JenisBarang;


class ListBarangs extends ListRecords
{
    protected static string $resource = BarangResource::class;

    public function getTabs(): array
    {
        $tabs = [
            'all' => ListRecordsTab::make('Semua'),
        ];

        $jenisBarangs = JenisBarang::orderBy('nama_jenis')->get();

        foreach ($jenisBarangs as $jenis) {
            $tabs[$jenis->nama_jenis] = ListRecordsTab::make($jenis->nama_jenis)
                ->modifyQueryUsing(fn(Builder $query) => $query->where('jenis_barang_id', $jenis->id));
        }

        return $tabs;
    }

    protected static ?string $title = 'Kelola Data Barang';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->label('Tambah Barang')
        ];
    }
}
