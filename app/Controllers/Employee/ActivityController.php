<?php

namespace App\Controllers\Employee;

use App\Controllers\BaseController;
use App\Models\ActivityCommentModel;
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
        $employeeId = (int) session()->get('employee_id');
        $status     = (string) ($this->request->getGet('status') ?: '');
        $date       = (string) ($this->request->getGet('date') ?: '');

        $builder = $this->activityModel->where('employee_id', $employeeId)
            ->orderBy('activity_date', 'DESC')
            ->orderBy('created_at', 'DESC');

        if ($status !== '') {
            $builder->where('approval_status', $status);
        }

        if ($date !== '') {
            $builder->where('activity_date', $date);
        }

        return view('employee/activities/index', [
            'pageTitle'      => 'Aktivitas Harian Saya',
            'activities'     => $builder->findAll(),
            'selectedStatus' => $status,
            'selectedDate'   => $date,
        ]);
    }

    public function store()
    {
        $rules = [
            'title'         => 'required|min_length[3]|max_length[200]',
            'description'   => 'required|min_length[5]',
            'activity_date' => 'required|valid_date',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'progress'      => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
            'work_status'   => 'required|in_list[in_progress,completed,blocked]',
            'submit_action' => 'required|in_list[draft,pending]',
        ];

        $file = $this->request->getFile('attachment');
        if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['attachment'] = 'max_size[attachment,5120]|ext_in[attachment,jpg,jpeg,png,pdf,doc,docx,xls,xlsx]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $attachmentName = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $attachmentName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/activities', $attachmentName);
        }

        $title          = trim((string) $this->request->getPost('title'));
        $approvalStatus = (string) $this->request->getPost('submit_action');

        $this->activityModel->insert([
            'employee_id'     => (int) session()->get('employee_id'),
            'title'           => $title,
            'description'     => trim((string) $this->request->getPost('description')),
            'activity_date'   => $this->request->getPost('activity_date'),
            'start_time'      => $this->request->getPost('start_time'),
            'end_time'        => $this->request->getPost('end_time'),
            'progress'        => (int) $this->request->getPost('progress'),
            'work_status'     => $this->request->getPost('work_status'),
            'approval_status' => $approvalStatus,
            'attachment'      => $attachmentName,
        ]);

        log_activity('CREATE', 'work_activities', "Mencatat aktivitas: {$title} (Status: {$approvalStatus})");
        return redirect()->to('/employee/activities')->with('success', 'Aktivitas berhasil disimpan.');
    }

    public function update(int $id)
    {
        $employeeId = (int) session()->get('employee_id');
        $activity   = $this->activityModel->where('id', $id)->where('employee_id', $employeeId)->first();

        if (!$activity) {
            return redirect()->to('/employee/activities')->with('error', 'Aktivitas tidak ditemukan atau akses ditolak.');
        }

        if (!in_array($activity['approval_status'], ['draft', 'rejected'], true)) {
            return redirect()->to('/employee/activities')->with('error', 'Aktivitas yang sedang menunggu ACC atau sudah disetujui tidak dapat diubah.');
        }

        $rules = [
            'title'         => 'required|min_length[3]|max_length[200]',
            'description'   => 'required|min_length[5]',
            'activity_date' => 'required|valid_date',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'progress'      => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
            'work_status'   => 'required|in_list[in_progress,completed,blocked]',
            'submit_action' => 'required|in_list[draft,pending]',
        ];

        $file = $this->request->getFile('attachment');
        if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['attachment'] = 'max_size[attachment,5120]|ext_in[attachment,jpg,jpeg,png,pdf,doc,docx,xls,xlsx]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $attachmentName = $activity['attachment'];
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Hapus lampiran lama jika ada
            if ($attachmentName && is_file(FCPATH . 'uploads/activities/' . $attachmentName)) {
                unlink(FCPATH . 'uploads/activities/' . $attachmentName);
            }
            $attachmentName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/activities', $attachmentName);
        }

        $title          = trim((string) $this->request->getPost('title'));
        $approvalStatus = (string) $this->request->getPost('submit_action');

        $this->activityModel->update($id, [
            'title'            => $title,
            'description'      => trim((string) $this->request->getPost('description')),
            'activity_date'    => $this->request->getPost('activity_date'),
            'start_time'       => $this->request->getPost('start_time'),
            'end_time'         => $this->request->getPost('end_time'),
            'progress'         => (int) $this->request->getPost('progress'),
            'work_status'      => $this->request->getPost('work_status'),
            'approval_status'  => $approvalStatus,
            'attachment'       => $attachmentName,
            'rejection_reason' => ($approvalStatus === 'pending') ? null : $activity['rejection_reason'],
        ]);

        log_activity('UPDATE', 'work_activities', "Memperbarui aktivitas: {$title} (Status: {$approvalStatus})");
        return redirect()->to('/employee/activities')->with('success', 'Aktivitas berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $employeeId = (int) session()->get('employee_id');
        $activity   = $this->activityModel->where('id', $id)->where('employee_id', $employeeId)->first();

        if (!$activity) {
            return redirect()->to('/employee/activities')->with('error', 'Aktivitas tidak ditemukan.');
        }

        if ($activity['approval_status'] !== 'draft') {
            return redirect()->to('/employee/activities')->with('error', 'Hanya aktivitas berstatus Draft yang dapat dihapus.');
        }

        if ($activity['attachment'] && is_file(FCPATH . 'uploads/activities/' . $activity['attachment'])) {
            unlink(FCPATH . 'uploads/activities/' . $activity['attachment']);
        }

        $this->activityModel->delete($id);
        log_activity('DELETE', 'work_activities', "Menghapus draft aktivitas: {$activity['title']}");
        return redirect()->to('/employee/activities')->with('success', 'Draft aktivitas berhasil dihapus.');
    }

    public function detail(int $id)
    {
        $employeeId = (int) session()->get('employee_id');
        $activity   = $this->activityModel->where('id', $id)->where('employee_id', $employeeId)->first();

        if (!$activity) {
            return redirect()->to('/employee/activities')->with('error', 'Aktivitas tidak ditemukan atau akses ditolak.');
        }

        $commentModel = new ActivityCommentModel();
        $comments     = $commentModel->select('activity_comments.*, users.role, employees.full_name')
            ->join('users', 'users.id = activity_comments.user_id')
            ->join('employees', 'employees.user_id = users.id', 'left')
            ->where('activity_comments.activity_id', $id)
            ->orderBy('activity_comments.created_at', 'ASC')
            ->findAll();

        return view('employee/activities/detail', [
            'pageTitle' => 'Detail Aktivitas',
            'activity'  => $activity,
            'comments'  => $comments,
        ]);
    }

    public function comment(int $id)
    {
        $employeeId = (int) session()->get('employee_id');
        $activity   = $this->activityModel->where('id', $id)->where('employee_id', $employeeId)->first();

        if (!$activity) {
            return redirect()->to('/employee/activities')->with('error', 'Akses ditolak.');
        }

        if (!$this->validate(['comment' => 'required|min_length[2]'])) {
            return redirect()->back()->with('error', 'Komentar tidak boleh kosong.');
        }

        $commentModel = new ActivityCommentModel();
        $commentModel->insert([
            'activity_id' => $id,
            'user_id'     => (int) session()->get('user_id'),
            'comment'     => trim((string) $this->request->getPost('comment')),
        ]);

        return redirect()->to('/employee/activities/detail/' . $id)->with('success', 'Komentar berhasil dikirim.');
    }
}