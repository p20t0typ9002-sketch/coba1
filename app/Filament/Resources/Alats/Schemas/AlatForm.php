<?php

namespace App\Filament\Resources\Alats\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AlatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('name')
                    ->label('Nama Alat')
                    ->required()
                    ->maxLength(150)
                    ->live(onBlur: true)
                    ->afterStateUpdated(
                        fn ($set, ?string $state) =>
                            $set('slug', Str::slug($state ?? ''))
                    ),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(180)
                    ->unique(ignoreRecord: true),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(4)
                    ->columnSpanFull(),

                TextInput::make('stok')
                    ->label('Stok Tersedia')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                Select::make('kondisi')
                    ->label('Kondisi Alat')
                    ->options([
                        'baik' => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_berat' => 'Rusak Berat',
                    ])
                    ->default('baik')
                    ->required(),

                TextInput::make('location')
                    ->label('Lokasi Penyimpanan')
                    ->maxLength(150),

                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),

                Repeater::make('images')
                    ->label('Gambar Alat')
                    ->relationship('images')
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Foto Alat')
                            ->image()
                            ->disk('public')
                            ->directory('alat-images')
                            ->visibility('public')
                            ->required(),

                        TextInput::make('alt_text')
                            ->label('Deskripsi Gambar')
                            ->maxLength(255),

                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->integer()
                            ->default(0),

                        Toggle::make('is_primary')
                            ->label('Gambar Utama')
                            ->default(false),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->addActionLabel('Tambah Gambar')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}