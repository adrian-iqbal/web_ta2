<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\JenisBarang;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JenisBarangResource\Pages;
use App\Filament\Resources\JenisBarangResource\RelationManagers;
use Filament\Forms\Components\Section as FormSection;

class JenisBarangResource extends Resource
{
    protected static ?string $model = JenisBarang::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Manajemen Barang';


    public static function getNavigationLabel(): string
    {
        return 'Jenis';
    }

    public static function getModelLabel(): string
    {
        return 'Jenis Barang';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Jenis Barang';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormSection::make('Data Jenis Barang')
                    ->schema([
                        TextInput::make('nama_jenis')
                            ->label('Nama Jenis')
                            ->unique()
                            ->required()
                            ->placeholder('Contoh: Pewarna Tembok'),
                    ])
                    ->columns(2)
                    ->extraAttributes(['class' => 'rounded border shadow']),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->query(
                JenisBarang::query()->latest()
            )
            ->columns([
                TextColumn::make('nama_jenis')
                    ->label('Jenis Barang')
                    ->alignCenter()
                    ->searchable(),
            ])
            ->recordUrl(null)
            ->striped()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->color('primary'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-trash')
                        ->modalHeading('Hapus Jenis')
                        ->modalDescription('Kamu yakin menghapus jenis ini ?')
                        ->modalSubmitActionLabel('Yes')
                        ->modalCloseButton(false)
                ])
                    ->button()
                    ->label('Aksi')
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJenisBarangs::route('/'),
            'create' => Pages\CreateJenisBarang::route('/create'),
            'edit' => Pages\EditJenisBarang::route('/{record}/edit'),
        ];
    }
}
