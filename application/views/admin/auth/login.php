<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — BankCode</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body { background: #1e293b; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-card { width: 100%; max-width: 380px; }
    .brand-icon { font-size: 2.5rem; color: #38bdf8; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="card shadow-lg border-0">
      <div class="card-body p-4">
        <div class="text-center mb-4">
          <div class="brand-icon"><i class="bi bi-bank2"></i></div>
          <h5 class="fw-bold mt-2 mb-0">BankCode</h5>
          <small class="text-muted">Voucher Management System</small>
        </div>

        <?php if ($this->session->flashdata('error')): ?>
          <div class="alert alert-danger py-2">
            <i class="bi bi-exclamation-circle"></i>
            <?= $this->session->flashdata('error') ?>
          </div>
        <?php endif; ?>

        <?= form_open('admin/login') ?>
          <div class="mb-3">
            <label class="form-label fw-medium">Username</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-person"></i></span>
              <input type="text" name="username" class="form-control <?= form_error('username') ? 'is-invalid' : '' ?>"
                     value="<?= set_value('username') ?>" placeholder="Username" autofocus>
            </div>
            <?= form_error('username', '<div class="text-danger small mt-1">', '</div>') ?>
          </div>

          <div class="mb-4">
            <label class="form-label fw-medium">Password</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" name="password" class="form-control <?= form_error('password') ? 'is-invalid' : '' ?>"
                     placeholder="Password">
            </div>
            <?= form_error('password', '<div class="text-danger small mt-1">', '</div>') ?>
          </div>

          <button type="submit" class="btn btn-primary w-100 fw-semibold">
            <i class="bi bi-box-arrow-in-right"></i> Masuk
          </button>
        <?= form_close() ?>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
