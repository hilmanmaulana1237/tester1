<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At'),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->dehydrated(fn($state) => filled($state))
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Informasi E-Wallet')
                    ->description('Data e-wallet untuk pembayaran reward')
                    ->schema([
                        Select::make('ewallet_type')
                            ->label('Jenis E-Wallet')
                            ->options(User::EWALLETS)
                            ->searchable()
                            ->placeholder('Pilih E-Wallet'),
                        TextInput::make('ewallet_number')
                            ->label('Nomor E-Wallet')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('081234567890'),
                        TextInput::make('ewallet_name')
                            ->label('Nama Pemilik E-Wallet')
                            ->maxLength(255)
                            ->placeholder('Sesuai nama di E-Wallet'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Role & Status')
                    ->schema([
                        Select::make('role')
                            ->label('Role')
                            ->options(User::ROLES)
                            ->required()
                            ->default('user'),
                        Select::make('badge')
                            ->label('Badge')
                            ->options(User::BADGES)
                            ->required()
                            ->default('none'),
                        Toggle::make('is_banned')
                            ->label('Banned')
                            ->default(false),
                        DateTimePicker::make('ban_until')
                            ->label('Banned Until')
                            ->visible(fn($get) => $get('is_banned')),
                        TextInput::make('failed_task_count')
                            ->label('Failed Task Count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
