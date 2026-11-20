<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    public function readAll()
    {
        $userId = (int) session()->get('user_id');
        (new NotificationModel())
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->set(['is_read' => 1])
            ->update();

        return redirect()->back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }
}