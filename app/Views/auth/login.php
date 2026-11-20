<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — WORKLOG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
            background: #ffffff;
        }

        .brand-badge {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background-color: #0f172a;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="container px-3">
        <div class="login-card mx-auto p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="brand-badge mb-3">
                    <i data-lucide="activity"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">WORKLOG</h4>
                <p class="text-muted small mb-0">Employee Activity Monitoring System</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger py-2 small d-flex align-items-center gap-2" role="alert">
                    <i data-lucide="alert-circle" style="width:16px;height:16px;"></i>
                    <span><?= esc(session()->getFlashdata('error')) ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success py-2 small d-flex align-items-center gap-2" role="alert">
                    <i data-lucide="check-circle-2" style="width:16px;height:16px;"></i>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold text-secondary">Email</label>
                    <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                        id="email" name="email" value="<?= old('email') ?>" placeholder="Masukkan Email" required
                        autofocus>
                    <?php if (session('errors.email')): ?>
                        <div class="invalid-feedback"><?= esc(session('errors.email')) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
                    <input type="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
                        id="password" name="password" placeholder="Masukkan password" required>
                    <?php if (session('errors.password')): ?>
                        <div class="invalid-feedback"><?= esc(session('errors.password')) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold">
                    Masuk ke WORKLOG
                </button>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>

</html>