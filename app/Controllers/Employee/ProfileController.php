<?php

namespace App\Controllers\Employee;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\UserModel;

class ProfileController extends BaseController
{
    public function index()
    {
        $employeeId = (int) session()->get('employee_id');
        $empModel   = new EmployeeModel();

        $profile = $empModel->select('employees.*, users.email, users.last_login, departments.name as department_name, positions.name as position_name, work_schedules.name as schedule_name')
            ->join('users', 'users.id = employees.user_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->join('positions', 'positions.id = employees.position_id', 'left')
            ->join('work_schedules', 'work_schedules.id = employees.work_schedule_id', 'left')
            ->find($employeeId);

        return view('employee/profile/index', [
            'pageTitle' => 'Profil Saya',
            'profile'   => $profile,
        ]);
    }

    public function update()
    {
        $employeeId = (int) session()->get('employee_id');
        $empModel   = new EmployeeModel();
        $emp        = $empModel->find($employeeId);

        if (!$emp) {
            return redirect()->to('/employee/profile')->with('error', 'Data profil tidak ditemukan.');
        }

        $rules = [
            'full_name' => 'required|min_length[3]|max_length[150]',
            'phone'     => 'permit_empty|max_length[20]',
        ];

        $file = $this->request->getFile('avatar');
        if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['avatar'] = 'max_size[avatar,2048]|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $avatarName = $emp['avatar'];
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if ($avatarName && is_file(FCPATH . 'uploads/avatars/' . $avatarName)) {
                unlink(FCPATH . 'uploads/avatars/' . $avatarName);
            }
            $avatarName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/avatars', $avatarName);
        }

        $fullName = trim((string) $this->request->getPost('full_name'));
        $empModel->update($employeeId, [
            'full_name' => $fullName,
            'phone'     => trim((string) $this->request->getPost('phone')),
            'avatar'    => $avatarName,
        ]);

        // Perbarui session
        session()->set([
            'full_name' => $fullName,
            'avatar'    => $avatarName,
        ]);

        log_activity('UPDATE', 'profile', 'Memperbarui informasi profil pribadi');
        return redirect()->to('/employee/profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $userId    = (int) session()->get('user_id');
        $userModel = new UserModel();
        $user      = $userModel->find($userId);

        if (!$user || !password_verify((string) $this->request->getPost('current_password'), $user['password_hash'])) {
            return redirect()->back()->with('error', 'Password saat ini tidak sesuai.');
        }

        $userModel->update($userId, [
            'password_hash' => password_hash((string) $this->request->getPost('new_password'), PASSWORD_DEFAULT),
        ]);

        log_activity('UPDATE_PASSWORD', 'profile', 'Mengubah password akun');
        return redirect()->to('/employee/profile')->with('success', 'Password berhasil diperbarui.');
    }
}