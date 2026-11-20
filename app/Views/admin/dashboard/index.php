<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var string $selectedDate
 * @var string $departmentId
 * @var array  $departments
 * @var int    $totalEmployees
 * @var int    $presentCount
 * @var int    $absentCount
 * @var int    $lateCount
 * @var int    $workingCount
 * @var int    $todayActivities
 * @var int    $pendingReports
 * @var array  $chartLabels
 * @var array  $chartAttendance
 * @var array  $chartActivities
 * @var array  $recentActivities
 */
$this->extend('layouts/admin_layout');
?>

<?php $this->section('content'); ?>
<!-- Filter Bar -->
<div class="wl-card p-3 mb-4">
    <form method="get" action="<?= base_url('admin/dashboard') ?>" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small text-muted mb-1">Tanggal</label>
            <input type="date" name="date" class="form-control form-control-sm" value="<?= esc($selectedDate) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted mb-1">Departemen</label>
            <select name="department_id" class="form-select form-select-sm">
                <option value="">Semua Departemen</option>
                <?php foreach ($departments as $dept) : ?>
                    <option value="<?= $dept['id'] ?>" <?= (string) $departmentId === (string) $dept['id'] ? 'selected' : '' ?>>
                        <?= esc($dept['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm px-3">Terapkan Filter</button>
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="wl-card p-3 h-100 d-flex justify-content-between align-items-center">
            <div>
                <div class="small text-muted">Total Karyawan</div>
                <h4 class="fw-bold mb-0 mt-1"><?= $totalEmployees ?></h4>
            </div>
            <span class="wl-stat-icon bg-primary-subtle text-primary"><i data-lucide="users"></i></span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="wl-card p-3 h-100 d-flex justify-content-between align-items-center">
            <div>
                <div class="small text-muted">Hadir / Sedang Bekerja</div>
                <h4 class="fw-bold mb-0 mt-1"><?= $presentCount ?> <small class="fs-6 text-muted fw-normal">/ <?= $workingCount ?> aktif</small></h4>
            </div>
            <span class="wl-stat-icon bg-success-subtle text-success"><i data-lucide="user-check"></i></span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="wl-card p-3 h-100 d-flex justify-content-between align-items-center">
            <div>
                <div class="small text-muted">Belum Hadir / Terlambat</div>
                <h4 class="fw-bold mb-0 mt-1"><?= $absentCount ?> <small class="fs-6 text-warning-emphasis fw-normal">/ <?= $lateCount ?> telat</small></h4>
            </div>
            <span class="wl-stat-icon bg-warning-subtle text-warning-emphasis"><i data-lucide="clock-alert"></i></span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="wl-card p-3 h-100 d-flex justify-content-between align-items-center">
            <div>
                <div class="small text-muted">Aktivitas / Menunggu ACC</div>
                <h4 class="fw-bold mb-0 mt-1"><?= $todayActivities ?> <small class="fs-6 text-danger fw-normal">/ <?= $pendingReports ?> pending</small></h4>
            </div>
            <span class="wl-stat-icon bg-info-subtle text-info-emphasis"><i data-lucide="clipboard-list"></i></span>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="wl-card p-3 h-100">
            <h6 class="fw-semibold mb-3">Tren Kehadiran (7 Hari Terakhir)</h6>
            <canvas id="attendanceChart" height="140"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="wl-card p-3 h-100">
            <h6 class="fw-semibold mb-3">Tren Aktivitas Karyawan (7 Hari Terakhir)</h6>
            <canvas id="activityChart" height="140"></canvas>
        </div>
    </div>
</div>

<!-- Aktivitas Terbaru -->
<div class="wl-card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-semibold mb-0">Aktivitas Terbaru</h6>
        <a href="<?= base_url('admin/activities') ?>" class="btn btn-sm btn-outline-dark">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Karyawan</th>
                    <th>Aktivitas</th>
                    <th>Tanggal</th>
                    <th>Progres</th>
                    <th>Status ACC</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentActivities)) : ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas yang dicatat.</td></tr>
                <?php else : ?>
                    <?php foreach ($recentActivities as $act) : ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= esc($act['full_name']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= esc($act['department_name'] ?? '-') ?></div>
                            </td>
                            <td><?= esc($act['title']) ?></td>
                            <td><?= esc(date('d M Y', strtotime($act['activity_date']))) ?></td>
                            <td><?= (int) $act['progress'] ?>%</td>
                            <td><?= status_badge($act['approval_status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
const labels = <?= json_encode($chartLabels) ?>;
const attData = <?= json_encode($chartAttendance) ?>;
const actData = <?= json_encode($chartActivities) ?>;

new Chart(document.getElementById('attendanceChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{ label: 'Karyawan Hadir', data: attData, backgroundColor: '#0f172a', borderRadius: 6 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});

new Chart(document.getElementById('activityChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{ label: 'Total Aktivitas', data: actData, borderColor: '#2563eb', backgroundColor: 'rgba(37, 99, 235, 0.1)', fill: true, tension: 0.35 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
</script>
<?php $this->endSection(); ?>