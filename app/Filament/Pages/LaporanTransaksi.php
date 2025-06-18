<?php

namespace App\Filament\Pages;

use Filament\Tables;
use Filament\Pages\Page;
use App\Models\Transaksi;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanTransaksi extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string $view = 'filament.pages.laporan-transaksi';
    protected static ?string $title = 'Laporan Transaksi';
    protected static ?string $navigationGroup = 'Transaksi Dan Laporan';
    protected static ?int $navigationSort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(Transaksi::with('transaksiItems.barang')
                ->latest()
                )
            ->columns([
                Tables\Columns\TextColumn::make('no_transaksi')
                    ->label('No Transaksi')
                    ->searchable(),

                Tables\Columns\ViewColumn::make('transaksiItems')
                    ->label('Barang Yang Terjual')
                    ->view('tables.columns.transaksi-barang'),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal Dan Waktu')
                    ->dateTime('d M Y, H:i'),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total Harga')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('to')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('tanggal', '>=', $data['from']))
                            ->when($data['to'], fn($q) => $q->whereDate('tanggal', '<=', $data['to']));
                    }),
            ])
            ->striped()
            ->paginated(true)
            ->actions([]) // No row actions
            ->bulkActions([]); // No bulk actions
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->action('exportPdf')
                ->openUrlInNewTab(), // buka di tab baru
        ];
    }

    public function exportPdf()
    {
        // Ambil query dengan filter aktif
        $transaksis = $query = $this->getFilteredTableQuery();            // ambil query dengan filter aktif
        $this->applySortingToTableQuery($query);             // (opsional) terapkan juga sorting yg sedang aktif
        $transaksis = $query->with('transaksiItems.barang')->get();

        // Load view PDF
        $pdf = Pdf::loadView('pdf.transaksi_report', [
            'transaksis' => $transaksis,
        ])->setPaper('a4', 'landscape');

        // Stream download PDF
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'laporan_transaksi_' . now()->format('Ymd_His') . '.pdf'
        );
    }
}
