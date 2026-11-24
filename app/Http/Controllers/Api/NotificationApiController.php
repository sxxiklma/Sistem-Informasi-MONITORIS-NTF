<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function count(Request $request): JsonResponse
    {
        $count = $request->user()->appNotifications()->unread()->count();

        return response()->json(['count' => $count]);
    }

    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->appNotifications()
            ->limit(30)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'action_url' => $n->action_url,
                'is_read' => (bool) $n->read_at,
                'time_ago' => $n->created_at?->diffForHumans() ?? '-',
            ]);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
        ]);
    }

    public function markRead(Request $request, AppNotification $notification): JsonResponse
    {
        if ($notification->recipient_id === $request->user()->id) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->appNotifications()->unread()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
