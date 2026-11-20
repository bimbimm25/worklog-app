<?php

namespace App\Controllers\Employee;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\NotificationModel;
use App\Models\TaskModel;
use App\Models\WorkActivityModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $employeeId = (int) session()->get('employee_id');
        $userId     = (int) session()->get('user_id');
        $today      = date('Y-m-d');

        $attModel      = new AttendanceModel();
        $taskModel     = new TaskModel();
        $activityModel = new WorkActivityModel();
        $notifModel    = new NotificationModel();

        // Absensi hari ini
        $todayAttendance = $attModel->where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        // Total jam kerja bulan ini
        $monthMinutes = $attModel->selectSum('work_duration_minutes')
            ->where('employee_id', $employeeId)
            ->like('date', date('Y-m'), 'after')
            ->first()['work_duration_minutes'] ?? 0;

        // Statistik Tugas
        $activeTasks = $taskModel->where('assigned_to', $employeeId)
            ->whereIn('status', ['todo', 'in_progress', 'review'])
            ->countAllResults();

        $completedTasks = $taskModel->where('assigned_to', $employeeId)
            ->where('status', 'completed')
            ->countAllResults();

        // Statistik Aktivitas & Laporan Terbaru
        $loggedActivities = $activityModel->where('employee_id', $employeeId)
            ->where('activity_date', $today)
            ->countAllResults();

        $latestReports = $activityModel->where('employee_id', $employeeId)
            ->orderBy('created_at', 'DESC')
            ->findAll(5);

        $notifications = $notifModel->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll(5);

        return view('employee/dashboard/index', [
            'pageTitle'        => 'Dashboard Karyawan',
            'todayAttendance'  => $todayAttendance,
            'monthHours'       => round($monthMinutes / 60, 1),
            'activeTasks'      => $activeTasks,
            'completedTasks'   => $completedTasks,
            'loggedActivities' => $loggedActivities,
            'latestReports'    => $latestReports,
            'notifications'    => $notifications,
        ]);
    }
}