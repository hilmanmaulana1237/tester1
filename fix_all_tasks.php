<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING ALL TASKS ===\n\n";

// Get all tasks with 'taken' status
$takenTasks = \App\Models\UserTask::where('status', 'taken')->get();

echo "Found " . $takenTasks->count() . " tasks with status 'taken'\n\n";

foreach ($takenTasks as $task) {
    $oldTakenAt = $task->taken_at;
    $task->update(['taken_at' => now()]);
    
    echo "✅ Task ID {$task->id}:\n";
    echo "   Old taken_at: {$oldTakenAt}\n";
    echo "   New taken_at: {$task->fresh()->taken_at}\n";
    echo "   New deadline: " . $task->fresh()->taken_at->copy()->addMinutes(10) . "\n\n";
}

echo "=== DONE ===\n";
