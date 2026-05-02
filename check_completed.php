<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UserTask;

echo "=== Completed Tasks ===" . PHP_EOL;
$completed = UserTask::where('status', 'completed')->get();
foreach ($completed as $task) {
    echo "UserTask {$task->id} - Task {$task->task_id} - User {$task->user_id} - Status: {$task->status} - Payment: " . ($task->payment_status ?: 'null') . PHP_EOL;
}

if ($completed->isEmpty()) {
    echo "No completed tasks found." . PHP_EOL;
}
