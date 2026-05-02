<?php

namespace App\Filament\Resources\UserTasks;

use App\Models\UserTask;
use App\Models\Task;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use BackedEnum;
use UnitEnum;
use App\Filament\Resources\UserTasks\Pages\ListUserTasks;
use App\Filament\Resources\UserTasks\Pages\EditUserTask;
use App\Filament\Resources\UserTasks\Pages\ViewUserTask;

class UserTaskResource extends Resource
{
    protected static ?string $model = UserTask::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Kelola Tugas User';

    protected static ?string $modelLabel = 'Tugas User';

    protected static ?string $pluralModelLabel = 'Tugas User';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Tugas';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    /**
     * Optimize query with eager loading and filter by user role.
     * Admin biasa hanya bisa melihat user tasks dari task yang dia buat.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['task', 'task.category', 'user']);

        $user = auth()->user();

        // Superadmin bisa lihat semua
        if ($user->role === User::ROLE_SUPERADMIN) {
            return $query;
        }

        // Admin biasa hanya bisa lihat user tasks dari task yang dia buat
        return $query->whereHas('task', function ($q) use ($user) {
            $q->where('created_by', $user->id);
        });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('task_id')
                ->label('Task')
                ->relationship('task', 'title')
                ->required()
                ->searchable()
                ->disabled(fn($context) => $context === 'edit'),

            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->required()
                ->searchable()
                ->disabled(fn($context) => $context === 'edit'),

            Select::make('status')
                ->label('Status')
                ->options(UserTask::STATUSES)
                ->required()
                ->live(),

            DateTimePicker::make('taken_at')
                ->label('Taken At')
                ->disabled(),

            DateTimePicker::make('deadline_at')
                ->label('Deadline')
                ->required(),

            DateTimePicker::make('cancelled_at')
                ->label('Cancelled At')
                ->disabled(),

            TextInput::make('failed_count')
                ->label('Failed Count')
                ->numeric()
                ->default(0)
                ->disabled(),

            Textarea::make('verification_1_status')
                ->label('Verification 1 Status')
                ->disabled()
                ->visible(fn($get) => in_array($get('status'), [
                    UserTask::STATUS_PENDING_VERIFICATION_1,
                    UserTask::STATUS_PENDING_VERIFICATION_2,
                    UserTask::STATUS_COMPLETED
                ])),

            Textarea::make('verification_2_status')
                ->label('Verification 2 Status')
                ->disabled()
                ->visible(fn($get) => in_array($get('status'), [
                    UserTask::STATUS_PENDING_VERIFICATION_2,
                    UserTask::STATUS_COMPLETED
                ])),

            Select::make('payment_status')
                ->label('Payment Status')
                ->options(UserTask::PAYMENT_STATUSES)
                ->default(UserTask::PAYMENT_PENDING),

            TextInput::make('payment_amount')
                ->label('Payment Amount')
                ->numeric()
                ->step(0.01)
                ->prefix('Rp'),

            Textarea::make('amount_change_reason')
                ->label('Alasan Perubahan Nominal')
                ->placeholder('Isi jika nominal berbeda dari estimasi...')
                ->rows(2),

            Select::make('payment_verified_by_admin_id')
                ->label('Payment Verified By')
                ->relationship('paymentVerifiedByAdmin', 'name')
                ->searchable(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('task.category.name')
                    ->label('Category')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->color('info')
                    ->weight('bold')
                    ->toggleable(),

                TextColumn::make('task.title')
                    ->label('Task')
                    ->searchable()
                    ->wrap()
                    ->limit(25)
                    ->tooltip(fn(UserTask $record): string => $record->task->title ?? '')
                    ->description(
                        fn(UserTask $record): string =>
                        'Difficulty: ' . ($record->task->difficulty_level ?? 'N/A')
                    ),

                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->limit(15)
                    ->tooltip(fn(UserTask $record): string => $record->user->name ?? '')
                    ->description(
                        fn(UserTask $record): string =>
                        'Badge: ' . ($record->user->badge ?? 'None')
                    ),

                TextColumn::make('user.phone')
                    ->label('Kontak')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->iconColor('success')
                    ->formatStateUsing(fn(UserTask $record): string => $record->user->phone ?? '-')
                    ->copyable()
                    ->copyMessage('Nomor disalin!')
                    ->url(
                        fn(UserTask $record): ?string => $record->user->whatsapp
                        ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->user->whatsapp)
                        : ($record->user->phone ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->user->phone) : null)
                    )
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => UserTask::STATUSES[$state] ?? $state)
                    ->color(fn(UserTask $record) => $record->getStatusBadgeColorAttribute())
                    ->icon(fn(UserTask $record) => match ($record->status) {
                        UserTask::STATUS_TAKEN => 'heroicon-o-clock',
                        UserTask::STATUS_PENDING_VERIFICATION_1 => 'heroicon-o-eye',
                        UserTask::STATUS_PENDING_VERIFICATION_2 => 'heroicon-o-document-check',
                        UserTask::STATUS_COMPLETED => 'heroicon-o-check-circle',
                        UserTask::STATUS_FAILED => 'heroicon-o-x-circle',
                        UserTask::STATUS_CANCELLED => 'heroicon-o-x-circle',
                        default => null,
                    }),

                BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->formatStateUsing(fn($state) => UserTask::PAYMENT_STATUSES[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        UserTask::PAYMENT_SUCCESS => 'success',
                        UserTask::PAYMENT_FAILED => 'danger',
                        default => 'warning'
                    })
                    ->icon(fn($state) => match ($state) {
                        UserTask::PAYMENT_SUCCESS => 'heroicon-o-check-circle',
                        UserTask::PAYMENT_FAILED => 'heroicon-o-x-circle',
                        default => 'heroicon-o-clock',
                    })
                    ->toggleable(),

                TextColumn::make('payment_amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('taken_at')
                    ->label('Taken')
                    ->dateTime('d M H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->striped()
            ->defaultSort('taken_at', 'desc')
            ->defaultGroup('task.category.name')
            ->filters([
                SelectFilter::make('category')
                    ->label('Category')
                    ->relationship('task.category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(UserTask::STATUSES)
                    ->multiple(),

                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options(UserTask::PAYMENT_STATUSES),

                Filter::make('needs_action')
                    ->label('⚠️ Needs My Action')
                    ->query(fn(Builder $query) => $query->whereIn('status', [
                        UserTask::STATUS_PENDING_VERIFICATION_1,
                        UserTask::STATUS_PENDING_VERIFICATION_2
                    ])
                        ->orWhere(function ($q) {
                            $q->where('status', UserTask::STATUS_COMPLETED)
                                ->where('payment_status', UserTask::PAYMENT_PENDING);
                        }))
                    ->toggle(),

                Filter::make('overdue')
                    ->label('Overdue Only')
                    ->query(fn(Builder $query) => $query->where('deadline_at', '<', now())
                        ->whereIn('status', [
                            UserTask::STATUS_TAKEN,
                            UserTask::STATUS_PENDING_VERIFICATION_1,
                            UserTask::STATUS_PENDING_VERIFICATION_2
                        ]))
                    ->toggle(),

                Filter::make('completed_unpaid')
                    ->label('Completed (Awaiting Payment)')
                    ->query(fn(Builder $query) => $query
                        ->where('status', UserTask::STATUS_COMPLETED)
                        ->where('payment_status', UserTask::PAYMENT_PENDING))
                    ->toggle(),
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->recordUrl(
                fn(UserTask $record): string => EditUserTask::getUrl(['record' => $record])
            )
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    // View Proof Details - IMPROVED WITH IMAGE PREVIEW
                    Action::make('view_proofs')
                        ->label('View Proofs')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->visible(
                            fn(UserTask $record) =>
                            $record->verification_1_status || $record->verification_2_status
                        )
                        ->modalHeading(fn(UserTask $record) => 'Proof Details - Task #' . $record->id)
                        ->modalContent(function (UserTask $record) {
                            $content = '<div class="space-y-4 p-2">';

                            // Helper function to check if file is image
                            $isImage = function ($filename) {
                                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
                            };

                            // Helper function to parse verification status
                            $parseProofStatus = function ($status, $filesJson) {
                                if (!$status) {
                                    return [
                                        'submitted' => false,
                                        'submittedAt' => null,
                                        'files' => [],
                                        'description' => null,
                                        'approved' => false,
                                        'rejected' => false,
                                        'feedback' => null
                                    ];
                                }

                                $data = [
                                    'submitted' => str_contains($status, 'Submitted at'),
                                    'approved' => str_contains($status, 'Approved by admin'),
                                    'rejected' => str_contains($status, 'Rejected by admin'),
                                    'submittedAt' => null,
                                    'files' => [],
                                    'description' => null,
                                    'feedback' => null
                                ];

                                // Parse submitted proof
                                if (preg_match('/Submitted at ([\d\-: ]+)/', $status, $matches)) {
                                    $data['submittedAt'] = $matches[1];
                                }

                                // Read files from verification_x_files column
                                // Laravel Model Cast already decodes JSON to array
                                if ($filesJson) {
                                    $data['files'] = is_array($filesJson) ? $filesJson : json_decode($filesJson, true) ?? [];
                                }

                                // Parse description
                                if (preg_match('/Description: ([^-]+?)(?:\s*-\s*(?:Approved|Rejected)|$)/', $status, $matches)) {
                                    $data['description'] = trim($matches[1]);
                                }

                                // Parse rejection reason
                                if (preg_match('/Rejected by admin at [\d\-: ]+\. Reason: (.+)/', $status, $matches)) {
                                    $data['feedback'] = $matches[1];
                                }

                                return $data;
                            };

                            // Add lightbox script and styles
                            $content .= '<style>
                            .proof-image-thumbnail { 
                                cursor: pointer; 
                                transition: transform 0.2s; 
                                border-radius: 8px;
                                object-fit: cover;
                            }
                            .proof-image-thumbnail:hover { 
                                transform: scale(1.05); 
                                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                            }
                            .lightbox-overlay {
                                display: none;
                                position: fixed;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                background: rgba(0,0,0,0.9);
                                z-index: 9999;
                                justify-content: center;
                                align-items: center;
                                padding: 20px;
                            }
                            .lightbox-overlay.active {
                                display: flex;
                            }
                            .lightbox-image {
                                max-width: 90%;
                                max-height: 90%;
                                object-fit: contain;
                                border-radius: 8px;
                            }
                            .lightbox-close {
                                position: absolute;
                                top: 20px;
                                right: 30px;
                                color: white;
                                font-size: 40px;
                                font-weight: bold;
                                cursor: pointer;
                                z-index: 10000;
                            }
                            .lightbox-close:hover {
                                color: #ccc;
                            }
                        </style>';

                            $content .= '<div id="lightbox" class="lightbox-overlay" onclick="closeLightbox()">
                            <span class="lightbox-close">&times;</span>
                            <img id="lightbox-img" class="lightbox-image" src="" alt="Full size image">
                        </div>';

                            $content .= '<script>
                            function openLightbox(src) {
                                document.getElementById("lightbox").classList.add("active");
                                document.getElementById("lightbox-img").src = src;
                                event.stopPropagation();
                            }
                            function closeLightbox() {
                                document.getElementById("lightbox").classList.remove("active");
                            }
                            document.addEventListener("keydown", function(e) {
                                if (e.key === "Escape") closeLightbox();
                            });
                        </script>';

                            // PROOF 1
                            if ($record->verification_1_status) {
                                $proof1 = $parseProofStatus($record->verification_1_status, $record->verification_1_files);

                                $content .= '<div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-800">';
                                $content .= '<div class="flex items-center justify-between mb-3">';
                                $content .= '<h3 class="font-bold text-lg text-gray-900 dark:text-white">📄 Proof 1</h3>';

                                if ($proof1['approved']) {
                                    $content .= '<span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full text-xs font-semibold">✓ Approved</span>';
                                } elseif ($proof1['rejected']) {
                                    $content .= '<span class="px-3 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full text-xs font-semibold">✗ Rejected</span>';
                                } elseif ($proof1['submitted']) {
                                    $content .= '<span class="px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full text-xs font-semibold">⏳ Pending Review</span>';
                                }

                                $content .= '</div>';

                                if ($proof1['submittedAt']) {
                                    $content .= '<p class="text-sm text-gray-600 dark:text-gray-400 mb-2"><strong>Submitted:</strong> ' . $proof1['submittedAt'] . '</p>';
                                }

                                if (!empty($proof1['files'])) {
                                    $content .= '<div class="mb-3"><strong class="text-sm text-gray-700 dark:text-gray-300 block mb-2">Uploaded Files:</strong>';
                                    $content .= '<div class="grid grid-cols-2 md:grid-cols-3 gap-3">';

                                    foreach ($proof1['files'] as $file) {
                                        $filePath = trim($file);
                                        $fileUrl = \Storage::url($filePath);

                                        if ($isImage($filePath)) {
                                            // Image preview with lightbox
                                            $content .= '<div class="relative group">';
                                            $content .= '<img src="' . $fileUrl . '" 
                                                    alt="Proof image" 
                                                    class="proof-image-thumbnail w-full h-32 border-2 border-gray-300 dark:border-gray-600"
                                                    onclick="openLightbox(\'' . $fileUrl . '\')"
                                                    loading="lazy">';
                                            $content .= '<div class="absolute inset-0 bg-opacity-0 group-hover:bg-opacity-30 transition-all rounded-lg flex items-center justify-center">';
                                            $content .= '<span class="text-white opacity-0 group-hover:opacity-100 text-sm font-semibold">🔍 Click to enlarge</span>';
                                            $content .= '</div>';
                                            $content .= '</div>';
                                        } else {
                                            // Non-image file link
                                            $fileName = basename($filePath);
                                            $content .= '<a href="' . $fileUrl . '" target="_blank" class="flex items-center gap-2 p-2 border rounded bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">';
                                            $content .= '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>';
                                            $content .= '<span class="text-xs text-blue-600 dark:text-blue-400 truncate">' . htmlspecialchars($fileName) . '</span>';
                                            $content .= '</a>';
                                        }
                                    }

                                    $content .= '</div></div>';
                                }

                                if ($proof1['description']) {
                                    $content .= '<div class="mb-2 mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded"><strong class="text-sm text-gray-700 dark:text-gray-300">User Description:</strong>';
                                    $content .= '<p class="text-sm text-gray-600 dark:text-gray-400 mt-1 italic">"' . nl2br(htmlspecialchars($proof1['description'])) . '"</p></div>';
                                }

                                if ($proof1['feedback']) {
                                    $content .= '<div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded">';
                                    $content .= '<strong class="text-sm text-red-700 dark:text-red-400">Rejection Reason:</strong>';
                                    $content .= '<p class="text-sm text-red-600 dark:text-red-300 mt-1">' . htmlspecialchars($proof1['feedback']) . '</p>';
                                    $content .= '</div>';
                                }

                                $content .= '</div>';
                            }

                            // PROOF 2
                            if ($record->verification_2_status) {
                                $proof2 = $parseProofStatus($record->verification_2_status, $record->verification_2_files);

                                $content .= '<div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-800">';
                                $content .= '<div class="flex items-center justify-between mb-3">';
                                $content .= '<h3 class="font-bold text-lg text-gray-900 dark:text-white">📄 Proof 2</h3>';

                                if ($proof2['approved']) {
                                    $content .= '<span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full text-xs font-semibold">✓ Approved</span>';
                                } elseif ($proof2['rejected']) {
                                    $content .= '<span class="px-3 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full text-xs font-semibold">✗ Rejected</span>';
                                } elseif ($proof2['submitted']) {
                                    $content .= '<span class="px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full text-xs font-semibold">⏳ Pending Review</span>';
                                }

                                $content .= '</div>';

                                if ($proof2['submittedAt']) {
                                    $content .= '<p class="text-sm text-gray-600 dark:text-gray-400 mb-2"><strong>Submitted:</strong> ' . $proof2['submittedAt'] . '</p>';
                                }

                                if (!empty($proof2['files'])) {
                                    $content .= '<div class="mb-3"><strong class="text-sm text-gray-700 dark:text-gray-300 block mb-2">Uploaded Files:</strong>';
                                    $content .= '<div class="grid grid-cols-2 md:grid-cols-3 gap-3">';

                                    foreach ($proof2['files'] as $file) {
                                        $filePath = trim($file);
                                        $fileUrl = \Storage::url($filePath);

                                        if ($isImage($filePath)) {
                                            // Image preview with lightbox
                                            $content .= '<div class="relative group">';
                                            $content .= '<img src="' . $fileUrl . '" 
                                                    alt="Proof image" 
                                                    class="proof-image-thumbnail w-full h-32 border-2 border-gray-300 dark:border-gray-600"
                                                    onclick="openLightbox(\'' . $fileUrl . '\')"
                                                    loading="lazy">';
                                            $content .= '<div class="absolute inset-0 bg-opacity-0 group-hover:bg-opacity-30 transition-all rounded-lg flex items-center justify-center">';
                                            $content .= '<span class="text-white opacity-0 group-hover:opacity-100 text-sm font-semibold">🔍 Click to enlarge</span>';
                                            $content .= '</div>';
                                            $content .= '</div>';
                                        } else {
                                            // Non-image file link
                                            $fileName = basename($filePath);
                                            $content .= '<a href="' . $fileUrl . '" target="_blank" class="flex items-center gap-2 p-2 border rounded bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">';
                                            $content .= '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>';
                                            $content .= '<span class="text-xs text-blue-600 dark:text-blue-400 truncate">' . htmlspecialchars($fileName) . '</span>';
                                            $content .= '</a>';
                                        }
                                    }

                                    $content .= '</div></div>';
                                }

                                if ($proof2['description']) {
                                    $content .= '<div class="mb-2 mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded"><strong class="text-sm text-gray-700 dark:text-gray-300">User Description:</strong>';
                                    $content .= '<p class="text-sm text-gray-600 dark:text-gray-400 mt-1 italic">"' . nl2br(htmlspecialchars($proof2['description'])) . '"</p></div>';
                                }

                                if ($proof2['feedback']) {
                                    $content .= '<div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded">';
                                    $content .= '<strong class="text-sm text-red-700 dark:text-red-400">Rejection Reason:</strong>';
                                    $content .= '<p class="text-sm text-red-600 dark:text-red-300 mt-1">' . htmlspecialchars($proof2['feedback']) . '</p>';
                                    $content .= '</div>';
                                }

                                $content .= '</div>';
                            }

                            if (!$record->verification_1_status && !$record->verification_2_status) {
                                $content .= '<div class="text-center py-8 text-gray-500">';
                                $content .= '<svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
                                $content .= '<p class="text-lg font-semibold">No proofs submitted yet</p>';
                                $content .= '</div>';
                            }

                            $content .= '</div>';

                            return view('filament.components.html-content', ['html' => $content]);
                        })
                        ->modalWidth('4xl')
                        ->slideOver(),

                    // Quick Approve Verification 1
                    Action::make('approve_verification_1')
                        ->label('✓ Approve V1')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(
                            fn(UserTask $record) =>
                            $record->status === UserTask::STATUS_PENDING_VERIFICATION_1 &&
                            $record->verification_1_status &&
                            str_contains($record->verification_1_status, 'Submitted') &&
                            !str_contains($record->verification_1_status, 'Approved by admin') &&
                            !str_contains($record->verification_1_status, 'Rejected by admin')
                        )
                        ->requiresConfirmation()
                        ->modalHeading('Approve Verification 1')
                        ->modalDescription('User will be able to submit proof 2.')
                        ->action(function (UserTask $record) {
                            $record->update([
                                'status' => UserTask::STATUS_PENDING_VERIFICATION_2,
                                'verification_1_status' => $record->verification_1_status . ' - Approved by admin at ' . now()->format('Y-m-d H:i:s'),
                                'verification_1_approved_by' => Auth::id(),
                                'verification_1_approved_at' => now(),
                            ]);

                            Notification::make()
                                ->title('Proof 1 Approved')
                                ->body('User can now submit proof 2')
                                ->success()
                                ->send();

                            // Notify user
                            \App\Services\NotificationService::create(
                                $record->user_id,
                                'Proof 1 Disetujui',
                                'Proof 1 untuk "' . $record->task->title . '" telah disetujui! Silakan submit Proof 2.',
                                'success',
                                '/user/task/' . $record->task_id . '/work'
                            );
                        }),

                    // Quick Reject Verification 1
                    Action::make('reject_verification_1')
                        ->label('✗ Reject V1')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(
                            fn(UserTask $record) =>
                            $record->status === UserTask::STATUS_PENDING_VERIFICATION_1 &&
                            $record->verification_1_status &&
                            str_contains($record->verification_1_status, 'Submitted') &&
                            !str_contains($record->verification_1_status, 'Approved by admin') &&
                            !str_contains($record->verification_1_status, 'Rejected by admin')
                        )
                        ->form([
                            Textarea::make('rejection_reason')
                                ->label('Rejection Reason')
                                ->required()
                                ->placeholder('Explain why this proof is rejected...')
                                ->rows(3)
                                ->maxLength(500),
                        ])
                        ->modalHeading('Reject Verification 1')
                        ->modalDescription('Task will be marked as failed and returned to pool.')
                        ->action(function (UserTask $record, array $data) {
                            $record->update([
                                'status' => UserTask::STATUS_FAILED,
                                'verification_1_status' => 'Rejected by admin at ' . now()->format('Y-m-d H:i:s') . '. Reason: ' . $data['rejection_reason'],
                                'failed_count' => ($record->failed_count ?? 0) + 1,
                                'cancelled_at' => now(),
                            ]);

                            Notification::make()
                                ->title('Proof 1 Rejected')
                                ->body('Task marked as failed and returned to pool')
                                ->warning()
                                ->send();

                            // Notify user
                            \App\Services\NotificationService::create(
                                $record->user_id,
                                'Proof 1 Ditolak',
                                'Proof 1 untuk "' . $record->task->title . '" ditolak. Alasan: ' . $data['rejection_reason'],
                                'error',
                                '/user/my-tasks'
                            );
                        }),

                    // Quick Approve Verification 2 & Set Payment
                    Action::make('approve_verification_2')
                        ->label('✓ Approve V2 & Complete')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(
                            fn(UserTask $record) =>
                            $record->status === UserTask::STATUS_PENDING_VERIFICATION_2 &&
                            $record->verification_2_status &&
                            str_contains($record->verification_2_status, 'Submitted') &&
                            !str_contains($record->verification_2_status, 'Approved by admin') &&
                            !str_contains($record->verification_2_status, 'Rejected by admin')
                        )
                        ->form([
                            \Filament\Forms\Components\Placeholder::make('user_payment_info')
                                ->label('Informasi Pembayaran User')
                                ->content(function (UserTask $record) {
                                    $html = '<div class="space-y-2">';
                                    $html .= '<div class="flex items-center gap-2"><span class="text-lg">👤</span><strong>' . htmlspecialchars($record->user->name) . '</strong></div>';

                                    if ($record->user->ewallet_type) {
                                        $html .= '<div class="flex items-center gap-2"><span>💳</span><span class="font-semibold text-blue-600 dark:text-blue-400">' . strtoupper($record->user->ewallet_type) . '</span></div>';
                                        $html .= '<div class="flex items-center gap-2"><span>📱</span><span class="font-mono text-sm">' . htmlspecialchars($record->user->ewallet_number) . '</span></div>';
                                        $html .= '<div class="flex items-center gap-2"><span>📝</span><span>A.n. <strong>' . htmlspecialchars($record->user->ewallet_name) . '</strong></span></div>';
                                    } else {
                                        $html .= '<div class="flex items-center gap-2 text-red-600 dark:text-red-400"><span>⚠️</span><span class="font-semibold">E-Wallet belum diisi user</span></div>';
                                    }

                                    $html .= '</div>';
                                    return new \Illuminate\Support\HtmlString($html);
                                })
                                ->columnSpanFull(),
                            \Filament\Forms\Components\Placeholder::make('estimated_info')
                                ->label('Estimasi Nominal dari Task')
                                ->content(fn(UserTask $record) => $record->task->estimated_amount
                                    ? 'Rp ' . number_format($record->task->estimated_amount, 0, ',', '.')
                                    : 'Belum diisi')
                                ->columnSpanFull(),
                            TextInput::make('payment_amount')
                                ->label('Payment Amount')
                                ->required()
                                ->numeric()
                                ->step(0.01)
                                ->prefix('Rp')
                                ->placeholder('Enter payment amount')
                                ->default(fn(UserTask $record) => $record->task->estimated_amount ?? 0)
                                ->live(),
                            Textarea::make('amount_change_reason')
                                ->label('Alasan Perubahan Nominal')
                                ->placeholder('Isi jika nominal berbeda dari estimasi...')
                                ->helperText('Wajib diisi jika nominal berbeda dari estimasi task')
                                ->rows(2),
                        ])
                        ->modalHeading('Complete Task')
                        ->modalDescription('Set payment amount and mark task as completed.')
                        ->action(function (UserTask $record, array $data) {
                            $updateData = [
                                'status' => UserTask::STATUS_COMPLETED,
                                'verification_2_status' => $record->verification_2_status . ' - Approved by admin at ' . now()->format('Y-m-d H:i:s'),
                                'verification_2_approved_by' => Auth::id(),
                                'verification_2_approved_at' => now(),
                                'completed_at' => now(),
                                'payment_amount' => $data['payment_amount'],
                                'payment_status' => UserTask::PAYMENT_PENDING,
                                'payment_verified_by_admin_id' => Auth::id(),
                                'payment_verified_at' => now(),
                            ];

                            // Simpan alasan perubahan jika ada
                            if (!empty($data['amount_change_reason'])) {
                                $updateData['amount_change_reason'] = $data['amount_change_reason'];
                            }

                            $record->update($updateData);

                            Notification::make()
                                ->title('Task Completed')
                                ->body('Payment amount: Rp ' . number_format($data['payment_amount'], 0, ',', '.'))
                                ->success()
                                ->send();

                            // Notify user
                            \App\Services\NotificationService::create(
                                $record->user_id,
                                'Task Selesai',
                                'Selamat! Task "' . $record->task->title . '" telah selesai disetujui. Pembayaran Rp ' . number_format($data['payment_amount'], 0, ',', '.') . ' akan segera diproses.',
                                'success',
                                '/user/task/' . $record->task_id . '/work'
                            );
                        }),

                    // Quick Reject Verification 2
                    Action::make('reject_verification_2')
                        ->label('✗ Reject V2')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(
                            fn(UserTask $record) =>
                            $record->status === UserTask::STATUS_PENDING_VERIFICATION_2 &&
                            $record->verification_2_status &&
                            str_contains($record->verification_2_status, 'Submitted') &&
                            !str_contains($record->verification_2_status, 'Approved by admin') &&
                            !str_contains($record->verification_2_status, 'Rejected by admin')
                        )
                        ->form([
                            Textarea::make('rejection_reason')
                                ->label('Rejection Reason')
                                ->required()
                                ->placeholder('Explain why this proof is rejected...')
                                ->rows(3)
                                ->maxLength(500),
                        ])
                        ->modalHeading('Reject Verification 2')
                        ->modalDescription('Task will be marked as failed and returned to pool.')
                        ->action(function (UserTask $record, array $data) {
                            $record->update([
                                'status' => UserTask::STATUS_FAILED,
                                'verification_2_status' => 'Rejected by admin at ' . now()->format('Y-m-d H:i:s') . '. Reason: ' . $data['rejection_reason'],
                                'failed_count' => ($record->failed_count ?? 0) + 1,
                                'cancelled_at' => now(),
                            ]);

                            Notification::make()
                                ->title('Proof 2 Rejected')
                                ->body('Task marked as failed and returned to pool')
                                ->warning()
                                ->send();

                            // Notify user
                            \App\Services\NotificationService::create(
                                $record->user_id,
                                'Proof 2 Ditolak',
                                'Proof 2 untuk "' . $record->task->title . '" ditolak. Alasan: ' . $data['rejection_reason'],
                                'error',
                                '/user/my-tasks'
                            );
                        }),

                    // Quick Payment Success
                    Action::make('mark_payment_success')
                        ->label('💰 Mark Paid')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->visible(
                            fn(UserTask $record) =>
                            $record->payment_status === UserTask::PAYMENT_PENDING &&
                            $record->status === UserTask::STATUS_COMPLETED
                        )
                        ->form([
                            \Filament\Forms\Components\Placeholder::make('user_payment_info')
                                ->label('Informasi Pembayaran User')
                                ->content(function (UserTask $record) {
                                    $html = '<div class="space-y-2">';
                                    $html .= '<div class="flex items-center gap-2"><span class="text-lg">👤</span><strong>' . htmlspecialchars($record->user->name) . '</strong></div>';

                                    if ($record->user->ewallet_type) {
                                        $html .= '<div class="flex items-center gap-2"><span>💳</span><span class="font-semibold text-blue-600 dark:text-blue-400">' . strtoupper($record->user->ewallet_type) . '</span></div>';
                                        $html .= '<div class="flex items-center gap-2"><span>📱</span><span class="font-mono text-sm">' . htmlspecialchars($record->user->ewallet_number) . '</span></div>';
                                        $html .= '<div class="flex items-center gap-2"><span>📝</span><span>A.n. <strong>' . htmlspecialchars($record->user->ewallet_name) . '</strong></span></div>';
                                    } else {
                                        $html .= '<div class="flex items-center gap-2 text-red-600 dark:text-red-400"><span>⚠️</span><span class="font-semibold">E-Wallet belum diisi user</span></div>';
                                    }

                                    $html .= '</div>';
                                    return new \Illuminate\Support\HtmlString($html);
                                })
                                ->columnSpanFull(),
                            \Filament\Forms\Components\Placeholder::make('estimated_info')
                                ->label('Estimasi Nominal dari Task')
                                ->content(fn(UserTask $record) => $record->task->estimated_amount
                                    ? 'Rp ' . number_format($record->task->estimated_amount, 0, ',', '.')
                                    : 'Belum diisi')
                                ->columnSpanFull(),
                            TextInput::make('payment_amount')
                                ->label('Payment Amount')
                                ->required()
                                ->numeric()
                                ->step(0.01)
                                ->prefix('Rp')
                                ->default(fn(UserTask $record) => $record->payment_amount ?? $record->task->estimated_amount ?? 0)
                                ->helperText('Anda bisa mengubah nominal jika diperlukan'),
                            Textarea::make('amount_change_reason')
                                ->label('Alasan Perubahan Nominal')
                                ->placeholder('Isi jika nominal diubah dari sebelumnya...')
                                ->helperText('Wajib diisi jika nominal berbeda')
                                ->rows(2)
                                ->default(fn(UserTask $record) => $record->amount_change_reason),
                        ])
                        ->modalHeading('Mark Payment as Success')
                        ->action(function (UserTask $record, array $data) {
                            $updateData = [
                                'payment_status' => UserTask::PAYMENT_SUCCESS,
                                'payment_amount' => $data['payment_amount'],
                                'payment_verified_by_admin_id' => Auth::id(),
                                'payment_verified_at' => now(),
                            ];

                            // Simpan/update alasan perubahan jika ada
                            if (!empty($data['amount_change_reason'])) {
                                $updateData['amount_change_reason'] = $data['amount_change_reason'];
                            }

                            $record->update($updateData);

                            Notification::make()
                                ->title('Payment Confirmed')
                                ->body('Paid: Rp ' . number_format($data['payment_amount'], 0, ',', '.'))
                                ->success()
                                ->send();
                        }),

                    // Download Proofs for single record
                    Action::make('download_proofs_single')
                        ->label('Download Proofs')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->visible(fn(UserTask $record) => $record->verification_1_files || $record->verification_2_files)
                        ->action(function (UserTask $record) {
                            $zipFileName = 'proofs_' . $record->user->name . '_Task' . $record->task_id . '_' . now()->format('Ymd_His') . '.zip';
                            $zipFileName = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $zipFileName);
                            $zipPath = storage_path('app/temp/' . $zipFileName);

                            if (!file_exists(storage_path('app/temp'))) {
                                mkdir(storage_path('app/temp'), 0755, true);
                            }

                            $zip = new \ZipArchive();
                            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                                Notification::make()->title('Error')->body('Cannot create ZIP')->danger()->send();
                                return;
                            }

                            $fileCount = 0;

                            // Proof 1
                            if ($record->verification_1_files) {
                                $files = is_array($record->verification_1_files) ? $record->verification_1_files : json_decode($record->verification_1_files, true) ?? [];
                                foreach ($files as $i => $file) {
                                    $path = Storage::disk('public')->path($file);
                                    if (file_exists($path)) {
                                        $zip->addFile($path, 'Proof1/' . basename($file));
                                        $fileCount++;
                                    }
                                }
                            }

                            // Proof 2
                            if ($record->verification_2_files) {
                                $files = is_array($record->verification_2_files) ? $record->verification_2_files : json_decode($record->verification_2_files, true) ?? [];
                                foreach ($files as $i => $file) {
                                    $path = Storage::disk('public')->path($file);
                                    if (file_exists($path)) {
                                        $zip->addFile($path, 'Proof2/' . basename($file));
                                        $fileCount++;
                                    }
                                }
                            }

                            $zip->close();

                            if ($fileCount === 0) {
                                @unlink($zipPath);
                                Notification::make()->title('No Files')->body('No proof files found')->warning()->send();
                                return;
                            }

                            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
                        }),

                    ViewAction::make()
                        ->label('Details'),
                    EditAction::make()
                        ->label('Edit'),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Download Proofs as ZIP
                    BulkAction::make('download_proofs')
                        ->label('📥 Download Proofs (ZIP)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Download Proofs')
                        ->modalDescription('Download all proof images from selected records as a ZIP file. Files will be organized by Proof1 and Proof2 folders.')
                        ->action(function (Collection $records) {
                            $zipFileName = 'proofs_' . now()->format('Y-m-d_His') . '.zip';
                            $zipPath = storage_path('app/temp/' . $zipFileName);

                            // Ensure temp directory exists
                            if (!file_exists(storage_path('app/temp'))) {
                                mkdir(storage_path('app/temp'), 0755, true);
                            }

                            $zip = new \ZipArchive();
                            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Cannot create ZIP file')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $fileCount = 0;

                            foreach ($records as $record) {
                                $userName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $record->user->name ?? 'unknown');
                                $taskId = $record->task_id;
                                $recordId = $record->id;

                                // Process Proof 1 files
                                if ($record->verification_1_files) {
                                    $files = is_array($record->verification_1_files)
                                        ? $record->verification_1_files
                                        : json_decode($record->verification_1_files, true) ?? [];

                                    foreach ($files as $index => $file) {
                                        $filePath = Storage::disk('public')->path($file);
                                        if (file_exists($filePath)) {
                                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                                            $zipEntryName = "Proof1/{$userName}_Task{$taskId}_Record{$recordId}_" . ($index + 1) . ".{$ext}";
                                            $zip->addFile($filePath, $zipEntryName);
                                            $fileCount++;
                                        }
                                    }
                                }

                                // Process Proof 2 files
                                if ($record->verification_2_files) {
                                    $files = is_array($record->verification_2_files)
                                        ? $record->verification_2_files
                                        : json_decode($record->verification_2_files, true) ?? [];

                                    foreach ($files as $index => $file) {
                                        $filePath = Storage::disk('public')->path($file);
                                        if (file_exists($filePath)) {
                                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                                            $zipEntryName = "Proof2/{$userName}_Task{$taskId}_Record{$recordId}_" . ($index + 1) . ".{$ext}";
                                            $zip->addFile($filePath, $zipEntryName);
                                            $fileCount++;
                                        }
                                    }
                                }
                            }

                            $zip->close();

                            if ($fileCount === 0) {
                                @unlink($zipPath);
                                Notification::make()
                                    ->title('No Files')
                                    ->body('No proof files found in selected records')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            Notification::make()
                                ->title('ZIP Created')
                                ->body("{$fileCount} files packaged. Download will start...")
                                ->success()
                                ->send();

                            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
                        }),

                    // Bulk Approve Verification 1
                    BulkAction::make('bulk_approve_v1')
                        ->label('Bulk Approve V1')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Bulk Approve Verification 1')
                        ->modalDescription('This will approve all selected Verification 1 submissions.')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (
                                    $record->status === UserTask::STATUS_PENDING_VERIFICATION_1 &&
                                    $record->verification_1_status &&
                                    str_contains($record->verification_1_status, 'Submitted')
                                ) {
                                    $record->update([
                                        'status' => UserTask::STATUS_PENDING_VERIFICATION_2,
                                        'verification_1_status' => $record->verification_1_status . ' - Approved by admin at ' . now()->format('Y-m-d H:i:s'),
                                        'verification_1_approved_by' => Auth::id(),
                                        'verification_1_approved_at' => now(),
                                    ]);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title('Bulk Approve Completed')
                                ->body("{$count} verification 1 submissions approved")
                                ->success()
                                ->send();
                        }),

                    // Bulk Approve Verification 2
                    BulkAction::make('bulk_approve_v2')
                        ->label('Bulk Approve V2 & Complete')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->form([
                            TextInput::make('default_payment')
                                ->label('Default Payment Amount (Applied to all)')
                                ->required()
                                ->numeric()
                                ->step(0.01)
                                ->prefix('Rp')
                                ->placeholder('Enter default payment amount'),
                        ])
                        ->modalHeading('Bulk Complete Tasks')
                        ->modalDescription('Set payment amount for all selected tasks.')
                        ->action(function (Collection $records, array $data) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (
                                    $record->status === UserTask::STATUS_PENDING_VERIFICATION_2 &&
                                    $record->verification_2_status &&
                                    str_contains($record->verification_2_status, 'Submitted')
                                ) {
                                    $record->update([
                                        'status' => UserTask::STATUS_COMPLETED,
                                        'verification_2_status' => $record->verification_2_status . ' - Approved by admin at ' . now()->format('Y-m-d H:i:s'),
                                        'verification_2_approved_by' => Auth::id(),
                                        'verification_2_approved_at' => now(),
                                        'completed_at' => now(),
                                        'payment_amount' => $data['default_payment'],
                                        'payment_status' => UserTask::PAYMENT_PENDING,
                                        'payment_verified_by_admin_id' => Auth::id(),
                                        'payment_verified_at' => now(),
                                    ]);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title('Bulk Complete Success')
                                ->body("{$count} tasks completed with payment Rp " . number_format($data['default_payment'], 0, ',', '.'))
                                ->success()
                                ->send();
                        }),

                    // Bulk Mark Payment Success
                    BulkAction::make('bulk_mark_paid')
                        ->label('Bulk Mark Paid')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Bulk Mark Payments as Success')
                        ->modalDescription('Mark all selected completed tasks as paid.')
                        ->action(function (Collection $records) {
                            $count = 0;
                            $totalAmount = 0;
                            foreach ($records as $record) {
                                if (
                                    $record->status === UserTask::STATUS_COMPLETED &&
                                    $record->payment_status === UserTask::PAYMENT_PENDING
                                ) {
                                    $record->update([
                                        'payment_status' => UserTask::PAYMENT_SUCCESS,
                                        'payment_verified_by_admin_id' => Auth::id(),
                                        'payment_verified_at' => now(),
                                    ]);
                                    $count++;
                                    $totalAmount += $record->payment_amount ?? 0;
                                }
                            }

                            Notification::make()
                                ->title('Bulk Payment Success')
                                ->body("{$count} payments marked as success. Total: Rp " . number_format($totalAmount, 0, ',', '.'))
                                ->success()
                                ->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserTasks::route('/'),
            'view' => ViewUserTask::route('/{record}'),
            'edit' => EditUserTask::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', [
            UserTask::STATUS_PENDING_VERIFICATION_1,
            UserTask::STATUS_PENDING_VERIFICATION_2
        ])->count();
    }
}
