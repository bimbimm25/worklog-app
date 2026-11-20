<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array $activity
 * @var array $comments
 */
$this->extend('layouts/admin_layout');
?>

<?php $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="<?= base_url('admin/activities') ?>" class="btn btn-sm btn-outline-secondary">
        <i data-lucide="arrow-left" style="width:14px;height:14px;" class="me-1"></i> Kembali
    </a>

    <?php if ($activity['approval_status'] === 'pending') : ?>
        <div class="d-flex gap-2">
            <form action="<?= base_url('admin/activities/approve/' . $activity['id']) ?>" method="post" onsubmit="return confirm('Setujui laporan aktivitas ini?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-success px-3">Setujui Laporan</button>
            </form>
            <button class="btn btn-sm btn-danger px-3" data-bs-toggle="modal" data-bs-target="#rejectModal">Tolak Laporan</button>
        </div>
    <?php endif; ?>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="wl-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5 class="fw-bold mb-1"><?= esc($activity['title']) ?></h5>
                    <div class="small text-muted">
                        <strong><?= esc($activity['full_name']) ?></strong> (<?= esc($activity['department_name'] ?? '-') ?>) •
                        <?= esc(date('d M Y', strtotime($activity['activity_date']))) ?> (<?= esc(substr($activity['start_time'], 0, 5)) ?> - <?= esc(substr($activity['end_time'], 0, 5)) ?>)
                    </div>
                </div>
                <?= status_badge($activity['approval_status']) ?>
            </div>

            <?php if ($activity['approval_status'] === 'rejected' && !empty($activity['rejection_reason'])) : ?>
                <div class="alert alert-danger small">
                    <strong>Alasan Penolakan:</strong> <?= esc($activity['rejection_reason']) ?>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <div class="small text-muted mb-1">Progres & Status Pekerjaan</div>
                <div class="d-flex align-items-center gap-3">
                    <div class="progress flex-fill" style="height:8px;">
                        <div class="progress-bar bg-dark" style="width: <?= (int) $activity['progress'] ?>%"></div>
                    </div>
                    <span class="small fw-semibold"><?= (int) $activity['progress'] ?>%</span>
                    <?= status_badge($activity['work_status']) ?>
                </div>
            </div>

            <div class="mb-3">
                <div class="small text-muted mb-1">Deskripsi Pekerjaan</div>
                <p class="small mb-0" style="white-space: pre-line;"><?= esc($activity['description']) ?></p>
            </div>

            <?php if (!empty($activity['attachment'])) : ?>
                <div class="pt-3 border-top">
                    <div class="small text-muted mb-2">Lampiran Bukti Pekerjaan</div>
                    <a href="<?= base_url('uploads/activities/' . $activity['attachment']) ?>" target="_blank" class="btn btn-sm btn-outline-dark">
                        <i data-lucide="paperclip" style="width:14px;height:14px;" class="me-1"></i> Lihat Lampiran
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Komentar -->
    <div class="col-lg-5">
        <div class="wl-card p-4 h-100 d-flex flex-column">
            <h6 class="fw-semibold mb-3">Komentar & Catatan Review</h6>
            <div class="flex-fill mb-3" style="max-height: 320px; overflow-y: auto;">
                <?php if (empty($comments)) : ?>
                    <div class="text-center text-muted small py-4">Belum ada komentar.</div>
                <?php else : ?>
                    <?php foreach ($comments as $c) : ?>
                        <div class="p-2 px-3 rounded mb-2 <?= $c['role'] === 'admin' ? 'bg-primary-subtle' : 'bg-light' ?>">
                            <div class="d-flex justify-content-between small mb-1">
                                <strong><?= esc($c['full_name'] ?? 'Admin') ?></strong>
                                <span class="text-muted" style="font-size:0.72rem;"><?= esc(date('d M H:i', strtotime($c['created_at']))) ?></span>
                            </div>
                            <div class="small"><?= esc($c['comment']) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <form action="<?= base_url('admin/activities/comment/' . $activity['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="input-group input-group-sm">
                    <input type="text" name="comment" class="form-control" placeholder="Tulis catatan untuk karyawan..." required>
                    <button type="submit" class="btn btn-dark">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tolak Laporan -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/activities/reject/' . $activity['id']) ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tolak Laporan Aktivitas</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label small fw-semibold">Alasan Penolakan (wajib diisi)</label>
                <textarea name="rejection_reason" class="form-control form-control-sm" rows="3" placeholder="Jelaskan bagian yang perlu diperbaiki karyawan..." required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-danger">Tolak Laporan</button>
            </div>
        </form>
    </div>
</div>
<?php $this->endSection(); ?>