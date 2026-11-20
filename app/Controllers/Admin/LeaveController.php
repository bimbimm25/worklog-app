<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LeaveRequestModel;

class LeaveController extends BaseController
{
    public function index()
    {
        $status     = (string) ($this->request->getGet('status') ?: '');
        $leaveModel = new LeaveRequestModel();

        $builder = $leaveModel->select('leave_requests.*, employees.full_name, employees.employee_code, departments.name as department_name')
            ->join('employees', 'employees.id = leave_requests.employee_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->orderBy('leave_requests.created_at', 'DESC');

        if ($status !== '') {
            $builder->where('leave_requests.status', $status);
        }

        return view('admin/leaves/index', [
            'pageTitle'      => 'Pengajuan Izin & Cuti',
            'leaves'         => $builder->findAll(),
            'selectedStatus' => $status,
        ]);
    }

    public function review(int $id)
    {
        $leaveModel = new LeaveRequestModel();
        $leave      = $leaveModel->select('leave_requests.*, employees.user_id as employee_user_id, employees.full_name')
            ->join('employees', 'employees.id = leave_requests.employee_id')
            ->where('leave_requests.id', $id)
            ->first();

        if (!$leave) {
            return redirect()->to('/admin/leaves')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $decision   = (string) $this->request->getPost('decision');
        $reviewNote = trim((string) $this->request->getPost('review_note'));

        if (!in_array($decision, ['approved', 'rejected'], true)) {
            return redirect()->back()->with('error', 'Keputusan tidak valid.');
        }

        $leaveModel->update($id, [
            'status'      => $decision,
            'reviewed_by' => (int) session()->get('user_id'),
            'review_note' => $reviewNote ?: null,
        ]);

        $statusText = ($decision === 'approved') ? 'Disetujui' : 'Ditolak';
        send_notification(
            (int) $leave['employee_user_id'],
            "Pengajuan " . ucfirst($leave['type']) . " {$statusText}",
            "Pengajuan {$leave['type']} tanggal {$leave['start_date']} s/d {$leave['end_date']} telah {$statusText}." . ($reviewNote ? " Catatan: {$reviewNote}" : ''),
            base_url('employee/leaves')
        );

        log_activity(strtoupper($decision), 'leave_requests', "Pengajuan {$leave['type']} milik {$leave['full_name']} {$statusText}");
        return redirect()->to('/admin/leaves')->with('success', "Pengajuan berhasil {$statusText}.");
    }
}