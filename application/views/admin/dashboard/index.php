<!-- Stat cards -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-lg-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="fs-2 text-primary"><i class="bi bi-ticket-perforated-fill"></i></div>
        <div>
          <div class="text-muted small">Total Item Aktif</div>
          <div class="fw-bold fs-5"><?= count($stock_summary) ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="fs-2 text-success"><i class="bi bi-check-circle-fill"></i></div>
        <div>
          <div class="text-muted small">Total Available</div>
          <div class="fw-bold fs-5"><?= array_sum(array_column($stock_summary, 'available')) ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="fs-2 text-info"><i class="bi bi-send-fill"></i></div>
        <div>
          <div class="text-muted small">Total Released</div>
          <div class="fw-bold fs-5"><?= array_sum(array_column($stock_summary, 'released')) ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="fs-2 text-warning"><i class="bi bi-calendar-check-fill"></i></div>
        <div>
          <div class="text-muted small">Transaksi Hari Ini</div>
          <div class="fw-bold fs-5"><?= $today_count ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <!-- Stok per item -->
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white fw-semibold border-bottom">Ringkasan Stok Voucher</div>
      <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
          <thead class="table-light">
            <tr>
              <th>Item / SKU</th>
              <th class="text-center">Available</th>
              <th class="text-center">Locked</th>
              <th class="text-center">Released</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($stock_summary as $row): ?>
            <tr>
              <td>
                <div class="fw-medium"><?= esc($row->item_name) ?></div>
                <small class="text-muted"><?= esc($row->product_name) ?> &bull; <code><?= esc($row->item_code) ?></code></small>
              </td>
              <td class="text-center"><span class="badge bg-success"><?= $row->available ?></span></td>
              <td class="text-center"><span class="badge bg-warning text-dark"><?= $row->locked ?></span></td>
              <td class="text-center"><span class="badge bg-info text-dark"><?= $row->released ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($stock_summary)): ?>
            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada item aktif.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Transaksi terbaru -->
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white fw-semibold border-bottom">10 Transaksi Terbaru</div>
      <div class="card-body p-0">
        <div class="list-group list-group-flush">
          <?php foreach ($recent_releases as $log): ?>
          <div class="list-group-item py-2 px-3">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="fw-medium small"><?= esc($log->order_id) ?></div>
                <div class="text-muted" style="font-size:.8rem">
                  <?= esc($log->item_name) ?> &bull; <?= esc($log->partner_name) ?>
                </div>
              </div>
              <small class="text-muted text-nowrap ms-2"><?= date('d/m H:i', strtotime($log->created_at)) ?></small>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($recent_releases)): ?>
          <div class="list-group-item text-center text-muted py-3">Belum ada transaksi.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
