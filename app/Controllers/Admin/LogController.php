<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;

class LogController extends BaseController
{
    public function index()
    {
        $module  = (string) ($this->request->getGet('module') ?: '');
        $keyword = trim((string) $this->request->getGet('q'));

        $logModel = new ActivityLogModel();
        $builder  = $logModel->select('activity_logs.*, users.email, users.role, employees.full_name')
            ->join('users', 'users.id = activity_logs.user_id', 'left')
            ->join('employees', 'employees.user_id = users.id', 'left')
            ->orderBy('activity_logs.created_at', 'DESC');

        if ($module !== '') {
            $builder->where('activity_logs.module', $module);
        }

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('activity_logs.description', $keyword)
                ->orLike('activity_logs.action', $keyword)
                ->orLike('employees.full_name', $keyword)
                ->groupEnd();
        }

        return view('admin/logs/index', [
            'pageTitle'      => 'Riwayat Aktivitas Sistem',
            'logs'           => $builder->findAll(100),
            'selectedModule' => $module,
            'keyword'        => $keyword,
        ]);
    }
}