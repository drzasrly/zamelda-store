<?php
// Pastikan autoload Composer dipanggil (jika pakai Composer)
require_once __DIR__ . '/../vendor/autoload.php'; // sesuaikan jika berbeda

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-89AMnftPmyi6SiLBqK93YO-Q';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;
