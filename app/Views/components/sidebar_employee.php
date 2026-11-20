<?php $uri = service('uri')->getPath(); ?>
<aside class="wl-sidebar" id="wlSidebar">
    <div class="wl-sidebar-brand">
        <span class="wl-brand-icon"><i data-lucide="activity" style="width:18px;height:18px;"></i></span>
        <div>
            <div class="fw-bold lh-1">WORKLOG</div>
            <small class="text-muted" style="font-size:0.7rem;">Employee Workspace</small>
        </div>
    </div>
    <nav class="wl-nav">
        <div class="wl-nav-label">Workspace</div>
        <a href="<?= base_url('employee/dashboard') ?>" class="wl-nav-link <?= str_contains($uri, 'employee/dashboard') ? 'active' : '' ?>">
            <i data-lucide="layout-dashboard"></i> Dashboard
        </a>
        <a href="<?= base_url('employee/attendance') ?>" class="wl-nav-link <?= str_contains($uri, 'employee/attendance') ? 'active' : '' ?>">
            <i data-lucide="fingerprint"></i> Absensi
        </a>
        <a href="<?= base_url('employee/activities') ?>" class="wl-nav-link <?= str_contains($uri, 'employee/activities') ? 'active' : '' ?>">
            <i data-lucide="clipboard-edit"></i> Aktivitas Harian
        </a>
        <a href="<?= base_url('employee/tasks') ?>" class="wl-nav-link <?= str_contains($uri, 'employee/tasks') ? 'active' : '' ?>">
            <i data-lucide="check-square"></i> Tugas Saya
        </a>
        <a href="<?= base_url('employee/leaves') ?>" class="wl-nav-link <?= str_contains($uri, 'employee/leaves') ? 'active' : '' ?>">
            <i data-lucide="calendar-off"></i> Izin & Cuti
        </a>

        <div class="wl-nav-label">Akun</div>
        <a href="<?= base_url('employee/profile') ?>" class="wl-nav-link <?= str_contains($uri, 'employee/profile') ? 'active' : '' ?>">
            <i data-lucide="user-circle"></i> Profil Saya
        </a>
    </nav>
</aside>