<?php
$notifData = get_user_notifications(5);
$fullName = session()->get('full_name') ?? 'User';
$role = session()->get('role') === 'admin' ? 'Administrator' : 'Karyawan';
$initials = strtoupper(substr($fullName, 0, 2));
?>
<header class="wl-topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-outline-secondary d-lg-none" id="wlSidebarToggle" type="button">
            <i data-lucide="menu" style="width:18px;height:18px;"></i>
        </button>
        <h6 class="mb-0 fw-semibold text-dark"><?= esc($pageTitle ?? 'Dashboard') ?></h6>
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- Notifikasi -->
        <div class="dropdown">
            <button class="btn btn-light btn-sm position-relative rounded-circle p-2" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i data-lucide="bell" style="width:18px;height:18px;"></i>
                <?php if ($notifData['unread_count'] > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size:0.65rem;">
                        <?= $notifData['unread_count'] ?>
                    </span>
                <?php endif; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-sm border p-0" style="width: 300px;">
                <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                    <span class="fw-semibold small">Notifikasi</span>
                    <?php if ($notifData['unread_count'] > 0): ?>
                        <form action="<?= base_url('notifications/read-all') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none"
                                style="font-size:0.75rem;">Tandai dibaca</button>
                        </form>
                    <?php else: ?>
                        <span class="badge bg-light text-dark">0 baru</span>
                    <?php endif; ?>
                </div>
                <div style="max-height: 260px; overflow-y: auto;">
                    <?php if (empty($notifData['items'])): ?>
                        <div class="p-3 text-center text-muted small">Belum ada notifikasi.</div>
                    <?php else: ?>
                        <?php foreach ($notifData['items'] as $n): ?>
                            <a href="<?= esc($n['link'] ?: '#') ?>"
                                class="dropdown-item py-2 px-3 border-bottom text-wrap <?= $n['is_read'] ? '' : 'bg-light' ?>">
                                <div class="fw-semibold small"><?= esc($n['title']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= esc($n['message']) ?></div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Profil -->
        <div class="dropdown">
            <button class="btn btn-link text-decoration-none text-dark d-flex align-items-center gap-2 p-0"
                data-bs-toggle="dropdown">
                <span class="wl-avatar"><?= esc($initials) ?></span>
                <div class="text-start d-none d-md-block lh-sm">
                    <div class="small fw-semibold"><?= esc($fullName) ?></div>
                    <div class="text-muted" style="font-size:0.72rem;"><?= esc($role) ?></div>
                </div>
                <i data-lucide="chevron-down" style="width:14px;height:14px;" class="text-muted"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2">
                <?php if (session()->get('role') === 'employee'): ?>
                    <li><a class="dropdown-item small" href="<?= base_url('employee/profile') ?>">Profil Saya</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                <?php endif; ?>
                <li>
                    <form action="<?= base_url('logout') ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" class="dropdown-item small text-danger d-flex align-items-center gap-2">
                            <i data-lucide="log-out" style="width:15px;height:15px;"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>