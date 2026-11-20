<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var string $mode
 * @var string $selectedDate
 * @var string $selectedMonth
 * @var string $departmentId
 * @var string $status
 * @var array  $departments
 * @var array  $records
 * @var int    $presentCount
 * @var int    $lateCount
 * @var int    $leaveCount
 * @var float  $totalHours
 */
$this->extend('layouts/admin_layout');
?>

<?php $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold mb-0">Monitoring Absensi</h5>
        <small class="text-muted">Rekap kehadiran harian dan bulanan seluruh karyawan</small>
    </div>
</div>

<!-- Filter Bar -->
<div class="wl-card p-3 mb-3">
    <form method="get" action="<?= base_url('admin/attendance') ?>" class="row g-2 align-items-end">
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Mode Rekap</label>
            <select name="mode" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="daily" <?= $mode === 'daily' ? 'selected' : '' ?>>Harian</option>
                <option value="monthly" <?= $mode === 'monthly' ? 'selected' : '' ?>>Bulanan</option>
            </select>
        </div>

        <?php if ($mode === 'monthly') : ?>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Bulan</label>
                <input type="month" name="month" class="form-control form-control-sm" value="<?= esc($selectedMonth) ?>">
            </div>
        <?php else : ?>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Tanggal</label>
                <input type="date" name="date" class="form-control form-control-sm" value="<?= esc($selectedDate) ?>">
            </div>
        <?php endif; ?>

        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Departemen</label>
            <select name="department_id" class="form-select form-select-sm">
                <option value="">Semua Departemen</option>
                <?php foreach ($departments as $d) : ?>
                    <option value="<?= $d['id'] ?>" <?= $departmentId === (string) $d['id'] ? 'selected' : '' ?>><?= esc($d['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua</option>
                <option value="present" <?= $status === 'present' ? 'selected' : '' ?>>Hadir</option>
                <option value="late" <?= $status === 'late' ? 'selected' : '' ?>>Terlambat</option>
                <option value="leave" <?= $status === 'leave' ? 'selected' : '' ?>>Izin/Cuti</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm flex-fill">Filter</button>
            <a href="<?= base_url('admin/attendance') ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-3">
        <div class="wl-card p-3">
            <div class="small text-muted">Hadir Tepat Waktu</div>
            <h4 class="fw-bold text-success mb-0 mt-1"><?= $presentCount ?></h4>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="wl-card p-3">
            <div class="small text-muted">Terlambat</div>
            <h4 class="fw-bold text-warning-emphasis mb-0 mt-1"><?= $lateCount ?></h4>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="wl-card p-3">
            <div class="small text-muted">Izin / Cuti</div>
            <h4 class="fw-bold text-info-emphasis mb-0 mt-1"><?= $leaveCount ?></h4>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="wl-card p-3">
            <div class="small text-muted">Total Jam Kerja</div>
            <h4 class="fw-bold text-dark mb-0 mt-1"><?= $totalHours ?> Jam</h4>
        </div>
    </div>
</div>

<!-- Tabel Absensi -->
<div class="wl-card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Karyawan</th>
                    <th>Departemen</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)) : ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data absensi untuk filter ini.</td></tr>
                <?php else : ?>
                    <?php foreach ($records as $r) : ?>
                        <tr>
                            <td><?= esc(date('d M Y', strtotime($r['date']))) ?></td>
                            <td>
                                <div class="fw-semibold"><?= esc($r['full_name']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= esc($r['employee_code']) ?></div>
                            </td>
                            <td><?= esc($r['department_name'] ?? '-') ?></td>
                            <td><?= $r['check_in'] ? esc(date('H:i', strtotime($r['check_in']))) . ' WIB' : '-' ?></td>
                            <td><?= $r['check_out'] ? esc(date('H:i', strtotime($r['check_out']))) . ' WIB' : '-' ?></td>
                            <td><?= (int) $r['work_duration_minutes'] > 0 ? round(((int) $r['work_duration_minutes']) / 60, 1) . ' jam' : '-' ?></td>
                            <td><?= status_badge($r['status']) ?></td>
                            <td class="text-muted"><?= esc($r['notes'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->endSection(); ?>