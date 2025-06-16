<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Gagal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="text-center mb-4">
        <h2 class="text-danger">❌ Transaksi Gagal</h2>
        <p class="text-muted">Maaf, pembayaran Anda tidak berhasil diproses.</p>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-danger text-white">Informasi Transaksi</div>
        <div class="card-body">
            <p><strong>Penyebab Umum:</strong></p>
            <ul>
                <li>Pembayaran dibatalkan oleh pengguna</li>
                <li>Koneksi internet terputus saat proses pembayaran</li>
                <li>Saldo tidak mencukupi atau masalah bank</li>
            </ul>
            <p>Silakan coba lagi atau gunakan metode pembayaran lain.</p>
        </div>
    </div>

    <div class="text-center">
        <a href="../index.php?page=keranjang" class="btn btn-warning me-2">🔁 Coba Lagi</a>
        <a href="../index.php" class="btn btn-outline-secondary">🏠 Kembali ke Beranda</a>
    </div>
</div>
</body>
</html>
