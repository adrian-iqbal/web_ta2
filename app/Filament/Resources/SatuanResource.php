<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Satuan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SatuanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SatuanResource\RelationManagers;

class SatuanResource extends Resource
{
    protected static ?string $model = Satuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Manajemen Barang';

    public static function getNavigationLabel(): string
    {
        return 'Satuan';
    }

    public static function getModelLabel(): string
    {
        return 'Satuan Barang';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Satuan Barang';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Satuan')
                    ->schema([
                        TextInput::make('nama_satuan')
                            ->placeholder('Contoh: Kilogram')
                            ->unique()
                            ->required(),
                    ])
                    ->columns(2)
                    ->extraAttributes(['class' => 'rounded border shadow']),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Satuan::query()->latest()
            )
            ->columns([
                TextColumn::make('nama_satuan')
                    ->label('Satuan Barang')
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
                        ->modalHeading('Hapus Satuan')
                        ->modalDescription('Kamu yakin menghapus satuan ini ?')
                        ->modalSubmitActionLabel('Yes')
                        ->modalCloseButton(false),

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
            'index' => Pages\ListSatuans::route('/'),
            'create' => Pages\CreateSatuan::route('/create'),
            'edit' => Pages\EditSatuan::route('/{record}/edit'),
        ];
    }
}
