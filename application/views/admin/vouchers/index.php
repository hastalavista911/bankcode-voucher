<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <span class="text-muted small">Total: <strong><?= $total ?></strong> voucher</span>
  </div>
  <div class="d-flex gap-2">
    <a href="<?= site_url('admin/vouchers/export?' . http_build_query($filters)) ?>" class="btn btn-sm btn-outline-success">
      <i class="bi bi-download"></i> Export CSV
    </a>
    <a href="<?= site_url('admin/vouchers/create') ?>" class="btn btn-sm btn-primary">
      <i class="bi bi-plus-lg"></i> Tambah Voucher
    </a>
  </div>
</div>

<!-- Filter -->
<form method="get" action="<?= site_url('admin/vouchers') ?>" class="row g-2 mb-3">
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
    <select name="item_id" class="form-select form-select-sm" onchange="this.form.submit()">
      <option value="">Semua Item</option>
      <?php foreach ($items as $it): ?>
        <option value="<?= $it->id ?>" <?= $filters['item_id'] == $it->id ? 'selected' : '' ?>>
          <?= esc($it->item_name) ?> (<?= esc($it->item_code) ?>)
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-auto">
    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
      <option value="">Semua Status</option>
      <option value="available" <?= $filters['status'] === 'available' ? 'selected' : '' ?>>Available</option>
      <option value="locked"    <?= $filters['status'] === 'locked'    ? 'selected' : '' ?>>Locked</option>
      <option value="released"  <?= $filters['status'] === 'released'  ? 'selected' : '' ?>>Released</option>
    </select>
  </div>
</form>

<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <table class="table table-hover table-striped mb-0">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Produk</th>
          <th>Item / SKU</th>
          <th>Voucher Code</th>
          <th>Serial Number</th>
          <th class="text-end">Harga</th>
          <th>Expired</th>
          <th class="text-center">Status</th>
          <th>Order ID</th>
          <th>Released At</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vouchers as $i => $v): ?>
        <tr>
          <td class="text-muted small"><?= $offset + $i + 1 ?></td>
          <td>
            <div class="fw-medium"><?= esc($v->product_name) ?></div>
            <small class="text-muted"><?= esc($v->product_code) ?></small>
          </td>
          <td>
            <div><?= esc($v->item_name) ?></div>
            <small class="text-muted"><code><?= esc($v->item_code) ?></code></small>
          </td>
          <td><code><?= esc($v->voucher_code) ?></code></td>
          <td><?= $v->serial_number ? esc($v->serial_number) : '<span class="text-muted">—</span>' ?></td>
          <td class="text-end">Rp <?= number_format($v->price, 0, ',', '.') ?></td>
          <td><?= $v->expired_date ? date('d/m/Y', strtotime($v->expired_date)) : '<span class="text-muted">—</span>' ?></td>
          <td class="text-center">
            <?php if ($v->status === 'available'): ?>
              <span class="badge bg-success">Available</span>
            <?php elseif ($v->status === 'locked'): ?>
              <span class="badge bg-warning text-dark">Locked</span>
            <?php else: ?>
              <span class="badge bg-info text-dark">Released</span>
            <?php endif; ?>
          </td>
          <td><small class="text-muted"><?= $v->order_id ? esc($v->order_id) : '—' ?></small></td>
          <td><small class="text-muted"><?= $v->released_at ? date('d/m/Y H:i', strtotime($v->released_at)) : '—' ?></small></td>
          <td>
            <?php if ($v->status === 'available'): ?>
            <a href="<?= site_url('admin/vouchers/delete/' . $v->id) ?>"
               class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Hapus voucher ini?')">
              <i class="bi bi-trash"></i>
            </a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($vouchers)): ?>
        <tr><td colspan="11" class="text-center text-muted py-4">Tidak ada voucher.</td></tr>
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
