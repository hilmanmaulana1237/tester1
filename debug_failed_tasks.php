<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserTask;

$failedTasks = UserTask::where('status', 'failed')->get();

echo "=== DEBUG FAILED TASKS (FIXED VERSION) ===" . PHP_EOL;
echo "Total failed tasks: " . $failedTasks->count() . PHP_EOL . PHP_EOL;

foreach ($failedTasks as $task) {
    echo "Task ID: {$task->id}" . PHP_EOL;
    echo "User ID: {$task->user_id}" . PHP_EOL;
    echo "Status: {$task->status}" . PHP_EOL;
    echo "Cancelled At: " . ($task->cancelled_at ? $task->cancelled_at : 'NULL') . PHP_EOL;
    echo "Verification 1: " . ($task->verification_1_status ?: 'NULL') . PHP_EOL;
    echo "Verification 2: " . ($task->verification_2_status ?: 'NULL') . PHP_EOL;

    // Test the NEW condition (without cancelled_at requirement)
    $isRejected = $task->status === UserTask::STATUS_FAILED &&
        (
            ($task->verification_1_status &&
                strpos($task->verification_1_status, 'Rejected by admin') !== false) ||
            ($task->verification_2_status &&
                strpos($task->verification_2_status, 'Rejected by admin') !== false)
        );

    echo "Is Rejected And Cancelled (NEW): " . ($isRejected ? 'TRUE' : 'FALSE') . PHP_EOL;

    // Test feedback extraction with new pattern
    if ($task->verification_2_status && strpos($task->verification_2_status, 'Rejected by admin') !== false) {
        preg_match('/Rejected by admin at .+?\. (.+)$/', $task->verification_2_status, $matches);
        $feedback = isset($matches[1]) ? $matches[1] : 'No feedback found';
        echo "Feedback (from v2): " . $feedback . PHP_EOL;
    } elseif ($task->verification_1_status && strpos($task->verification_1_status, 'Rejected by admin') !== false) {
        preg_match('/Rejected by admin at .+?\. (.+)$/', $task->verification_1_status, $matches);
        $feedback = isset($matches[1]) ? $matches[1] : 'No feedback found';
        echo "Feedback (from v1): " . $feedback . PHP_EOL;
    }

    echo "---" . PHP_EOL . PHP_EOL;
}
