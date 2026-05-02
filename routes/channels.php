<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\UserTask;
use App\Models\SupportThread;

Broadcast::channel('chat.{userTaskId}', function ($user, $userTaskId) {
    $userTask = UserTask::find($userTaskId);

    if (!$userTask) {
        return false;
    }

    // Allow if user is the task owner OR user is admin
    return $user->id === $userTask->user_id || $user->role === 'admin' || $user->role === 'superadmin';
});

Broadcast::channel('support.thread.{threadId}', function ($user, $threadId) {
    $thread = SupportThread::find($threadId);

    if (!$thread) {
        return false;
    }

    // Allow if user is the thread owner OR user is admin/superadmin
    return $user->id === $thread->user_id || $user->role === 'admin' || $user->role === 'superadmin';
});
