<?php
session_start();
include '../../config/database.php';

if (isset($_SESSION['kodeTransaksi'])) {
    $kodeTransaksi = mysqli_real_escape_string($kon, $_SESSION['kodeTransaksi']);
} else {
    header("Location: ../index.php");
    exit;
}


// Ambil data transaksi
$query = mysqli_query($kon, "
    SELECT t.kodeTransaksi, t.tanggal, t.metode, p.namaPelanggan
    FROM transaksi t
    JOIN pelanggan p ON t.kodePelanggan = p.kodePelanggan
    WHERE t.kodeTransaksi = '$kodeTransaksi'
");

if (!$query || mysqli_num_rows($query) == 0) {
    echo "<h2>Transaksi tidak ditemukan (kode: $kodeTransaksi)</h2>";
    exit;
}
$transaksi = mysqli_fetch_assoc($query);


// Ambil detail barang
$detail = mysqli_query($kon, "
    SELECT d.jumlah, d.status, b.namaBarang, vb.size, vb.typeVarian
    FROM detail_transaksi d
    JOIN varianbarang vb ON d.idVarian = vb.idVarian
    JOIN barang b ON vb.kodeBarang = b.kodeBarang
    WHERE d.kodeTransaksi = '$kodeTransaksi'
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Berhasil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="text-center mb-4">
        <h2 class="text-success">✅ Transaksi Berhasil!</h2>
        <p>Terima kasih, <?= htmlspecialchars($transaksi['namaPelanggan']) ?>. Pesanan Anda telah dicatat.</p>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">Ringkasan Transaksi</div>
        <div class="card-body">
            <p><strong>Kode Transaksi:</strong> <?= $transaksi['kodeTransaksi'] ?></p>
            <p><strong>Tanggal:</strong> <?= $transaksi['tanggal'] ?></p>
            <p><strong>Metode Pembayaran:</strong>
                <?php
                $metode = ['COD', 'Transfer Bank', 'E-Wallet', 'QRIS', 'Lainnya'];
                echo $metode[(int)$transaksi['metode']];
                ?>
            </p>

            <h5>Barang Dipesan:</h5>
            <ul class="list-group">
                <?php while ($row = mysqli_fetch_assoc($detail)): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($row['namaBarang']) ?>
                        (<?= $row['typeVarian'] ?> - <?= $row['size'] ?>)
                        x <?= $row['jumlah'] ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <div class="text-center">
        <a href="../index.php?page=transaksi-saya" class="btn btn-primary me-2">📦 Lihat Transaksi Saya</a>
        <a href="../index.php?page=barang" class="btn btn-outline-success">🛒 Lanjut Belanja</a>
    </div>
</div>
</body>
</html>
