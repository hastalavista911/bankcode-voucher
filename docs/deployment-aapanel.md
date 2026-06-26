# Panduan Deploy ke aaPanel

Dokumen ini menjelaskan langkah-langkah deploy aplikasi **BankCode Voucher Management** ke server berbasis aaPanel (Linux + Nginx + MySQL + PHP 8.1).

---

## Prasyarat Server

| Komponen | Versi Minimum |
|----------|--------------|
| PHP      | 8.1          |
| MySQL    | 8.0          |
| Nginx    | 1.18         |
| aaPanel  | 7.x          |

PHP Extensions yang wajib aktif:

- `mysqli`
- `mbstring`
- `json`
- `openssl`
- `fileinfo`
- `ctype`

---

## 1. Persiapan di aaPanel

### 1.1 Install Software

Di aaPanel → **App Store**, pastikan sudah terinstall:

- **Nginx** (bukan Apache)
- **MySQL 8.0**
- **PHP 8.1** + ekstensi di atas

### 1.2 Buat Database

1. aaPanel → **Database** → **Add Database**
2. Isi:
   - **Database Name**: `bankcode` (atau nama pilihan)
   - **Username**: `bankcode_user`
   - **Password**: buat password kuat
3. Catat ketiga nilai di atas — dibutuhkan di langkah konfigurasi.

### 1.3 Buat Website / Virtual Host

1. aaPanel → **Website** → **Add Site**
2. Isi:
   - **Domain**: `yourdomain.com`
   - **Root Directory**: `/www/wwwroot/bankcode` (aaPanel akan membuat folder ini)
   - **PHP Version**: 8.1
   - **Database**: pilih database yang baru dibuat
3. Aktifkan **SSL** (Let's Encrypt) jika domain sudah pointing ke server.

---

## 2. Clone Repository

Login ke server via SSH, lalu:

```bash
cd /www/wwwroot
rm -rf bankcode          # hapus folder kosong yang dibuat aaPanel
git clone https://github.com/hastalavista911/bankcode-voucher.git bankcode
cd bankcode
```

---

## 3. Konfigurasi Environment

### 3.1 config.php

```bash
cp application/config/config.example.php application/config/config.php
nano application/config/config.php
```

Ubah nilai berikut:

```php
$config['base_url']       = 'https://yourdomain.com/';
$config['encryption_key'] = 'ISI_RANDOM_STRING_32_KARAKTER_UNIK';
```

> Generate encryption key: `openssl rand -base64 32`

### 3.2 database.php

```bash
cp application/config/database.example.php application/config/database.php
nano application/config/database.php
```

Ubah nilai berikut:

```php
'hostname' => 'localhost',
'username' => 'bankcode_user',
'password' => 'PASSWORD_DB_ANDA',
'database' => 'bankcode',
```

---

## 4. Import Database

```bash
mysql -u bankcode_user -p bankcode < database/schema.sql
mysql -u bankcode_user -p bankcode < database/seed.sql
```

> `seed.sql` berisi data awal: akun admin default dan API key contoh.

Login default setelah import:

| Field    | Value      |
|----------|------------|
| Username | `admin`    |
| Password | `admin123` |

**Ganti password segera setelah login pertama.**

---

## 5. Konfigurasi Nginx

Di aaPanel → **Website** → klik nama domain → **Config** → tab **Nginx Config**.

Ganti isi config dengan berikut (sesuaikan `root` dan `server_name`):

```nginx
server {
    listen 80;
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    root /www/wwwroot/bankcode;
    index index.php index.html;

    # SSL — diisi otomatis oleh aaPanel jika pakai Let's Encrypt
    # ssl_certificate     ...;
    # ssl_certificate_key ...;

    # Redirect HTTP ke HTTPS
    if ($scheme = http) {
        return 301 https://$host$request_uri;
    }

    # CodeIgniter — semua request diarahkan ke index.php
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass   unix:/tmp/php-cgi-81.sock;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }

    # Blokir akses langsung ke folder sensitif
    location ~ ^/(application|system|database|docs|instruction)/ {
        deny all;
        return 403;
    }

    # Blokir file tersembunyi (.git, .env, dll)
    location ~ /\. {
        deny all;
    }

    access_log  /www/wwwlogs/bankcode.access.log;
    error_log   /www/wwwlogs/bankcode.error.log;
}
```

Klik **Save** lalu **Reload Nginx**.

> Sesuaikan path socket PHP (`/tmp/php-cgi-81.sock`) — cek di aaPanel → **PHP** → versi yang dipakai → path socket ditampilkan di halaman tersebut.

---

## 6. Permission Folder

```bash
cd /www/wwwroot/bankcode

# Ownership ke user Nginx
chown -R www:www .

# Folder yang perlu writable
chmod -R 755 application/cache
chmod -R 755 application/logs
```

---

## 7. Verifikasi Deployment

Buka browser dan akses:

| URL | Ekspektasi |
|-----|-----------|
| `https://yourdomain.com/` | Redirect ke halaman login |
| `https://yourdomain.com/admin/login` | Form login admin |
| `https://yourdomain.com/admin/dashboard` | Dashboard (setelah login) |
| `https://yourdomain.com/api/v1/release-voucher` | `405 Method Not Allowed` (karena GET, bukan POST) |

Jika muncul **404** atau **500**, cek:

```bash
tail -50 /www/wwwlogs/bankcode.error.log
tail -50 /www/wwwroot/bankcode/application/logs/log-$(date +%Y-%m-%d).php
```

---

## 8. Update Aplikasi (Deploy Ulang)

Jika ada commit baru di GitHub:

```bash
cd /www/wwwroot/bankcode
git pull origin main
```

Jika ada perubahan skema database, jalankan migration manual (file SQL ada di folder `database/`).

> File `application/config/config.php` dan `database.php` **tidak ter-overwrite** oleh `git pull` karena ada di `.gitignore`.

---

## 9. Checklist Sebelum Go-Live

- [ ] `base_url` sudah diset ke domain production
- [ ] `encryption_key` sudah diisi string acak (bukan placeholder)
- [ ] Kredensial database sudah benar
- [ ] Schema + seed SQL sudah diimport
- [ ] Nginx config sudah direload
- [ ] SSL aktif (HTTPS berjalan)
- [ ] Login admin berhasil
- [ ] Password admin sudah diganti dari default
- [ ] Akses ke `/application`, `/system`, `/database` diblokir (return 403)
- [ ] API endpoint merespons dengan benar (test dengan Postman / curl)
- [ ] Log error tidak menampilkan pesan kritis

---

## 10. Integrasi Mitra (BankCode API)

Setelah deploy, tambahkan API key untuk mitra di tabel `api_keys`:

```sql
INSERT INTO api_keys (mitra_name, api_key, is_active, created_at)
VALUES ('Nama Mitra', SHA2('kunci-rahasia-mitra', 256), 1, NOW());
```

Mitra menggunakan header `X-API-Key: kunci-rahasia-mitra` (bukan hasil hash-nya).

Dokumentasi API lengkap: `https://yourdomain.com/admin/api-docs`
