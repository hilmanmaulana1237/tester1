<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CURRENT STATE CHECK ===\n\n";

// Check task 1
$task = \App\Models\UserTask::find(1);

if ($task) {
    echo "Task ID: {$task->id}\n";
    echo "Status: {$task->status}\n";
    echo "Taken at: {$task->taken_at}\n";
    
    if ($task->taken_at) {
        $deadline = $task->taken_at->copy()->addMinutes(10);
        echo "Deadline: {$deadline}\n";
        echo "Current time: " . now() . "\n";
        
        $diff = now()->diffInSeconds($deadline, false);
        echo "Time remaining: {$diff} seconds\n";
        
        if ($diff < 0) {
            echo "⚠️  NEGATIVE TIME - Will cause reload!\n";
        } else if ($diff > 600) {
            echo "⚠️  Time > 10 minutes - Something wrong!\n";
        } else {
            echo "✅ Time is valid: " . gmdate('i:s', $diff) . "\n";
        }
    }
    
    echo "\nVerification 1 status: " . ($task->verification_1_status ?? 'NULL') . "\n";
}

// Check if there's activerecord/timezone issues
echo "\n=== TIMEZONE INFO ===\n";
echo "App timezone: " . config('app.timezone') . "\n";
echo "DB timezone: " . \DB::select("SELECT @@session.time_zone as tz")[0]->tz ?? 'Unknown' . "\n";
echo "PHP timezone: " . date_default_timezone_get() . "\n";
