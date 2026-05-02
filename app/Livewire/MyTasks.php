<?php

namespace App\Livewire;

use App\Models\UserTask;
use App\Services\CacheService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

class MyTasks extends Component
{
    public $selectedTask = null;
    public $sortBy = 'deadline'; // deadline, priority, status
    public $filterStatus = 'all'; // all, taken, pending, near_deadline

    public function mount()
    {
        // Security checks are handled by middleware (auth, not-banned, can-take-task)
        // But double-check here as well
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->isBanned()) {
            Auth::logout();
            session()->flash('error', 'Akun Anda sedang dibanned.');
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return redirect('/admin');
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $userId = Auth::id();

        // Get active tasks with caching
        $activeTasks = CacheService::remember(
            CacheService::userKey($userId, 'my_active_tasks'),
            function () use ($userId) {
                return UserTask::with(['task.category'])
                    ->where('user_id', $userId)
                    // only active user task statuses
                    ->active()
                    // exclude user tasks where the user's own deadline has passed
                    ->where(function ($q) {
                        $q->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
                    })
                    // ensure underlying Task is still active (not expired)
                    ->whereHas('task', function ($q) {
                        $q->where('is_expired', false)
                            ->where('expired_at', '>', now())
                            // ensure Category is also active and not expired
                            ->whereHas('category', function ($catQuery) {
                                $catQuery->where('is_active', true)
                                    ->where('expired_at', '>', now());
                            });
                    })
                    ->orderBy('deadline_at', 'asc')
                    ->get();
            },
            5
        );

        // Apply filters
        $filteredTasks = $activeTasks;

        if ($this->filterStatus !== 'all') {
            $filteredTasks = $activeTasks->filter(function ($userTask) {
                return match ($this->filterStatus) {
                    'taken' => $userTask->status === 'taken',
                    'pending' => in_array($userTask->status, ['pending_verification_1', 'pending_verification_2']),
                    'near_deadline' => $userTask->deadline_at && $userTask->deadline_at->diffInHours(now()) <= 24,
                    default => true
                };
            });
        }

        // Apply sorting - urgent tasks always on top
        $sortedTasks = $filteredTasks->sortBy(function ($userTask) {
            // Calculate urgency score (lower = more urgent)
            $urgencyScore = 0;

            if ($userTask->deadline_at) {
                $hoursLeft = $userTask->deadline_at->diffInHours(now(), false);

                // If deadline passed or very close, highest priority
                if ($hoursLeft <= 0) {
                    $urgencyScore = -1000; // Overdue - top priority
                } elseif ($hoursLeft <= 1) {
                    $urgencyScore = -500; // Less than 1 hour
                } elseif ($hoursLeft <= 6) {
                    $urgencyScore = -100; // Less than 6 hours
                } elseif ($hoursLeft <= 24) {
                    $urgencyScore = -50; // Less than 24 hours
                } else {
                    $urgencyScore = $hoursLeft; // Sort by hours remaining
                }
            } else {
                $urgencyScore = 9999; // No deadline, lowest priority
            }

            return $urgencyScore;
        });

        // Calculate stats
        $stats = [
            'total_active' => $activeTasks->count(),
            'in_progress' => $activeTasks->where('status', 'taken')->count(),
            'pending_verification' => $activeTasks->whereIn('status', ['pending_verification_1', 'pending_verification_2'])->count(),
            'near_deadline' => $activeTasks->filter(fn($t) => $t->deadline_at && $t->deadline_at->diffInHours(now()) <= 24)->count(),
            'potential_earnings' => $activeTasks->sum('payment_amount'),
        ];

        return view('livewire.my-tasks', [
            'tasks' => $sortedTasks,
            'stats' => $stats,
        ]);
    }

    public function viewTask($taskId)
    {
        $this->selectedTask = $taskId;
    }

    public function closeModal()
    {
        $this->selectedTask = null;
    }

    public function updateSort($sortBy)
    {
        $this->sortBy = $sortBy;
    }

    public function updateFilter($status)
    {
        $this->filterStatus = $status;
    }
}
