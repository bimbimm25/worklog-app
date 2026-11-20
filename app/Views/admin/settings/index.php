<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array $settings
 */
$this->extend('layouts/admin_layout');
?>

<?php $this->section('content'); ?>
<div class="mb-3">
    <h5 class="fw-bold mb-0">Pengaturan Sistem</h5>
    <small class="text-muted">Konfigurasi identitas perusahaan dan aturan default WORKLOG</small>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="wl-card p-4">
            <form action="<?= base_url('admin/settings/update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Perusahaan</label>
                    <input type="text" name="company_name" class="form-control form-control-sm" value="<?= esc($settings['company_name'] ?? 'PT Worklog Indonesia') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Alamat Perusahaan</label>
                    <textarea name="company_address" class="form-control form-control-sm" rows="3"><?= esc($settings['company_address'] ?? '') ?></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Toleransi Keterlambatan Default (Menit)</label>
                    <input type="number" name="default_late_tolerance" class="form-control form-control-sm" min="0" value="<?= esc($settings['default_late_tolerance'] ?? '15') ?>" required>
                </div>
                <button type="submit" class="btn btn-dark btn-sm px-4">Simpan Pengaturan</button>
            </form>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>