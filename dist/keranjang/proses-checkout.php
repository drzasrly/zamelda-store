<?php
session_start();
include '../../config/database.php';

if (!isset($_POST['pilih']) || empty($_POST['pilih'])) {
    header("Location:../index.php?page=keranjang&error=tidak_ada_pilihan");
    exit;
}

$_SESSION['checkout'] = [
    'pilih' => $_POST['pilih'],
    'jumlah' => $_POST['jumlah'],
];

$pilih = $_POST['pilih'];
$jumlah = $_POST['jumlah'];
$kodePelanggan = $_SESSION['kodePengguna'];
$pelanggan = mysqli_fetch_assoc(mysqli_query($kon, "SELECT * FROM pelanggan WHERE kodePelanggan='$kodePelanggan'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Zamelda Store</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 1000px; margin: 30px auto; background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .section { margin-bottom: 30px; }
        .section-title { font-weight: bold; color:rgb(11, 72, 78); margin-bottom: 10px; }
        .alamat, .produk, .pembayaran, .total { border-top: 1px solid #eee; padding-top: 20px; }
        .alamat p, .produk-item, .total-baris { display: flex; justify-content: space-between; margin: 10px 0; }
        .produk-item img { width: 70px; height: auto; border: 1px solid #ddd; border-radius: 5px; margin-right: 10px; }
        .produk-detail { flex: 1; }
        .produk-info { display: flex; gap: 10px; }
        .highlight { color: rgb(11, 72, 78); font-weight: bold; }
        .select { width: 100%; padding: 10px; margin-top: 10px; }
        .btn-order { background-color: rgb(11, 72, 78); color: white; padding: 15px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; width: 100%; }
    </style>
</head>
<body>
<div class="container">
    <form action="submit.php" method="post">
        <!-- Alamat Pengiriman -->
        <div class="section alamat">
            <div class="section-title">Alamat Pengiriman</div>
            <p><strong><?= $pelanggan['namaPelanggan'] ?></strong> (<?= $pelanggan['noTelp'] ?>)</p>
            <p><?= $pelanggan['alamat'] ?></p>
        </div>

        <!-- Produk Dipesan -->
        <div class="section produk">
            <div class="section-title">Produk Dipesan</div>
            <?php
            $total = 0;
            foreach ($pilih as $idVarian):
                $jumlahBeli = intval($jumlah[$idVarian]);
                $q = mysqli_query($kon, "SELECT vb.*, b.namaBarang, gv.gambarVarian FROM varianBarang vb 
                                         JOIN barang b ON vb.kodeBarang = b.kodeBarang 
                                         LEFT JOIN gambarVarian gv ON vb.idGambarVarian = gv.idGambarVarian
                                         WHERE vb.idVarian='$idVarian'");
                $v = mysqli_fetch_assoc($q);
                if ($v['stok'] < $jumlahBeli) {
                    echo "<p class='highlight'>Stok tidak cukup untuk: {$v['namaBarang']} ({$v['stok']} tersedia)</p>";
                    continue;
                }
                $subtotal = $v['harga'] * $jumlahBeli;
                $total += $subtotal;
            ?>
            <div class="produk-item">
                <div class="produk-info">
                    <img src="../barang/gambar/<?= $v['gambarVarian'] ?>" alt="<?= $v['namaBarang'] ?>">
                    <div class="produk-detail">
                        <div><strong><?= $v['namaBarang'] ?></strong></div>
                        <div>Type: <?= $v['typeVarian'] ?> | Ukuran: <?= $v['size'] ?></div>
                        <div>Jumlah: <?= $jumlahBeli ?></div>
                    </div>
                </div>
                <div class="highlight">Rp<?= number_format($subtotal, 0, ',', '.') ?></div>
            </div>
            <input type="hidden" name="pilih[]" value="<?= $idVarian ?>">
            <input type="hidden" name="jumlah[<?= $idVarian ?>]" value="<?= $jumlahBeli ?>">
            <?php endforeach; ?>
        </div>

        <!-- Metode Pembayaran -->
        <div class="section pembayaran">
            <div class="section-title">Metode Pembayaran</div>
            <select name="metode" required>
                <option value="">-- Pilih Metode --</option>
                <option value="0">Bayar di Tempat (COD)</option>
                <option value="1">Transfer Bank</option>
                <option value="2">ShopeePay</option>
                <option value="3">Bayar di Alfamart</option>
                <option value="4">Bayar di Indomaret</option>
            </select>
            <p class="highlight">Pastikan Anda memilih metode pembayaran yang sesuai.</p>
        </div>

        <!-- Total Pembayaran -->
        <div class="section total">
            <div class="total-bar">
                <div class="total-bar">Subtotal Pesanan</div>
                <div class="highlight">Rp<?= number_format($total, 0, ',', '.') ?></div>
            </div>
            <div class="total-bar">
                <div class="total-bar">Biaya Pengiriman</div>
                <div>Rp0</div>
            </div>
            <div class="total-bar">
                <div class="total-bar">Total Pembayaran</div>
                <div class="highlight">Rp<?= number_format($total, 0, ',', '.') ?></div>
            </div>
        </div>

        <button type="submit" class="btn-order" style="background-color: #118C8C; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Buat Pesanan</button>
    </form>
</div>
</body>
</html>
