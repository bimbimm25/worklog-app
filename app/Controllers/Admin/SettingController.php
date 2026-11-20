<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class SettingController extends BaseController
{
    public function index()
    {
        $settingModel = new SettingModel();

        return view('admin/settings/index', [
            'pageTitle' => 'Pengaturan Sistem',
            'settings'  => $settingModel->getAllAsKeyValue(),
        ]);
    }

    public function update()
    {
        $rules = [
            'company_name'           => 'required|min_length[2]|max_length[150]',
            'company_address'        => 'permit_empty|max_length[255]',
            'default_late_tolerance' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $settingModel = new SettingModel();
        $settingModel->setValue('company_name', trim((string) $this->request->getPost('company_name')));
        $settingModel->setValue('company_address', trim((string) $this->request->getPost('company_address')));
        $settingModel->setValue('default_late_tolerance', (string) (int) $this->request->getPost('default_late_tolerance'));

        log_activity('UPDATE', 'settings', 'Memperbarui pengaturan sistem perusahaan');
        return redirect()->to('/admin/settings')->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}

?>