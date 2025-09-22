<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Badge;
use App\Models\VisitRecord;

class NotificationService
{
    public function createBadgeEarnedNotification(User $user, Badge $badge): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_BADGE_EARNED,
            'title' => '🏅 새로운 배지 획득!',
            'message' => "축하합니다! '{$badge->name}' 배지를 획득하셨습니다.",
            'data' => [
                'badge_id' => $badge->id,
                'badge_name' => $badge->name,
                'badge_description' => $badge->description,
                'required_visits' => $badge->required_visits,
            ]
        ]);
    }

    public function createVisitApprovedNotification(VisitRecord $visitRecord): Notification
    {
        return Notification::create([
            'user_id' => $visitRecord->user_id,
            'type' => Notification::TYPE_VISIT_APPROVED,
            'title' => '✅ 방문 기록 승인!',
            'message' => "'{$visitRecord->castle->name_korean}' 방문 기록이 승인되었습니다.",
            'data' => [
                'visit_record_id' => $visitRecord->id,
                'castle_name' => $visitRecord->castle->name_korean,
                'castle_name_jp' => $visitRecord->castle->name,
                'visited_at' => $visitRecord->created_at->format('Y-m-d H:i'),
            ]
        ]);
    }

    public function createVisitRejectedNotification(VisitRecord $visitRecord): Notification
    {
        return Notification::create([
            'user_id' => $visitRecord->user_id,
            'type' => Notification::TYPE_VISIT_REJECTED,
            'title' => '❌ 방문 기록 거부됨',
            'message' => "'{$visitRecord->castle->name_korean}' 방문 기록이 거부되었습니다. 다시 시도해보세요.",
            'data' => [
                'visit_record_id' => $visitRecord->id,
                'castle_name' => $visitRecord->castle->name_korean,
                'castle_name_jp' => $visitRecord->castle->name,
                'visited_at' => $visitRecord->created_at->format('Y-m-d H:i'),
            ]
        ]);
    }

    public function getUnreadCount(User $user): int
    {
        return $user->notifications()->unread()->count();
    }

    public function getRecentNotifications(User $user, int $limit = 10)
    {
        return $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function markAsRead(User $user, int $notificationId): bool
    {
        $notification = $user->notifications()->find($notificationId);

        if ($notification && $notification->isUnread()) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    public function markAllAsRead(User $user): int
    {
        return $user->notifications()
            ->unread()
            ->update(['read_at' => now()]);
    }
}