<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <span class="text-muted small">Total: <strong><?= $total ?></strong> item</span>
  </div>
  <a href="<?= site_url('admin/items/create') ?>" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-lg"></i> Tambah Item
  </a>
</div>

<!-- Filter -->
<form method="get" action="<?= site_url('admin/items') ?>" class="row g-2 mb-3">
  <div class="col-auto">
    <select name="product_id" class="form-select form-select-sm" onchange="this.form.submit()">
      <option value="">Semua Produk</option>
      <?php foreach ($products as $p): ?>
        <option value="<?= $p->id ?>" <?= $filters['product_id'] == $p->id ? 'selected' : '' ?>>
          <?= esc($p->product_name) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
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
          <th>Kode Item</th>
          <th>Nama Item / SKU</th>
          <th>Produk</th>
          <th class="text-center">Status</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $i => $item): ?>
        <tr>
          <td class="text-muted small"><?= $i + 1 ?></td>
          <td><code><?= esc($item->item_code) ?></code></td>
          <td class="fw-medium"><?= esc($item->item_name) ?></td>
          <td>
            <div><?= esc($item->product_name) ?></div>
            <small class="text-muted"><?= esc($item->product_code) ?></small>
          </td>
          <td class="text-center">
            <?php if ($item->is_active): ?>
              <span class="badge bg-success">Aktif</span>
            <?php else: ?>
              <span class="badge bg-secondary">Nonaktif</span>
            <?php endif; ?>
          </td>
          <td class="text-center">
            <a href="<?= site_url('admin/items/edit/' . $item->id) ?>" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-pencil"></i>
            </a>
            <a href="<?= site_url('admin/items/toggle/' . $item->id) ?>"
               class="btn btn-sm <?= $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' ?>"
               onclick="return confirm('Ubah status item ini?')">
              <i class="bi bi-toggle-<?= $item->is_active ? 'on' : 'off' ?>"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($items)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada item.</td></tr>
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
