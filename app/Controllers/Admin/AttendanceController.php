<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\DepartmentModel;

class AttendanceController extends BaseController
{
    public function index()
    {
        $mode         = (string) ($this->request->getGet('mode') ?: 'daily');
        $selectedDate = (string) ($this->request->getGet('date') ?: date('Y-m-d'));
        $selectedMonth = (string) ($this->request->getGet('month') ?: date('Y-m'));
        $departmentId = (string) ($this->request->getGet('department_id') ?: '');
        $status       = (string) ($this->request->getGet('status') ?: '');

        $attModel  = new AttendanceModel();
        $deptModel = new DepartmentModel();

        $builder = $attModel->select('attendance.*, employees.employee_code, employees.full_name, departments.name as department_name')
            ->join('employees', 'employees.id = attendance.employee_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->orderBy('attendance.date', 'DESC')
            ->orderBy('attendance.check_in', 'DESC');

        if ($mode === 'monthly') {
            $builder->like('attendance.date', $selectedMonth, 'after');
        } else {
            $builder->where('attendance.date', $selectedDate);
        }

        if ($departmentId !== '') {
            $builder->where('employees.department_id', $departmentId);
        }

        if ($status !== '') {
            $builder->where('attendance.status', $status);
        }

        $records = $builder->findAll();

        // Hitung ringkasan
        $presentCount = 0;
        $lateCount    = 0;
        $leaveCount   = 0;
        $totalMinutes = 0;

        foreach ($records as $r) {
            if ($r['status'] === 'present') {
                $presentCount++;
            } elseif ($r['status'] === 'late') {
                $lateCount++;
            } elseif ($r['status'] === 'leave') {
                $leaveCount++;
            }
            $totalMinutes += (int) $r['work_duration_minutes'];
        }

        return view('admin/attendance/index', [
            'pageTitle'     => 'Monitoring Absensi',
            'mode'          => $mode,
            'selectedDate'  => $selectedDate,
            'selectedMonth' => $selectedMonth,
            'departmentId'  => $departmentId,
            'status'        => $status,
            'departments'   => $deptModel->orderBy('name', 'ASC')->findAll(),
            'records'       => $records,
            'presentCount'  => $presentCount,
            'lateCount'     => $lateCount,
            'leaveCount'    => $leaveCount,
            'totalHours'    => round($totalMinutes / 60, 1),
        ]);
    }
}