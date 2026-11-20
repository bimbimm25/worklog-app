<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\TaskModel;
use App\Models\TaskUpdateModel;

class TaskController extends BaseController
{
    protected TaskModel $taskModel;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
    }

    public function index()
    {
        $status     = (string) ($this->request->getGet('status') ?: '');
        $priority   = (string) ($this->request->getGet('priority') ?: '');
        $employeeId = (string) ($this->request->getGet('employee_id') ?: '');

        $builder = $this->taskModel->select('tasks.*, employees.full_name, employees.employee_code')
            ->join('employees', 'employees.id = tasks.assigned_to')
            ->orderBy('tasks.deadline', 'ASC');

        if ($status !== '') {
            $builder->where('tasks.status', $status);
        }
        if ($priority !== '') {
            $builder->where('tasks.priority', $priority);
        }
        if ($employeeId !== '') {
            $builder->where('tasks.assigned_to', $employeeId);
        }

        $empModel  = new EmployeeModel();
        $employees = $empModel->select('employees.id, employees.full_name, employees.employee_code')
            ->join('users', 'users.id = employees.user_id')
            ->where('users.role', 'employee')
            ->where('users.is_active', 1)
            ->orderBy('employees.full_name', 'ASC')
            ->findAll();

        return view('admin/tasks/index', [
            'pageTitle'        => 'Manajemen Tugas',
            'tasks'            => $builder->findAll(),
            'employees'        => $employees,
            'selectedStatus'   => $status,
            'selectedPriority' => $priority,
            'selectedEmployee' => $employeeId,
        ]);
    }

    public function store()
    {
        $rules = [
            'title'       => 'required|min_length[3]|max_length[200]',
            'assigned_to' => 'required|integer',
            'priority'    => 'required|in_list[low,medium,high,urgent]',
            'deadline'    => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $assignedTo = (int) $this->request->getPost('assigned_to');
        $title      = trim((string) $this->request->getPost('title'));
        $deadline   = (string) $this->request->getPost('deadline');

        $this->taskModel->insert([
            'title'       => $title,
            'description' => trim((string) $this->request->getPost('description')),
            'assigned_to' => $assignedTo,
            'assigned_by' => (int) session()->get('user_id'),
            'priority'    => $this->request->getPost('priority'),
            'status'      => 'todo',
            'progress'    => 0,
            'deadline'    => $deadline,
        ]);

        // Kirim notifikasi ke karyawan
        $emp = (new EmployeeModel())->find($assignedTo);
        if ($emp) {
            send_notification(
                (int) $emp['user_id'],
                'Tugas Baru Diberikan',
                "Anda mendapat tugas baru: \"{$title}\" (Deadline: " . date('d M Y', strtotime($deadline)) . ').',
                base_url('employee/tasks')
            );
        }

        log_activity('CREATE', 'tasks', "Membuat tugas \"{$title}\" untuk employee #{$assignedTo}");
        return redirect()->to('/admin/tasks')->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            return redirect()->to('/admin/tasks')->with('error', 'Tugas tidak ditemukan.');
        }

        $rules = [
            'title'       => 'required|min_length[3]|max_length[200]',
            'assigned_to' => 'required|integer',
            'priority'    => 'required|in_list[low,medium,high,urgent]',
            'status'      => 'required|in_list[todo,in_progress,review,completed]',
            'deadline'    => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $title       = trim((string) $this->request->getPost('title'));
        $newDeadline = (string) $this->request->getPost('deadline');
        $assignedTo  = (int) $this->request->getPost('assigned_to');

        $this->taskModel->update($id, [
            'title'       => $title,
            'description' => trim((string) $this->request->getPost('description')),
            'assigned_to' => $assignedTo,
            'priority'    => $this->request->getPost('priority'),
            'status'      => $this->request->getPost('status'),
            'deadline'    => $newDeadline,
        ]);

        // Notifikasi jika deadline berubah
        if ($task['deadline'] !== $newDeadline) {
            $emp = (new EmployeeModel())->find($assignedTo);
            if ($emp) {
                send_notification(
                    (int) $emp['user_id'],
                    'Perubahan Deadline Tugas',
                    "Deadline tugas \"{$title}\" diubah menjadi " . date('d M Y', strtotime($newDeadline)) . '.',
                    base_url('employee/tasks')
                );
            }
        }

        log_activity('UPDATE', 'tasks', "Memperbarui tugas #{$id} ({$title})");
        return redirect()->to('/admin/tasks')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $task = $this->taskModel->find($id);
        if (!$task) {
            return redirect()->to('/admin/tasks')->with('error', 'Tugas tidak ditemukan.');
        }

        $this->taskModel->delete($id);
        log_activity('DELETE', 'tasks', "Menghapus tugas \"{$task['title']}\"");
        return redirect()->to('/admin/tasks')->with('success', 'Tugas berhasil dihapus.');
    }
}