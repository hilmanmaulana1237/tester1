<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Models\UserTask;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestUserTasksWidget extends BaseWidget
{
    protected static ?string $heading = 'Pengerjaan Task Terbaru';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = auth()->user();

        $query = UserTask::query()
            ->with(['task', 'user'])
            ->latest('updated_at');

        // Filter based on role
        if (!$user->isSuperAdmin()) {
            $taskIds = Task::where('created_by', $user->id)->pluck('id');
            $query->whereIn('task_id', $taskIds);
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('task.title')
                    ->label('Task')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'taken' => 'info',
                        'pending_verification_1', 'pending_verification_2' => 'warning',
                        'completed' => 'success',
                        'failed', 'banned' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'taken' => 'Diambil',
                        'pending_verification_1' => 'Verifikasi 1',
                        'pending_verification_2' => 'Verifikasi 2',
                        'completed' => 'Selesai',
                        'failed' => 'Gagal',
                        'cancelled' => 'Batal',
                        'banned' => 'Banned',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('payment_amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'success' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Update Terakhir')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
