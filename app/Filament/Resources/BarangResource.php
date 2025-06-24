<?php

namespace App\Filament\Resources;

use App\Models\Barang;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

use Filament\Forms\Components\Grid as FormGrid;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions;
use Filament\Tables\Actions\BulkActionGroup;

use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

use App\Filament\Resources\BarangResource\Pages;
use App\Models\JenisBarang;
use App\Models\Satuan;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationGroup = 'Manajemen Barang';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getNavigationLabel(): string
    {
        return 'Data Barang';
    }

    public static function getModelLabel(): string
    {
        return 'Barang';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Barang';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Grid 2 Kolom: Membungkus 2 FormSection
                FormGrid::make(2)->schema([

                    // Kolom Kiri
                    FormSection::make('Detail Barang')
                        ->columnSpan(1)
                        ->extraAttributes(['class' => 'rounded border shadow'])
                        ->schema([
                            TextInput::make('kode_barang')
                                ->label('Kode Barang')
                                ->placeholder('Masukkan kode barang')
                                ->numeric()
                                ->dehydrated() // Supaya tetap tersimpan ke database
                                ->required(),

                            TextInput::make('nama_barang')
                                ->label('Nama Barang')
                                ->required()
                                ->placeholder('Masukkan nama barang'),

                            Select::make('satuan_id')
                                ->label('Satuan')
                                ->searchable()
                                ->reactive()
                                ->options(
                                    Satuan::orderBy('nama_satuan', 'asc') // <-- Ini bagian penting
                                        ->pluck('nama_satuan', 'id')
                                        ->toArray()
                                )
                                ->required()
                                ->placeholder('Pilih satuan'),

                            Select::make('jenis_barang_id')
                                ->label('Jenis Barang')
                                ->searchable()
                                ->reactive()
                                ->options(
                                    JenisBarang::orderBy('nama_jenis', 'asc') // <-- Ini bagian penting
                                        ->pluck('nama_jenis', 'id')
                                        ->toArray()
                                )
                                ->required()
                                ->placeholder('Pilih jenis barang'),
                        ]),

                    // Kolom Kanan
                    FormSection::make('Detail Harga')
                        ->columnSpan(1) 
                        ->extraAttributes(['class' => 'rounded border shadow'])
                        ->schema([
                            TextInput::make('harga_beli')
                                ->label('Harga Beli')
                                ->numeric()
                                ->prefix('Rp. ')
                                ->required()
                                ->placeholder('Masukkan harga beli'),

                            TextInput::make('harga_jual')
                                ->label('Harga Jual')
                                ->numeric()
                                ->prefix('Rp. ')
                                ->required()
                                ->placeholder('Masukkan harga jual'),

                            TextInput::make('stok')
                                ->label('Stok')
                                ->numeric()
                                ->required()
                                ->placeholder('Masukkan stok barang'),
                        ]),
                ]),

                // FormSection untuk upload gambar
                FormSection::make('Gambar Barang')
                    ->extraAttributes(['class' => 'rounded border shadow'])
                    ->schema([
                        FileUpload::make('gambar')
                            ->label('Upload Gambar')
                            ->image()
                            ->imagePreviewHeight('200')
                            ->directory('gambar-barang')
                            ->maxSize(300)
                            ->downloadable()
                            ->openable()
                            ->hint('Ukuran maksimal 300KB')
                            ->columnSpanFull()
                            ->disk('gambar-barang') // <-- Pastikan ini sesuai dengan disk yang kamu buat di config/filesystems.php
                            ->visibility('public')
                            ->image()
                    ]),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table

            ->query(
                Barang::query()->latest()
            )
            ->columns([
                ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->disk('gambar-barang')
                    ->height(60)
                    ->width(60),
                TextColumn::make('kode_barang')->label('Kode')->searchable(),
                TextColumn::make('nama_barang')->label('Nama Barang')->searchable(),
                TextColumn::make('stok')
                    ->label('Stok')
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state === 0 => 'danger',       // merah
                        $state <= 10 => 'warning',      // kuning
                        default => 'success',           // hijau
                    }),
                TextColumn::make('harga_jual')
                    ->label('Harga Jual')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('harga_beli')
                    ->label('Harga Beli')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('satuan.nama_satuan')->label('Satuan'),
                TextColumn::make('jenisBarang.nama_jenis')->label('Jenis'),
            ])
            ->recordUrl(null)
            ->striped()
            ->actions([
                Actions\ActionGroup::make([
                    Actions\ViewAction::make()
                        ->modalHeading(false)
                        ->modalWidth('3xl')
                        ->slideOver()
                        ->modalCancelAction(false)
                        ->infolist([
                            InfoSection::make('Detail Barang')
                                ->extraAttributes(['class' => 'rounded border shadow'])
                                ->schema([
                                    InfoGrid::make(2)->schema([
                                        Group::make([
                                            TextEntry::make('kode_barang')
                                                ->label(false)
                                                ->prefix('Kode Barang : '),
                                            TextEntry::make('nama_barang')
                                                ->label(false)
                                                ->prefix('Nama Barang : '),
                                            TextEntry::make('jenisBarang.nama_jenis')
                                                ->label(false)
                                                ->prefix('Jenis Barang : '),
                                            TextEntry::make('satuan.nama_satuan')
                                                ->label(false)
                                                ->prefix('Satuan Barang : '),
                                            TextEntry::make('stok')
                                                ->label(false)
                                                ->prefix('Stok : '),
                                            TextEntry::make('harga_jual')
                                                ->label(false)
                                                ->prefix('Harga Jual : ')
                                                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                                            TextEntry::make('harga_beli')
                                                ->label(false)
                                                ->prefix('Harga Beli : ')
                                                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                                        ]),
                                        Group::make([
                                            ImageEntry::make('gambar')
                                                ->label('Gambar Barang')
                                                ->disk('gambar-barang')
                                                ->width(300)
                                                ->height(300)
                                                ->alignment('center')
                                        ]),
                                    ]),
                                ]),
                        ]),
                    Actions\EditAction::make()
                        ->color('primary'),
                    Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-trash')
                        ->modalHeading('Hapus Barang')
                        ->modalDescription('Kamu yakin menghapus barang ini ?')
                        ->modalSubmitActionLabel('Yes')
                        ->modalCloseButton(false),
                ])->button()->label('Aksi'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}
