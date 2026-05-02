<?php

namespace App\Filament\Pages;

use App\Models\UserTask;
use App\Models\TaskMessage;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class TaskChatsOverview extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.pages.task-chats-overview';

    protected static ?string $navigationLabel = 'Task Chats';

    protected static ?string $title = 'Task Chats Overview';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                UserTask::query()
                    ->whereHas('messages')
                    ->with(['user', 'task', 'messages' => function ($query) {
                        $query->latest()->limit(1);
                    }])
            )
            ->defaultSort('updated_at', 'desc')
            // Removed auto-polling to prevent performance issues and input reset
            // Table will refresh when actions are performed
            ->columns([
                Tables\Columns\ImageColumn::make('user_avatar')
                    ->label('Avatar')
                    ->circular()
                    ->state(fn(UserTask $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->user->name) . '&color=7F9CF5&background=EBF4FF'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->description(fn(UserTask $record) => $record->user->email)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('task.title')
                    ->label('Task')
                    ->searchable()
                    ->limit(40)
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

                Tables\Columns\TextColumn::make('messages_count')
                    ->label('Messages')
                    ->counts('messages')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('unread_messages')
                    ->label('Unread')
                    ->badge()
                    ->state(
                        fn(UserTask $record) => $record->messages()
                            ->where('sender_type', 'user')
                            ->where('is_read', false)
                            ->count()
                    )
                    ->color('danger')
                    ->visible(
                        fn(UserTask $record) => $record->messages()
                            ->where('sender_type', 'user')
                            ->where('is_read', false)
                            ->exists()
                    ),

                Tables\Columns\TextColumn::make('last_message')
                    ->label('Last Message')
                    ->state(function (UserTask $record) {
                        $lastMessage = $record->messages()->latest()->first();
                        if (!$lastMessage) return '-';

                        $prefix = $lastMessage->sender_type === 'admin' ? 'You: ' : $record->user->name . ': ';
                        $content = $lastMessage->message ?? ($lastMessage->file_path ? '📎 [File]' : '');

                        return $prefix . \Illuminate\Support\Str::limit($content, 50);
                    })
                    ->description(fn(UserTask $record) => $record->messages()->latest()->first()?->created_at?->diffForHumans())
                    ->wrap(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Activity')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->since()
                    ->description(fn(UserTask $record) => $record->updated_at->format('d M Y, H:i')),
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
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('unread')
                    ->label('Unread Messages Only')
                    ->query(fn(Builder $query) => $query->whereHas(
                        'messages',
                        fn($q) =>
                        $q->where('sender_type', 'user')->where('is_read', false)
                    ))
                    ->toggle(),

                Tables\Filters\Filter::make('active_today')
                    ->label('Active Today')
                    ->query(fn(Builder $query) => $query->whereHas(
                        'messages',
                        fn($q) =>
                        $q->whereDate('created_at', today())
                    ))
                    ->toggle(),
            ])
            ->actions([
                Action::make('chat')
                    ->label('Open Chat')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->badge(
                        fn(UserTask $record) => $record->messages()
                            ->where('sender_type', 'user')
                            ->where('is_read', false)
                            ->count() ?: null
                    )
                    ->badgeColor('danger')
                    ->modalHeading(fn(UserTask $record) => "💬 Chat: {$record->user->name} - {$record->task->title}")
                    ->modalContent(fn(UserTask $record) => new HtmlString(
                        Blade::render(
                            '<div class="p-4">' .
                                '@livewire("task-chat", ["userTask" => $userTask])' .
                                '</div>',
                            ['userTask' => $record]
                        )
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('5xl')
                    ->slideOver()
                    ->after(function (UserTask $record) {
                        $record->messages()
                            ->where('sender_type', 'user')
                            ->where('is_read', false)
                            ->update([
                                'is_read' => true,
                                'read_at' => now(),
                            ]);
                    }),

                Action::make('quick_reply')
                    ->label('Quick Reply')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('message')
                            ->label('Message')
                            ->required()
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Type your message here...'),
                        Forms\Components\FileUpload::make('file')
                            ->label('Attachment (optional)')
                            ->disk('public')
                            ->directory('task-messages')
                            ->maxSize(10240)
                            ->acceptedFileTypes(['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ])
                    ->action(function (UserTask $record, array $data) {
                        $filePath = null;
                        $fileName = null;
                        $fileType = null;
                        $fileSize = null;

                        if (!empty($data['file'])) {
                            $filePath = $data['file'];
                            $fileName = basename($filePath);
                            if (Storage::disk('public')->exists($filePath)) {
                                $fileSize = Storage::disk('public')->size($filePath);
                                $fullPath = Storage::disk('public')->path($filePath);
                                $fileType = mime_content_type($fullPath) ?: null;
                            }
                        }

                        TaskMessage::create([
                            'user_task_id' => $record->id,
                            'user_id' => auth()->check() ? auth()->id() : null,
                            'sender_type' => TaskMessage::SENDER_ADMIN,
                            'message' => $data['message'],
                            'file_path' => $filePath,
                            'file_name' => $fileName,
                            'file_type' => $fileType,
                            'file_size' => $fileSize,
                            'is_read' => false,
                        ]);

                        Notification::make()
                            ->title('Message sent')
                            ->body('Your message has been sent to ' . $record->user->name)
                            ->success()
                            ->send();
                    }),

                Action::make('mark_read')
                    ->label('Mark Read')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
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
            ])
            ->bulkActions([
                BulkAction::make('mark_all_read')
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

    public function getHeaderWidgets(): array
    {
        return [
            // Add widgets here if needed
        ];
    }
}
