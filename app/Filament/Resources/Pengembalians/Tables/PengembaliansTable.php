<?php

namespace App\Filament\Resources\Pengembalians\Tables;

use App\Models\Pengembalian;
use App\Services\PengembalianService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PengembaliansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn ($query) => $query
                    ->with([
                        'peminjaman.user',
                        'petugasPenerima',
                    ])
                    ->withCount('details')
                    ->latest()
            )
            ->columns([
                TextColumn::make('peminjaman.pinjam_code')
                    ->label('Kode Peminjaman')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('peminjaman.user.name')
                    ->label('Nama Peminjam')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_kembali')
                    ->label('Tanggal Pengembalian')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('details_count')
                    ->label('Jumlah Detail')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'diajukan' => 'warning',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('petugasPenerima.name')
                    ->label('Diterima Oleh')
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pengembalian')
                    ->options([
                        'diajukan' => 'Diajukan',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                Action::make('accept')
                    ->label('Terima')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Terima Pengembalian')
                    ->modalDescription(
                        'Pengembalian akan diterima. Stok hanya bertambah untuk alat dengan kondisi baik.'
                    )
                    ->visible(
                        fn (Pengembalian $record): bool =>
                            (auth()->user()?->isPetugas() ?? false)
                            && $record->status === 'diajukan'
                    )
                    ->action(function (Pengembalian $record): void {
                        app(PengembalianService::class)->accept(
                            $record,
                            auth()->user()
                        );

                        Notification::make()
                            ->success()
                            ->title('Pengembalian diterima')
                            ->body('Data pengembalian dan stok alat berhasil diperbarui.')
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pengembalian')
                    ->schema([
                        Textarea::make('alasan')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->visible(
                        fn (Pengembalian $record): bool =>
                            (auth()->user()?->isPetugas() ?? false)
                            && $record->status === 'diajukan'
                    )
                    ->action(function (
                        Pengembalian $record,
                        array $data
                    ): void {
                        app(PengembalianService::class)->reject(
                            $record,
                            auth()->user(),
                            $data['alasan']
                        );

                        Notification::make()
                            ->success()
                            ->title('Pengembalian ditolak')
                            ->body('Alasan penolakan berhasil dicatat.')
                            ->send();
                    }),

                EditAction::make()
                    ->visible(
                        fn (Pengembalian $record): bool =>
                            (auth()->user()?->isAdmin() ?? false)
                            && in_array(
                                $record->status,
                                ['diajukan', 'ditolak'],
                                true
                            )
                    ),

                DeleteAction::make()
                    ->visible(
                        fn (Pengembalian $record): bool =>
                            (auth()->user()?->isAdmin() ?? false)
                            && in_array(
                                $record->status,
                                ['diajukan', 'ditolak'],
                                true
                            )
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}
