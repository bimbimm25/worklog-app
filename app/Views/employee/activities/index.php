<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array  $activities
 * @var string $selectedStatus
 * @var string $selectedDate
 */
$this->extend('layouts/employee_layout');
?>

<?php $this->section('content'); ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h5 class="fw-bold mb-0">Aktivitas Harian Saya</h5>
        <small class="text-muted">Catat pekerjaan harian dan kirimkan laporan ke Admin</small>
    </div>
    <button class="btn btn-dark btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addActModal">
        <i data-lucide="plus" style="width:15px;height:15px;" class="me-1"></i> Catat Aktivitas
    </button>
</div>

<!-- Filter -->
<div class="wl-card p-3 mb-3">
    <form method="get" action="<?= base_url('employee/activities') ?>" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small text-muted mb-1">Tanggal</label>
            <input type="date" name="date" class="form-control form-control-sm" value="<?= esc($selectedDate) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted mb-1">Status Persetujuan</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status</option>
                <option value="draft" <?= $selectedStatus === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="pending" <?= $selectedStatus === 'pending' ? 'selected' : '' ?>>Menunggu ACC</option>
                <option value="approved" <?= $selectedStatus === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                <option value="rejected" <?= $selectedStatus === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm px-3">Filter</button>
            <a href="<?= base_url('employee/activities') ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Tabel Aktivitas -->
<div class="wl-card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Tanggal & Waktu</th>
                    <th>Judul Aktivitas</th>
                    <th>Progres</th>
                    <th>Status Kerja</th>
                    <th>Status ACC</th>
                    <th class="text-end" style="width:190px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($activities)) : ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada aktivitas yang dicatat.</td></tr>
                <?php else : ?>
                    <?php foreach ($activities as $a) : ?>
                        <tr>
                            <td>
                                <div class="fw-medium"><?= esc(date('d M Y', strtotime($a['activity_date']))) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= esc(substr($a['start_time'], 0, 5)) ?> - <?= esc(substr($a['end_time'], 0, 5)) ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= esc($a['title']) ?></div>
                                <?php if ($a['approval_status'] === 'rejected' && !empty($a['rejection_reason'])) : ?>
                                    <div class="text-danger" style="font-size:0.75rem;">Alasan tolak: <?= esc($a['rejection_reason']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="width:120px;">
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-dark" style="width: <?= (int) $a['progress'] ?>%"></div>
                                </div>
                                <small class="text-muted"><?= (int) $a['progress'] ?>%</small>
                            </td>
                            <td><?= status_badge($a['work_status']) ?></td>
                            <td><?= status_badge($a['approval_status']) ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('employee/activities/detail/' . $a['id']) ?>" class="btn btn-sm btn-outline-dark">Detail</a>
                                <?php if (in_array($a['approval_status'], ['draft', 'rejected'], true)) : ?>
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editAct<?= $a['id'] ?>">Edit</button>
                                <?php endif; ?>
                                <?php if ($a['approval_status'] === 'draft') : ?>
                                    <form action="<?= base_url('employee/activities/delete/' . $a['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus draft aktivitas ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <?php if (in_array($a['approval_status'], ['draft', 'rejected'], true)) : ?>
                            <!-- Modal Edit -->
                            <div class="modal fade" id="editAct<?= $a['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="<?= base_url('employee/activities/update/' . $a['id']) ?>" method="post" enctype="multipart/form-data" class="modal-content">
                                        <?= csrf_field() ?>
                                        <div class="modal-header">
                                            <h6 class="modal-title fw-bold">Edit / Kirim Ulang Aktivitas</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label small fw-semibold">Judul Aktivitas</label>
                                                    <input type="text" name="title" class="form-control form-control-sm" value="<?= esc($a['title']) ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold">Tanggal</label>
                                                    <input type="date" name="activity_date" class="form-control form-control-sm" value="<?= esc($a['activity_date']) ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold">Waktu Mulai</label>
                                                    <input type="time" name="start_time" class="form-control form-control-sm" value="<?= esc(substr($a['start_time'], 0, 5)) ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold">Waktu Selesai</label>
                                                    <input type="time" name="end_time" class="form-control form-control-sm" value="<?= esc(substr($a['end_time'], 0, 5)) ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">Progres (%)</label>
                                                    <input type="number" name="progress" class="form-control form-control-sm" min="0" max="100" value="<?= (int) $a['progress'] ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">Status Pekerjaan</label>
                                                    <select name="work_status" class="form-select form-select-sm" required>
                                                        <option value="in_progress" <?= $a['work_status'] === 'in_progress' ? 'selected' : '' ?>>Proses</option>
                                                        <option value="completed" <?= $a['work_status'] === 'completed' ? 'selected' : '' ?>>Selesai</option>
                                                        <option value="blocked" <?= $a['work_status'] === 'blocked' ? 'selected' : '' ?>>Terhambat</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-semibold">Deskripsi Pekerjaan</label>
                                                    <textarea name="description" class="form-control form-control-sm" rows="3" required><?= esc($a['description']) ?></textarea>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-semibold">Ganti Lampiran (opsional, max 5MB)</label>
                                                    <input type="file" name="attachment" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="submit_action" value="draft" class="btn btn-sm btn-outline-secondary">Simpan Draft</button>
                                            <button type="submit" name="submit_action" value="pending" class="btn btn-sm btn-dark">Kirim ke Admin</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addActModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('employee/activities/store') ?>" method="post" enctype="multipart/form-data" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Catat Aktivitas Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Judul Aktivitas</label>
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="Contoh: Pengembangan API Modul Absensi" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Tanggal</label>
                        <input type="date" name="activity_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Waktu Mulai</label>
                        <input type="time" name="start_time" class="form-control form-control-sm" value="08:00" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Waktu Selesai</label>
                        <input type="time" name="end_time" class="form-control form-control-sm" value="12:00" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Progres (%)</label>
                        <input type="number" name="progress" class="form-control form-control-sm" min="0" max="100" value="100" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Status Pekerjaan</label>
                        <select name="work_status" class="form-select form-select-sm" required>
                            <option value="in_progress">Proses</option>
                            <option value="completed" selected>Selesai</option>
                            <option value="blocked">Terhambat</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Deskripsi Pekerjaan</label>
                        <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Jelaskan detail pekerjaan yang diselesaikan..." required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Lampiran / Bukti Pekerjaan (opsional: JPG, PNG, PDF, DOC, XLS max 5MB)</label>
                        <input type="file" name="attachment" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="submit_action" value="draft" class="btn btn-sm btn-outline-secondary">Simpan Draft</button>
                <button type="submit" name="submit_action" value="pending" class="btn btn-sm btn-dark">Kirim ke Admin</button>
            </div>
        </form>
    </div>
</div>
<?php $this->endSection(); ?>