<?php 
session_start();
include '../../config/database.php';

if (!isset($_POST['pilih']) || empty($_POST['pilih']) || !isset($_POST['metode'])) {
    header("Location:../index.php?page=keranjang&error=input_invalid");
    exit;
}

$metode = mysqli_real_escape_string($kon, $_POST['metode']);
$tanggal = date('Y-m-d');
$kodePelanggan = $_SESSION['kodePengguna'];

$metode = $_POST['metode'];
$allowed_methods = ['0', '1', '2', '3', '4'];
if (!in_array($metode, $allowed_methods)) {
    die("Metode pembayaran tidak valid.");
}


mysqli_query($kon, "START TRANSACTION");

$q = mysqli_query($kon, "SELECT MAX(idTransaksi) as idTerbesar FROM transaksi");
$d = mysqli_fetch_assoc($q);
$idBaru = $d['idTerbesar'] + 1;
$kodeTransaksi = "tr" . sprintf("%03s", $idBaru);

$simpan_transaksi = mysqli_query($kon, 
    "INSERT INTO transaksi (kodeTransaksi, kodePelanggan, tanggal, metode) 
     VALUES ('$kodeTransaksi','$kodePelanggan','$tanggal','$metode')"
);

$sukses_detail = true;

foreach ($_POST['pilih'] as $idVarian) {
    $jumlah = intval($_POST['jumlah'][$idVarian]);

    $result = mysqli_query($kon, "SELECT kodeBarang, harga, stok FROM varianBarang WHERE idVarian='$idVarian'");
    $row = mysqli_fetch_assoc($result);

    if (!$row || $jumlah > $row['stok']) {
        $sukses_detail = false;
        break;
    }

    $kodeBarang = $row['kodeBarang'];
    $tglTransaksi = date('Y-m-d H:i:s');

    $simpan_detail = mysqli_query($kon, 
        "INSERT INTO detail_transaksi (kodeTransaksi, kodeBarang, idVarian, tglTransaksi, jumlah, status) 
         VALUES ('$kodeTransaksi', '$kodeBarang', '$idVarian', '$tglTransaksi', $jumlah, '1')"
    );

    $update_stok = mysqli_query($kon, "UPDATE varianBarang SET stok = stok - $jumlah WHERE idVarian='$idVarian'");

    if (!$simpan_detail || !$update_stok) {
        $sukses_detail = false;
        break;
    }

    unset($_SESSION['cart_barang'][$idVarian]);
}

if ($simpan_transaksi && $sukses_detail) {
    mysqli_query($kon, "COMMIT");
    unset($_SESSION['checkout']);
    echo "<div style='text-align:center; padding: 50px;'>
        <h2 style='color: green;'>🎉 Transaksi Berhasil!</h2>
        <p>Kode Transaksi: <strong>$kodeTransaksi</strong></p>
        <a href='../index.php?page=transaksi-saya' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Lihat Transaksi Saya</a>
    </div>";
} else {
    mysqli_query($kon, "ROLLBACK");
    echo "<div style='text-align:center; padding: 50px;'>
        <h2 style='color: red;'>❌ Transaksi Gagal</h2>
        <p>Terjadi kesalahan saat menyimpan data transaksi.</p>
        <a href='../index.php?page=keranjang' class='btn btn-danger'>Kembali ke Keranjang</a>
    </div>";
}
?>
