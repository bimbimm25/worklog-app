<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DepartmentModel;

class DepartmentController extends BaseController
{
    protected DepartmentModel $departmentModel;

    public function __construct()
    {
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        return view('admin/departments/index', [
            'pageTitle'   => 'Manajemen Departemen',
            'departments' => $this->departmentModel->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'code' => 'required|min_length[2]|max_length[20]|is_unique[departments.code]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $name = trim((string) $this->request->getPost('name'));
        $code = strtoupper(trim((string) $this->request->getPost('code')));

        $this->departmentModel->insert([
            'name'        => $name,
            'code'        => $code,
            'description' => trim((string) $this->request->getPost('description')),
        ]);

        log_activity('CREATE', 'departments', "Menambahkan departemen {$name} ({$code})");
        return redirect()->to('/admin/departments')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $dept = $this->departmentModel->find($id);
        if (!$dept) {
            return redirect()->to('/admin/departments')->with('error', 'Departemen tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'code' => "required|min_length[2]|max_length[20]|is_unique[departments.code,id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $name = trim((string) $this->request->getPost('name'));
        $code = strtoupper(trim((string) $this->request->getPost('code')));

        $this->departmentModel->update($id, [
            'name'        => $name,
            'code'        => $code,
            'description' => trim((string) $this->request->getPost('description')),
        ]);

        log_activity('UPDATE', 'departments', "Memperbarui departemen {$name} ({$code})");
        return redirect()->to('/admin/departments')->with('success', 'Departemen berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $dept = $this->departmentModel->find($id);
        if (!$dept) {
            return redirect()->to('/admin/departments')->with('error', 'Departemen tidak ditemukan.');
        }

        $this->departmentModel->delete($id);
        log_activity('DELETE', 'departments', "Menghapus departemen {$dept['name']}");
        return redirect()->to('/admin/departments')->with('success', 'Departemen berhasil dihapus.');
    }
}