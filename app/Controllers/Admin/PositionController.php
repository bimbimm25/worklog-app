<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PositionModel;

class PositionController extends BaseController
{
    protected PositionModel $positionModel;

    public function __construct()
    {
        $this->positionModel = new PositionModel();
    }

    public function index()
    {
        return view('admin/positions/index', [
            'pageTitle' => 'Manajemen Jabatan',
            'positions' => $this->positionModel->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function store()
    {
        if (!$this->validate(['name' => 'required|min_length[2]|max_length[100]'])) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $name = trim((string) $this->request->getPost('name'));
        $this->positionModel->insert([
            'name'        => $name,
            'description' => trim((string) $this->request->getPost('description')),
        ]);

        log_activity('CREATE', 'positions', "Menambahkan jabatan {$name}");
        return redirect()->to('/admin/positions')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $pos = $this->positionModel->find($id);
        if (!$pos) {
            return redirect()->to('/admin/positions')->with('error', 'Jabatan tidak ditemukan.');
        }

        if (!$this->validate(['name' => 'required|min_length[2]|max_length[100]'])) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $name = trim((string) $this->request->getPost('name'));
        $this->positionModel->update($id, [
            'name'        => $name,
            'description' => trim((string) $this->request->getPost('description')),
        ]);

        log_activity('UPDATE', 'positions', "Memperbarui jabatan {$name}");
        return redirect()->to('/admin/positions')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $pos = $this->positionModel->find($id);
        if (!$pos) {
            return redirect()->to('/admin/positions')->with('error', 'Jabatan tidak ditemukan.');
        }

        $this->positionModel->delete($id);
        log_activity('DELETE', 'positions', "Menghapus jabatan {$pos['name']}");
        return redirect()->to('/admin/positions')->with('success', 'Jabatan berhasil dihapus.');
    }
}