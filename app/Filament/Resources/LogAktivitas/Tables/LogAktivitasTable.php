<?php

namespace App\Filament\Resources\LogAktivitas\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LogAktivitasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn ($query) => $query
                    ->with('user')
                    ->latest()
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->placeholder('Pengguna tidak ditemukan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('aktivitas')
                    ->label('Jenis Aktivitas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'login' => 'success',
                        'logout' => 'gray',
                        'data_dibuat' => 'info',
                        'data_diubah' => 'warning',
                        'data_dihapus' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->wrap()
                    ->searchable()
                    ->limit(80),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(),

                TextColumn::make('user_agent')
                    ->label('Browser / Perangkat')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->user_agent)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('aktivitas')
                    ->label('Jenis Aktivitas')
                    ->options([
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'data_dibuat' => 'Data Dibuat',
                        'data_diubah' => 'Data Diubah',
                        'data_dihapus' => 'Data Dihapus',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}
