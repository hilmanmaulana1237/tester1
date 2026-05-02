<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use App\Models\UserTask;
use App\Models\TaskMessage;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

use BackedEnum;

class TaskChatsRelationManager extends RelationManager
{
    protected static string $relationship = 'userTasks';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'Task Chats';

    protected static string|BackedEnum|null $icon = 'heroicon-o-chat-bubble-left-right';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('updated_at', 'desc')
            // Removed auto-polling to prevent input reset issues
            // Users can manually refresh if needed
            ->columns([
                Tables\Columns\ImageColumn::make('user_avatar')
                    ->label('Avatar')
                    ->circular()
                    ->state(fn(UserTask $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->user->name) . '&color=7F9CF5&background=EBF4FF'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->description(fn(UserTask $record) => $record->user->email),

                Tables\Columns\TextColumn::make('task.title')
                    ->label('Task')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn(UserTask $record) => $record->task->title),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'taken',
                        'info' => 'pending_verification_1',
                        'primary' => 'pending_verification_2',
                        'success' => 'completed',
                        'danger' => ['rejected', 'failed'],
                    ]),

                Tables\Columns\TextColumn::make('last_message')
                    ->label('Last Message')
                    ->state(function (UserTask $record) {
                        $lastMessage = $record->messages()->latest()->first();
                        if (!$lastMessage) return '-';

                        $prefix = $lastMessage->sender_type === 'admin' ? 'You: ' : '';
                        $content = $lastMessage->message ?? ($lastMessage->file_path ? '[File]' : '');

                        return $prefix . \Illuminate\Support\Str::limit($content, 40);
                    })
                    ->description(fn(UserTask $record) => $record->messages()->latest()->first()?->created_at?->diffForHumans()),

                Tables\Columns\IconColumn::make('unread_count')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-s-bell-alert')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-check')
                    ->falseColor('success')
                    ->state(
                        fn(UserTask $record) => $record->messages()
                            ->where('sender_type', 'user')
                            ->where('is_read', false)
                            ->exists()
                    )
                    ->tooltip(fn($state) => $state ? 'New messages!' : 'All caught up'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'taken' => 'Taken',
                        'pending_verification_1' => 'Pending Verification 1',
                        'pending_verification_2' => 'Pending Verification 2',
                        'completed' => 'Completed',
                        'rejected' => 'Rejected',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\Filter::make('has_chat')
                    ->label('Has Messages')
                    ->query(fn(Builder $query) => $query->whereHas('messages')),
                Tables\Filters\Filter::make('unread')
                    ->label('Unread Messages')
                    ->query(fn(Builder $query) => $query->whereHas(
                        'messages',
                        fn($q) =>
                        $q->where('sender_type', 'user')->where('is_read', false)
                    )),
            ])
            ->headerActions([])
            ->actions([
                Action::make('chat')
                    ->label('Chat')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->badge(
                        fn(UserTask $record) => $record->messages()
                            ->where('sender_type', 'user')
                            ->where('is_read', false)
                            ->count() ?: null
                    )
                    ->badgeColor('danger')
                    ->modalContent(fn(UserTask $record) => view('filament.components.chat-modal', [
                        'userTask' => $record
                    ]))
                    ->modalHeading(fn(UserTask $record) => 'Chat: ' . $record->user->name)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('3xl')
                    ->extraModalWindowAttributes([
                        'class' => 'filament-chat-modal',
                    ])
                    ->action(function (UserTask $record) {
                        // Mark messages as read when opening
                        $record->messages()
                            ->where('sender_type', 'user')
                            ->where('is_read', false)
                            ->update([
                                'is_read' => true,
                                'read_at' => now(),
                            ]);
                    }),

                Action::make('mark_all_read')
                    ->label('Mark Read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(
                        fn(UserTask $record) => $record->messages()
                            ->where('sender_type', 'user')
                            ->where('is_read', false)
                            ->exists()
                    )
                    ->action(function (UserTask $record) {
                        $count = $record->messages()
                            ->where('sender_type', 'user')
                            ->where('is_read', false)
                            ->update([
                                'is_read' => true,
                                'read_at' => now(),
                            ]);

                        Notification::make()
                            ->title('Messages marked as read')
                            ->body("{$count} messages marked as read")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(false),

                Action::make('force_cancel')
                    ->label('Cancel Task')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->visible(fn(UserTask $record) => in_array($record->status, ['taken', 'pending_verification_1', 'pending_verification_2']))
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Task User')
                    ->modalDescription('User akan menerima notifikasi dan task akan dikembalikan ke status cancelled sehingga user bisa ambil task lain.')
                    ->modalSubmitActionLabel('Ya, Batalkan Task')
                    ->action(function (UserTask $record) {
                        // Update status to cancelled
                        $record->update([
                            'status' => UserTask::STATUS_CANCELLED,
                            'cancelled_at' => now(),
                        ]);

                        // Send notification message to user
                        TaskMessage::create([
                            'user_task_id' => $record->id,
                            'user_id' => auth()->id(),
                            'sender_type' => TaskMessage::SENDER_ADMIN,
                            'message' => 'Task Anda telah dibatalkan oleh admin. Silakan ambil task lain yang sesuai dengan kemampuan Anda. Terima kasih.',
                            'is_read' => false,
                        ]);

                        Notification::make()
                            ->title('Task berhasil dibatalkan')
                            ->body("Task telah dikembalikan ke dashboard. User {$record->user->name} dapat mengambil task lain.")
                            ->success()
                            ->send();
                    })
            ])
            ->bulkActions([
                BulkAction::make('mark_all_as_read')
                    ->label('Mark All as Read')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($records) {
                        $totalCount = 0;
                        foreach ($records as $record) {
                            $count = $record->messages()
                                ->where('sender_type', 'user')
                                ->where('is_read', false)
                                ->update([
                                    'is_read' => true,
                                    'read_at' => now(),
                                ]);
                            $totalCount += $count;
                        }

                        Notification::make()
                            ->title('All messages marked as read')
                            ->body("{$totalCount} messages marked as read")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }
}
