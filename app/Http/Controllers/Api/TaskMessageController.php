<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaskMessage;
use App\Models\UserTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskMessageController extends Controller
{
    /**
     * Get messages for a task
     */
    public function index(UserTask $userTask)
    {
        $messages = $userTask->messages()
            ->with('user:id,name,role')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'file_path' => $message->file_path,
                    'file_name' => $message->file_name,
                    'created_at' => $message->created_at->toISOString(),
                    'is_read' => $message->is_read,
                ];
            });

        return response()->json($messages);
    }

    /**
     * Send a message
     */
    public function store(Request $request, UserTask $userTask)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = TaskMessage::create([
            'user_task_id' => $userTask->id,
            'user_id' => Auth::id(),
            'sender_type' => TaskMessage::SENDER_ADMIN,
            'message' => trim($validated['message']),
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
        ], 201);
    }
}
