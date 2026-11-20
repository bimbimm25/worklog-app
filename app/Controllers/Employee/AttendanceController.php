<?php

namespace App\Controllers\Employee;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\EmployeeModel;

class AttendanceController extends BaseController
{
    public function index()
    {
        $employeeId = (int) session()->get('employee_id');
        $today      = date('Y-m-d');
        $month      = (string) ($this->request->getGet('month') ?: date('Y-m'));

        $empModel = new EmployeeModel();
        $attModel = new AttendanceModel();

        // Ambil jadwal kerja karyawan
        $employee = $empModel->select('employees.*, work_schedules.name as schedule_name, work_schedules.start_time, work_schedules.end_time, work_schedules.late_tolerance_minutes')
            ->join('work_schedules', 'work_schedules.id = employees.work_schedule_id', 'left')
            ->find($employeeId);

        // Absensi hari ini
        $todayAttendance = $attModel->where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        // Riwayat absensi bulan terpilih
        $history = $attModel->where('employee_id', $employeeId)
            ->like('date', $month, 'after')
            ->orderBy('date', 'DESC')
            ->findAll();

        // Ringkasan bulanan
        $presentCount = 0;
        $lateCount    = 0;
        $totalMinutes = 0;

        foreach ($history as $row) {
            if ($row['status'] === 'present') {
                $presentCount++;
            } elseif ($row['status'] === 'late') {
                $lateCount++;
            }
            $totalMinutes += (int) $row['work_duration_minutes'];
        }

        return view('employee/attendance/index', [
            'pageTitle'       => 'Absensi Karyawan',
            'employee'        => $employee,
            'todayAttendance' => $todayAttendance,
            'history'         => $history,
            'selectedMonth'   => $month,
            'presentCount'    => $presentCount,
            'lateCount'       => $lateCount,
            'totalHours'      => round($totalMinutes / 60, 1),
        ]);
    }

    public function checkIn()
    {
        $employeeId = (int) session()->get('employee_id');
        $today      = date('Y-m-d');
        $now        = date('Y-m-d H:i:s');

        $attModel = new AttendanceModel();

        // Cegah duplikasi check-in di hari yang sama
        $existing = $attModel->where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return redirect()->to('/employee/attendance')->with('error', 'Anda sudah melakukan check-in hari ini.');
        }

        // Ambil jadwal kerja untuk cek keterlambatan
        $empModel = new EmployeeModel();
        $employee = $empModel->select('work_schedules.start_time, work_schedules.late_tolerance_minutes')
            ->join('work_schedules', 'work_schedules.id = employees.work_schedule_id', 'left')
            ->find($employeeId);

        $startTime = $employee['start_time'] ?? '08:00:00';
        $tolerance = (int) ($employee['late_tolerance_minutes'] ?? 15);

        $cutoffTimestamp = strtotime($today . ' ' . $startTime) + ($tolerance * 60);
        $status          = (time() > $cutoffTimestamp) ? 'late' : 'present';

        $attModel->insert([
            'employee_id' => $employeeId,
            'date'        => $today,
            'check_in'    => $now,
            'status'      => $status,
            'notes'       => trim((string) $this->request->getPost('notes')) ?: null,
        ]);

        $statusLabel = ($status === 'late') ? 'Terlambat' : 'Hadir';
        log_activity('CHECK_IN', 'attendance', "Check-in pukul " . date('H:i') . " WIB ({$statusLabel})");

        return redirect()->to('/employee/attendance')->with('success', "Check-in berhasil dicatat pukul " . date('H:i') . " WIB ({$statusLabel}).");
    }

    public function checkOut()
    {
        $employeeId = (int) session()->get('employee_id');
        $today      = date('Y-m-d');
        $now        = date('Y-m-d H:i:s');

        $attModel = new AttendanceModel();
        $att      = $attModel->where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if (!$att) {
            return redirect()->to('/employee/attendance')->with('error', 'Anda belum melakukan check-in hari ini.');
        }

        if (!empty($att['check_out'])) {
            return redirect()->to('/employee/attendance')->with('error', 'Anda sudah melakukan check-out hari ini.');
        }

        $durationMinutes = max(0, (int) round((strtotime($now) - strtotime($att['check_in'])) / 60));
        $notes           = trim((string) $this->request->getPost('notes'));

        $updateData = [
            'check_out'             => $now,
            'work_duration_minutes' => $durationMinutes,
        ];

        if ($notes !== '') {
            $updateData['notes'] = $notes;
        }

        $attModel->update($att['id'], $updateData);
        log_activity('CHECK_OUT', 'attendance', "Check-out pukul " . date('H:i') . " WIB (Durasi: {$durationMinutes} menit)");

        return redirect()->to('/employee/attendance')->with('success', 'Check-out berhasil dicatat pukul ' . date('H:i') . ' WIB.');
    }
}