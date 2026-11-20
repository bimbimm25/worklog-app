<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array $departments
 */
$this->extend('layouts/admin_layout');
?>

<?php $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold mb-0">Daftar Departemen</h5>
        <small class="text-muted">Kelola unit dan divisi perusahaan</small>
    </div>
    <button class="btn btn-dark btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addDeptModal">
        <i data-lucide="plus" style="width:15px;height:15px;" class="me-1"></i> Tambah Departemen
    </button>
</div>

<div class="wl-card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th style="width:100px;">Kode</th>
                    <th>Nama Departemen</th>
                    <th>Deskripsi</th>
                    <th class="text-end" style="width:140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($departments)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada data departemen.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($departments as $d): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border"><?= esc($d['code']) ?></span></td>
                            <td class="fw-semibold"><?= esc($d['name']) ?></td>
                            <td class="text-muted"><?= esc($d['description'] ?: '-') ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editDept<?= $d['id'] ?>">Edit</button>
                                <form action="<?= base_url('admin/departments/delete/' . $d['id']) ?>" method="post"
                                    class="d-inline" onsubmit="return confirm('Hapus departemen ini?')">
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

<!-- Modal Edit Departemen (Diletakkan di luar tabel) -->
<?php if (!empty($departments)): ?>
    <?php foreach ($departments as $d): ?>
        <div class="modal fade" id="editDept<?= $d['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="<?= base_url('admin/departments/update/' . $d['id']) ?>" method="post" class="modal-content">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Edit Departemen</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kode</label>
                            <input type="text" name="code" class="form-control form-control-sm" value="<?= esc($d['code']) ?>"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Departemen</label>
                            <input type="text" name="name" class="form-control form-control-sm" value="<?= esc($d['name']) ?>"
                                required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control form-control-sm"
                                rows="3"><?= esc($d['description'] ?? '') ?></textarea>
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
<div class="modal fade" id="addDeptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/departments/store') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tambah Departemen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Kode (contoh: IT, HR, MKT)</label>
                    <input type="text" name="code" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Departemen</label>
                    <input type="text" name="name" class="form-control form-control-sm" required>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold">Deskripsi</label>
                    <textarea name="description" class="form-control form-control-sm" rows="3"></textarea>
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