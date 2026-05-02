<?php

namespace App\Livewire;

use App\Models\UserTask;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TaskHistory extends Component
{
    use WithPagination;

    public $filterStatus = 'all';
    public $filterCategory = 'all';
    public $search = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'filterStatus' => ['except' => 'all'],
        'filterCategory' => ['except' => 'all'],
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Security: Check if user is banned
        if ($user->isBanned()) {
            Auth::logout();
            session()->flash('error', 'Akun Anda sedang dibanned.');
            return redirect()->route('login');
        }

        // Security: Redirect admin to filament
        if ($user->isAdmin()) {
            return redirect('/admin');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc'; // Changed from 'asc' to 'desc' for newest first
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['filterStatus', 'filterCategory', 'search']);
        $this->resetPage();
    }

    public function getTasksProperty()
    {
        $query = UserTask::query()
            ->with(['task', 'task.category'])
            ->where('user_id', Auth::id());

        // Apply status filter
        if ($this->filterStatus !== 'all') {
            if ($this->filterStatus === 'in_progress') {
                // Special handling for "in_progress" - include all active statuses and exclude overdue
                $query->where(function ($q) {
                    $q->whereIn('status', [
                        UserTask::STATUS_TAKEN,
                        UserTask::STATUS_PENDING_VERIFICATION_1,
                        UserTask::STATUS_PENDING_VERIFICATION_2
                    ])->where(function ($q2) {
                        $q2->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
                    });
                });
            } elseif ($this->filterStatus === 'failed') {
                // Special handling for failed - include explicit failed + overdue active tasks
                $query->where(function ($q) {
                    $q->where('status', UserTask::STATUS_FAILED)
                        ->orWhere(function ($q2) {
                            $q2->whereIn('status', [
                                UserTask::STATUS_TAKEN,
                                UserTask::STATUS_PENDING_VERIFICATION_1,
                                UserTask::STATUS_PENDING_VERIFICATION_2
                            ])
                                ->whereNotNull('deadline_at')
                                ->where('deadline_at', '<=', now());
                        });
                });
            } else {
                $query->where('status', $this->filterStatus);
            }
        }

        // Apply category filter
        if ($this->filterCategory !== 'all') {
            $query->whereHas('task', function ($q) {
                $q->where('category_id', $this->filterCategory);
            });
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(15);
    }

    public function getCategoriesProperty()
    {
        return Category::where('is_active', true)->get();
    }

    public function getStatsProperty()
    {
        $userId = Auth::id();

        return [
            'total' => UserTask::where('user_id', $userId)->count(),
            'in_progress' => UserTask::where('user_id', $userId)
                ->where(function ($query) {
                    // Include working statuses but exclude those whose deadline already passed
                    $query->where(function ($q) {
                        $q->whereIn('status', [UserTask::STATUS_TAKEN, UserTask::STATUS_PENDING_VERIFICATION_1, UserTask::STATUS_PENDING_VERIFICATION_2])
                            ->where(function ($q2) {
                                $q2->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
                            });
                    })
                        ->orWhere(function ($q) {
                        // Include completed but awaiting payment regardless of deadline
                        $q->where('status', UserTask::STATUS_COMPLETED)
                            ->whereIn('payment_status', ['pending', 'failed']);
                    });
                })
                ->count(),
            'completed' => UserTask::where('user_id', $userId)
                ->where('status', UserTask::STATUS_COMPLETED)
                ->where('payment_status', 'success')
                ->count(),
            'failed' => UserTask::where('user_id', $userId)
                ->where(function ($q) {
                    $q->where('status', UserTask::STATUS_FAILED)
                        ->orWhere(function ($q2) {
                            $q2->whereIn('status', [
                                UserTask::STATUS_TAKEN,
                                UserTask::STATUS_PENDING_VERIFICATION_1,
                                UserTask::STATUS_PENDING_VERIFICATION_2
                            ])
                                ->whereNotNull('deadline_at')
                                ->where('deadline_at', '<=', now());
                        });
                })
                ->count(),
            'cancelled' => UserTask::where('user_id', $userId)
                ->where('status', UserTask::STATUS_CANCELLED)
                ->count(),
        ];
    }

    public function getStatusBadgeClass($status)
    {
        return match ($status) {
            UserTask::STATUS_TAKEN => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            UserTask::STATUS_PENDING_VERIFICATION_1 => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
            UserTask::STATUS_PENDING_VERIFICATION_2 => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            UserTask::STATUS_COMPLETED => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            UserTask::STATUS_FAILED => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
            UserTask::STATUS_CANCELLED => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300',
        };
    }

    public function getStatusIcon($status)
    {
        return match ($status) {
            UserTask::STATUS_TAKEN => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            UserTask::STATUS_PENDING_VERIFICATION_1 => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            UserTask::STATUS_PENDING_VERIFICATION_2 => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            UserTask::STATUS_COMPLETED => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
            UserTask::STATUS_FAILED => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
            UserTask::STATUS_CANCELLED => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>',
            default => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        };
    }

    public function render()
    {
        return view('livewire.task-history', [
            'tasks' => $this->tasks,
            'categories' => $this->categories,
            'stats' => $this->stats,
        ]);
    }
}
