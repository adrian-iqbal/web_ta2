<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use App\Models\Satuan;
use App\Models\Transaksi;
use App\Models\JenisBarang;
use App\Models\TransaksiItem;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{

    
    protected function getStats(): array
    {
        $countBarang = Barang::count();
        $countJenis = JenisBarang::count();
        $countSatuan = Satuan::count();
        $countTransaksi = Transaksi::count();
        $totalTerjual = TransaksiItem::sum('quantity');
        $user = Filament::auth()->user();

        return [
            Stat::make('Total Barang', $countBarang)
                ->description('Total Barang')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success')
                ->descriptionIcon('heroicon-m-cube'),

            Stat::make('Total Jenis', $countJenis)
                ->description('Total Jenis Barang')
                ->chart([3, 5, 4, 6, 5, 7, 6])
                ->color('info')
                ->descriptionIcon('heroicon-m-rectangle-stack'),

            Stat::make('Total Satuan', $countSatuan)
                ->description('Total Satuan Barang')
                ->chart([2, 4, 3, 2, 5, 3, 6])
                ->color('warning')
                ->descriptionIcon('heroicon-m-tag'),

            Stat::make('Total Transaksi', $countTransaksi)
                ->description('Total Transaksi')
                ->chart([1, 3, 2, 4, 3, 6, 5])
                ->color('primary')
                ->descriptionIcon('heroicon-m-receipt-percent'),

            Stat::make('Barang Terjual', $totalTerjual)
                ->description('Total Barang Terjual')
                ->chart([2, 6, 4, 8, 6, 10, 7])
                ->color('danger')
                ->descriptionIcon('heroicon-m-shopping-cart'),

            Stat::make('Pengguna', $user->name)
                ->description($user->email)
                ->chart([1, 1, 1, 1, 1, 1, 1]) // Bisa juga dummy data
                ->color('gray')
                ->descriptionIcon('heroicon-m-user'),
        ];
    }
}
