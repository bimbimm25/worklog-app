<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var string $type
 * @var string $startDate
 * @var string $endDate
 * @var string $departmentId
 * @var array  $departments
 * @var string $reportTitle
 * @var array  $headers
 * @var array  $rows
 * @var array  $summary
 * @var array  $chartLabels
 * @var array  $chartValues
 */
$this->extend('layouts/admin_layout');

$queryParams = http_build_query([
    'type'          => $type,
    'start_date'    => $startDate,
    'end_date'      => $endDate,
    'department_id' => $departmentId,
]);
?>

<?php $this->section('content'); ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h5 class="fw-bold mb-0">Laporan & Analitik</h5>
        <small class="text-muted">Filter periode operasional dan unduh laporan dalam format PDF atau Excel</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/reports/export-pdf?' . $queryParams) ?>" class="btn btn-outline-danger btn-sm px-3">
            <i data-lucide="file-text" style="width:15px;height:15px;" class="me-1"></i> Export PDF
        </a>
        <a href="<?= base_url('admin/reports/export-excel?' . $queryParams) ?>" class="btn btn-success btn-sm px-3">
            <i data-lucide="file-spreadsheet" style="width:15px;height:15px;" class="me-1"></i> Export Excel
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="wl-card p-3 mb-4">
    <form method="get" action="<?= base_url('admin/reports') ?>" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Jenis Laporan</label>
            <select name="type" class="form-select form-select-sm">
                <option value="attendance" <?= $type === 'attendance' ? 'selected' : '' ?>>Kehadiran & Keterlambatan</option>
                <option value="activities" <?= $type === 'activities' ? 'selected' : '' ?>>Aktivitas Karyawan</option>
                <option value="tasks" <?= $type === 'tasks' ? 'selected' : '' ?>>Progres & Tugas</option>
                <option value="leaves" <?= $type === 'leaves' ? 'selected' : '' ?>>Izin & Cuti</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= esc($startDate) ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= esc($endDate) ?>">
        </div>
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
            <button type="submit" class="btn btn-dark btn-sm w-100">Tampilkan</button>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <?php foreach ($summary as $label => $val) : ?>
        <div class="col-sm-6 col-xl-3">
            <div class="wl-card p-3">
                <div class="small text-muted"><?= esc($label) ?></div>
                <h4 class="fw-bold mb-0 mt-1"><?= esc((string) $val) ?></h4>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Grafik & Preview Tabel -->
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="wl-card p-3 h-100">
            <h6 class="fw-semibold mb-3">Distribusi — <?= esc($reportTitle) ?></h6>
            <canvas id="reportChart" height="220"></canvas>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="wl-card p-3 h-100">
            <h6 class="fw-semibold mb-3">Preview Data (<?= count($rows) ?> baris)</h6>
            <div class="table-responsive" style="max-height: 360px;">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light sticky-top">
                        <tr>
                            <?php foreach ($headers as $h) : ?>
                                <th><?= esc($h) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)) : ?>
                            <tr><td colspan="<?= count($headers) ?>" class="text-center text-muted py-4">Tidak ada data pada periode ini.</td></tr>
                        <?php else : ?>
                            <?php foreach ($rows as $r) : ?>
                                <tr>
                                    <?php foreach ($r as $cell) : ?>
                                        <td><?= esc((string) $cell) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
new Chart(document.getElementById('reportChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
            data: <?= json_encode($chartValues) ?>,
            backgroundColor: ['#0f172a', '#2563eb', '#f59e0b', '#ef4444'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
<?php $this->endSection(); ?>