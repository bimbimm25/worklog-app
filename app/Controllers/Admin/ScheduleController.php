<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WorkScheduleModel;

class ScheduleController extends BaseController
{
    protected WorkScheduleModel $scheduleModel;

    public function __construct()
    {
        $this->scheduleModel = new WorkScheduleModel();
    }

    public function index()
    {
        return view('admin/schedules/index', [
            'pageTitle' => 'Jadwal Kerja',
            'schedules' => $this->scheduleModel->orderBy('start_time', 'ASC')->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'name'                   => 'required|min_length[2]|max_length[100]',
            'start_time'             => 'required',
            'end_time'               => 'required',
            'late_tolerance_minutes' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $name = trim((string) $this->request->getPost('name'));
        $this->scheduleModel->insert([
            'name'                   => $name,
            'start_time'             => $this->request->getPost('start_time'),
            'end_time'               => $this->request->getPost('end_time'),
            'late_tolerance_minutes' => (int) $this->request->getPost('late_tolerance_minutes'),
        ]);

        log_activity('CREATE', 'work_schedules', "Menambahkan jadwal kerja {$name}");
        return redirect()->to('/admin/schedules')->with('success', 'Jadwal kerja berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $schedule = $this->scheduleModel->find($id);
        if (!$schedule) {
            return redirect()->to('/admin/schedules')->with('error', 'Jadwal kerja tidak ditemukan.');
        }

        $rules = [
            'name'                   => 'required|min_length[2]|max_length[100]',
            'start_time'             => 'required',
            'end_time'               => 'required',
            'late_tolerance_minutes' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $name = trim((string) $this->request->getPost('name'));
        $this->scheduleModel->update($id, [
            'name'                   => $name,
            'start_time'             => $this->request->getPost('start_time'),
            'end_time'               => $this->request->getPost('end_time'),
            'late_tolerance_minutes' => (int) $this->request->getPost('late_tolerance_minutes'),
        ]);

        log_activity('UPDATE', 'work_schedules', "Memperbarui jadwal kerja {$name}");
        return redirect()->to('/admin/schedules')->with('success', 'Jadwal kerja berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $schedule = $this->scheduleModel->find($id);
        if (!$schedule) {
            return redirect()->to('/admin/schedules')->with('error', 'Jadwal kerja tidak ditemukan.');
        }

        $this->scheduleModel->delete($id);
        log_activity('DELETE', 'work_schedules', "Menghapus jadwal kerja {$schedule['name']}");
        return redirect()->to('/admin/schedules')->with('success', 'Jadwal kerja berhasil dihapus.');
    }
}