<nav id="sidebar">
  <div class="brand"><i class="bi bi-bank2"></i> BankCode</div>
  <ul class="nav flex-column mt-2">
    <li class="nav-item">
      <a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= (uri_string() === 'admin/dashboard') ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= site_url('admin/products') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/products') === 0) ? 'active' : '' ?>">
        <i class="bi bi-box-seam"></i> Produk
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= site_url('admin/items') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/items') === 0) ? 'active' : '' ?>">
        <i class="bi bi-tags"></i> Item / SKU
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= site_url('admin/vouchers') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/vouchers') === 0) ? 'active' : '' ?>">
        <i class="bi bi-ticket-perforated"></i> Voucher
      </a>
    </li>
    <li class="nav-item mt-2">
      <div class="text-secondary px-3 mb-1" style="font-size:.7rem;letter-spacing:.08em;text-transform:uppercase">Pengembang</div>
    </li>
    <li class="nav-item">
      <a href="<?= site_url('admin/api-docs') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/api-docs') === 0) ? 'active' : '' ?>">
        <i class="bi bi-code-square"></i> API Docs
      </a>
    </li>
  </ul>

  <div style="position:absolute; bottom:0; width:100%; padding:.75rem 1rem; border-top:1px solid rgba(255,255,255,.1);">
    <small class="text-secondary d-block mb-1"><?= esc($admin_name) ?></small>
    <a href="<?= site_url('admin/logout') ?>" class="btn btn-sm btn-outline-danger w-100">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </div>
</nav>

<div id="main">
  <div class="topbar">
    <h6 class="mb-0 fw-semibold"><?= isset($title) ? esc($title) : '' ?></h6>
  </div>
  <div class="content">

  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $this->session->flashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= $this->session->flashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
