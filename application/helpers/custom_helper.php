<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Fungsi esc() tersedia di CI4 tapi tidak di CI3 — didefinisikan di sini
if (!function_exists('esc')) {
    function esc($str) {
        return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Generate kode voucher acak.
 *
 * @param  string $kode_produk  Prefix/kode produk, contoh: 'RF'
 * @param  string $pattern      Pola karakter: A = huruf acak, 9 = angka acak,
 *                              karakter lain diteruskan apa adanya.
 *                              Default: 'AAAA-9999-AAAA'
 * @return string  Contoh: RF-KXPD-4821-WZNQ
 */
if (!function_exists('generate_voucher_code')) {
    function generate_voucher_code($kode_produk, $pattern = 'AAAA-9999-AAAA') {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result  = '';
        $len     = strlen($pattern);

        for ($i = 0; $i < $len; $i++) {
            $c = $pattern[$i];
            if ($c === 'A') {
                $result .= $letters[random_int(0, 25)];
            } elseif ($c === '9') {
                $result .= (string) random_int(0, 9);
            } else {
                $result .= $c;
            }
        }

        $prefix = strtoupper(trim($kode_produk));
        return $prefix !== '' ? $prefix . '-' . $result : $result;
    }
}
