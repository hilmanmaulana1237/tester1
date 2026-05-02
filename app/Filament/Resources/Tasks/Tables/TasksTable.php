<?php

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('admin.name')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('whatsapp_group_link')
                    ->searchable(),
                TextColumn::make('tutorial_link')
                    ->label('Tutorial')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('difficulty_level')
                    ->searchable(),
                TextColumn::make('expired_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_expired')
                    ->boolean(),
                TextColumn::make('priority_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estimated_amount')
                    ->label('Estimasi (Rp)')
                    ->money('IDR')
                    ->sortable()
                    ->placeholder('Belum diisi'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
