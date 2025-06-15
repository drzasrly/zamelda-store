<?php 
session_start();
include '../../config/database.php';

if (!isset($_POST['pilih']) || empty($_POST['pilih']) || !isset($_POST['metode'])) {
    header("Location: ../index.php?page=keranjang&error=input_invalid");
    exit;
}

$metode = mysqli_real_escape_string($kon, $_POST['metode']);
$allowed_methods = ['0', '1', '2', '3', '4'];
if (!in_array($metode, $allowed_methods)) {
    die("Metode pembayaran tidak valid.");
}

$tanggal = date('Y-m-d');
$kodePelanggan = $_SESSION['kodePengguna'];

mysqli_query($kon, "START TRANSACTION");

// Generate kode transaksi
$q = mysqli_query($kon, "SELECT MAX(idTransaksi) as idTerbesar FROM transaksi");
$d = mysqli_fetch_assoc($q);
$idBaru = $d['idTerbesar'] + 1;
$kodeTransaksi = "tr" . sprintf("%03s", $idBaru);

// Simpan transaksi
$simpan_transaksi = mysqli_query($kon, 
    "INSERT INTO transaksi (kodeTransaksi, kodePelanggan, tanggal, idMetode) 
     VALUES ('$kodeTransaksi','$kodePelanggan','$tanggal','$metode')"
);

$sukses_detail = true;
$error_message = "";

foreach ($_POST['pilih'] as $idVarian) {
    $idVarian = intval($idVarian);
    $jumlah = intval($_POST['jumlah'][$idVarian]);

    $result = mysqli_query($kon, "SELECT kodeBarang, harga, stok FROM varianBarang WHERE idVarian='$idVarian'");
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
    // handle varian tidak ditemukan
}
$harga = $row['harga'];

    if (!$row) {
        $sukses_detail = false;
        $error_message = "❌ Varian dengan ID $idVarian tidak ditemukan.";
        break;
    }

    if ($jumlah > $row['stok']) {
        $sukses_detail = false;
        $error_message = "❌ Stok tidak cukup untuk barang {$row['kodeBarang']}. Sisa stok: {$row['stok']}, diminta: $jumlah.";
        break;
    }

    $kodeBarang = $row['kodeBarang'];
    $tglTransaksi = date('Y-m-d H:i:s');

    $simpan_detail = mysqli_query($kon, 
        "INSERT INTO detail_transaksi (kodeTransaksi, kodeBarang, idVarian, tglTransaksi, jumlah,harga, status) 
         VALUES ('$kodeTransaksi', '$kodeBarang', '$idVarian', '$tglTransaksi', '$jumlah','$harga', '1')"
    );

    $update_stok = mysqli_query($kon, 
        "UPDATE varianBarang SET stok = stok - $jumlah WHERE idVarian='$idVarian'"
    );

    if (!$simpan_detail || !$update_stok) {
        $sukses_detail = false;
        $error_message = "❌ Gagal menyimpan detail transaksi untuk barang {$row['kodeBarang']}.";
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
        <p>$error_message</p>
        <a href='../index.php?page=keranjang' style='padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px;'>Kembali ke Keranjang</a>
    </div>";
}
?>
