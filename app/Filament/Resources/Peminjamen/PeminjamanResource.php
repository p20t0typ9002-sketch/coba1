<?php

namespace App\Filament\Resources\Peminjamen;

use App\Filament\Resources\Peminjamen\Pages\CreatePeminjaman;
use App\Filament\Resources\Peminjamen\Pages\EditPeminjaman;
use App\Filament\Resources\Peminjamen\Pages\ListPeminjamen;
use App\Filament\Resources\Peminjamen\Schemas\PeminjamanForm;
use App\Filament\Resources\Peminjamen\Tables\PeminjamenTable;
use App\Models\Peminjaman;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Data Peminjaman';

    protected static ?string $modelLabel = 'Peminjaman';

    protected static ?string $pluralModelLabel = 'Data Peminjaman';

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->isPetugas();
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    
    public static function canEdit($record): bool
    {
        return (auth()->user()?->isAdmin() ?? false)
            && in_array($record->status, ['menunggu', 'ditolak'], true);
    }

    public static function canDelete($record): bool
    {
        return (auth()->user()?->isAdmin() ?? false)
            && in_array($record->status, ['menunggu', 'ditolak'], true)
            && ! $record->pengembalians()->exists();
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }


    public static function form(Schema $schema): Schema
    {
        return PeminjamanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PeminjamenTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPeminjamen::route('/'),
            'create' => CreatePeminjaman::route('/create'),
            'edit' => EditPeminjaman::route('/{record}/edit'),
        ];
    }
}
