<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Dashboard') ?> — WORKLOG Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/worklog.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="wl-wrapper">
        <?= view('components/sidebar_admin') ?>
        <div class="wl-main">
            <?= view('components/topbar', ['pageTitle' => $pageTitle ?? 'Dashboard']) ?>
            <main class="wl-content">
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <?= view('components/toast') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="<?= base_url('assets/js/worklog.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>