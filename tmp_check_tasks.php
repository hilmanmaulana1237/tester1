<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Task;
use App\Models\UserTask;

echo 'Available: ' . Task::available()->count() . PHP_EOL;
echo 'Taken: ' . Task::whereHas('activeUserTask')->count() . PHP_EOL;
$sample = UserTask::where('status', 'cancelled')->with('task')->first();
if ($sample) {
    echo 'Sample cancelled UserTask id: ' . $sample->id . ' task_id: ' . $sample->task_id . PHP_EOL;
    echo 'Task isTaken(): ' . ($sample->task->isTaken() ? 'true' : 'false') . PHP_EOL;
    echo 'Task activeUserTask count: ' . $sample->task->activeUserTask()->count() . PHP_EOL;
}
