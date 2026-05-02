<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Livewire\Volt\Volt;
use App\Http\Controllers\Api\TaskMessageController;
use App\Livewire\TaskChat;
use App\Models\UserTask;
use App\Livewire\Admin\SupportChat;
use App\Http\Middleware\AdminMiddleware;

// Broadcasting auth endpoint - required for private/presence channels (support chat)
Broadcast::routes(['middleware' => ['web', 'auth']]);

// Lightweight polling endpoint - returns message count without blocking Livewire
Route::get('/api/support-thread/{threadId}/count', function ($threadId) {
    return response()->json([
        'count' => \App\Models\SupportMessage::where('support_thread_id', $threadId)->count()
    ]);
})->middleware(['web', 'auth']);

Route::get('/api/task-thread/{userTaskId}/count', function ($userTaskId) {
    return response()->json([
        'count' => \App\Models\TaskMessage::where('user_task_id', $userTaskId)->count()
    ]);
})->middleware(['web', 'auth']);

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Chat Page Route (outside Filament)
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/admin/chat/{record}', function ($record) {
        $userTask = UserTask::with(['user', 'task'])->findOrFail($record);

        // Mark messages as read
        $userTask->messages()
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return view('chat-page', ['userTask' => $userTask]);
    })->name('admin.chat');

    // Admin Support Chat (outside Filament)
    Route::get('/admin/support-chat', SupportChat::class)->name('admin.support-chat');
});

// API Routes for Task Messages (protected by auth middleware)
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    Route::get('/task-messages/{userTask}', [TaskMessageController::class, 'index'])->name('task-messages.index');
    Route::post('/task-messages/{userTask}', [TaskMessageController::class, 'store'])->name('task-messages.store');
});

Route::get('/dashboard', \App\Livewire\UserDashboard::class)->middleware(['auth', 'not-banned'])->name('dashboard');

Route::get('/test', function () {
    return view('test');
})->name('test');

// Download CSV template for task import
Route::get('/sample_tasks.csv', function () {
    $path = public_path('sample_tasks.csv');
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->download($path, 'sample_tasks.csv', [
        'Content-Type' => 'text/csv',
    ]);
})->name('sample-tasks-csv');

// User Task Routes (Non-Filament)
// Protected with auth, not-banned, and can-take-task middleware
Route::middleware(['auth', 'not-banned', 'can-take-task'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', \App\Livewire\TaskDashboard::class)->name('dashboard');
    Route::get('/my-tasks', \App\Livewire\MyTasks::class)->name('my-tasks');
    Route::get('/task/{task}/work', \App\Livewire\TaskWorkWizard::class)->name('task.work');
    Route::get('/task/{task}/vcf', function (\App\Models\Task $task) {
        if (empty($task->vcf_data)) {
            abort(404, 'VCF data not found');
        }
        $filename = 'task-' . $task->id . '-' . \Illuminate\Support\Str::slug($task->title) . '.vcf';

        // Legacy support: if vcf_data starts with BEGIN:VCARD, it's inline content (old format)
        if (str_starts_with(trim($task->vcf_data), 'BEGIN:VCARD')) {
            return response($task->vcf_data, 200, [
                'Content-Type' => 'text/vcard',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        // New format: vcf_data is a file path on public disk
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($task->vcf_data)) {
            abort(404, 'VCF file not found');
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download($task->vcf_data, $filename, [
            'Content-Type' => 'text/vcard',
        ]);
    })->name('task.vcf');
    Route::get('/history', \App\Livewire\TaskHistory::class)->name('history');
});

Route::middleware(['auth', 'not-banned'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Information Page
    Volt::route('pages/information', 'pages.information')->name('pages.information');

    // Tips & Trick Pages
    Volt::route('pages/panduan-task', 'pages.panduan-task')->name('pages.panduan-task');
    Volt::route('pages/tips-sukses', 'pages.tips-sukses')->name('pages.tips-sukses');
    Volt::route('pages/faq', 'pages.faq')->name('pages.faq');
    Volt::route('pages/tutorial-page', 'pages.tutorial-page')->name('pages.tutorial-page');
});

require __DIR__ . '/auth.php';
