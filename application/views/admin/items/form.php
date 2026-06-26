<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <?php $is_edit = isset($item) && $item; ?>
        <?php $action = $is_edit ? site_url('admin/items/update/' . $item->id) : site_url('admin/items/store'); ?>

        <?= form_open($action) ?>
          <div class="mb-3">
            <label class="form-label fw-medium">Produk <span class="text-danger">*</span></label>
            <select name="product_id"
                    class="form-select <?= form_error('product_id') ? 'is-invalid' : '' ?>"
                    <?= $is_edit ? 'disabled' : '' ?>>
              <option value="">-- Pilih Produk --</option>
              <?php foreach ($products as $p): ?>
                <option value="<?= $p->id ?>"
                  <?= $is_edit ? ($p->id == $item->product_id ? 'selected' : '') : set_select('product_id', $p->id) ?>>
                  <?= esc($p->product_name) ?> (<?= esc($p->product_code) ?>)
                </option>
              <?php endforeach; ?>
            </select>
            <?php if ($is_edit): ?>
              <input type="hidden" name="product_id" value="<?= $item->product_id ?>">
            <?php endif; ?>
            <?= form_error('product_id', '<div class="invalid-feedback">', '</div>') ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium">Kode Item <span class="text-danger">*</span></label>
            <input type="text" name="item_code"
                   class="form-control text-uppercase <?= form_error('item_code') ? 'is-invalid' : '' ?>"
                   value="<?= $is_edit ? esc($item->item_code) : set_value('item_code') ?>"
                   placeholder="Contoh: RF25000"
                   <?= $is_edit ? 'readonly' : '' ?>>
            <div class="form-text">Kode unik, tidak bisa diubah setelah disimpan.</div>
            <?= form_error('item_code', '<div class="invalid-feedback">', '</div>') ?>
          </div>

          <div class="mb-4">
            <label class="form-label fw-medium">Nama Item / SKU <span class="text-danger">*</span></label>
            <input type="text" name="item_name"
                   class="form-control <?= form_error('item_name') ? 'is-invalid' : '' ?>"
                   value="<?= $is_edit ? esc($item->item_name) : set_value('item_name') ?>"
                   placeholder="Contoh: 86 Diamond, 530 Diamond, Weekly Pass">
            <?= form_error('item_name', '<div class="invalid-feedback">', '</div>') ?>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-lg"></i> <?= $is_edit ? 'Update' : 'Simpan' ?>
            </button>
            <a href="<?= site_url('admin/items') ?>" class="btn btn-outline-secondary">Batal</a>
          </div>
        <?= form_close() ?>
      </div>
    </div>
  </div>
</div>
