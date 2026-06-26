# BankCode — Voucher Management System

Sistem manajemen dan distribusi voucher game berbasis CodeIgniter 3. Mendukung hierarki **Produk → Item/SKU → Voucher** dengan REST API untuk integrasi mitra eksternal.

---

## Fitur

- **Admin Panel** — CRUD produk, item/SKU, dan voucher dengan pagination & filter
- **Bulk Import** — Upload voucher via CSV
- **REST API v1** — Release voucher secara atomik (tidak ada duplikasi)
- **Idempotency** — Request ulang dengan `order_id` yang sama mengembalikan voucher yang sama
- **API Key** — Autentikasi per mitra dengan SHA256 key
- **API Docs** — Halaman dokumentasi endpoint tersedia di `/admin/api-docs`

---

## Tech Stack

- PHP 8.1 + CodeIgniter 3
- MySQL 8.x
- Bootstrap 5 + Bootstrap Icons
- Apache (Laragon / production server)

---

## Instalasi

### 1. Clone repository

```bash
git clone https://github.com/hastalavista911/bankcode-voucher.git
cd bankcode-voucher
```

### 2. Konfigurasi environment

```bash
cp application/config/config.example.php application/config/config.php
cp application/config/database.example.php application/config/database.php
```

Edit `application/config/config.php`:
```php
$config['base_url']       = 'https://yourdomain.com/';
$config['encryption_key'] = 'isi-dengan-random-string-32-karakter';
```

Edit `application/config/database.php`:
```php
'hostname' => 'localhost',
'username' => 'YOUR_DB_USERNAME',
'password' => 'YOUR_DB_PASSWORD',
'database' => 'YOUR_DB_NAME',
```

### 3. Import database

```bash
# Buat database terlebih dahulu, lalu:
mysql -u root -p YOUR_DB_NAME < database/schema.sql
mysql -u root -p YOUR_DB_NAME < database/seed.sql
```

### 4. Konfigurasi virtual host Apache

```apache
<VirtualHost *:80>
    DocumentRoot "/path/to/bankcode-voucher"
    ServerName yourdomain.com
    <Directory "/path/to/bankcode-voucher">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## Login Default

| Field    | Value      |
|----------|------------|
| Username | `admin`    |
| Password | `admin123` |

> Ganti password setelah login pertama.

---

## Struktur Hierarki

```
Produk (platform/game)
└── Item / SKU (nominal / paket)
    └── Voucher (kode unik yang dirilis ke pembeli)
```

Contoh:
```
RF-Return
└── RF Return 25.000 (RF25000)
    ├── RF-AAAA-1111-BBBB
    ├── RF-CCCC-2222-DDDD
    └── RF-EEEE-3333-FFFF
```

---

## REST API

### Endpoint

```
POST /api/v1/release-voucher
```

### Headers

```
Content-Type: application/json
X-API-Key: {api_key}
```

### Request Body

```json
{
  "order_id":  "ORD-20260625-00123",
  "item_code": "RF25000"
}
```

### Response Sukses (200)

```json
{
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
}
```

### Error Codes

| HTTP | error_code          | Keterangan                        |
|------|---------------------|-----------------------------------|
| 400  | VALIDATION_ERROR    | Field wajib kosong / JSON invalid |
| 401  | INVALID_API_KEY     | API key salah atau tidak aktif    |
| 404  | INVALID_PRODUCT     | item_code tidak ditemukan         |
| 405  | METHOD_NOT_ALLOWED  | Request bukan POST                |
| 422  | OUT_OF_STOCK        | Stok voucher habis                |
| 500  | SERVER_ERROR        | Kesalahan internal server         |

Dokumentasi lengkap tersedia di `/admin/api-docs` setelah login.

---

## Struktur Direktori

```
application/
├── config/
│   ├── config.example.php      # Template konfigurasi (salin ke config.php)
│   ├── database.example.php    # Template database (salin ke database.php)
│   └── routes.php
├── controllers/
│   ├── admin/                  # Auth, Dashboard, Products, Items, Vouchers, Apidocs
│   └── api/V1.php              # REST API
├── models/                     # Admin, Apikey, Item, Product, Releaselog, Voucher
├── views/admin/                # Layout, auth, dashboard, products, items, vouchers
└── core/MY_Controller.php      # Base controller dengan session guard
```

---

## Lisensi

MIT
