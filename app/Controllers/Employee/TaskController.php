<?php

namespace App\Controllers\Employee;

use App\Controllers\BaseController;
use App\Models\TaskModel;
use App\Models\TaskUpdateModel;

class TaskController extends BaseController
{
    public function index()
    {
        $employeeId = (int) session()->get('employee_id');
        $status     = (string) ($this->request->getGet('status') ?: '');

        $taskModel = new TaskModel();
        $builder   = $taskModel->where('assigned_to', $employeeId)
            ->orderBy('deadline', 'ASC');

        if ($status !== '') {
            $builder->where('status', $status);
        }

        $tasks = $builder->findAll();

        // Ambil riwayat update terakhir per tugas
        $updateModel = new TaskUpdateModel();
        $updatesMap  = [];
        foreach ($tasks as $t) {
            $updatesMap[$t['id']] = $updateModel->where('task_id', $t['id'])
                ->orderBy('created_at', 'DESC')
                ->findAll(3);
        }

        return view('employee/tasks/index', [
            'pageTitle'      => 'Tugas Saya',
            'tasks'          => $tasks,
            'updatesMap'     => $updatesMap,
            'selectedStatus' => $status,
        ]);
    }

    public function updateProgress(int $id)
    {
        $employeeId = (int) session()->get('employee_id');
        $taskModel  = new TaskModel();

        // Proteksi Anti-IDOR
        $task = $taskModel->where('id', $id)->where('assigned_to', $employeeId)->first();
        if (!$task) {
            return redirect()->to('/employee/tasks')->with('error', 'Tugas tidak ditemukan atau akses ditolak.');
        }

        $rules = [
            'progress' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
            'status'   => 'required|in_list[todo,in_progress,review,completed]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $progress = (int) $this->request->getPost('progress');
        $status   = (string) $this->request->getPost('status');
        $note     = trim((string) $this->request->getPost('note'));

        if ($status === 'completed') {
            $progress = 100;
        }

        $taskModel->update($id, [
            'progress' => $progress,
            'status'   => $status,
        ]);

        (new TaskUpdateModel())->insert([
            'task_id'  => $id,
            'user_id'  => (int) session()->get('user_id'),
            'progress' => $progress,
            'status'   => $status,
            'note'     => $note ?: null,
        ]);

        log_activity('UPDATE_TASK', 'tasks', "Memperbarui progres tugas \"{$task['title']}\" menjadi {$progress}% ({$status})");
        return redirect()->to('/employee/tasks')->with('success', 'Progres tugas berhasil diperbarui.');
    }
}