<?php
// Konfigurasi Midtrans Manual tanpa Composer
require_once '../../Midtrans/Config.php';
require_once '../../Midtrans/Snap.php';
require_once '../../Midtrans/Transaction.php';
require_once '../../Midtrans/Sanitizer.php';        // WAJIB
require_once '../../Midtrans/ApiRequestor.php';     // WAJIB
require_once '../../Midtrans/CoreApi.php';          // Jika pakai fitur pembayaran langsung
require_once '../../Midtrans/Notification.php';     // Jika pakai webhook

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-89AMnftPmyi6SiLBqK93YO-Q';
\Midtrans\Config::$isProduction = false;   // Ubah ke true saat go live
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;
?>
