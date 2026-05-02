<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UserTask;

echo "Creating test data...\n";

// Update UserTask untuk testing different scenarios
$userTasks = UserTask::limit(4)->get();

if ($userTasks->count() >= 4) {
    // Scenario 1: Completed task with payment
    $userTasks[0]->update([
        'status' => 'completed',
        'payment_status' => 'success',
        'payment_amount' => 150000,
        'verification_1_status' => 'Approved by admin at ' . now()->format('Y-m-d H:i:s'),
        'verification_2_status' => 'Approved by admin at ' . now()->format('Y-m-d H:i:s'),
        'completed_at' => now()
    ]);
    echo "UserTask 1: Set to COMPLETED with payment\n";

    // Scenario 2: Failed at stage 1
    $userTasks[1]->update([
        'status' => 'failed',
        'failed_count' => 1,
        'verification_1_status' => 'Rejected by admin at ' . now()->format('Y-m-d H:i:s') . '. Bukti tidak memenuhi kriteria yang diminta. Silakan submit ulang dengan perbaikan.'
    ]);
    echo "UserTask 2: Set to FAILED at stage 1\n";

    // Scenario 3: Pending verification 1 (submitted proof 1)
    $userTasks[2]->update([
        'status' => 'pending_verification_1',
        'verification_1_status' => 'Submitted at ' . now()->format('Y-m-d H:i:s') . '. Files: proof1.jpg, proof2.jpg. Description: Bukti pengerjaan stage 1'
    ]);
    echo "UserTask 3: Set to PENDING_VERIFICATION_1\n";

    // Scenario 4: Pending verification 2 but not submitted yet (approved stage 1)
    $userTasks[3]->update([
        'status' => 'pending_verification_2',
        'verification_1_status' => 'Approved by admin at ' . now()->format('Y-m-d H:i:s'),
        'verification_2_status' => null // Belum submit stage 2
    ]);
    echo "UserTask 4: Set to PENDING_VERIFICATION_2 (can submit stage 2)\n";
}

echo "Test data created successfully!\n";
