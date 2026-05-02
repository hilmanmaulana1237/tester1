<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\UserTask;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class UserDashboard extends Component
{
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

        // Redirect admin to filament panel
        if ($user->isAdmin()) {
            return redirect('/admin');
        }
    }

    public function getStatsProperty()
    {
        $userId = Auth::id();

        return [
            'total_tasks' => UserTask::where('user_id', $userId)->count(),
            'in_progress' => UserTask::where('user_id', $userId)
                ->where(function ($q) {
                    $q->whereIn('status', [
                        UserTask::STATUS_TAKEN,
                        UserTask::STATUS_PENDING_VERIFICATION_1,
                        UserTask::STATUS_PENDING_VERIFICATION_2
                    ])
                        ->where(function ($q2) {
                            $q2->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
                        });
                })->count(),
            'completed' => UserTask::where('user_id', $userId)
                ->where('status', UserTask::STATUS_COMPLETED)->count(),
            'failed' => UserTask::where('user_id', $userId)
                ->where(function ($q) {
                    $q->where('status', UserTask::STATUS_FAILED)
                        ->orWhere(function ($q2) {
                            $q2->whereIn('status', [
                                UserTask::STATUS_TAKEN,
                                UserTask::STATUS_PENDING_VERIFICATION_1,
                                UserTask::STATUS_PENDING_VERIFICATION_2,
                            ])
                                ->whereNotNull('deadline_at')
                                ->where('deadline_at', '<=', now());
                        });
                })->count(),
            'total_earnings' => UserTask::where('user_id', $userId)
                ->where('status', UserTask::STATUS_COMPLETED)
                ->where('payment_status', UserTask::PAYMENT_SUCCESS)
                ->sum('payment_amount'),
            'pending_payment' => UserTask::where('user_id', $userId)
                ->where('status', UserTask::STATUS_COMPLETED)
                ->where('payment_status', UserTask::PAYMENT_PENDING)
                ->sum('payment_amount'),
            'available_tasks' => Task::available()
                ->count(),
        ];
    }

    public function getRecentTasksProperty()
    {
        return UserTask::where('user_id', Auth::id())
            ->with(['task.category'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function getWeeklyActivityProperty()
    {
        $userId = Auth::id();
        $weeklyData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = UserTask::where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->count();

            $weeklyData[] = [
                'day' => $date->format('D'),
                'count' => $count,
                'date' => $date->format('Y-m-d'),
            ];
        }

        return $weeklyData;
    }

    public function getPaymentStatsProperty()
    {
        $userId = Auth::id();

        return [
            'paid' => UserTask::where('user_id', $userId)
                ->where('payment_status', UserTask::PAYMENT_SUCCESS)
                ->count(),
            'pending' => UserTask::where('user_id', $userId)
                ->where('payment_status', UserTask::PAYMENT_PENDING)
                ->count(),
            'failed' => UserTask::where('user_id', $userId)
                ->where('payment_status', UserTask::PAYMENT_FAILED)
                ->count(),
        ];
    }

    public function render()
    {
        return view('livewire.user-dashboard', [
            'stats' => $this->stats,
            'recentTasks' => $this->recentTasks,
            'weeklyActivity' => $this->weeklyActivity,
            'paymentStats' => $this->paymentStats,
        ]);
    }
}
