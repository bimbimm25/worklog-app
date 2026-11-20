<?php $uri = service('uri')->getPath(); ?>
<aside class="wl-sidebar" id="wlSidebar">
    <div class="wl-sidebar-brand">
        <span class="wl-brand-icon"><i data-lucide="activity" style="width:18px;height:18px;"></i></span>
        <div>
            <div class="fw-bold lh-1">WORKLOG</div>
            <small class="text-muted" style="font-size:0.7rem;">Admin Panel</small>
        </div>
    </div>
    <nav class="wl-nav">
        <div class="wl-nav-label">Utama</div>
        <a href="<?= base_url('admin/dashboard') ?>" class="wl-nav-link <?= str_contains($uri, 'admin/dashboard') ? 'active' : '' ?>">
            <i data-lucide="layout-dashboard"></i> Dashboard
        </a>

        <div class="wl-nav-label">Organisasi</div>
        <a href="<?= base_url('admin/employees') ?>" class="wl-nav-link <?= str_contains($uri, 'admin/employees') ? 'active' : '' ?>">
            <i data-lucide="users"></i> Karyawan
        </a>
        <a href="<?= base_url('admin/departments') ?>" class="wl-nav-link <?= str_contains($uri, 'admin/departments') ? 'active' : '' ?>">
            <i data-lucide="building-2"></i> Departemen
        </a>
        <a href="<?= base_url('admin/positions') ?>" class="wl-nav-link <?= str_contains($uri, 'admin/positions') ? 'active' : '' ?>">
            <i data-lucide="briefcase"></i> Jabatan
        </a>
        <a href="<?= base_url('admin/schedules') ?>" class="wl-nav-link <?= str_contains($uri, 'admin/schedules') ? 'active' : '' ?>">
            <i data-lucide="clock"></i> Jadwal Kerja
        </a>

        <div class="wl-nav-label">Monitoring</div>
        <a href="<?= base_url('admin/attendance') ?>" class="wl-nav-link <?= str_contains($uri, 'admin/attendance') ? 'active' : '' ?>">
            <i data-lucide="calendar-check"></i> Absensi
        </a>
        <a href="<?= base_url('admin/activities') ?>" class="wl-nav-link <?= str_contains($uri, 'admin/activities') ? 'active' : '' ?>">
            <i data-lucide="clipboard-list"></i> Aktivitas & Laporan
        </a>
        <a href="<?= base_url('admin/tasks') ?>" class="wl-nav-link <?= str_contains($uri, 'admin/tasks') ? 'active' : '' ?>">
            <i data-lucide="check-square"></i> Manajemen Tugas
        </a>
        <a href="<?= base_url('admin/leaves') ?>" class="wl-nav-link <?= str_contains($uri, 'admin/leaves') ? 'active' : '' ?>">
            <i data-lucide="calendar-off"></i> Izin & Cuti
        </a>

        <div class="wl-nav-label">Analitik & Sistem</div>
        <a href="<?= base_url('admin/reports') ?>" class="wl-nav-link <?= str_contains($uri, 'admin/reports') ? 'active' : '' ?>">
            <i data-lucide="bar-chart-3"></i> Laporan & Export
        </a>
        <a href="<?= base_url('admin/logs') ?>" class="wl-nav-link <?= str_contains($uri, 'admin/logs') ? 'active' : '' ?>">
            <i data-lucide="history"></i> Riwayat Aktivitas
        </a>
        <a href="<?= base_url('admin/settings') ?>" class="wl-nav-link <?= str_contains($uri, 'admin/settings') ? 'active' : '' ?>">
            <i data-lucide="settings"></i> Pengaturan
        </a>
    </nav>
</aside>