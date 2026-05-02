<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->sortable(),
                TextColumn::make('ewallet_type')
                    ->label('E-Wallet')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => User::EWALLETS[$state] ?? $state)
                    ->colors([
                        'success' => 'gopay',
                        'info' => 'ovo',
                        'warning' => 'dana',
                        'danger' => 'shopeepay',
                        'primary' => 'linkaja',
                    ])
                    ->sortable(),
                TextColumn::make('ewallet_number')
                    ->label('No. E-Wallet')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor copied!')
                    ->toggleable(),
                TextColumn::make('ewallet_name')
                    ->label('Nama E-Wallet')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => User::ROLES[$state] ?? $state)
                    ->colors([
                        'danger' => 'superadmin',
                        'warning' => 'admin',
                        'success' => 'user',
                    ])
                    ->searchable()
                    ->sortable(),
                TextColumn::make('badge')
                    ->label('Badge')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => User::BADGES[$state] ?? $state)
                    ->colors([
                        'gray' => 'none',
                        'success' => 'junior',
                        'info' => 'senior',
                        'warning' => 'god',
                        'danger' => 'premium_admin',
                    ])
                    ->sortable(),
                IconColumn::make('is_banned')
                    ->label('Banned')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('failed_task_count')
                    ->label('Failed Tasks')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options(User::ROLES),
                SelectFilter::make('ewallet_type')
                    ->label('E-Wallet Type')
                    ->options(User::EWALLETS),
                SelectFilter::make('is_banned')
                    ->label('Banned Status')
                    ->options([
                        '1' => 'Banned',
                        '0' => 'Active',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
