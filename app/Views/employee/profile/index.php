<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array $profile
 */
$this->extend('layouts/employee_layout');
?>

<?php $this->section('content'); ?>
<div class="row g-3">
    <!-- Informasi Profil -->
    <div class="col-lg-7">
        <div class="wl-card p-4">
            <h6 class="fw-bold mb-3">Informasi Pribadi</h6>
            <form action="<?= base_url('employee/profile/update') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <?php if (!empty($profile['avatar'])): ?>
                        <img src="<?= base_url('uploads/avatars/' . $profile['avatar']) ?>" alt="Avatar"
                            class="rounded-circle object-fit-cover" style="width:64px;height:64px;">
                    <?php else: ?>
                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold"
                            style="width:64px;height:64px;font-size:1.25rem;">
                            <?= esc(strtoupper(substr($profile['full_name'], 0, 2))) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <div class="fw-semibold"><?= esc($profile['full_name']) ?></div>
                        <div class="small text-muted"><?= esc($profile['employee_code']) ?> •
                            <?= esc($profile['department_name'] ?? '-') ?> /
                            <?= esc($profile['position_name'] ?? '-') ?></div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" name="full_name" class="form-control form-control-sm"
                            value="<?= esc($profile['full_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email (Tidak dapat diubah)</label>
                        <input type="email" class="form-control form-control-sm bg-light"
                            value="<?= esc($profile['email']) ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">No. Telepon</label>
                        <input type="text" name="phone" class="form-control form-control-sm"
                            value="<?= esc($profile['phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Jadwal Kerja</label>
                        <input type="text" class="form-control form-control-sm bg-light"
                            value="<?= esc($profile['schedule_name'] ?? '-') ?>" disabled>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Ganti Foto Profil (JPG/PNG, max 2MB)</label>
                        <input type="file" name="avatar" class="form-control form-control-sm"
                            accept="image/jpeg,image/png">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-dark btn-sm px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ganti Password -->
    <div class="col-lg-5">
        <div class="wl-card p-4">
            <h6 class="fw-bold mb-3">Keamanan & Password</h6>
            <form action="<?= base_url('employee/profile/password') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Saat Ini</label>
                    <input type="password" name="current_password" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Baru</label>
                    <input type="password" name="new_password" class="form-control form-control-sm"
                        placeholder="Min. 6 karakter" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" class="form-control form-control-sm" required>
                </div>
                <button type="submit" class="btn btn-outline-dark btn-sm px-4">Perbarui Password</button>
            </form>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>