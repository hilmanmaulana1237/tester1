<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserTask;
use App\Models\Task;
use App\Livewire\TaskWorkWizard;

// Find a failed UserTask to test
$failedUserTask = UserTask::where('status', 'failed')->first();

if (!$failedUserTask) {
    echo "No failed tasks found!" . PHP_EOL;
    exit;
}

echo "=== TESTING WIZARD FOR FAILED TASK ===" . PHP_EOL;
echo "UserTask ID: {$failedUserTask->id}" . PHP_EOL;
echo "Task ID: {$failedUserTask->task_id}" . PHP_EOL;
echo "Status: {$failedUserTask->status}" . PHP_EOL;
echo "User ID: {$failedUserTask->user_id}" . PHP_EOL . PHP_EOL;

// Get the task
$task = Task::find($failedUserTask->task_id);

if (!$task) {
    echo "Task not found!" . PHP_EOL;
    exit;
}

echo "Task Title: {$task->title}" . PHP_EOL . PHP_EOL;

// Test the TaskWorkWizard logic manually
echo "=== TESTING WIZARD METHODS ===" . PHP_EOL;

// Test isTaskRejectedAndCancelled logic
$isRejected = $failedUserTask->status === UserTask::STATUS_FAILED &&
    (
        ($failedUserTask->verification_1_status &&
            strpos($failedUserTask->verification_1_status, 'Rejected by admin') !== false) ||
        ($failedUserTask->verification_2_status &&
            strpos($failedUserTask->verification_2_status, 'Rejected by admin') !== false)
    );

echo "isTaskRejectedAndCancelled: " . ($isRejected ? 'TRUE' : 'FALSE') . PHP_EOL;

// Test currentStep logic
$currentStep = 1;
switch ($failedUserTask->status) {
    case UserTask::STATUS_FAILED:
        if (
            ($failedUserTask->verification_1_status && strpos($failedUserTask->verification_1_status, 'Rejected by admin') !== false) ||
            ($failedUserTask->verification_2_status && strpos($failedUserTask->verification_2_status, 'Rejected by admin') !== false)
        ) {
            $currentStep = 4; // Show final step with feedback
        } else {
            if ($failedUserTask->verification_2_status && strpos($failedUserTask->verification_2_status, 'Rejected') !== false) {
                $currentStep = 3; // Failed at stage 2
            } else {
                $currentStep = 2; // Failed at stage 1
            }
        }
        break;
}

echo "Calculated currentStep: {$currentStep}" . PHP_EOL;

// Test feedback extraction
$feedback = null;
if ($failedUserTask->verification_2_status && strpos($failedUserTask->verification_2_status, 'Rejected by admin') !== false) {
    preg_match('/Rejected by admin at .+?\. (.+)$/', $failedUserTask->verification_2_status, $matches);
    $feedback = isset($matches[1]) ? $matches[1] : 'Tidak ada detail feedback.';
} elseif ($failedUserTask->verification_1_status && strpos($failedUserTask->verification_1_status, 'Rejected by admin') !== false) {
    preg_match('/Rejected by admin at .+?\. (.+)$/', $failedUserTask->verification_1_status, $matches);
    $feedback = isset($matches[1]) ? $matches[1] : 'Tidak ada detail feedback.';
}

echo "Extracted feedback: " . ($feedback ?: 'No feedback found') . PHP_EOL . PHP_EOL;

echo "=== EXPECTED BEHAVIOR ===" . PHP_EOL;
echo "- User clicks 'View Details' in history" . PHP_EOL;
echo "- Wizard should show Step 4 (currentStep = 4)" . PHP_EOL;
echo "- Step 4 should show rejection feedback with admin message" . PHP_EOL;
echo "- isTaskRejectedAndCancelled() should return TRUE" . PHP_EOL;
echo "- getRejectionFeedback() should return: '{$feedback}'" . PHP_EOL;
