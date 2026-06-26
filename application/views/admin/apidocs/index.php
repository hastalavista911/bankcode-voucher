<?php $api_base = rtrim($base_url, '/') . '/api/v1'; ?>

<div class="row g-4">

  <!-- Sidebar navigasi -->
  <div class="col-lg-3 d-none d-lg-block">
    <div class="card border-0 shadow-sm sticky-top" style="top:1rem">
      <div class="card-body p-3">
        <div class="fw-semibold text-muted small text-uppercase mb-2">Navigasi</div>
        <nav class="nav flex-column gap-1">
          <a class="nav-link py-1 px-2 rounded" href="#overview">Overview</a>
          <a class="nav-link py-1 px-2 rounded" href="#authentication">Autentikasi</a>
          <a class="nav-link py-1 px-2 rounded" href="#release-voucher">POST release-voucher</a>
          <a class="nav-link py-1 px-2 rounded" href="#response-format">Format Response</a>
          <a class="nav-link py-1 px-2 rounded" href="#error-codes">Error Codes</a>
          <a class="nav-link py-1 px-2 rounded" href="#curl-example">Contoh cURL</a>
        </nav>
      </div>
    </div>
  </div>

  <!-- Konten -->
  <div class="col-lg-9">

    <!-- Overview -->
    <section id="overview" class="mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="fw-bold mb-1">BankCode Voucher API <span class="badge bg-primary ms-1">v1</span></h5>
          <p class="text-muted mb-3">API untuk merilis voucher secara otomatis. Semua request dan response menggunakan format <strong>JSON</strong>.</p>
          <div class="p-3 rounded" style="background:#f8f9fa">
            <div class="text-muted small fw-medium mb-1">Base URL</div>
            <code class="fs-6"><?= esc($api_base) ?></code>
          </div>
        </div>
      </div>
    </section>

    <!-- Autentikasi -->
    <section id="authentication" class="mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold border-bottom">Autentikasi</div>
        <div class="card-body">
          <p class="mb-2">Setiap request harus menyertakan API Key di header:</p>
          <pre class="p-3 rounded mb-3" style="background:#1e1e1e;color:#d4d4d4;font-size:.85rem">X-API-Key: {api_key_anda}</pre>
          <div class="alert alert-warning mb-0 small">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            Jangan sertakan API Key di URL atau body request. API Key bersifat rahasia dan tidak boleh dibagikan.
          </div>
        </div>
      </div>
    </section>

    <!-- Endpoint: release-voucher -->
    <section id="release-voucher" class="mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
          <span class="badge bg-success me-2">POST</span>
          <code>/api/v1/release-voucher</code>
        </div>
        <div class="card-body">
          <p class="text-muted mb-4">Merilis satu voucher yang tersedia untuk item/SKU tertentu. Proses bersifat <strong>atomik</strong> — dijamin tidak ada dua request yang mendapat voucher yang sama.</p>

          <!-- Request Headers -->
          <h6 class="fw-semibold mb-2">Request Headers</h6>
          <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm mb-0">
              <thead class="table-light">
                <tr><th>Header</th><th>Wajib</th><th>Keterangan</th></tr>
              </thead>
              <tbody>
                <tr>
                  <td><code>Content-Type</code></td>
                  <td><span class="badge bg-success">Ya</span></td>
                  <td><code>application/json</code></td>
                </tr>
                <tr>
                  <td><code>X-API-Key</code></td>
                  <td><span class="badge bg-success">Ya</span></td>
                  <td>API Key yang diberikan oleh admin</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Request Body -->
          <h6 class="fw-semibold mb-2">Request Body</h6>
          <div class="table-responsive mb-3">
            <table class="table table-bordered table-sm mb-0">
              <thead class="table-light">
                <tr><th>Field</th><th>Tipe</th><th>Wajib</th><th>Keterangan</th></tr>
              </thead>
              <tbody>
                <tr>
                  <td><code>order_id</code></td>
                  <td>string</td>
                  <td><span class="badge bg-success">Ya</span></td>
                  <td>ID order unik dari sistem Anda. Digunakan untuk idempotency — request ulang dengan <code>order_id</code> yang sama akan mengembalikan voucher yang sama.</td>
                </tr>
                <tr>
                  <td><code>item_code</code></td>
                  <td>string</td>
                  <td><span class="badge bg-success">Ya</span></td>
                  <td>Kode item/SKU yang ingin dirilis vouchernya. Contoh: <code>RF25000</code>, <code>RF50000</code>, <code>RF100000</code>.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <pre class="p-3 rounded mb-4" style="background:#1e1e1e;color:#d4d4d4;font-size:.85rem">{
  "order_id":  "ORD-20260625-00123",
  "item_code": "RF25000"
}</pre>

          <!-- Response sukses -->
          <h6 class="fw-semibold mb-2">Response Sukses <span class="badge bg-success ms-1">200 OK</span></h6>
          <pre class="p-3 rounded mb-0" style="background:#1e1e1e;color:#d4d4d4;font-size:.85rem">{
  "success": true,
  "message": "Voucher released successfully",
  "data": {
    "order_id":      "ORD-20260625-00123",
    "item_code":     "RF25000",
    "item_name":     "RF Return 25.000",
    "product_code":  "RF",
    "voucher_code":  "RF-AAAA-1111-BBBB",
    "serial_number": "SN-RF-001",
    "expired_date":  "2026-12-31"
  }
}</pre>
        </div>
      </div>
    </section>

    <!-- Format Response -->
    <section id="response-format" class="mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold border-bottom">Format Response</div>
        <div class="card-body">
          <p class="mb-3">Semua response menggunakan struktur yang sama:</p>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="fw-medium small mb-1 text-success">Sukses</div>
              <pre class="p-3 rounded mb-0" style="background:#1e1e1e;color:#d4d4d4;font-size:.82rem">{
  "success": true,
  "message": "...",
  "data":    { ... }
}</pre>
            </div>
            <div class="col-md-6">
              <div class="fw-medium small mb-1 text-danger">Error</div>
              <pre class="p-3 rounded mb-0" style="background:#1e1e1e;color:#d4d4d4;font-size:.82rem">{
  "success":    false,
  "message":    "...",
  "error_code": "..."
}</pre>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Error Codes -->
    <section id="error-codes" class="mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold border-bottom">Error Codes</div>
        <div class="card-body p-0">
          <table class="table table-hover table-striped mb-0">
            <thead class="table-light">
              <tr>
                <th>HTTP Status</th>
                <th>error_code</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><span class="badge bg-secondary">400</span> Bad Request</td>
                <td><code>VALIDATION_ERROR</code></td>
                <td>Body JSON tidak valid, atau field <code>order_id</code> / <code>item_code</code> kosong</td>
              </tr>
              <tr>
                <td><span class="badge bg-danger">401</span> Unauthorized</td>
                <td><code>INVALID_API_KEY</code></td>
                <td>Header <code>X-API-Key</code> tidak ada, salah, atau API key nonaktif</td>
              </tr>
              <tr>
                <td><span class="badge bg-warning text-dark">404</span> Not Found</td>
                <td><code>INVALID_PRODUCT</code></td>
                <td><code>item_code</code> tidak ditemukan atau item nonaktif</td>
              </tr>
              <tr>
                <td><span class="badge bg-warning text-dark">405</span> Method Not Allowed</td>
                <td><code>METHOD_NOT_ALLOWED</code></td>
                <td>Request bukan <code>POST</code></td>
              </tr>
              <tr>
                <td><span class="badge bg-info text-dark">422</span> Unprocessable</td>
                <td><code>OUT_OF_STOCK</code></td>
                <td>Tidak ada voucher <code>available</code> untuk item tersebut</td>
              </tr>
              <tr>
                <td><span class="badge bg-dark">500</span> Server Error</td>
                <td><code>SERVER_ERROR</code></td>
                <td>Kesalahan internal server</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- Contoh cURL -->
    <section id="curl-example" class="mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold border-bottom">Contoh cURL</div>
        <div class="card-body">

          <div class="fw-medium small mb-2">Request</div>
          <pre class="p-3 rounded mb-4" style="background:#1e1e1e;color:#d4d4d4;font-size:.82rem">curl -X POST <?= esc($api_base) ?>/release-voucher \
  -H "Content-Type: application/json" \
  -H "X-API-Key: {api_key_anda}" \
  -d '{
    "order_id":  "ORD-20260625-00123",
    "item_code": "RF25000"
  }'</pre>

          <div class="fw-medium small mb-2">Response <span class="text-success">(200)</span></div>
          <pre class="p-3 rounded mb-4" style="background:#1e1e1e;color:#d4d4d4;font-size:.82rem">{
  "success": true,
  "message": "Voucher released successfully",
  "data": {
    "order_id":      "ORD-20260625-00123",
    "item_code":     "RF25000",
    "item_name":     "RF Return 25.000",
    "product_code":  "RF",
    "voucher_code":  "RF-AAAA-1111-BBBB",
    "serial_number": "SN-RF-001",
    "expired_date":  "2026-12-31"
  }
}</pre>

          <div class="fw-medium small mb-2">Response <span class="text-danger">(422 — Stok habis)</span></div>
          <pre class="p-3 rounded mb-0" style="background:#1e1e1e;color:#d4d4d4;font-size:.82rem">{
  "success":    false,
  "message":    "Insufficient stock",
  "error_code": "OUT_OF_STOCK"
}</pre>
        </div>
      </div>
    </section>

  </div>
</div>
