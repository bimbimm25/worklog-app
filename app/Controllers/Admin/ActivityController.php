<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityCommentModel;
use App\Models\DepartmentModel;
use App\Models\WorkActivityModel;

class ActivityController extends BaseController
{
    protected WorkActivityModel $activityModel;

    public function __construct()
    {
        $this->activityModel = new WorkActivityModel();
    }

    public function index()
    {
        $keyword      = trim((string) $this->request->getGet('q'));
        $departmentId = (string) ($this->request->getGet('department_id') ?: '');
        $status       = (string) ($this->request->getGet('status') ?: '');
        $date         = (string) ($this->request->getGet('date') ?: '');

        $builder = $this->activityModel->select('work_activities.*, employees.full_name, employees.employee_code, departments.name as department_name')
            ->join('employees', 'employees.id = work_activities.employee_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('work_activities.approval_status !=', 'draft')
            ->orderBy('work_activities.activity_date', 'DESC')
            ->orderBy('work_activities.created_at', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('work_activities.title', $keyword)
                ->orLike('employees.full_name', $keyword)
                ->groupEnd();
        }

        if ($departmentId !== '') {
            $builder->where('employees.department_id', $departmentId);
        }

        if ($status !== '') {
            $builder->where('work_activities.approval_status', $status);
        }

        if ($date !== '') {
            $builder->where('work_activities.activity_date', $date);
        }

        return view('admin/activities/index', [
            'pageTitle'      => 'Monitoring Aktivitas & Laporan',
            'activities'     => $builder->findAll(),
            'departments'    => (new DepartmentModel())->orderBy('name', 'ASC')->findAll(),
            'keyword'        => $keyword,
            'departmentId'   => $departmentId,
            'selectedStatus' => $status,
            'selectedDate'   => $date,
        ]);
    }

    public function detail(int $id)
    {
        $activity = $this->activityModel->select('work_activities.*, employees.full_name, employees.employee_code, employees.user_id as employee_user_id, departments.name as department_name, positions.name as position_name')
            ->join('employees', 'employees.id = work_activities.employee_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->join('positions', 'positions.id = employees.position_id', 'left')
            ->where('work_activities.id', $id)
            ->first();

        if (!$activity) {
            return redirect()->to('/admin/activities')->with('error', 'Aktivitas tidak ditemukan.');
        }

        $commentModel = new ActivityCommentModel();
        $comments     = $commentModel->select('activity_comments.*, users.role, employees.full_name')
            ->join('users', 'users.id = activity_comments.user_id')
            ->join('employees', 'employees.user_id = users.id', 'left')
            ->where('activity_comments.activity_id', $id)
            ->orderBy('activity_comments.created_at', 'ASC')
            ->findAll();

        return view('admin/activities/detail', [
            'pageTitle' => 'Review Aktivitas Karyawan',
            'activity'  => $activity,
            'comments'  => $comments,
        ]);
    }

    public function approve(int $id)
    {
        $activity = $this->activityModel->select('work_activities.*, employees.user_id as employee_user_id, employees.full_name')
            ->join('employees', 'employees.id = work_activities.employee_id')
            ->where('work_activities.id', $id)
            ->first();

        if (!$activity) {
            return redirect()->to('/admin/activities')->with('error', 'Aktivitas tidak ditemukan.');
        }

        $this->activityModel->update($id, [
            'approval_status'  => 'approved',
            'approved_by'      => (int) session()->get('user_id'),
            'approved_at'      => date('Y-m-d H:i:s'),
            'rejection_reason' => null,
        ]);

        send_notification(
            (int) $activity['employee_user_id'],
            'Laporan Aktivitas Disetujui',
            "Laporan \"{$activity['title']}\" telah disetujui oleh Admin.",
            base_url('employee/activities/detail/' . $id)
        );

        log_activity('APPROVE', 'work_activities', "Menyetujui laporan aktivitas #{$id} ({$activity['full_name']})");
        return redirect()->to('/admin/activities/detail/' . $id)->with('success', 'Laporan aktivitas berhasil disetujui.');
    }

    public function reject(int $id)
    {
        $activity = $this->activityModel->select('work_activities.*, employees.user_id as employee_user_id, employees.full_name')
            ->join('employees', 'employees.id = work_activities.employee_id')
            ->where('work_activities.id', $id)
            ->first();

        if (!$activity) {
            return redirect()->to('/admin/activities')->with('error', 'Aktivitas tidak ditemukan.');
        }

        $reason = trim((string) $this->request->getPost('rejection_reason'));
        if ($reason === '') {
            return redirect()->back()->with('error', 'Alasan penolakan wajib diisi.');
        }

        $this->activityModel->update($id, [
            'approval_status'  => 'rejected',
            'approved_by'      => (int) session()->get('user_id'),
            'approved_at'      => date('Y-m-d H:i:s'),
            'rejection_reason' => $reason,
        ]);

        send_notification(
            (int) $activity['employee_user_id'],
            'Laporan Aktivitas Ditolak',
            "Laporan \"{$activity['title']}\" ditolak. Alasan: {$reason}",
            base_url('employee/activities/detail/' . $id)
        );

        log_activity('REJECT', 'work_activities', "Menolak laporan aktivitas #{$id} ({$activity['full_name']})");
        return redirect()->to('/admin/activities/detail/' . $id)->with('success', 'Laporan aktivitas ditolak dan notifikasi dikirim ke karyawan.');
    }

    public function comment(int $id)
    {
        $activity = $this->activityModel->select('work_activities.*, employees.user_id as employee_user_id')
            ->join('employees', 'employees.id = work_activities.employee_id')
            ->where('work_activities.id', $id)
            ->first();

        if (!$activity) {
            return redirect()->to('/admin/activities')->with('error', 'Aktivitas tidak ditemukan.');
        }

        if (!$this->validate(['comment' => 'required|min_length[2]'])) {
            return redirect()->back()->with('error', 'Komentar tidak boleh kosong.');
        }

        $commentText  = trim((string) $this->request->getPost('comment'));
        $commentModel = new ActivityCommentModel();
        $commentModel->insert([
            'activity_id' => $id,
            'user_id'     => (int) session()->get('user_id'),
            'comment'     => $commentText,
        ]);

        send_notification(
            (int) $activity['employee_user_id'],
            'Komentar Baru dari Admin',
            "Admin mengomentari laporan \"{$activity['title']}\": " . mb_strimwidth($commentText, 0, 80, '...'),
            base_url('employee/activities/detail/' . $id)
        );

        return redirect()->to('/admin/activities/detail/' . $id)->with('success', 'Komentar berhasil ditambahkan.');
    }
}