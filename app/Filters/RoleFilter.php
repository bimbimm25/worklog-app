<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userRole = session()->get('role');

        if (empty($arguments) || !in_array($userRole, $arguments, true)) {
            $redirectUrl = ($userRole === 'admin') ? '/admin/dashboard' : '/employee/dashboard';
            return redirect()->to($redirectUrl)->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi tambahan
    }
}