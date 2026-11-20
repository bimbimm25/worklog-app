<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array  $tasks
 * @var array  $updatesMap
 * @var string $selectedStatus
 */
$this->extend('layouts/employee_layout');
?>

<?php $this->section('content'); ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h5 class="fw-bold mb-0">Tugas Saya</h5>
        <small class="text-muted">Daftar tugas dari Admin dan pembaruan progres</small>
    </div>
    <form method="get" action="<?= base_url('employee/tasks') ?>" class="d-flex gap-2">
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="todo" <?= $selectedStatus === 'todo' ? 'selected' : '' ?>>To Do</option>
            <option value="in_progress" <?= $selectedStatus === 'in_progress' ? 'selected' : '' ?>>Proses</option>
            <option value="review" <?= $selectedStatus === 'review' ? 'selected' : '' ?>>Review</option>
            <option value="completed" <?= $selectedStatus === 'completed' ? 'selected' : '' ?>>Selesai</option>
        </select>
    </form>
</div>

<?php if (empty($tasks)) : ?>
    <div class="wl-card p-5 text-center text-muted small">Belum ada tugas yang diberikan kepada Anda.</div>
<?php else : ?>
    <div class="row g-3">
        <?php foreach ($tasks as $t) : ?>
            <div class="col-md-6">
                <div class="wl-card p-4 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0"><?= esc($t['title']) ?></h6>
                        <?= status_badge($t['status']) ?>
                    </div>
                    <p class="small text-muted mb-3"><?= esc($t['description'] ?: 'Tidak ada deskripsi.') ?></p>

                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Prioritas: <strong class="text-dark"><?= esc(ucfirst($t['priority'])) ?></strong></span>
                        <span>Deadline: <strong class="text-dark"><?= esc(date('d M Y', strtotime($t['deadline']))) ?></strong></span>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Progres</span>
                            <span class="fw-semibold"><?= (int) $t['progress'] ?>%</span>
                        </div>
                        <div class="progress" style="height:7px;">
                            <div class="progress-bar bg-dark" style="width: <?= (int) $t['progress'] ?>%"></div>
                        </div>
                    </div>

                    <?php if (!empty($updatesMap[$t['id']])) : ?>
                        <div class="border-top pt-2 mb-3 small">
                            <div class="text-muted mb-1" style="font-size:0.75rem;">Update Terakhir:</div>
                            <?php foreach ($updatesMap[$t['id']] as $u) : ?>
                                <div class="text-muted" style="font-size:0.75rem;">
                                    • <strong><?= (int) $u['progress'] ?>%</strong> — <?= esc($u['note'] ?: $u['status']) ?> (<?= date('d M H:i', strtotime($u['created_at'])) ?>)
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-auto">
                        <button class="btn btn-sm btn-dark w-100" data-bs-toggle="modal" data-bs-target="#updTask<?= $t['id'] ?>">
                            Perbarui Progres
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Update Progres -->
            <div class="modal fade" id="updTask<?= $t['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <form action="<?= base_url('employee/tasks/update-progress/' . $t['id']) ?>" method="post" class="modal-content">
                        <?= csrf_field() ?>
                        <div class="modal-header">
                            <h6 class="modal-title fw-bold">Update Progres Tugas</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Status Pekerjaan</label>
                                <select name="status" class="form-select form-select-sm" required>
                                    <option value="todo" <?= $t['status'] === 'todo' ? 'selected' : '' ?>>To Do</option>
                                    <option value="in_progress" <?= $t['status'] === 'in_progress' ? 'selected' : '' ?>>Proses</option>
                                    <option value="review" <?= $t['status'] === 'review' ? 'selected' : '' ?>>Review</option>
                                    <option value="completed" <?= $t['status'] === 'completed' ? 'selected' : '' ?>>Selesai</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Progres (%)</label>
                                <input type="number" name="progress" class="form-control form-control-sm" min="0" max="100" value="<?= (int) $t['progress'] ?>" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-semibold">Catatan Pekerjaan</label>
                                <textarea name="note" class="form-control form-control-sm" rows="3" placeholder="Apa yang sudah dikerjakan?"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-dark">Simpan Update</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php $this->endSection(); ?>