<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserTask;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;

class FailOverdueUserTasks extends Command
{
    protected $signature = 'tasks:fail-overdue';
    protected $description = 'Mark overdue user tasks as failed/gugur and clean caches.';

    public function handle()
    {
        $this->info('Starting to fail overdue user tasks...');

        $count = 0;

        UserTask::whereIn('status', [
            UserTask::STATUS_TAKEN,
            UserTask::STATUS_PENDING_VERIFICATION_1,
            UserTask::STATUS_PENDING_VERIFICATION_2,
        ])
            ->whereNotNull('deadline_at')
            ->where('deadline_at', '<=', now())
            ->chunkById(100, function ($tasks) use (&$count) {
                foreach ($tasks as $userTask) {
                    try {
                        DB::beginTransaction();

                        $userTask->update([
                            'status' => UserTask::STATUS_FAILED,
                            'failed_count' => ($userTask->failed_count ?? 0) + 1,
                            'cancelled_at' => now(),
                            'verification_1_status' => 'Failed: Deadline passed',
                        ]);

                        // Clear caches related to tasks and the user
                        CacheService::clearTaskCache();
                        CacheService::forget(CacheService::userKey($userTask->user_id, 'tasks'));
                        CacheService::forget(CacheService::userKey($userTask->user_id, 'active_tasks'));
                        CacheService::forget(CacheService::userKey($userTask->user_id, 'my_active_tasks'));
                        CacheService::forget(CacheService::userKey($userTask->user_id, 'my_active_tasks_count'));
                        CacheService::forget(CacheService::userKey($userTask->user_id, 'dashboard_stats'));
                        CacheService::forget('available_tasks_count');

                        DB::commit();
                        $count++;
                    } catch (\Exception $e) {
                        DB::rollBack();
                        \Log::error('Failed marking overdue userTask id=' . $userTask->id . ' error:' . $e->getMessage());
                    }
                }
            });

        $this->info("Done. Marked {$count} overdue tasks as failed.");

        return 0;
    }
}
