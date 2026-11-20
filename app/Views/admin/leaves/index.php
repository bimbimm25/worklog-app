<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array  $leaves
 * @var string $selectedStatus
 */
$this->extend('layouts/admin_layout');
?>

<?php $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold mb-0">Persetujuan Izin & Cuti</h5>
        <small class="text-muted">Kelola pengajuan izin, sakit, dan cuti karyawan</small>
    </div>
    <form method="get" action="<?= base_url('admin/leaves') ?>">
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="pending" <?= $selectedStatus === 'pending' ? 'selected' : '' ?>>Menunggu</option>
            <option value="approved" <?= $selectedStatus === 'approved' ? 'selected' : '' ?>>Disetujui</option>
            <option value="rejected" <?= $selectedStatus === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
        </select>
    </form>
</div>

<div class="wl-card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Karyawan</th>
                    <th>Jenis & Periode</th>
                    <th>Alasan & Lampiran</th>
                    <th>Status</th>
                    <th class="text-end" style="width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leaves)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Tidak ada pengajuan izin atau cuti.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leaves as $l): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= esc($l['full_name']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= esc($l['department_name'] ?? '-') ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border mb-1"><?= esc(ucfirst($l['type'])) ?></span>
                                <div><?= esc(date('d M Y', strtotime($l['start_date']))) ?> —
                                    <?= esc(date('d M Y', strtotime($l['end_date']))) ?></div>
                            </td>
                            <td>
                                <div><?= esc($l['reason']) ?></div>
                                <?php if (!empty($l['attachment'])): ?>
                                    <a href="<?= base_url('uploads/leaves/' . $l['attachment']) ?>" target="_blank"
                                        class="small text-decoration-none">Lihat Lampiran</a>
                                <?php endif; ?>
                            </td>
                            <td><?= status_badge($l['status']) ?></td>
                            <td class="text-end">
                                <?php if ($l['status'] === 'pending'): ?>
                                    <button class="btn btn-sm btn-dark" data-bs-toggle="modal"
                                        data-bs-target="#revLeave<?= $l['id'] ?>">Proses</button>
                                <?php else: ?>
                                    <span class="text-muted small"><?= esc($l['review_note'] ?: 'Selesai') ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Review Izin & Cuti (Di luar tabel) -->
<?php if (!empty($leaves)): ?>
    <?php foreach ($leaves as $l): ?>
        <?php if ($l['status'] === 'pending'): ?>
            <div class="modal fade" id="revLeave<?= $l['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="<?= base_url('admin/leaves/review/' . $l['id']) ?>" method="post" class="modal-content">
                        <?= csrf_field() ?>
                        <div class="modal-header">
                            <h6 class="modal-title fw-bold">Review Pengajuan — <?= esc($l['full_name']) ?></h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Catatan / Keterangan (opsional)</label>
                                <textarea name="review_note" class="form-control form-control-sm" rows="3"
                                    placeholder="Tambahkan catatan untuk karyawan..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="decision" value="rejected" class="btn btn-sm btn-danger">Tolak</button>
                            <button type="submit" name="decision" value="approved" class="btn btn-sm btn-success">Setujui</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
<?php $this->endSection(); ?>