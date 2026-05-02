<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\UserTask;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopPerformersWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Performers (User Terbaik)';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('role', 'user')
                    ->withCount([
                        'userTasks as completed_tasks_count' => function (Builder $query) {
                            $query->where('status', 'completed');
                        },
                        'userTasks as total_tasks_count',
                    ])
                    ->withSum([
                        'userTasks as total_earned' => function (Builder $query) {
                            $query->where('status', 'completed')
                                ->where('payment_status', 'success');
                        },
                    ], 'payment_amount')
                    ->whereHas('userTasks', function (Builder $query) {
                        $query->where('status', 'completed');
                    })
                    ->orderByDesc('completed_tasks_count')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('completed_tasks_count')
                    ->label('Selesai')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('total_tasks_count')
                    ->label('Total')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_earned')
                    ->label('Total Pendapatan')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('badge')
                    ->label('Badge')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'god' => 'danger',
                        'senior' => 'warning',
                        'junior' => 'info',
                        'premium_admin' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst(str_replace('_', ' ', $state))),
            ])
            ->defaultSort('completed_tasks_count', 'desc')
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }
}
