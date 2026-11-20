<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DepartmentModel;
use App\Models\EmployeeModel;
use App\Models\PositionModel;
use App\Models\UserModel;
use App\Models\WorkScheduleModel;

class EmployeeController extends BaseController
{
    public function index()
    {
        $keyword      = trim((string) $this->request->getGet('q'));
        $departmentId = (string) $this->request->getGet('department_id');

        $empModel = new EmployeeModel();
        $builder  = $empModel->select('employees.*, users.email, users.role, users.is_active, departments.name as department_name, positions.name as position_name, work_schedules.name as schedule_name')
            ->join('users', 'users.id = employees.user_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->join('positions', 'positions.id = employees.position_id', 'left')
            ->join('work_schedules', 'work_schedules.id = employees.work_schedule_id', 'left')
            ->orderBy('employees.created_at', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('employees.full_name', $keyword)
                ->orLike('employees.employee_code', $keyword)
                ->orLike('users.email', $keyword)
                ->groupEnd();
        }

        if ($departmentId !== '') {
            $builder->where('employees.department_id', $departmentId);
        }

        return view('admin/employees/index', [
            'pageTitle'    => 'Manajemen Karyawan',
            'employees'    => $builder->findAll(),
            'departments'  => (new DepartmentModel())->orderBy('name', 'ASC')->findAll(),
            'positions'    => (new PositionModel())->orderBy('name', 'ASC')->findAll(),
            'schedules'    => (new WorkScheduleModel())->orderBy('name', 'ASC')->findAll(),
            'keyword'      => $keyword,
            'departmentId' => $departmentId,
        ]);
    }

    public function store()
    {
        $rules = [
            'employee_code' => 'required|max_length[30]|is_unique[employees.employee_code]',
            'full_name'     => 'required|min_length[3]|max_length[150]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'password'      => 'required|min_length[6]',
            'role'          => 'required|in_list[admin,employee]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $db        = db_connect();
        $userModel = new UserModel();
        $empModel  = new EmployeeModel();

        $db->transStart();
        $userId = $userModel->insert([
            'email'         => trim((string) $this->request->getPost('email')),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => $this->request->getPost('role'),
            'is_active'     => 1,
        ], true);

        $fullName = trim((string) $this->request->getPost('full_name'));
        $empModel->insert([
            'user_id'          => $userId,
            'employee_code'    => strtoupper(trim((string) $this->request->getPost('employee_code'))),
            'full_name'        => $fullName,
            'phone'            => trim((string) $this->request->getPost('phone')),
            'department_id'    => $this->request->getPost('department_id') ?: null,
            'position_id'      => $this->request->getPost('position_id') ?: null,
            'work_schedule_id' => $this->request->getPost('work_schedule_id') ?: null,
            'join_date'        => $this->request->getPost('join_date') ?: date('Y-m-d'),
        ]);
        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data karyawan.');
        }

        log_activity('CREATE', 'employees', "Menambahkan karyawan {$fullName}");
        return redirect()->to('/admin/employees')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $empModel  = new EmployeeModel();
        $userModel = new UserModel();

        $emp = $empModel->find($id);
        if (!$emp) {
            return redirect()->to('/admin/employees')->with('error', 'Karyawan tidak ditemukan.');
        }

        $userId = (int) $emp['user_id'];
        $rules  = [
            'employee_code' => "required|max_length[30]|is_unique[employees.employee_code,id,{$id}]",
            'full_name'     => 'required|min_length[3]|max_length[150]',
            'email'         => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'role'          => 'required|in_list[admin,employee]',
            'is_active'     => 'required|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $db = db_connect();
        $db->transStart();

        $userData = [
            'email'     => trim((string) $this->request->getPost('email')),
            'role'      => $this->request->getPost('role'),
            'is_active' => (int) $this->request->getPost('is_active'),
        ];

        $newPassword = (string) $this->request->getPost('password');
        if ($newPassword !== '') {
            if (strlen($newPassword) < 6) {
                return redirect()->back()->with('error', 'Password baru minimal 6 karakter.');
            }
            $userData['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $userModel->update($userId, $userData);

        $fullName = trim((string) $this->request->getPost('full_name'));
        $empModel->update($id, [
            'employee_code'    => strtoupper(trim((string) $this->request->getPost('employee_code'))),
            'full_name'        => $fullName,
            'phone'            => trim((string) $this->request->getPost('phone')),
            'department_id'    => $this->request->getPost('department_id') ?: null,
            'position_id'      => $this->request->getPost('position_id') ?: null,
            'work_schedule_id' => $this->request->getPost('work_schedule_id') ?: null,
            'join_date'        => $this->request->getPost('join_date') ?: null,
        ]);
        $db->transComplete();

        log_activity('UPDATE', 'employees', "Memperbarui data karyawan {$fullName}");
        return redirect()->to('/admin/employees')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $empModel  = new EmployeeModel();
        $userModel = new UserModel();

        $emp = $empModel->find($id);
        if (!$emp) {
            return redirect()->to('/admin/employees')->with('error', 'Karyawan tidak ditemukan.');
        }

        if ((int) $emp['user_id'] === (int) session()->get('user_id')) {
            return redirect()->to('/admin/employees')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Menghapus user otomatis menghapus employee karena FK ON DELETE CASCADE
        $userModel->delete($emp['user_id']);
        log_activity('DELETE', 'employees', "Menghapus karyawan {$emp['full_name']}");
        return redirect()->to('/admin/employees')->with('success', 'Karyawan berhasil dihapus.');
    }
}