<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array|null $employee
 * @var array|null $todayAttendance
 * @var array      $history
 * @var string     $selectedMonth
 * @var int        $presentCount
 * @var int        $lateCount
 * @var float      $totalHours
 */
$this->extend('layouts/employee_layout');
?>

<?php $this->section('content'); ?>
<div class="row g-3 mb-4">
    <!-- Panel Check-in / Check-out -->
    <div class="col-lg-7">
        <div class="wl-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="small text-muted">Absensi Hari Ini — <?= date('d M Y') ?></div>
                    <h4 class="fw-bold mb-0 mt-1" id="liveClock"><?= date('H:i:s') ?> WIB</h4>
                </div>
                <span class="badge bg-light text-dark border">
                    <?= esc($employee['schedule_name'] ?? 'Regular (08:00 - 17:00)') ?>
                </span>
            </div>

            <?php if (!$todayAttendance) : ?>
                <form action="<?= base_url('employee/attendance/check-in') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Catatan (opsional)</label>
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: WFO / WFH">
                    </div>
                    <button type="submit" class="btn btn-dark px-4">
                        <i data-lucide="log-in" style="width:16px;height:16px;" class="me-1"></i> Check-in Sekarang
                    </button>
                </form>
            <?php elseif (empty($todayAttendance['check_out'])) : ?>
                <div class="alert alert-light border small mb-3">
                    Status: <?= status_badge($todayAttendance['status']) ?> —
                    Check-in pukul <strong><?= esc(date('H:i', strtotime($todayAttendance['check_in']))) ?> WIB</strong>
                </div>
                <form action="<?= base_url('employee/attendance/check-out') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Catatan Pulang (opsional)</label>
                        <input type="text" name="notes" class="form-control form-control-sm" value="<?= esc($todayAttendance['notes'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-danger px-4" onclick="return confirm('Lakukan Check-out sekarang?')">
                        <i data-lucide="log-out" style="width:16px;height:16px;" class="me-1"></i> Check-out Sekarang
                    </button>
                </form>
            <?php else : ?>
                <div class="alert alert-success border-0 small mb-0">
                    <div class="fw-semibold mb-1">Absensi hari ini telah selesai <?= status_badge($todayAttendance['status']) ?></div>
                    <div>
                        Check-in: <strong><?= esc(date('H:i', strtotime($todayAttendance['check_in']))) ?></strong> |
                        Check-out: <strong><?= esc(date('H:i', strtotime($todayAttendance['check_out']))) ?></strong> |
                        Durasi: <strong><?= round(((int) $todayAttendance['work_duration_minutes']) / 60, 1) ?> jam</strong>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ringkasan Bulan Ini -->
    <div class="col-lg-5">
        <div class="wl-card p-4 h-100">
            <h6 class="fw-semibold mb-3">Rekap Bulan <?= esc(date('F Y', strtotime($selectedMonth . '-01'))) ?></h6>
            <div class="row g-2 text-center">
                <div class="col-4">
                    <div class="p-3 rounded bg-light">
                        <div class="small text-muted">Hadir</div>
                        <h4 class="fw-bold text-success mb-0 mt-1"><?= $presentCount ?></h4>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded bg-light">
                        <div class="small text-muted">Terlambat</div>
                        <h4 class="fw-bold text-warning-emphasis mb-0 mt-1"><?= $lateCount ?></h4>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded bg-light">
                        <div class="small text-muted">Total Jam</div>
                        <h4 class="fw-bold text-dark mb-0 mt-1"><?= $totalHours ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Absensi -->
<div class="wl-card p-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        <h6 class="fw-semibold mb-0">Riwayat Absensi</h6>
        <form method="get" action="<?= base_url('employee/attendance') ?>" class="d-flex gap-2">
            <input type="month" name="month" class="form-control form-control-sm" value="<?= esc($selectedMonth) ?>">
            <button type="submit" class="btn btn-dark btn-sm">Tampilkan</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history)) : ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat absensi pada bulan ini.</td></tr>
                <?php else : ?>
                    <?php foreach ($history as $h) : ?>
                        <tr>
                            <td class="fw-medium"><?= esc(date('d M Y', strtotime($h['date']))) ?></td>
                            <td><?= $h['check_in'] ? esc(date('H:i', strtotime($h['check_in']))) . ' WIB' : '-' ?></td>
                            <td><?= $h['check_out'] ? esc(date('H:i', strtotime($h['check_out']))) . ' WIB' : '-' ?></td>
                            <td><?= (int) $h['work_duration_minutes'] > 0 ? round(((int) $h['work_duration_minutes']) / 60, 1) . ' jam' : '-' ?></td>
                            <td><?= status_badge($h['status']) ?></td>
                            <td class="text-muted"><?= esc($h['notes'] ?: '-') ?></td>
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
setInterval(() => {
    const now = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    const el = document.getElementById('liveClock');
    if (el) {
        el.textContent = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())} WIB`;
    }
}, 1000);
</script>
<?php $this->endSection(); ?>