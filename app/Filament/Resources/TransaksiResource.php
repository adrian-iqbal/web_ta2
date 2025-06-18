<?php

namespace App\Filament\Resources;

use App\Models\Barang;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\TransaksiResource\Pages;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;


class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Transaksi Dan Laporan';

    protected static ?int $navigationSort = 1;

    public static function getNavigationUrl(): string
    {
        return static::getUrl('create');
    }

    public static function getNavigationLabel(): string
    {
        return 'Transaksi';
    }

    public static function getModelLabel(): string
    {
        return 'Transaksi Baru';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Laporan Transaksi';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Transaksi')
                ->columns(2)
                ->schema([
                    TextInput::make('no_transaksi')
                        ->required(),

                    DateTimePicker::make('tanggal')
                        ->default(now()->setTimezone('Asia/Jakarta')->toDateTimeString())
                        ->required(),
                ]),

            // Grid 2 kolom: Kiri (Detail Barang), Kanan (Total)
            Grid::make(2)
                ->schema([
                    // KIRI: Detail Barang
                    Section::make('Transaksi Barang')
                        ->schema([
                            Repeater::make('transaksiItems')
                                ->createItemButtonLabel('Tambah Barang Lagi')
                                ->label(false)
                                ->relationship()
                                ->default([])
                                ->columns(1)
                                ->schema([
                                    Grid::make(2) // Baris atas: Kode dan Nama
                                        ->schema([
                                            TextInput::make('kode_barang')
                                                ->label('Kode')
                                                ->disabled()
                                                ->reactive()
                                                ->afterStateHydrated(fn(callable $set, callable $get) => optional(\App\Models\Barang::find($get('barang_id')))->kode_barang && $set('kode_barang', \App\Models\Barang::find($get('barang_id'))->kode_barang)),

                                            Select::make('barang_id')
                                                ->label('Nama')
                                                ->searchable()
                                                ->placeholder('Pilih Barang')
                                                ->options(
                                                    \App\Models\Barang::orderBy('nama_barang')->pluck('nama_barang', 'id')->toArray()
                                                )
                                                ->required()
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                    if (!$state) {
                                                        // Jika user menghapus pilihan barang
                                                        $set('kode_barang', null);
                                                        $set('harga', null);
                                                        $set('subtotal', null);
                                                        $set('quantity', null);
                                                        return;
                                                    }

                                                    $barang = \App\Models\Barang::find($state);
                                                    if ($barang) {
                                                        $set('kode_barang', $barang->kode_barang);
                                                        $set('harga', $barang->harga_jual);
                                                        $set('subtotal', $barang->harga_jual * ($get('quantity') ?? 1));
                                                    }
                                                })

                                        ]),

                                    Grid::make(4) // Gambar | Jumlah | Harga | Subtotal
                                        ->schema([
                                            Placeholder::make('gambar')
                                                ->label('Gambar')
                                                ->content(function (callable $get) {
                                                    $barang_id = $get('barang_id');
                                                    $barang = \App\Models\Barang::find($barang_id);
                                                    if (!$barang || !$barang->gambar) return 'Tidak ada gambar';
                                                    $url = asset('storage/' . $barang->gambar);
                                                    return new \Illuminate\Support\HtmlString("
                            <div style='
                                width: 100px;
                                height: 100px;
                                border: 1px solid #ccc;
                                border-radius: 6px;
                                overflow: hidden;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            '>
                                <img src='{$url}' style='max-width: 100%; max-height: 100%; object-fit: contain;' />
                            </div>
                        ");
                                                }),

                                            TextInput::make('quantity')
                                                ->label('Jumlah')
                                                ->numeric()
                                                ->default(0)
                                                ->reactive()
                                                ->afterStateUpdated(fn(callable $get, callable $set) => self::updateFormData($get, $set))
                                                ->rules(fn(callable $get) => optional(\App\Models\Barang::find($get('barang_id')))->stok ? ['max:' . \App\Models\Barang::find($get('barang_id'))->stok] : [])
                                                ->helperText(fn(callable $get) => optional(\App\Models\Barang::find($get('barang_id')))->stok ? "Stok tersedia: " . \App\Models\Barang::find($get('barang_id'))->stok : null),

                                            TextInput::make('harga')
                                                ->label('Harga')
                                                ->numeric()
                                                ->readOnly()
                                                ->reactive()
                                                ->prefix('Rp')
                                                ->formatStateUsing(fn($state) => $state !== null ? 'Rp.' . number_format($state, 0, ',', '.') : null)
                                                ->dehydrateStateUsing(fn($state) => (int) preg_replace('/[^0-9]/', '', $state))
                                                ->afterStateUpdated(fn(callable $get, callable $set) => self::updateFormData($get, $set)),

                                            TextInput::make('subtotal')
                                                ->label('Subtotal')
                                                ->numeric()
                                                ->readOnly()
                                                ->default(0)
                                                ->prefix('Rp')
                                                ->formatStateUsing(fn($state) => $state !== null ? 'Rp.' . number_format($state, 0, ',', '.') : null)
                                                ->dehydrateStateUsing(fn($state) => (int) preg_replace('/[^0-9]/', '', $state))
                                                ->afterStateUpdated(fn(callable $get, callable $set) => self::updateFormData($get, $set)),
                                        ]),
                                ])
                                ->live()
                        ]),

                    // KANAN: Total & Barang Terpilih
                    Section::make(' ')
                        ->aside() // agar tampil seperti sidebar
                        ->schema([
                            Placeholder::make('Daftar Barang Dipilih')
                                ->label('Rangkuman Belanja')
                                ->content(function (callable $get) {
                                    $items = $get('transaksiItems');
                                    if (!$items || !is_array($items) || empty($items)) {
                                        return 'Belum ada barang dipilih.';
                                    }

                                    $html = '<table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th style="border: 1px solid #ccc; padding: 6px;">Barang</th>
                                            <th style="border: 1px solid #ccc; padding: 6px;">Jumlah</th>
                                            <th style="border: 1px solid #ccc; padding: 6px;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

                                    $total = 0;

                                    foreach ($items as $item) {
                                        if (!isset($item['barang_id'])) continue;
                                        $barang = \App\Models\Barang::find($item['barang_id']);
                                        if (!$barang) continue;

                                        $jumlah = $item['quantity'] ?? 0;
                                        $subtotal = $item['subtotal'] ?? 0;
                                        $total += $subtotal;

                                        $html .= '<tr>  
                                        <td style="border: 1px solid #ccc; padding: 6px;">' . e($barang->nama_barang) . '</td>
                                        <td style="border: 1px solid #ccc; padding: 6px; text-align: center;">' . $jumlah . '</td>
                                        <td style="border: 1px solid #ccc; padding: 6px; text-align: right;">Rp' . number_format($subtotal, 0, ',', '.') . '</td>
                                    </tr>';
                                    }

                                    $html .= '</tbody></table>';

                                    return new \Illuminate\Support\HtmlString($html);
                                }),

                            TextInput::make('total')
                                ->label('Total Harga')
                                ->prefix('Rp')
                                ->readOnly()
                                ->dehydrated(true)
                                ->dehydrateStateUsing(fn($state) => (int) preg_replace('/[^0-9]/', '', $state))
                                ->formatStateUsing(fn($state) => number_format((int) $state, 0, ',', '.'))
                                ->default(0),

                            TextInput::make('bayar')
                                ->label('Dibayar')
                                ->prefix('Rp')
                                ->numeric()
                                ->required()
                                ->reactive()
                                ->rules(function (callable $get) {
                                    $total = (int) $get('total') ?? 0;
                                    return ['gte:' . $total]; // gte = greater than or equal
                                })
                                ->dehydrateStateUsing(fn($state) => (int) preg_replace('/[^0-9]/', '', $state))
                                ->formatStateUsing(fn($state) => number_format((int) $state, 0, ',', '.'))
                                ->afterStateUpdated(function (callable $get, callable $set) {
                                    $bayar = (int) $get('bayar');
                                    $total = (int) $get('total');
                                    $set('kembalian', max($bayar - $total, 0));
                                }),


                            TextInput::make('kembalian')
                                ->label('Kembalian')
                                ->prefix('Rp')
                                ->readOnly()
                                ->default(0)
                                ->dehydrated(true) // ⬅️ GANTI dari false ke true
                                ->dehydrateStateUsing(fn($state) => (int) preg_replace('/[^0-9]/', '', $state))
                                ->formatStateUsing(fn($state) => number_format((int) $state, 0, ',', '.')),
                        ]),
                ]),
        ]);
    }


    public static function getRelations(): array
    {
        return [];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksis::route('/'),
            'create' => Pages\CreateTransaksi::route('/create'),
        ];
    }
    // ini buat logika kalkulator automatis
    public static function updateFormData($get, $set)
    {
        // Ambil semua data dari parent form
        $formData = $get('../../');

        // Pastikan nama key sama dengan nama Repeater-nya
        $items = $formData['transaksiItems'] ?? [];

        $total = 0;

        // Hitung total dari semua item di repeater
        foreach ($items as $item) {
            $harga = floatval($item['harga'] ?? 0);
            $quantity = floatval($item['quantity'] ?? 0);
            $total += $harga * $quantity;
        }

        // Hitung ulang subtotal dari current item (jaga-jaga ada perubahan harga/quantity)
        $harga = floatval($get('harga') ?? 0);
        $quantity = floatval($get('quantity') ?? 0);
        $subtotal = $harga * $quantity;

        // Set nilai subtotal ke input
        $set('subtotal', $subtotal);

        // Set nilai total ke form utama
        $set('../../total', $total);
    }
}
