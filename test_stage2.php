<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UserTask;

echo "Creating stage 2 submission test data...\n";

// Buat satu UserTask yang sudah submit stage 2 untuk test admin approval
$userTask = UserTask::where('status', 'pending_verification_2')->first();

if ($userTask) {
    $userTask->update([
        'verification_2_status' => 'Submitted at ' . now()->format('Y-m-d H:i:s') . '. Files: final_proof1.jpg, final_proof2.jpg. Description: Bukti pengerjaan stage 2 final'
    ]);
    echo "UserTask updated: Now has submitted stage 2 proof\n";
    echo "Admin should now be able to approve/reject V2 for this task\n";
} else {
    echo "No PENDING_VERIFICATION_2 task found\n";
}

echo "Test completed!\n";
