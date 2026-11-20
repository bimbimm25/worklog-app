<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array|null $todayAttendance
 * @var float      $monthHours
 * @var int        $activeTasks
 * @var int        $completedTasks
 * @var int        $loggedActivities
 * @var array      $latestReports
 * @var array      $notifications
 */
$this->extend('layouts/employee_layout');
?>

<?php $this->section('content'); ?>
<!-- Attendance Banner -->
<div class="wl-card p-4 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <div class="small text-muted mb-1">Status Absensi Hari Ini (<?= date('d M Y') ?>)</div>
        <?php if (!$todayAttendance): ?>
            <h5 class="fw-bold mb-1">Anda belum melakukan Check-in</h5>
            <p class="small text-muted mb-0">Silakan lakukan check-in untuk memulai pencatatan jam kerja hari ini.</p>
        <?php elseif (empty($todayAttendance['check_out'])): ?>
            <h5 class="fw-bold mb-1">Sedang Bekerja <?= status_badge($todayAttendance['status']) ?></h5>
            <p class="small text-muted mb-0">Check-in pukul <?= esc(date('H:i', strtotime($todayAttendance['check_in']))) ?>
                WIB</p>
        <?php else: ?>
            <h5 class="fw-bold mb-1">Selesai Bekerja <?= status_badge($todayAttendance['status']) ?></h5>
            <p class="small text-muted mb-0">
                Check-in: <?= esc(date('H:i', strtotime($todayAttendance['check_in']))) ?> —
                Check-out: <?= esc(date('H:i', strtotime($todayAttendance['check_out']))) ?> WIB
            </p>
        <?php endif; ?>
    </div>
    <div>
        <a href="<?= base_url('employee/attendance') ?>" class="btn btn-dark px-4">
            <i data-lucide="fingerprint" style="width:16px;height:16px;" class="me-1"></i>
            <?= !$todayAttendance ? 'Check-in Sekarang' : (empty($todayAttendance['check_out']) ? 'Check-out' : 'Lihat Riwayat') ?>
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="wl-card p-3 h-100">
            <div class="small text-muted">Jam Kerja Bulan Ini</div>
            <h4 class="fw-bold mt-1 mb-0"><?= $monthHours ?> Jam</h4>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="wl-card p-3 h-100">
            <div class="small text-muted">Tugas Aktif</div>
            <h4 class="fw-bold mt-1 mb-0"><?= $activeTasks ?></h4>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="wl-card p-3 h-100">
            <div class="small text-muted">Tugas Selesai</div>
            <h4 class="fw-bold mt-1 mb-0"><?= $completedTasks ?></h4>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="wl-card p-3 h-100">
            <div class="small text-muted">Aktivitas Hari Ini</div>
            <h4 class="fw-bold mt-1 mb-0"><?= $loggedActivities ?></h4>
        </div>
    </div>
</div>

<!-- Laporan Terbaru & Notifikasi -->
<div class="row g-3">
    <div class="col-lg-7">
        <div class="wl-card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-semibold mb-0">Status Laporan Aktivitas Terbaru</h6>
                <a href="<?= base_url('employee/activities') ?>" class="btn btn-sm btn-outline-dark">Catat Aktivitas</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Judul Aktivitas</th>
                            <th>Tanggal</th>
                            <th>Progres</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($latestReports)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada laporan aktivitas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($latestReports as $rep): ?>
                                <tr>
                                    <td class="fw-medium"><?= esc($rep['title']) ?></td>
                                    <td><?= esc(date('d M Y', strtotime($rep['activity_date']))) ?></td>
                                    <td><?= (int) $rep['progress'] ?>%</td>
                                    <td><?= status_badge($rep['approval_status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="wl-card p-3 h-100">
            <h6 class="fw-semibold mb-3">Notifikasi dari Admin</h6>
            <?php if (empty($notifications)): ?>
                <div class="text-center text-muted small py-4">Belum ada notifikasi baru.</div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($notifications as $n): ?>
                        <div class="list-group-item px-0">
                            <div class="fw-semibold small"><?= esc($n['title']) ?></div>
                            <div class="text-muted small"><?= esc($n['message']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>