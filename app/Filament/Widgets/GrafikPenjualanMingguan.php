<?php

namespace App\Filament\Widgets;

use App\Models\TransaksiItem;
use App\Models\Transaksi;

use Filament\Widgets\BarChartWidget;

use Carbon\Carbon;


class GrafikPenjualanMingguan extends BarChartWidget
{
    protected static ?string $heading = 'Grafik Penjualan & Pendapatan';

    // Menambahkan filter untuk memilih mode grafik
    protected function getFilters(): ?array
    {
        return [
            'penjualan_harian' => 'Penjualan Harian',
            'pendapatan_harian' => 'Pendapatan Harian',
            'pendapatan_mingguan' => 'Pendapatan Mingguan',
            'pendapatan_bulanan' => 'Pendapatan Bulanan',
        ];
    }

    protected function getData(): array
    {
        // Default filter 'penjualan_harian'
        $filter = $this->filter ?? 'penjualan_harian';

        $data = collect();
        $labels = collect();

        if ($filter === 'pendapatan_mingguan') {
            // 7 minggu terakhir
            for ($i = 6; $i >= 0; $i--) {
                $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
                $endOfWeek = $startOfWeek->copy()->endOfWeek();

                $total = Transaksi::whereBetween('tanggal', [$startOfWeek, $endOfWeek])
                    ->sum('total');

                $labels->push('Minggu ' . $startOfWeek->format('W'));
                $data->push($total);
            }
        } elseif ($filter === 'pendapatan_bulanan') {
            // 12 bulan terakhir
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);

                $total = Transaksi::whereYear('tanggal', $month->year)
                    ->whereMonth('tanggal', $month->month)
                    ->sum('total');

                $labels->push($month->format('M Y'));
                $data->push($total);
            }
        } elseif ($filter === 'pendapatan_harian') {
            // 7 hari terakhir pendapatan
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);

                $total = Transaksi::whereDate('tanggal', $date)
                    ->sum('total');

                $labels->push($date->format('d M'));
                $data->push($total);
            }
        } else {
            // Default: penjualan harian (jumlah barang terjual)
            for ($i = 6; $i >= 0; $i--) {
                $tanggal = Carbon::today()->subDays($i)->toDateString();

                $jumlahTerjual = TransaksiItem::whereDate('created_at', $tanggal)
                    ->sum('quantity');

                $labels->push(Carbon::parse($tanggal)->format('d M'));
                $data->push($jumlahTerjual);
            }
        }

        // Warna chart disesuaikan berdasarkan filter
        $colorMap = [
            'penjualan_harian' => '#3b82f6',      // biru
            'pendapatan_harian' => '#10b981',     // hijau
            'pendapatan_mingguan' => '#6366f1',   // ungu
            'pendapatan_bulanan' => '#f59e0b',    // kuning/orange
        ];
        $color = $colorMap[$filter] ?? '#3b82f6';

        // Label dataset disesuaikan
        $labelMap = [
            'penjualan_harian' => 'Barang Terjual',
            'pendapatan_harian' => 'Pendapatan Harian',
            'pendapatan_mingguan' => 'Pendapatan Mingguan',
            'pendapatan_bulanan' => 'Pendapatan Bulanan',
        ];
        $label = $labelMap[$filter] ?? 'Barang Terjual';

        return [
            'datasets' => [
                [
                    'label' => $label,
                    'data' => $data,
                    'borderWidth' => 0,
                    'backgroundColor' => $color,
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bisa diganti 'line' jika mau
    }

    protected static ?int $sort = 2; // posisi di dashboard
}
