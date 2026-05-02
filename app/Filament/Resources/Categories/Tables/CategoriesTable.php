<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Category Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => substr($record->description ?? '', 0, 60) . '...'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                // Count of total tasks in category
                TextColumn::make('tasks_count')
                    ->label('Total Tasks')
                    ->counts('tasks')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                // Count of active/available tasks
                TextColumn::make('available_tasks_count')
                    ->label('Available')
                    ->getStateUsing(function ($record) {
                        return $record->tasks()
                            ->where('is_expired', false)
                            ->whereDoesntHave('userTasks', function (Builder $query) {
                                $query->whereIn('status', ['taken', 'pending_verification_1', 'pending_verification_2', 'completed']);
                            })
                            ->count();
                    })
                    ->badge()
                    ->color('success'),

                // Count of tasks being worked on
                TextColumn::make('in_progress_count')
                    ->label('In Progress')
                    ->getStateUsing(function ($record) {
                        return $record->userTasks()
                            ->whereIn('status', ['taken', 'pending_verification_1', 'pending_verification_2'])
                            ->count();
                    })
                    ->badge()
                    ->color('warning'),

                // Count of completed tasks
                TextColumn::make('completed_count')
                    ->label('Completed')
                    ->getStateUsing(function ($record) {
                        return $record->userTasks()
                            ->where('status', 'completed')
                            ->count();
                    })
                    ->badge()
                    ->color('success'),

                // Count pending verification
                TextColumn::make('pending_verification_count')
                    ->label('Need Review')
                    ->getStateUsing(function ($record) {
                        return $record->userTasks()
                            ->whereIn('status', ['pending_verification_1', 'pending_verification_2'])
                            ->count();
                    })
                    ->badge()
                    ->color('danger'),

                TextColumn::make('createdByAdmin.name')
                    ->label('Created By')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('expired_at')
                    ->label('Expires At')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->label('Manage Tasks')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn($record) => route('filament.admin.resources.categories.view', ['record' => $record->id])),
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
