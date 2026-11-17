<?php

namespace App\Filament\Resources\Alats;

use App\Filament\Resources\Alats\Pages\CreateAlat;
use App\Filament\Resources\Alats\Pages\EditAlat;
use App\Filament\Resources\Alats\Pages\ListAlats;
use App\Filament\Resources\Alats\Schemas\AlatForm;
use App\Filament\Resources\Alats\Tables\AlatsTable;
use App\Models\Alat;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AlatResource extends Resource
{
    protected static ?string $model = Alat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Data Alat';

    protected static ?string $modelLabel = 'Alat';

    protected static ?string $pluralModelLabel = 'Data Alat';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isPetugas();
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isPetugas();
    }

    public static function canDelete(Model $record): bool
    {
        return (auth()->user()?->isAdmin() ?? false)
            && ! $record->detailPeminjamans()->exists();
    }

    public static function form(Schema $schema): Schema
    {
        return AlatForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlatsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlats::route('/'),
            'create' => CreateAlat::route('/create'),
            'edit' => EditAlat::route('/{record}/edit'),
        ];
    }
}