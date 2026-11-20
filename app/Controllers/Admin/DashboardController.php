<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\DepartmentModel;
use App\Models\EmployeeModel;
use App\Models\WorkActivityModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $selectedDate = $this->request->getGet('date') ?: date('Y-m-d');
        $departmentId = $this->request->getGet('department_id') ?: '';

        $deptModel     = new DepartmentModel();
        $empModel      = new EmployeeModel();
        $attModel      = new AttendanceModel();
        $activityModel = new WorkActivityModel();

        // Filter karyawan (hanya role employee)
        $empBuilder = $empModel->select('employees.id')
            ->join('users', 'users.id = employees.user_id')
            ->where('users.role', 'employee')
            ->where('users.is_active', 1);

        if ($departmentId !== '') {
            $empBuilder->where('employees.department_id', $departmentId);
        }
        $employeeIds    = array_column($empBuilder->findAll(), 'id');
        $totalEmployees = count($employeeIds);

        // Statistik Absensi
        $presentCount = 0;
        $lateCount    = 0;
        $workingCount = 0;

        if ($totalEmployees > 0) {
            $presentCount = $attModel->whereIn('employee_id', $employeeIds)
                ->where('date', $selectedDate)
                ->whereIn('status', ['present', 'late'])
                ->countAllResults();

            $lateCount = $attModel->whereIn('employee_id', $employeeIds)
                ->where('date', $selectedDate)
                ->where('status', 'late')
                ->countAllResults();

            $workingCount = $attModel->whereIn('employee_id', $employeeIds)
                ->where('date', $selectedDate)
                ->where('check_in IS NOT NULL', null, false)
                ->where('check_out IS NULL', null, false)
                ->countAllResults();
        }

        $absentCount = max(0, $totalEmployees - $presentCount);

        // Statistik Aktivitas & Laporan Pending
        $actBuilder = $activityModel->where('activity_date', $selectedDate);
        if ($totalEmployees > 0 && $departmentId !== '') {
            $actBuilder->whereIn('employee_id', $employeeIds);
        }
        $todayActivities = ($departmentId !== '' && $totalEmployees === 0) ? 0 : $actBuilder->countAllResults();

        $pendingBuilder = $activityModel->where('approval_status', 'pending');
        if ($totalEmployees > 0 && $departmentId !== '') {
            $pendingBuilder->whereIn('employee_id', $employeeIds);
        }
        $pendingReports = ($departmentId !== '' && $totalEmployees === 0) ? 0 : $pendingBuilder->countAllResults();

        // Data Grafik 7 Hari Terakhir
        $chartLabels     = [];
        $chartAttendance = [];
        $chartActivities = [];

        for ($i = 6; $i >= 0; $i--) {
            $d             = date('Y-m-d', strtotime($selectedDate . " -{$i} days"));
            $chartLabels[] = date('d M', strtotime($d));

            if ($totalEmployees > 0) {
                $chartAttendance[] = $attModel->whereIn('employee_id', $employeeIds)
                    ->where('date', $d)
                    ->whereIn('status', ['present', 'late'])
                    ->countAllResults();

                $chartActivities[] = $activityModel->whereIn('employee_id', $employeeIds)
                    ->where('activity_date', $d)
                    ->countAllResults();
            } else {
                $chartAttendance[] = 0;
                $chartActivities[] = 0;
            }
        }

        // Aktivitas Terbaru
        $recentBuilder = $activityModel->select('work_activities.*, employees.full_name, departments.name as department_name')
            ->join('employees', 'employees.id = work_activities.employee_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->orderBy('work_activities.created_at', 'DESC');

        if ($departmentId !== '') {
            $recentBuilder->where('employees.department_id', $departmentId);
        }
        $recentActivities = $recentBuilder->findAll(6);

        return view('admin/dashboard/index', [
            'pageTitle'        => 'Dashboard Perusahaan',
            'selectedDate'     => $selectedDate,
            'departmentId'     => $departmentId,
            'departments'      => $deptModel->orderBy('name', 'ASC')->findAll(),
            'totalEmployees'   => $totalEmployees,
            'presentCount'     => $presentCount,
            'absentCount'      => $absentCount,
            'lateCount'        => $lateCount,
            'workingCount'     => $workingCount,
            'todayActivities'  => $todayActivities,
            'pendingReports'   => $pendingReports,
            'chartLabels'      => $chartLabels,
            'chartAttendance'  => $chartAttendance,
            'chartActivities'  => $chartActivities,
            'recentActivities' => $recentActivities,
        ]);
    }
}