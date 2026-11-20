<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array  $logs
 * @var string $selectedModule
 * @var string $keyword
 */
$this->extend('layouts/admin_layout');
?>

<?php $this->section('content'); ?>
<div class="mb-3">
    <h5 class="fw-bold mb-0">Riwayat Aktivitas Sistem</h5>
    <small class="text-muted">Audit trail 100 aktivitas terbaru pengguna di WORKLOG</small>
</div>

<div class="wl-card p-3 mb-3">
    <form method="get" action="<?= base_url('admin/logs') ?>" class="row g-2 align-items-end">
        <div class="col-md-5">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari aksi, deskripsi, atau nama pengguna..." value="<?= esc($keyword) ?>">
        </div>
        <div class="col-md-4">
            <select name="module" class="form-select form-select-sm">
                <option value="">Semua Modul</option>
                <option value="auth" <?= $selectedModule === 'auth' ? 'selected' : '' ?>>Auth</option>
                <option value="attendance" <?= $selectedModule === 'attendance' ? 'selected' : '' ?>>Absensi</option>
                <option value="work_activities" <?= $selectedModule === 'work_activities' ? 'selected' : '' ?>>Aktivitas</option>
                <option value="tasks" <?= $selectedModule === 'tasks' ? 'selected' : '' ?>>Tugas</option>
                <option value="leave_requests" <?= $selectedModule === 'leave_requests' ? 'selected' : '' ?>>Izin & Cuti</option>
                <option value="employees" <?= $selectedModule === 'employees' ? 'selected' : '' ?>>Karyawan</option>
                <option value="settings" <?= $selectedModule === 'settings' ? 'selected' : '' ?>>Pengaturan</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm flex-fill">Filter</button>
            <a href="<?= base_url('admin/logs') ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

<div class="wl-card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Waktu</th>
                    <th>Pengguna</th>
                    <th>Modul & Aksi</th>
                    <th>Deskripsi</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)) : ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada catatan aktivitas.</td></tr>
                <?php else : ?>
                    <?php foreach ($logs as $log) : ?>
                        <tr>
                            <td class="text-nowrap"><?= esc(date('d M Y H:i', strtotime($log['created_at']))) ?></td>
                            <td>
                                <div class="fw-semibold"><?= esc($log['full_name'] ?? $log['email'] ?? 'System') ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= esc(ucfirst($log['role'] ?? '-')) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border me-1"><?= esc($log['module']) ?></span>
                                <span class="badge bg-secondary-subtle text-secondary"><?= esc($log['action']) ?></span>
                            </td>
                            <td><?= esc($log['description']) ?></td>
                            <td class="text-muted"><?= esc($log['ip_address'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->endSection(); ?>