# Contoh Implementasi Cache di Project

## 1. Update Dashboard Component

```php
// app/Livewire/Dashboard.php
namespace App\Livewire;

use Livewire\Component;
use App\Services\CacheService;
use App\Models\Task;
use App\Models\UserTask;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $userId = Auth::id();

        // Cache available tasks (15 menit)
        $availableTasks = CacheService::remember(
            'available_tasks_dashboard',
            function() {
                return Task::where('status', 'published')
                    ->whereDoesntHave('userTasks', function($q) {
                        $q->active();
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            },
            CacheService::SHORT_TTL,
            [CacheService::TAG_TASKS]
        );

        // Cache user's active tasks (5 menit - sering berubah)
        $activeTasks = CacheService::remember(
            CacheService::userKey($userId, 'active_tasks_dashboard'),
            function() use ($userId) {
                return UserTask::with('task')
                    ->where('user_id', $userId)
                    ->active()
                    ->get();
            },
            5,
            [CacheService::TAG_TASKS, CacheService::TAG_USERS]
        );

        // Cache stats (30 menit)
        $stats = CacheService::remember(
            CacheService::userKey($userId, 'stats'),
            function() use ($userId) {
                return [
                    'completed' => UserTask::where('user_id', $userId)->completed()->count(),
                    'active' => UserTask::where('user_id', $userId)->active()->count(),
                    'earnings' => UserTask::where('user_id', $userId)
                        ->where('payment_status', 'success')
                        ->sum('payment_amount'),
                ];
            },
            30,
            [CacheService::TAG_USERS]
        );

        return view('livewire.dashboard', [
            'availableTasks' => $availableTasks,
            'activeTasks' => $activeTasks,
            'stats' => $stats,
        ]);
    }

    public function refreshData()
    {
        // Clear cache ketika user klik refresh
        CacheService::forget('available_tasks_dashboard');
        CacheService::forget(CacheService::userKey(Auth::id(), 'active_tasks_dashboard'));
        CacheService::forget(CacheService::userKey(Auth::id(), 'stats'));

        $this->dispatch('toast', [
            'message' => 'Data refreshed!',
            'type' => 'success'
        ]);
    }
}
```

## 2. Update TaskWorkWizard Component

```php
// app/Livewire/TaskWorkWizard.php - Tambahkan caching
use App\Services\CacheService;

public function mount(Task $task)
{
    // ... existing code ...

    // Cache task details
    $this->task = CacheService::remember(
        CacheService::taskKey($task->id, 'details'),
        function() use ($task) {
            return $task->load('category');
        },
        CacheService::DEFAULT_TTL,
        [CacheService::TAG_TASKS]
    );

    // ... rest of code ...
}

public function submitProof1()
{
    // ... existing validation & upload ...

    // Clear cache after submit
    CacheService::forget(CacheService::userKey(Auth::id(), 'active_tasks_dashboard'));
    CacheService::forget('available_tasks_dashboard');

    // ... rest of code ...
}
```

## 3. API Endpoints dengan Cache

```php
// routes/api.php
use App\Http\Controllers\CachedTaskController;

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/tasks/available', [CachedTaskController::class, 'getAvailableTasks']);
    Route::get('/tasks/active', [CachedTaskController::class, 'getUserActiveTasks']);
    Route::get('/tasks/completed', [CachedTaskController::class, 'getUserCompletedTasks']);
    Route::get('/tasks/{id}', [CachedTaskController::class, 'getTask']);

    // Admin only
    Route::post('/cache/clear', [CachedTaskController::class, 'clearCache'])
        ->middleware('admin');
});
```

## 4. Tambahkan Refresh Button di View

```blade
<!-- resources/views/livewire/dashboard.blade.php -->
<div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-bold">Dashboard</h2>
    <button
        wire:click="refreshData"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        <svg wire:loading wire:target="refreshData" class="inline w-4 h-4 animate-spin" ...>...</svg>
        <span wire:loading.remove wire:target="refreshData">Refresh Data</span>
        <span wire:loading wire:target="refreshData">Refreshing...</span>
    </button>
</div>
```

## 5. Monitor Cache Usage

```php
// Tambahkan ke AdminController atau buat halaman monitoring
public function cacheStats()
{
    $cacheStats = [
        'total_keys' => DB::table('cache')->count(),
        'total_size' => DB::table('cache')->sum(DB::raw('LENGTH(value)')),
        'expired' => DB::table('cache')
            ->where('expiration', '<', now()->timestamp)
            ->count(),
    ];

    return view('admin.cache-stats', compact('cacheStats'));
}
```

## Testing

```bash
# Test cache command
php artisan cache:clear-expired

# Test dalam code
use App\Services\CacheService;

// Set cache
CacheService::put('test_key', 'test_value', 5);

// Get cache
$value = CacheService::get('test_key'); // 'test_value'

// Wait 6 minutes...
php artisan cache:clear-expired

// Cache should be cleared
$value = CacheService::get('test_key'); // null
```
