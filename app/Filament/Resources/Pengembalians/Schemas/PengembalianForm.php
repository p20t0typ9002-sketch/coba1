<?php

namespace App\Filament\Resources\Pengembalians\Schemas;

use App\Models\DetailPeminjaman;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PengembalianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pengembalian')
                ->schema([
                    Select::make('peminjaman_id')
                        ->label('Kode Peminjaman')
                        ->relationship('peminjaman', 'pinjam_code')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(
                            fn (Set $set) => $set('details', [])
                        )
                        ->required(),

                    DatePicker::make('tanggal_kembali')
                        ->label('Tanggal Pengembalian')
                        ->required(),

                    Textarea::make('catatan')
                        ->label('Catatan')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Detail Alat yang Dikembalikan')
                ->schema([
                    Repeater::make('details')
                        ->label('Daftar Alat')
                        ->relationship()
                        ->schema([
                            Select::make('detail_pinjam_id')
                                ->label('Detail Peminjaman')
                                ->options(function (Get $get): array {
                                    $peminjamanId = $get('../../peminjaman_id');

                                    if (! $peminjamanId) {
                                        return [];
                                    }

                                    return DetailPeminjaman::query()
                                        ->with('alat')
                                        ->where('pinjam_id', $peminjamanId)
                                        ->get()
                                        ->mapWithKeys(function ($detail) {
                                            $jumlahSudahKembali = $detail
                                                ->detailPengembalians()
                                                ->whereHas(
                                                    'pengembalian',
                                                    fn ($query) => $query
                                                        ->where('status', 'diterima')
                                                )
                                                ->sum('jumlah_kembali');

                                            $sisa = $detail->jumlah
                                                - $jumlahSudahKembali;

                                            $namaAlat = $detail->alat?->name
                                                ?? 'Alat tidak ditemukan';

                                            return [
                                                $detail->id => "{$namaAlat} | Dipinjam: {$detail->jumlah} | Sisa: {$sisa}",
                                            ];
                                        })
                                        ->all();
                                })
                                ->searchable()
                                ->live()
                                ->required(),

                            TextInput::make('jumlah_kembali')
                                ->label('Jumlah Dikembalikan')
                                ->numeric()
                                ->integer()
                                ->minValue(1)
                                ->maxValue(function (Get $get): ?int {
                                    $detailPinjamId = $get('detail_pinjam_id');

                                    if (! $detailPinjamId) {
                                        return null;
                                    }

                                    $detail = DetailPeminjaman::find(
                                        $detailPinjamId
                                    );

                                    if (! $detail) {
                                        return null;
                                    }

                                    $jumlahSudahKembali = $detail
                                        ->detailPengembalians()
                                        ->whereHas(
                                            'pengembalian',
                                            fn ($query) => $query
                                                ->where('status', 'diterima')
                                        )
                                        ->sum('jumlah_kembali');

                                    return max(
                                        0,
                                        $detail->jumlah - $jumlahSudahKembali
                                    );
                                })
                                ->required(),

                            Select::make('kondisi_alat')
                                ->label('Kondisi Alat')
                                ->options([
                                    'baik' => 'Baik',
                                    'rusak_ringan' => 'Rusak Ringan',
                                    'rusak_berat' => 'Rusak Berat',
                                    'hilang' => 'Hilang',
                                ])
                                ->required(),

                            TextInput::make('denda')
                                ->label('Denda')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0)
                                ->minValue(0)
                                ->required(),

                            Textarea::make('catatan')
                                ->label('Catatan Detail')
                                ->rows(2)
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->minItems(1)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
