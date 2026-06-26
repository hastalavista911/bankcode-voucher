<div class="row g-4">
  <!-- Form tambah satu voucher -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white fw-semibold">Tambah Voucher Manual</div>
      <div class="card-body">
        <?= form_open('admin/vouchers/store') ?>
          <div class="mb-3">
            <label class="form-label fw-medium">Item / SKU <span class="text-danger">*</span></label>
            <select name="item_id" id="item_select"
                    class="form-select <?= form_error('item_id') ? 'is-invalid' : '' ?>"
                    onchange="updateVoucherPrefix(this)">
              <option value="">-- Pilih Item --</option>
              <?php foreach ($items as $it): ?>
                <option value="<?= $it->id ?>"
                        data-product-code="<?= esc($it->product_code) ?>"
                        <?= set_select('item_id', $it->id) ?>>
                  <?= esc($it->product_name) ?> &mdash; <?= esc($it->item_name) ?>
                  (<?= esc($it->item_code) ?>)
                </option>
              <?php endforeach; ?>
            </select>
            <?= form_error('item_id', '<div class="invalid-feedback">', '</div>') ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium">Kode Voucher <span class="text-danger">*</span></label>
            <input type="text" name="voucher_code" id="voucher_code"
                   class="form-control <?= form_error('voucher_code') ? 'is-invalid' : '' ?>"
                   value="<?= set_value('voucher_code', $generated_code) ?>"
                   placeholder="Contoh: RF-ABCD-1234-EFGH">
            <?= form_error('voucher_code', '<div class="invalid-feedback">', '</div>') ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium">Harga (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="price" min="0" step="100"
                   class="form-control <?= form_error('price') ? 'is-invalid' : '' ?>"
                   value="<?= set_value('price') ?>"
                   placeholder="Contoh: 15000">
            <?= form_error('price', '<div class="invalid-feedback">', '</div>') ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium">Serial Number <small class="text-muted">(opsional)</small></label>
            <input type="text" name="serial_number"
                   class="form-control"
                   value="<?= set_value('serial_number') ?>"
                   placeholder="Kosongkan jika tidak ada">
          </div>

          <div class="mb-4">
            <label class="form-label fw-medium">Expired Date <small class="text-muted">(opsional)</small></label>
            <input type="date" name="expired_date"
                   class="form-control"
                   value="<?= set_value('expired_date') ?>">
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-plus-lg"></i> Simpan
            </button>
            <a href="<?= site_url('admin/vouchers') ?>" class="btn btn-outline-secondary">Batal</a>
          </div>
        <?= form_close() ?>
      </div>
    </div>
  </div>

  <!-- Bulk import CSV -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white fw-semibold">Import Bulk via CSV</div>
      <div class="card-body">
        <p class="text-muted small mb-3">
          Format CSV: <code>item_code, voucher_code, serial_number (opsional), price, expired_date (opsional)</code><br>
          Baris pertama dianggap header dan akan dilewati. Baris dengan <code>item_code</code> tidak dikenali akan dilewati.
        </p>

        <?= form_open_multipart('admin/vouchers/import') ?>
          <div class="mb-4">
            <label class="form-label fw-medium">File CSV <span class="text-danger">*</span></label>
            <input type="file" name="csv_file" class="form-control" accept=".csv">
          </div>

          <button type="submit" class="btn btn-success">
            <i class="bi bi-upload"></i> Import
          </button>
        <?= form_close() ?>

        <hr class="my-3">
        <a href="<?= site_url('admin/vouchers/template') ?>" class="btn btn-sm btn-outline-secondary">
          <i class="bi bi-download"></i> Download Template CSV
        </a>
      </div>
    </div>
  </div>
</div>

<script>
function updateVoucherPrefix(select) {
  var field = document.getElementById('voucher_code');
  if (!field) return;
  var opt  = select.options[select.selectedIndex];
  var code = opt && opt.dataset.productCode ? opt.dataset.productCode : 'RF';
  field.value = code + '-' + generateSuffix();
}

function generateSuffix(pattern) {
  pattern = pattern || 'AAAA-9999-AAAA';
  var letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  var result  = '';
  for (var i = 0; i < pattern.length; i++) {
    var c = pattern[i];
    if      (c === 'A') result += letters[Math.floor(Math.random() * 26)];
    else if (c === '9') result += Math.floor(Math.random() * 10);
    else                result += c;
  }
  return result;
}
</script>
