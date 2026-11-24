<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread count (for header bell badge).
     */
    public function count(): JsonResponse
    {
        $count = Auth::user()->appNotifications()->unread()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get latest notifications (for dropdown).
     */
    public function index(): JsonResponse
    {
        $notifications = Auth::user()
            ->appNotifications()
            ->limit(20)
            ->get(['id', 'type', 'title', 'message', 'action_url', 'entity_type', 'entity_id', 'read_at', 'created_at']);

        return response()->json($notifications->map(fn ($n) => [
            'id' => $n->id,
            'type' => $n->type,
            'title' => $n->title,
            'message' => $n->message,
            'action_url' => $n->action_url,
            'is_read' => (bool) $n->read_at,
            'time_ago' => $n->created_at->diffForHumans(),
        ]));
    }

    /**
     * Mark specific notification as read.
     */
    public function markRead(AppNotification $appNotification): JsonResponse
    {
        abort_unless($appNotification->recipient_id === Auth::id(), 403);
        $appNotification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(): JsonResponse
    {
        Auth::user()->appNotifications()->unread()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
