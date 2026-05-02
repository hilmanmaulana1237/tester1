<?php

namespace App\Observers;

use App\Models\UserTask;

class UserTaskObserver
{
    /**
     * Handle the UserTask "updated" event.
     * This will trigger badge update when task is completed with successful payment.
     */
    public function updated(UserTask $userTask): void
    {
        // Check if this update involves marking payment as success for a completed task
        if (
            $userTask->status === UserTask::STATUS_COMPLETED &&
            $userTask->payment_status === 'success' &&
            $userTask->wasChanged('payment_status')
        ) {

            // Update the user's badge based on their new total earnings
            $userTask->user->updateBadgeBasedOnEarnings();
        }
    }
}
