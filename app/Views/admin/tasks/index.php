<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array  $tasks
 * @var array  $employees
 * @var string $selectedStatus
 * @var string $selectedPriority
 * @var string $selectedEmployee
 */
$this->extend('layouts/admin_layout');
?>

<?php $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold mb-0">Manajemen Tugas</h5>
        <small class="text-muted">Buat tugas, tentukan PIC, prioritas, dan pantau progresnya</small>
    </div>
    <button class="btn btn-dark btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addTaskModal">
        <i data-lucide="plus" style="width:15px;height:15px;" class="me-1"></i> Buat Tugas
    </button>
</div>

<!-- Filter -->
<div class="wl-card p-3 mb-3">
    <form method="get" action="<?= base_url('admin/tasks') ?>" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Karyawan</label>
            <select name="employee_id" class="form-select form-select-sm">
                <option value="">Semua Karyawan</option>
                <?php foreach ($employees as $e) : ?>
                    <option value="<?= $e['id'] ?>" <?= $selectedEmployee === (string) $e['id'] ? 'selected' : '' ?>><?= esc($e['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Prioritas</label>
            <select name="priority" class="form-select form-select-sm">
                <option value="">Semua Prioritas</option>
                <option value="low" <?= $selectedPriority === 'low' ? 'selected' : '' ?>>Low</option>
                <option value="medium" <?= $selectedPriority === 'medium' ? 'selected' : '' ?>>Medium</option>
                <option value="high" <?= $selectedPriority === 'high' ? 'selected' : '' ?>>High</option>
                <option value="urgent" <?= $selectedPriority === 'urgent' ? 'selected' : '' ?>>Urgent</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status</option>
                <option value="todo" <?= $selectedStatus === 'todo' ? 'selected' : '' ?>>To Do</option>
                <option value="in_progress" <?= $selectedStatus === 'in_progress' ? 'selected' : '' ?>>Proses</option>
                <option value="review" <?= $selectedStatus === 'review' ? 'selected' : '' ?>>Review</option>
                <option value="completed" <?= $selectedStatus === 'completed' ? 'selected' : '' ?>>Selesai</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm flex-fill">Filter</button>
            <a href="<?= base_url('admin/tasks') ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Tabel -->
<div class="wl-card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Tugas</th>
                    <th>PIC</th>
                    <th>Prioritas</th>
                    <th>Deadline</th>
                    <th>Progres</th>
                    <th>Status</th>
                    <th class="text-end" style="width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tasks)) : ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada tugas.</td></tr>
                <?php else : ?>
                    <?php foreach ($tasks as $t) : ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= esc($t['title']) ?></div>
                                <div class="text-muted text-truncate" style="max-width:260px;font-size:0.75rem;"><?= esc($t['description'] ?: '-') ?></div>
                            </td>
                            <td><?= esc($t['full_name']) ?></td>
                            <td>
                                <?php
                                $prioClass = match ($t['priority']) {
                                    'urgent' => 'bg-danger text-white',
                                    'high'   => 'bg-warning-subtle text-warning-emphasis',
                                    'medium' => 'bg-info-subtle text-info-emphasis',
                                    default  => 'bg-light text-dark border',
                                };
                                ?>
                                <span class="badge <?= $prioClass ?>"><?= esc(ucfirst($t['priority'])) ?></span>
                            </td>
                            <td><?= esc(date('d M Y', strtotime($t['deadline']))) ?></td>
                            <td style="width:110px;">
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar bg-dark" style="width: <?= (int) $t['progress'] ?>%"></div>
                                </div>
                                <small class="text-muted"><?= (int) $t['progress'] ?>%</small>
                            </td>
                            <td><?= status_badge($t['status']) ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editTask<?= $t['id'] ?>">Edit</button>
                                <form action="<?= base_url('admin/tasks/delete/' . $t['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus tugas ini?')">
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

<!-- Modal Edit Tugas (Di luar tabel) -->
<?php if (!empty($tasks)) : ?>
    <?php foreach ($tasks as $t) : ?>
        <div class="modal fade" id="editTask<?= $t['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="<?= base_url('admin/tasks/update/' . $t['id']) ?>" method="post" class="modal-content">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Edit Tugas</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Judul Tugas</label>
                            <input type="text" name="title" class="form-control form-control-sm" value="<?= esc($t['title']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Karyawan (PIC)</label>
                            <select name="assigned_to" class="form-select form-select-sm" required>
                                <?php foreach ($employees as $e) : ?>
                                    <option value="<?= $e['id'] ?>" <?= (int) $t['assigned_to'] === (int) $e['id'] ? 'selected' : '' ?>><?= esc($e['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <label class="form-label small fw-semibold">Prioritas</label>
                                <select name="priority" class="form-select form-select-sm">
                                    <option value="low" <?= $t['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                                    <option value="medium" <?= $t['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                    <option value="high" <?= $t['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                                    <option value="urgent" <?= $t['priority'] === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-semibold">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="todo" <?= $t['status'] === 'todo' ? 'selected' : '' ?>>To Do</option>
                                    <option value="in_progress" <?= $t['status'] === 'in_progress' ? 'selected' : '' ?>>Proses</option>
                                    <option value="review" <?= $t['status'] === 'review' ? 'selected' : '' ?>>Review</option>
                                    <option value="completed" <?= $t['status'] === 'completed' ? 'selected' : '' ?>>Selesai</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-semibold">Deadline</label>
                                <input type="date" name="deadline" class="form-control form-control-sm" value="<?= esc($t['deadline']) ?>" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control form-control-sm" rows="3"><?= esc($t['description'] ?? '') ?></textarea>
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
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/tasks/store') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Buat Tugas Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Judul Tugas</label>
                    <input type="text" name="title" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tugaskan Kepada</label>
                    <select name="assigned_to" class="form-select form-select-sm" required>
                        <option value="">- Pilih Karyawan -</option>
                        <?php foreach ($employees as $e) : ?>
                            <option value="<?= $e['id'] ?>"><?= esc($e['full_name']) ?> (<?= esc($e['employee_code']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Prioritas</label>
                        <select name="priority" class="form-select form-select-sm" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Deadline</label>
                        <input type="date" name="deadline" class="form-control form-control-sm" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold">Deskripsi Tugas</label>
                    <textarea name="description" class="form-control form-control-sm" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-dark">Simpan Tugas</button>
            </div>
        </form>
    </div>
</div>
<?php $this->endSection(); ?>