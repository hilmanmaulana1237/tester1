<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserTask;
use App\Models\User;
use App\Models\Task;

// Create a test scenario: user takes task, submits proof, then admin rejects
$user = User::first();
$task = Task::first();

if (!$user || !$task) {
    echo "No user or task found in database\n";
    exit;
}

echo "Creating test rejection scenario...\n";

// Create a UserTask with submitted proof that will be rejected
$userTask = UserTask::create([
    'task_id' => $task->id,
    'user_id' => $user->id,
    'status' => UserTask::STATUS_PENDING_VERIFICATION_1,
    'taken_at' => now(),
    'deadline_at' => now()->addDays(3),
    'verification_1_status' => 'Submitted at ' . now()->format('Y-m-d H:i:s') . '. Files: test.jpg. Description: Test proof submission for rejection testing.',
]);

echo "Created UserTask id={$userTask->id} with pending verification 1\n";

// Simulate admin rejection (like in RelationManager)
$userTask->update([
    'status' => UserTask::STATUS_CANCELLED,
    'cancelled_at' => now(),
    'verification_1_status' => 'Rejected by admin at ' . now()->format('Y-m-d H:i:s') . '. Reason: Proof quality is not sufficient. Please provide clearer images and more detailed description.',
    'verification_2_status' => null,
    'verification_1_approved_at' => null,
    'verification_2_approved_at' => null,
    'verification_1_approved_by' => null,
    'verification_2_approved_by' => null,
    'payment_status' => UserTask::PAYMENT_PENDING,
    'payment_amount' => null,
    'payment_verified_by_admin_id' => null,
    'payment_verified_at' => null,
]);

// Mark other active user tasks for the same task as failed (simulating RelationManager behavior)
UserTask::where('task_id', $userTask->task_id)
    ->whereIn('status', [UserTask::STATUS_TAKEN, UserTask::STATUS_PENDING_VERIFICATION_1, UserTask::STATUS_PENDING_VERIFICATION_2])
    ->where('id', '!=', $userTask->id)
    ->update([
        'status' => UserTask::STATUS_FAILED,
        'failed_count' => \Illuminate\Support\Facades\DB::raw('COALESCE(failed_count, 0) + 1'),
        'verification_1_status' => null,
        'verification_2_status' => null,
        'verification_1_approved_at' => null,
        'verification_2_approved_at' => null,
        'verification_1_approved_by' => null,
        'verification_2_approved_by' => null,
    ]);

echo "Simulated admin rejection complete\n";
echo "UserTask status: {$userTask->fresh()->status}\n";
echo "Rejection feedback: {$userTask->fresh()->verification_1_status}\n";

// Test feedback extraction
$rejectionPattern = '/Reason: (.+)$/';
preg_match($rejectionPattern, $userTask->fresh()->verification_1_status, $matches);
$feedback = isset($matches[1]) ? $matches[1] : 'No feedback found';
echo "Extracted feedback: {$feedback}\n";
