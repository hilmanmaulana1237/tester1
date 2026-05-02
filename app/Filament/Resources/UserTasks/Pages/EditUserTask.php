<?php

namespace App\Filament\Resources\UserTasks\Pages;

use App\Filament\Resources\UserTasks\UserTaskResource;
use App\Models\UserTask;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUserTask extends EditRecord
{
    protected static string $resource = UserTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),

            // Custom actions for verification
            Actions\Action::make('approve_verification_1')
                ->label('Approve Verification 1')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->status === UserTask::STATUS_PENDING_VERIFICATION_1)
                ->action(function () {
                    $this->record->update([
                        'status' => UserTask::STATUS_PENDING_VERIFICATION_2,
                        'verification_1_status' => 'Approved by admin at ' . now()->format('Y-m-d H:i:s'),
                        'verification_1_approved_by' => Auth::id(),
                        'verification_1_approved_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Verification 1 Approved')
                        ->body('Task has been moved to verification 2 stage.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('reject_verification_1')
                ->label('Reject Verification 1')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn() => $this->record->status === UserTask::STATUS_PENDING_VERIFICATION_1)
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => UserTask::STATUS_FAILED,
                        'failed_count' => $this->record->failed_count + 1,
                        'verification_1_status' => 'Rejected by admin at ' . now()->format('Y-m-d H:i:s') . '. Task returned to available pool due to verification 1 failure.',
                        'cancelled_at' => now(), // Mark as returned to pool
                    ]);

                    Notification::make()
                        ->title('Verification 1 Rejected')
                        ->body('Task has been returned to available pool.')
                        ->warning()
                        ->send();
                }),

            Actions\Action::make('approve_verification_2')
                ->label('Approve & Complete')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->status === UserTask::STATUS_PENDING_VERIFICATION_2 &&
                    $this->record->verification_2_status &&
                    str_contains($this->record->verification_2_status, 'Submitted'))
                ->action(function () {
                    $this->record->update([
                        'status' => UserTask::STATUS_COMPLETED,
                        'verification_2_approved_by' => Auth::id(),
                        'verification_2_approved_at' => now(),
                        'completed_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Task Completed')
                        ->body('Task has been successfully completed!')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('reject_verification_2')
                ->label('Reject Verification 2')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn() => $this->record->status === UserTask::STATUS_PENDING_VERIFICATION_2 &&
                    $this->record->verification_2_status &&
                    str_contains($this->record->verification_2_status, 'Submitted'))
                ->requiresConfirmation()
                ->action(function () {
                    // Mark task as failed and make it available again
                    $this->record->update([
                        'status' => UserTask::STATUS_FAILED,
                        'failed_count' => $this->record->failed_count + 1,
                        'verification_2_status' => 'Rejected by admin at ' . now()->format('Y-m-d H:i:s') . '. Task returned to available pool. Reason: Stage 2 verification failed.',
                        'cancelled_at' => now(), // Mark as cancelled so task becomes available
                    ]);

                    Notification::make()
                        ->title('Verification 2 Rejected')
                        ->body('Task has been rejected and returned to available tasks.')
                        ->warning()
                        ->send();
                }),

            Actions\Action::make('mark_payment_success')
                ->label('Mark Payment Success')
                ->icon('heroicon-o-currency-dollar')
                ->color('success')
                ->visible(fn() => $this->record->status === UserTask::STATUS_COMPLETED && $this->record->payment_status !== UserTask::PAYMENT_SUCCESS)
                ->action(function () {
                    $this->record->update([
                        'payment_status' => UserTask::PAYMENT_SUCCESS,
                        'payment_verified_by_admin_id' => Auth::id(),
                    ]);

                    Notification::make()
                        ->title('Payment Approved')
                        ->body('Payment has been marked as successful.')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Auto-set payment verifier when payment status changes to success
        if ($data['payment_status'] === UserTask::PAYMENT_SUCCESS && !$data['payment_verified_by_admin_id']) {
            $data['payment_verified_by_admin_id'] = Auth::id();
        }

        return $data;
    }
}
