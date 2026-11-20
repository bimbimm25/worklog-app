<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array $leaves
 */
$this->extend('layouts/employee_layout');
?>

<?php $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold mb-0">Pengajuan Izin & Cuti</h5>
        <small class="text-muted">Ajukan izin, sakit, atau cuti beserta lampiran pendukung</small>
    </div>
    <button class="btn btn-dark btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addLeaveModal">
        <i data-lucide="plus" style="width:15px;height:15px;" class="me-1"></i> Ajukan Izin / Cuti
    </button>
</div>

<div class="wl-card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Jenis</th>
                    <th>Periode</th>
                    <th>Alasan</th>
                    <th>Lampiran</th>
                    <th>Status</th>
                    <th>Catatan Admin</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leaves)) : ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pengajuan izin atau cuti.</td></tr>
                <?php else : ?>
                    <?php foreach ($leaves as $l) : ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border"><?= esc(ucfirst($l['type'])) ?></span></td>
                            <td><?= esc(date('d M Y', strtotime($l['start_date']))) ?> — <?= esc(date('d M Y', strtotime($l['end_date']))) ?></td>
                            <td><?= esc($l['reason']) ?></td>
                            <td>
                                <?php if (!empty($l['attachment'])) : ?>
                                    <a href="<?= base_url('uploads/leaves/' . $l['attachment']) ?>" target="_blank" class="btn btn-sm btn-outline-dark py-0">Lihat</a>
                                <?php else : ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= status_badge($l['status']) ?></td>
                            <td class="text-muted"><?= esc($l['review_note'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajukan -->
<div class="modal fade" id="addLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('employee/leaves/store') ?>" method="post" enctype="multipart/form-data" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Ajukan Izin / Cuti</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jenis Pengajuan</label>
                    <select name="type" class="form-select form-select-sm" required>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="cuti">Cuti</option>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Alasan</label>
                    <textarea name="reason" class="form-control form-control-sm" rows="3" required></textarea>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold">Lampiran / Surat Dokter (opsional: JPG, PNG, PDF max 3MB)</label>
                    <input type="file" name="attachment" class="form-control form-control-sm">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-dark">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>
<?php $this->endSection(); ?>