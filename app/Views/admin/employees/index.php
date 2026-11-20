<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array  $employees
 * @var array  $departments
 * @var array  $positions
 * @var array  $schedules
 * @var string $keyword
 * @var string $departmentId
 */
$this->extend('layouts/admin_layout');
?>

<?php $this->section('content'); ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h5 class="fw-bold mb-0">Manajemen Karyawan</h5>
        <small class="text-muted">Kelola akun login, departemen, jabatan, dan jadwal kerja</small>
    </div>
    <button class="btn btn-dark btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addEmpModal">
        <i data-lucide="user-plus" style="width:15px;height:15px;" class="me-1"></i> Tambah Karyawan
    </button>
</div>

<!-- Filter & Search -->
<div class="wl-card p-3 mb-3">
    <form method="get" action="<?= base_url('admin/employees') ?>" class="row g-2">
        <div class="col-md-5">
            <input type="text" name="q" class="form-control form-control-sm"
                placeholder="Cari nama, kode karyawan, atau email..." value="<?= esc($keyword) ?>">
        </div>
        <div class="col-md-4">
            <select name="department_id" class="form-select form-select-sm">
                <option value="">Semua Departemen</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= $departmentId === (string) $d['id'] ? 'selected' : '' ?>>
                        <?= esc($d['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm flex-fill">Cari</button>
            <a href="<?= base_url('admin/employees') ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Tabel Karyawan -->
<div class="wl-card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama & Email</th>
                    <th>Departemen / Jabatan</th>
                    <th>Jadwal</th>
                    <th>Role & Status</th>
                    <th class="text-end" style="width:140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($employees)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada data karyawan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($employees as $e): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border"><?= esc($e['employee_code']) ?></span></td>
                            <td>
                                <div class="fw-semibold"><?= esc($e['full_name']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= esc($e['email']) ?></div>
                            </td>
                            <td>
                                <div><?= esc($e['department_name'] ?? '-') ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= esc($e['position_name'] ?? '-') ?></div>
                            </td>
                            <td><?= esc($e['schedule_name'] ?? '-') ?></td>
                            <td>
                                <span
                                    class="badge bg-secondary-subtle text-secondary me-1"><?= esc(ucfirst($e['role'])) ?></span>
                                <?= (int) $e['is_active'] === 1 ? '<span class="badge bg-success-subtle text-success">Aktif</span>' : '<span class="badge bg-danger-subtle text-danger">Nonaktif</span>' ?>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editEmp<?= $e['id'] ?>">Edit</button>
                                <form action="<?= base_url('admin/employees/delete/' . $e['id']) ?>" method="post"
                                    class="d-inline" onsubmit="return confirm('Hapus karyawan ini beserta akun loginnya?')">
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

<!-- Modal Edit Karyawan (Diletakkan di luar tabel) -->
<?php if (!empty($employees)): ?>
    <?php foreach ($employees as $e): ?>
        <div class="modal fade" id="editEmp<?= $e['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="<?= base_url('admin/employees/update/' . $e['id']) ?>" method="post" class="modal-content">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Edit Karyawan</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Kode Karyawan</label>
                                <input type="text" name="employee_code" class="form-control form-control-sm"
                                    value="<?= esc($e['employee_code']) ?>" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">Nama Lengkap</label>
                                <input type="text" name="full_name" class="form-control form-control-sm"
                                    value="<?= esc($e['full_name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email Login</label>
                                <input type="email" name="email" class="form-control form-control-sm"
                                    value="<?= esc($e['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Password Baru (kosongkan jika tetap)</label>
                                <input type="password" name="password" class="form-control form-control-sm"
                                    placeholder="Min. 6 karakter">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">No. Telepon</label>
                                <input type="text" name="phone" class="form-control form-control-sm"
                                    value="<?= esc($e['phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Role</label>
                                <select name="role" class="form-select form-select-sm" required>
                                    <option value="employee" <?= $e['role'] === 'employee' ? 'selected' : '' ?>>Employee</option>
                                    <option value="admin" <?= $e['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Status Akun</label>
                                <select name="is_active" class="form-select form-select-sm" required>
                                    <option value="1" <?= (int) $e['is_active'] === 1 ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= (int) $e['is_active'] === 0 ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Departemen</label>
                                <select name="department_id" class="form-select form-select-sm">
                                    <option value="">- Pilih -</option>
                                    <?php foreach ($departments as $d): ?>
                                        <option value="<?= $d['id'] ?>" <?= (string) $e['department_id'] === (string) $d['id'] ? 'selected' : '' ?>><?= esc($d['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Jabatan</label>
                                <select name="position_id" class="form-select form-select-sm">
                                    <option value="">- Pilih -</option>
                                    <?php foreach ($positions as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= (string) $e['position_id'] === (string) $p['id'] ? 'selected' : '' ?>><?= esc($p['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Jadwal Kerja</label>
                                <select name="work_schedule_id" class="form-select form-select-sm">
                                    <option value="">- Pilih -</option>
                                    <?php foreach ($schedules as $s): ?>
                                        <option value="<?= $s['id'] ?>" <?= (string) $e['work_schedule_id'] === (string) $s['id'] ? 'selected' : '' ?>><?= esc($s['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Tanggal Bergabung</label>
                                <input type="date" name="join_date" class="form-control form-control-sm"
                                    value="<?= esc($e['join_date'] ?? '') ?>">
                            </div>
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
<div class="modal fade" id="addEmpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('admin/employees/store') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tambah Karyawan Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Kode Karyawan</label>
                        <input type="text" name="employee_code" class="form-control form-control-sm"
                            placeholder="EMP-0003" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" name="full_name" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email Login</label>
                        <input type="email" name="email" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Password Awal</label>
                        <input type="password" name="password" class="form-control form-control-sm"
                            placeholder="Min. 6 karakter" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">No. Telepon</label>
                        <input type="text" name="phone" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Role</label>
                        <select name="role" class="form-select form-select-sm" required>
                            <option value="employee">Employee</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Departemen</label>
                        <select name="department_id" class="form-select form-select-sm">
                            <option value="">- Pilih -</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= esc($d['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Jabatan</label>
                        <select name="position_id" class="form-select form-select-sm">
                            <option value="">- Pilih -</option>
                            <?php foreach ($positions as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= esc($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Jadwal Kerja</label>
                        <select name="work_schedule_id" class="form-select form-select-sm">
                            <option value="">- Pilih -</option>
                            <?php foreach ($schedules as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= esc($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Tanggal Bergabung</label>
                        <input type="date" name="join_date" class="form-control form-control-sm"
                            value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-dark">Simpan Karyawan</button>
            </div>
        </form>
    </div>
</div>
<?php $this->endSection(); ?>