<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('is_logged_in')) {
            return $this->redirectByRole(session()->get('role'));
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        $userModel = new UserModel();
        $user      = $userModel->findByEmailWithEmployee($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        if ((int) $user['is_active'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'Akun Anda dinonaktifkan. Hubungi Administrator.');
        }

        // Regenerate session ID untuk mencegah session fixation
        session()->regenerate(true);
        session()->set([
            'is_logged_in'  => true,
            'user_id'       => $user['id'],
            'employee_id'   => $user['employee_id'],
            'employee_code' => $user['employee_code'],
            'full_name'     => $user['full_name'] ?? $user['email'],
            'email'         => $user['email'],
            'role'          => $user['role'],
            'avatar'        => $user['avatar'],
        ]);

        // Update waktu login terakhir & catat di activity_logs
        $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
        log_activity('LOGIN', 'auth', 'Pengguna berhasil login ke sistem', (int) $user['id']);

        return $this->redirectByRole($user['role'])->with('success', 'Selamat datang kembali, ' . ($user['full_name'] ?? $user['email']) . '!');
    }

    public function logout()
    {
        if (session()->get('is_logged_in')) {
            log_activity('LOGOUT', 'auth', 'Pengguna logout dari sistem');
        }

        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout.');
    }

    private function redirectByRole(string $role)
    {
        return ($role === 'admin')
            ? redirect()->to('/admin/dashboard')
            : redirect()->to('/employee/dashboard');
    }
}