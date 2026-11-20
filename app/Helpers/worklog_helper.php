<?php

use App\Models\ActivityLogModel;
use App\Models\NotificationModel;

if (!function_exists('log_activity')) {
    function log_activity(string $action, string $module, string $description, ?int $userId = null): void
    {
        $request  = service('request');
        $logModel = new ActivityLogModel();

        $logModel->insert([
            'user_id'     => $userId ?? session()->get('user_id'),
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'ip_address'  => $request->getIPAddress(),
            'user_agent'  => substr((string) $request->getUserAgent(), 0, 255),
        ]);
    }
}

if (!function_exists('get_user_notifications')) {
    function get_user_notifications(int $limit = 5): array
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return ['unread_count' => 0, 'items' => []];
        }

        $model = new NotificationModel();
        $items = $model->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);

        $unreadCount = $model->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();

        return [
            'unread_count' => $unreadCount,
            'items'        => $items,
        ];
    }
}

if (!function_exists('status_badge')) {
    function status_badge(string $status): string
    {
        $map = [
            'present'     => ['bg-success-subtle text-success', 'Hadir'],
            'late'        => ['bg-warning-subtle text-warning-emphasis', 'Terlambat'],
            'absent'      => ['bg-danger-subtle text-danger', 'Tidak Hadir'],
            'leave'       => ['bg-info-subtle text-info-emphasis', 'Izin/Cuti'],
            'draft'       => ['bg-secondary-subtle text-secondary', 'Draft'],
            'pending'     => ['bg-warning-subtle text-warning-emphasis', 'Menunggu'],
            'approved'    => ['bg-success-subtle text-success', 'Disetujui'],
            'rejected'    => ['bg-danger-subtle text-danger', 'Ditolak'],
            'in_progress' => ['bg-primary-subtle text-primary', 'Proses'],
            'completed'   => ['bg-success-subtle text-success', 'Selesai'],
            'blocked'     => ['bg-danger-subtle text-danger', 'Terhambat'],
            'todo'        => ['bg-secondary-subtle text-secondary', 'To Do'],
            'review'      => ['bg-info-subtle text-info-emphasis', 'Review'],
        ];

        $cfg = $map[$status] ?? ['bg-light text-dark', ucfirst($status)];
        return '<span class="badge rounded-pill fw-medium ' . $cfg[0] . '">' . esc($cfg[1]) . '</span>';
    }
}

if (!function_exists('send_notification')) {
    function send_notification(int $userId, string $title, string $message, ?string $link = null): void
    {
        $notifModel = new NotificationModel();
        $notifModel->insert([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
            'is_read' => 0,
        ]);
    }
}