<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array $schedules
 */
$this->extend('layouts/admin_layout');
?>

<?php $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold mb-0">Jadwal Kerja</h5>
        <small class="text-muted">Atur jam masuk, jam pulang, dan toleransi keterlambatan</small>
    </div>
    <button class="btn btn-dark btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addSchModal">
        <i data-lucide="plus" style="width:15px;height:15px;" class="me-1"></i> Tambah Jadwal
    </button>
</div>

<div class="wl-card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Nama Jadwal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Toleransi Terlambat</th>
                    <th class="text-end" style="width:140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules)) : ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada jadwal kerja.</td></tr>
                <?php else : ?>
                    <?php foreach ($schedules as $s) : ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($s['name']) ?></td>
                            <td><?= esc(substr($s['start_time'], 0, 5)) ?> WIB</td>
                            <td><?= esc(substr($s['end_time'], 0, 5)) ?> WIB</td>
                            <td><?= (int) $s['late_tolerance_minutes'] ?> menit</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editSch<?= $s['id'] ?>">Edit</button>
                                <form action="<?= base_url('admin/schedules/delete/' . $s['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus jadwal kerja ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit Jadwal (Di luar tabel) -->
<?php if (!empty($schedules)) : ?>
    <?php foreach ($schedules as $s) : ?>
        <div class="modal fade" id="editSch<?= $s['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="<?= base_url('admin/schedules/update/' . $s['id']) ?>" method="post" class="modal-content">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Edit Jadwal Kerja</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Jadwal</label>
                            <input type="text" name="name" class="form-control form-control-sm" value="<?= esc($s['name']) ?>" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Jam Masuk</label>
                                <input type="time" name="start_time" class="form-control form-control-sm" value="<?= esc(substr($s['start_time'], 0, 5)) ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Jam Pulang</label>
                                <input type="time" name="end_time" class="form-control form-control-sm" value="<?= esc(substr($s['end_time'], 0, 5)) ?>" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-semibold">Toleransi Terlambat (menit)</label>
                            <input type="number" name="late_tolerance_minutes" class="form-control form-control-sm" min="0" value="<?= (int) $s['late_tolerance_minutes'] ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-dark">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Modal Tambah -->
<div class="modal fade" id="addSchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/schedules/store') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tambah Jadwal Kerja</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Jadwal</label>
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="Contoh: Shift Pagi" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Jam Masuk</label>
                        <input type="time" name="start_time" class="form-control form-control-sm" value="08:00" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Jam Pulang</label>
                        <input type="time" name="end_time" class="form-control form-control-sm" value="17:00" required>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold">Toleransi Terlambat (menit)</label>
                    <input type="number" name="late_tolerance_minutes" class="form-control form-control-sm" min="0" value="15" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-dark">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php $this->endSection(); ?>