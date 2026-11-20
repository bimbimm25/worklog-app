<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array $positions
 */
$this->extend('layouts/admin_layout');
?>

<?php $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold mb-0">Daftar Jabatan</h5>
        <small class="text-muted">Kelola posisi dan peran karyawan</small>
    </div>
    <button class="btn btn-dark btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addPosModal">
        <i data-lucide="plus" style="width:15px;height:15px;" class="me-1"></i> Tambah Jabatan
    </button>
</div>

<div class="wl-card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Nama Jabatan</th>
                    <th>Deskripsi</th>
                    <th class="text-end" style="width:140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($positions)): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">Belum ada data jabatan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($positions as $p): ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($p['name']) ?></td>
                            <td class="text-muted"><?= esc($p['description'] ?: '-') ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editPos<?= $p['id'] ?>">Edit</button>
                                <form action="<?= base_url('admin/positions/delete/' . $p['id']) ?>" method="post"
                                    class="d-inline" onsubmit="return confirm('Hapus jabatan ini?')">
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

<!-- Modal Edit Jabatan (Di luar tabel) -->
<?php if (!empty($positions)): ?>
    <?php foreach ($positions as $p): ?>
        <div class="modal fade" id="editPos<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="<?= base_url('admin/positions/update/' . $p['id']) ?>" method="post" class="modal-content">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Edit Jabatan</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Jabatan</label>
                            <input type="text" name="name" class="form-control form-control-sm" value="<?= esc($p['name']) ?>"
                                required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control form-control-sm"
                                rows="3"><?= esc($p['description'] ?? '') ?></textarea>
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
<div class="modal fade" id="addPosModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/positions/store') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tambah Jabatan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Jabatan</label>
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