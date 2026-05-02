<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use App\Models\UserTask;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Resources\RelationManagers\RelationManager;

class UserTasksRelationManager extends RelationManager
{
    protected static string $relationship = 'userTasks';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'User Tasks Management';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            // Form tidak diperlukan untuk relation manager ini
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('taken_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width('60px'),

                TextColumn::make('task.title')
                    ->label('Task')
                    ->searchable()
                    ->wrap()
                    ->limit(40),

                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->wrap(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'taken',
                        'info' => 'pending_verification_1',
                        'primary' => 'pending_verification_2',
                        'success' => 'completed',
                        'danger' => ['rejected', 'failed'],
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'taken',
                        'heroicon-o-eye' => 'pending_verification_1',
                        'heroicon-o-document-check' => 'pending_verification_2',
                        'heroicon-o-check-circle' => 'completed',
                        'heroicon-o-x-circle' => ['rejected', 'failed'],
                    ]),

                BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'success',
                        'danger' => 'failed',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'success',
                        'heroicon-o-x-circle' => 'failed',
                    ])
                    ->placeholder('Not Set'),

                TextColumn::make('payment_amount')
                    ->label('Amount')
                    ->money('IDR', true)
                    ->placeholder('-'),

                TextColumn::make('taken_at')
                    ->label('Taken')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('submitted_at_1')
                    ->label('Proof 1 Submitted')
                    ->getStateUsing(function (UserTask $record): ?string {
                        if ($record->verification_1_status && str_contains($record->verification_1_status, 'Submitted at')) {
                            preg_match('/Submitted at (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $record->verification_1_status, $matches);
                            if (isset($matches[1])) {
                                return \Carbon\Carbon::parse($matches[1])->format('d M Y H:i');
                            }
                        }
                        return null;
                    })
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('submitted_at_2')
                    ->label('Proof 2 Submitted')
                    ->getStateUsing(function (UserTask $record): ?string {
                        if ($record->verification_2_status && str_contains($record->verification_2_status, 'Submitted at')) {
                            preg_match('/Submitted at (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $record->verification_2_status, $matches);
                            if (isset($matches[1])) {
                                return \Carbon\Carbon::parse($matches[1])->format('d M Y H:i');
                            }
                        }
                        return null;
                    })
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'taken' => 'Taken',
                        'pending_verification_1' => 'Pending Verification 1',
                        'pending_verification_2' => 'Pending Verification 2',
                        'completed' => 'Completed',
                        'rejected' => 'Rejected',
                        'failed' => 'Failed',
                    ])
                    ->multiple(),

                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ]),

                Filter::make('needs_verification')
                    ->label('Needs Verification')
                    ->query(fn(Builder $query) => $query->whereIn('status', [
                        'pending_verification_1',
                        'pending_verification_2'
                    ])),

                Filter::make('completed_awaiting_payment')
                    ->label('Awaiting Payment')
                    ->query(fn(Builder $query) => $query->where('status', 'completed')
                        ->where('payment_status', 'pending')),
            ])
            ->headerActions([
                // Header actions tidak diperlukan untuk relation manager ini
            ])
            ->actions([
                // View Proofs (Combined Proof 1 & 2)
                Action::make('view_proofs')
                    ->label('👁️ View Proofs')
                    ->icon('heroicon-o-document-text')
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

                            if (preg_match('/Submitted at ([\d\-: ]+)/', $status, $matches)) {
                                $data['submittedAt'] = $matches[1];
                            }

                            // Read files from verification_x_files column
                            // Laravel Model Cast already decodes JSON to array
                            if ($filesJson) {
                                $data['files'] = is_array($filesJson) ? $filesJson : json_decode($filesJson, true) ?? [];
                            }

                            if (preg_match('/Description: ([^-]+?)(?:\s*-\s*(?:Approved|Rejected)|$)/', $status, $matches)) {
                                $data['description'] = trim($matches[1]);
                            }

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
                                width: 100%;
                                height: 180px;
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
                            .proof-card {
                                border: 2px solid #e5e7eb;
                                border-radius: 12px;
                                overflow: hidden;
                                background: white;
                            }
                            .dark .proof-card {
                                border-color: #374151;
                                background: #1f2937;
                            }
                            .file-item {
                                border: 2px solid #e5e7eb;
                                border-radius: 8px;
                                overflow: hidden;
                                transition: all 0.2s;
                            }
                            .file-item:hover {
                                border-color: #10b981;
                                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
                            }
                            .dark .file-item {
                                border-color: #374151;
                            }
                            .dark .file-item:hover {
                                border-color: #10b981;
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

                        $content .= '</div>';
                        return new \Illuminate\Support\HtmlString($content);
                    })
                    ->modalWidth('4xl'),

                // Approve Proof 1
                Action::make('approve_proof_1')
                    ->label('Approve P1')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (UserTask $record) {
                        $record->update([
                            'status' => 'pending_verification_2',
                            'verification_1_approved_at' => now(),
                            'verification_1_approved_by' => Auth::id(),
                            'verification_1_status' => $record->verification_1_status . ' - Approved by admin at ' . now()->format('Y-m-d H:i:s'),
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
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Approve Proof 1')
                    ->modalDescription('Are you sure you want to approve this proof? User will be allowed to submit proof 2.')
                    ->visible(
                        fn(UserTask $record): bool =>
                        $record->status === 'pending_verification_1' &&
                        $record->verification_1_status &&
                        str_contains($record->verification_1_status, 'Submitted at') &&
                        !str_contains($record->verification_1_status, 'Approved by admin') &&
                        !str_contains($record->verification_1_status, 'Rejected by admin')
                    ),

                // Reject Proof 1
                Action::make('reject_proof_1')
                    ->label('Reject P1')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Textarea::make('feedback_1')
                            ->label('Rejection Reason')
                            ->required()
                            ->placeholder('Explain why this proof is rejected...')
                            ->maxLength(500),
                    ])
                    ->action(function (UserTask $record, array $data) {
                        // Mark this user task as failed (not cancelled) so it shows in history properly
                        $record->update([
                            'status' => UserTask::STATUS_FAILED,
                            'cancelled_at' => now(), // Keep cancelled_at for tracking when it was rejected
                            'verification_1_status' => 'Rejected by admin at ' . now()->format('Y-m-d H:i:s') . '. Reason: ' . $data['feedback_1'],
                            'verification_2_status' => null,
                            'verification_1_approved_at' => null,
                            'verification_2_approved_at' => null,
                            'verification_1_approved_by' => null,
                            'verification_2_approved_by' => null,
                            'payment_status' => UserTask::PAYMENT_PENDING,
                            'payment_amount' => null,
                            'payment_verified_by_admin_id' => null,
                            'payment_verified_at' => null,
                            'failed_count' => ($record->failed_count ?? 0) + 1,
                        ]);

                        // Mark any other active user tasks for the same task as failed so the task is free
                        UserTask::where('task_id', $record->task_id)
                            ->whereIn('status', [UserTask::STATUS_TAKEN, UserTask::STATUS_PENDING_VERIFICATION_1, UserTask::STATUS_PENDING_VERIFICATION_2])
                            ->where('id', '!=', $record->id)
                            ->update([
                                'status' => UserTask::STATUS_FAILED,
                                'verification_1_status' => null,
                                'verification_2_status' => null,
                                'verification_1_approved_at' => null,
                                'verification_2_approved_at' => null,
                                'verification_1_approved_by' => null,
                                'verification_2_approved_by' => null,
                            ]);

                        Notification::make()
                            ->title('Proof 1 Rejected')
                            ->body('Task marked as failed and returned to available pool')
                            ->warning()
                            ->send();

                        // Notify user
                        \App\Services\NotificationService::create(
                            $record->user_id,
                            'Proof 1 Ditolak',
                            'Proof 1 untuk "' . $record->task->title . '" ditolak. Alasan: ' . $data['feedback_1'],
                            'error',
                            '/user/my-tasks'
                        );
                    })
                    ->modalHeading('Reject Proof 1')
                    ->modalDescription('Please provide feedback for the user about why this proof was rejected.')
                    ->visible(
                        fn(UserTask $record): bool =>
                        $record->status === 'pending_verification_1' &&
                        $record->verification_1_status &&
                        str_contains($record->verification_1_status, 'Submitted at') &&
                        !str_contains($record->verification_1_status, 'Approved by admin') &&
                        !str_contains($record->verification_1_status, 'Rejected by admin')
                    ),

                // Approve Proof 2 & Complete Task
                Action::make('approve_proof_2')
                    ->label('Approve P2')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
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
                        TextInput::make('payment_amount')
                            ->label('Payment Amount')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->prefix('Rp')
                            ->placeholder('Enter payment amount')
                            ->default(fn(UserTask $record) => $record->task->base_reward ?? 0),
                    ])
                    ->action(function (UserTask $record, array $data) {
                        $record->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                            'verification_2_approved_at' => now(),
                            'verification_2_approved_by' => Auth::id(),
                            'verification_2_status' => $record->verification_2_status . ' - Approved by admin at ' . now()->format('Y-m-d H:i:s'),
                            'payment_amount' => $data['payment_amount'],
                            'payment_status' => 'pending',
                            'payment_verified_by_admin_id' => Auth::id(),
                            'payment_verified_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Task Completed')
                            ->body('Task completed with payment amount: Rp ' . number_format($data['payment_amount'], 0, ',', '.'))
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
                    })
                    ->modalHeading('Complete Task')
                    ->modalDescription('This will approve proof 2 and mark the task as completed. Please set the payment amount.')
                    ->visible(
                        fn(UserTask $record): bool =>
                        $record->status === 'pending_verification_2' &&
                        $record->verification_2_status &&
                        str_contains($record->verification_2_status, 'Submitted at') &&
                        !str_contains($record->verification_2_status, 'Approved by admin') &&
                        !str_contains($record->verification_2_status, 'Rejected by admin')
                    ),

                // Reject Proof 2
                Action::make('reject_proof_2')
                    ->label('Reject P2')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Textarea::make('feedback_2')
                            ->label('Rejection Reason')
                            ->required()
                            ->placeholder('Explain why this proof is rejected...')
                            ->maxLength(500),
                    ])
                    ->action(function (UserTask $record, array $data) {
                        // Mark as failed (not cancelled) so it appears correctly in history
                        $record->update([
                            'status' => UserTask::STATUS_FAILED,
                            'cancelled_at' => now(), // Keep cancelled_at for tracking when it was rejected
                            'verification_2_status' => 'Rejected by admin at ' . now()->format('Y-m-d H:i:s') . '. Reason: ' . $data['feedback_2'],
                            // Keep verification_1_status as it was already approved
                            'verification_1_approved_at' => $record->verification_1_approved_at,
                            'verification_2_approved_at' => null,
                            'verification_1_approved_by' => $record->verification_1_approved_by,
                            'verification_2_approved_by' => null,
                            'payment_status' => UserTask::PAYMENT_PENDING,
                            'payment_amount' => null,
                            'payment_verified_by_admin_id' => null,
                            'payment_verified_at' => null,
                            'failed_count' => ($record->failed_count ?? 0) + 1,
                        ]);

                        // Mark other active user tasks for the same task as failed
                        UserTask::where('task_id', $record->task_id)
                            ->whereIn('status', [UserTask::STATUS_TAKEN, UserTask::STATUS_PENDING_VERIFICATION_1, UserTask::STATUS_PENDING_VERIFICATION_2])
                            ->where('id', '!=', $record->id)
                            ->update([
                                'status' => UserTask::STATUS_FAILED,
                                'verification_1_status' => null,
                                'verification_2_status' => null,
                                'verification_1_approved_at' => null,
                                'verification_2_approved_at' => null,
                                'verification_1_approved_by' => null,
                                'verification_2_approved_by' => null,
                            ]);

                        Notification::make()
                            ->title('Proof 2 Rejected')
                            ->body('Task marked as failed and returned to available pool')
                            ->warning()
                            ->send();

                        // Notify user
                        \App\Services\NotificationService::create(
                            $record->user_id,
                            'Proof 2 Ditolak',
                            'Proof 2 untuk "' . $record->task->title . '" ditolak. Alasan: ' . $data['feedback_2'],
                            'error',
                            '/user/my-tasks'
                        );
                    })
                    ->modalHeading('Reject Proof 2')
                    ->modalDescription('Please provide feedback for the user about why this proof was rejected.')
                    ->visible(
                        fn(UserTask $record): bool =>
                        $record->status === 'pending_verification_2' &&
                        $record->verification_2_status &&
                        str_contains($record->verification_2_status, 'Submitted at') &&
                        !str_contains($record->verification_2_status, 'Approved by admin') &&
                        !str_contains($record->verification_2_status, 'Rejected by admin')
                    ),

                // Mark Payment Success
                Action::make('mark_payment_success')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
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

                                if ($record->payment_amount) {
                                    $html .= '<div class="flex items-center gap-2 mt-3 pt-3 border-t"><span>💰</span><span class="text-lg font-bold text-green-600 dark:text-green-400">Rp ' . number_format($record->payment_amount, 0, ',', '.') . '</span></div>';
                                }

                                $html .= '</div>';
                                return new \Illuminate\Support\HtmlString($html);
                            })
                            ->columnSpanFull(),
                    ])
                    ->action(function (UserTask $record) {
                        $record->update([
                            'payment_status' => 'success',
                        ]);

                        Notification::make()
                            ->title('Payment Marked as Success')
                            ->body('Payment has been processed successfully')
                            ->success()
                            ->send();

                        // Notify user
                        \App\Services\NotificationService::create(
                            $record->user_id,
                            'Pembayaran Berhasil',
                            'Pembayaran untuk task "' . $record->task->title . '" telah berhasil dikirim ke E-Wallet Anda.',
                            'success',
                            '/user/task/' . $record->task_id . '/work'
                        );
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Mark Payment as Success')
                    ->modalDescription('Confirm payment details below before marking as paid.')
                    ->visible(
                        fn(UserTask $record): bool =>
                        $record->status === 'completed' && $record->payment_status === 'pending'
                    ),

                // Mark Payment Failed
                Action::make('mark_payment_failed')
                    ->label('Payment Failed')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function (UserTask $record) {
                        $record->update([
                            'payment_status' => 'failed',
                        ]);

                        Notification::make()
                            ->title('Payment Marked as Failed')
                            ->body('Payment has been marked as failed')
                            ->warning()
                            ->send();

                        // Notify user
                        \App\Services\NotificationService::create(
                            $record->user_id,
                            'Pembayaran Gagal',
                            'Pembayaran untuk task "' . $record->task->title . '" gagal diproses.',
                            'error',
                            '/user/task/' . $record->task_id . '/work'
                        );
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Mark Payment as Failed')
                    ->modalDescription('Are you sure you want to mark this payment as failed?')
                    ->visible(
                        fn(UserTask $record): bool =>
                        $record->status === 'completed' && $record->payment_status === 'pending'
                    ),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Bulk Approve Proof 1
                    BulkAction::make('bulk_approve_proof_1')
                        ->label('Approve Selected (Proof 1)')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'pending_verification_1') {
                                    $record->update([
                                        'status' => 'pending_verification_2',
                                        'verification_1_approved_at' => now(),
                                        'verification_1_approved_by' => Auth::id(),
                                    ]);

                                    // Notify user
                                    \App\Services\NotificationService::create(
                                        $record->user_id,
                                        'Proof 1 Disetujui',
                                        'Proof 1 untuk "' . $record->task->title . '" telah disetujui! Silakan submit Proof 2.',
                                        'success',
                                        '/user/task/' . $record->task_id . '/work'
                                    );

                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title("Bulk Approval Completed")
                                ->body("{$count} proof 1 submissions have been approved")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Bulk Approve Proof 1')
                        ->modalDescription('This will approve all selected proof 1 submissions. Are you sure?'),

                    // Bulk Mark Payment Success
                    BulkAction::make('bulk_mark_payment_success')
                        ->label('Mark Payments Success')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'completed' && $record->payment_status === 'pending') {
                                    $record->update([
                                        'payment_status' => 'success',
                                    ]);

                                    // Notify user
                                    \App\Services\NotificationService::create(
                                        $record->user_id,
                                        'Pembayaran Berhasil',
                                        'Pembayaran untuk task "' . $record->task->title . '" telah berhasil dikirim ke E-Wallet Anda.',
                                        'success',
                                        '/user/task/' . $record->task_id . '/work'
                                    );

                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title("Bulk Payment Update Completed")
                                ->body("{$count} payments have been marked as successful")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Bulk Mark Payments as Success')
                        ->modalDescription('This will mark all selected pending payments as successful. Are you sure?'),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
