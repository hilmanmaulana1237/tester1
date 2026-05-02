<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\UserTask;

echo "=== Users ===" . PHP_EOL;
foreach (User::all() as $user) {
    echo "ID: {$user->id}, Email: {$user->email}" . PHP_EOL;
}

echo PHP_EOL . "=== User ID 2 Tasks ===" . PHP_EOL;
$userTasks = UserTask::where('user_id', 2)->get();
foreach ($userTasks as $task) {
    echo "UserTask ID: {$task->id}, Task ID: {$task->task_id}, Status: {$task->status}" . PHP_EOL;
}
