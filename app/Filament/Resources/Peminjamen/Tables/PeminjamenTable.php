<?php

namespace App\Filament\Resources\Peminjamen\Tables;

use App\Models\Peminjaman;
use App\Services\PeminjamanService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PeminjamenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn ($query) => $query
                    ->with(['user', 'details.alat'])
                    ->withCount('details')
                    ->latest()
            )
            ->columns([
                TextColumn::make('pinjam_code')
                    ->label('Kode Peminjaman')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nama Peminjam')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_pinjam')
                    ->label('Tanggal Pinjam')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('tanggal_rencana_kembali')
                    ->label('Rencana Kembali')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('details_count')
                    ->label('Jenis Alat')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'dipinjam' => 'info',
                        'ditolak' => 'danger',
                        'selesai' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Peminjaman')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'dipinjam' => 'Dipinjam',
                        'ditolak' => 'Ditolak',
                        'selesai' => 'Selesai',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Peminjaman')
                    ->modalDescription('Stok alat akan dikurangi setelah peminjaman disetujui.')
                    ->visible(fn (Peminjaman $record): bool =>
                        (auth()->user()?->isPetugas() ?? false)
                        && $record->status === 'menunggu'
                    )
                    ->action(function (Peminjaman $record): void {
                        app(PeminjamanService::class)->approve(
                            $record,
                            auth()->user()
                        );

                        Notification::make()
                            ->success()
                            ->title('Peminjaman disetujui')
                            ->body('Stok alat telah diperbarui.')
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Peminjaman')
                    ->schema([
                        Textarea::make('alasan')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->visible(fn (Peminjaman $record): bool =>
                        (auth()->user()?->isPetugas() ?? false)
                        && $record->status === 'menunggu'
                    )
                    ->action(function (Peminjaman $record, array $data): void {
                        app(PeminjamanService::class)->reject(
                            $record,
                            auth()->user(),
                            $data['alasan']
                        );

                        Notification::make()
                            ->success()
                            ->title('Peminjaman ditolak')
                            ->body('Penolakan berhasil dicatat.')
                            ->send();
                    }),

                EditAction::make()
                    ->visible(fn (Peminjaman $record): bool =>
                        (auth()->user()?->isAdmin() ?? false)
                        && in_array($record->status, ['menunggu', 'ditolak'], true)
                    ),

                DeleteAction::make()
                    ->visible(fn (Peminjaman $record): bool =>
                        (auth()->user()?->isAdmin() ?? false)
                        && in_array($record->status, ['menunggu', 'ditolak'], true)
                        && ! $record->pengembalians()->exists()
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}
