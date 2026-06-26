<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <span class="text-muted small">Total: <strong><?= $total ?></strong> produk</span>
  </div>
  <a href="<?= site_url('admin/products/create') ?>" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-lg"></i> Tambah Produk
  </a>
</div>

<!-- Filter -->
<form method="get" action="<?= site_url('admin/products') ?>" class="row g-2 mb-3">
  <div class="col-auto">
    <select name="is_active" class="form-select form-select-sm" onchange="this.form.submit()">
      <option value="">Semua Status</option>
      <option value="1" <?= $filters['is_active'] === '1' ? 'selected' : '' ?>>Aktif</option>
      <option value="0" <?= $filters['is_active'] === '0' ? 'selected' : '' ?>>Nonaktif</option>
    </select>
  </div>
</form>

<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <table class="table table-hover table-striped mb-0">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Kode Produk</th>
          <th>Nama Produk</th>
          <th>Provider</th>
          <th class="text-center">Status</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $i => $p): ?>
        <tr>
          <td class="text-muted small"><?= $i + 1 ?></td>
          <td><code><?= esc($p->product_code) ?></code></td>
          <td><?= esc($p->product_name) ?></td>
          <td><?= esc($p->provider) ?></td>
          <td class="text-center">
            <?php if ($p->is_active): ?>
              <span class="badge bg-success">Aktif</span>
            <?php else: ?>
              <span class="badge bg-secondary">Nonaktif</span>
            <?php endif; ?>
          </td>
          <td class="text-center">
            <a href="<?= site_url('admin/products/edit/' . $p->id) ?>" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-pencil"></i>
            </a>
            <a href="<?= site_url('admin/products/toggle/' . $p->id) ?>"
               class="btn btn-sm <?= $p->is_active ? 'btn-outline-warning' : 'btn-outline-success' ?>"
               onclick="return confirm('Ubah status produk ini?')">
              <i class="bi bi-toggle-<?= $p->is_active ? 'on' : 'off' ?>"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada produk.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pagination): ?>
  <div class="card-footer bg-white d-flex justify-content-end">
    <?= $pagination ?>
  </div>
  <?php endif; ?>
</div>
