<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserTask;
use App\Models\Task;

echo "=== TESTING CORRECT FAILED TASK URL ===" . PHP_EOL;

$taskId = 2; // The correct task ID with failed status
$userId = 2;

$task = Task::find($taskId);
$userTask = UserTask::where('task_id', $taskId)->where('user_id', $userId)->first();

echo "Task ID: {$taskId}" . PHP_EOL;
echo "User ID: {$userId}" . PHP_EOL;
echo "Task Title: {$task->title}" . PHP_EOL;
echo "UserTask Status: {$userTask->status}" . PHP_EOL;
echo "Verification 1 Status: " . ($userTask->verification_1_status ?: 'NULL') . PHP_EOL;
echo "Verification 2 Status: " . ($userTask->verification_2_status ?: 'NULL') . PHP_EOL;

// Test mount logic
$isRejected = $userTask->status === UserTask::STATUS_FAILED &&
    (
        ($userTask->verification_1_status && strpos($userTask->verification_1_status, 'Rejected by admin') !== false) ||
        ($userTask->verification_2_status && strpos($userTask->verification_2_status, 'Rejected by admin') !== false)
    );

$isCompleted = $userTask->status === UserTask::STATUS_COMPLETED &&
    $userTask->completed_at !== null;

echo PHP_EOL . "=== MOUNT LOGIC TEST ===" . PHP_EOL;
echo "Is Task Rejected: " . ($isRejected ? 'TRUE' : 'FALSE') . PHP_EOL;
echo "Is Task Completed: " . ($isCompleted ? 'TRUE' : 'FALSE') . PHP_EOL;

if (!$userTask) {
    echo "❌ MOUNT RESULT: Redirect to dashboard (no UserTask)" . PHP_EOL;
} elseif ($isRejected) {
    echo "✅ MOUNT RESULT: Should show Step 4 with rejection feedback" . PHP_EOL;
} elseif ($isCompleted) {
    echo "❌ MOUNT RESULT: Redirect to dashboard (completed by admin)" . PHP_EOL;
} else {
    echo "✅ MOUNT RESULT: Continue with normal workflow" . PHP_EOL;
}

// Test feedback extraction
if ($userTask->verification_1_status && strpos($userTask->verification_1_status, 'Rejected by admin') !== false) {
    preg_match('/Rejected by admin at .+?\. (.+)$/', $userTask->verification_1_status, $matches);
    $feedback = isset($matches[1]) ? $matches[1] : 'No feedback';
    echo "Extracted feedback: {$feedback}" . PHP_EOL;
}

echo PHP_EOL . "=== CORRECT URL TO TEST ===" . PHP_EOL;
echo "✅ Use this URL: http://template_design.test/user/task/2/work" . PHP_EOL;
echo "❌ NOT this URL: http://template_design.test/user/task/24/work (no UserTask exists)" . PHP_EOL;
