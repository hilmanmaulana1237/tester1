<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserTask;
use Illuminate\Support\Facades\DB;

$ut = UserTask::whereIn('status', [UserTask::STATUS_TAKEN, UserTask::STATUS_PENDING_VERIFICATION_1, UserTask::STATUS_PENDING_VERIFICATION_2])->first();
if (!$ut) {
    echo "No active usertask found\n";
    exit;
}
echo "Will cancel UserTask id={$ut->id} task_id={$ut->task_id} status={$ut->status}\n";
$ut->update([
    'status' => UserTask::STATUS_CANCELLED,
    'cancelled_at' => now(),
    'verification_1_status' => null,
    'verification_2_status' => null,
    'verified_at_1' => null,
    'verification_1_approved_at' => null,
    'verification_2_approved_at' => null,
    'verification_1_approved_by' => null,
    'verification_2_approved_by' => null,
    'payment_verified_by_admin_id' => null,
    'payment_status' => UserTask::PAYMENT_PENDING,
    'payment_verified_by_admin_id' => null,
    'payment_verified_at' => null,
]);
UserTask::where('task_id', $ut->task_id)
    ->whereIn('status', [UserTask::STATUS_TAKEN, UserTask::STATUS_PENDING_VERIFICATION_1, UserTask::STATUS_PENDING_VERIFICATION_2])
    ->where('id', '!=', $ut->id)
    ->update([
        'status' => UserTask::STATUS_FAILED,
        'failed_count' => DB::raw('COALESCE(failed_count, 0) + 1'),
        'verification_1_status' => null,
        'verification_2_status' => null,
        'verification_1_approved_at' => null,
        'verification_2_approved_at' => null,
        'verification_1_approved_by' => null,
        'verification_2_approved_by' => null,
    ]);

echo "Cancelled and released to pool.\n";
