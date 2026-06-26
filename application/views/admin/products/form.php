<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <?php $is_edit = isset($product) && $product; ?>
        <?php $action = $is_edit ? site_url('admin/products/update/' . $product->id) : site_url('admin/products/store'); ?>

        <?= form_open($action) ?>
          <div class="mb-3">
            <label class="form-label fw-medium">Kode Produk <span class="text-danger">*</span></label>
            <input type="text" name="product_code"
                   class="form-control text-uppercase <?= form_error('product_code') ? 'is-invalid' : '' ?>"
                   value="<?= set_value('product_code', $is_edit ? $product->product_code : '') ?>"
                   placeholder="Contoh: RF"
                   <?= $is_edit ? 'readonly' : '' ?>>
            <?= form_error('product_code', '<div class="invalid-feedback">', '</div>') ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium">Nama Produk <span class="text-danger">*</span></label>
            <input type="text" name="product_name"
                   class="form-control <?= form_error('product_name') ? 'is-invalid' : '' ?>"
                   value="<?= set_value('product_name', $is_edit ? $product->product_name : '') ?>"
                   placeholder="Contoh: Mobile Legends: Bang Bang">
            <?= form_error('product_name', '<div class="invalid-feedback">', '</div>') ?>
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium">Provider / Game <span class="text-danger">*</span></label>
            <input type="text" name="provider"
                   class="form-control <?= form_error('provider') ? 'is-invalid' : '' ?>"
                   value="<?= set_value('provider', $is_edit ? $product->provider : '') ?>"
                   placeholder="Contoh: Moonton">
            <?= form_error('provider', '<div class="invalid-feedback">', '</div>') ?>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-lg"></i> <?= $is_edit ? 'Update' : 'Simpan' ?>
            </button>
            <a href="<?= site_url('admin/products') ?>" class="btn btn-outline-secondary">Batal</a>
          </div>
        <?= form_close() ?>
      </div>
    </div>
  </div>
</div>
