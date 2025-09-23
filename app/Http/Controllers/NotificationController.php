<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = $this->notificationService->getUnreadCount($user);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(Request $request, int $notificationId)
    {
        $user = auth()->user();
        $success = $this->notificationService->markAsRead($user, $notificationId);

        if ($request->wantsJson()) {
            return response()->json(['success' => $success]);
        }

        return redirect()->back();
    }

    public function markAllAsRead(Request $request)
    {
        $user = auth()->user();
        $count = $this->notificationService->markAllAsRead($user);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'count' => $count]);
        }

        return redirect()->back()->with('success', "{$count}개의 알림을 모두 읽음 처리했습니다.");
    }

    public function getUnreadCount(Request $request)
    {
        $user = auth()->user();
        $count = $this->notificationService->getUnreadCount($user);

        return response()->json(['count' => $count]);
    }

    public function getRecent(Request $request)
    {
        $user = auth()->user();
        $notifications = $this->notificationService->getRecentNotifications($user, 10);

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'data' => $notification->data,
                    'is_read' => $notification->isRead(),
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            }),
            'unread_count' => $this->notificationService->getUnreadCount($user),
        ]);
    }
}
