<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TIME DEBUG ===\n";
echo "Current time: " . now() . "\n";
echo "Current date: " . now()->format('Y-m-d H:i:s') . "\n";
echo "Timezone: " . config('app.timezone') . "\n\n";

$userTask = \App\Models\UserTask::where('status', 'taken')->first();

if ($userTask) {
    echo "UserTask ID: " . $userTask->id . "\n";
    echo "Task taken_at: " . $userTask->taken_at . "\n";
    echo "Deadline would be: " . $userTask->taken_at->copy()->addMinutes(10) . "\n";
    
    $diff = now()->diffInSeconds($userTask->taken_at->copy()->addMinutes(10), false);
    echo "Time remaining: " . $diff . " seconds\n";
    
    if ($diff < 0) {
        echo "⚠️  WARNING: Time is NEGATIVE (deadline in the past)!\n";
        echo "This will cause continuous reload!\n";
    }
} else {
    echo "No 'taken' task found\n";
}

echo "\n=== SOLUTION ===\n";
echo "Update taken_at to current time:\n";
if ($userTask) {
    $userTask->update(['taken_at' => now()]);
    echo "✅ Updated task {$userTask->id} taken_at to: " . $userTask->fresh()->taken_at . "\n";
}
