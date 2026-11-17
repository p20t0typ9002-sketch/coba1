<?php

namespace App\Filament\Resources\Pengembalians\Pages;

use App\Filament\Resources\Pengembalians\PengembalianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPengembalians extends ListRecords
{
    protected static string $resource = PengembalianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Pengembalian')
                ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
        ];
    }
}
