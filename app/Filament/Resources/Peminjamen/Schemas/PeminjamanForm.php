<?php

namespace App\Filament\Resources\Peminjamen\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PeminjamanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Peminjaman')
                    ->schema([
                        TextInput::make('pinjam_code')
                            ->label('Kode Peminjaman')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Select::make('user_id')
                            ->label('Peminjam')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        DatePicker::make('tanggal_pinjam')
                            ->label('Tanggal Peminjaman')
                            ->required(),

                        DatePicker::make('tanggal_rencana_kembali')
                            ->label('Rencana Tanggal Kembali')
                            ->required(),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Detail Alat yang Dipinjam')
                    ->schema([
                        Repeater::make('details')
                            ->label('Daftar Alat')
                            ->relationship()
                            ->schema([
                                Select::make('alat_id')
                                    ->label('Nama Alat')
                                    ->relationship('alat', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                TextInput::make('jumlah')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->minItems(1)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
