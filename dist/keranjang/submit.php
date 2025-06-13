<?php 
session_start();
include '../../config/database.php';

// Cek apakah ada item yang dipilih
if (!isset($_POST['pilih']) || empty($_POST['pilih'])) {
    header("Location:../index.php?page=keranjang&error=tidak_ada_pilihan");
    exit;
}

mysqli_query($kon, "START TRANSACTION");

// Buat kode transaksi baru
$query = mysqli_query($kon, "SELECT MAX(idTransaksi) as idTransaksi_terbesar FROM transaksi");
$data = mysqli_fetch_array($query);
$idTransaksi = $data['idTransaksi_terbesar'] + 1;
$kodeTransaksi = sprintf("%03s", $idTransaksi);

$tanggal = date('Y-m-d');
$kodePelanggan = $_SESSION['kodePengguna'];

$simpan_transaksi = mysqli_query($kon, "INSERT INTO transaksi (kodeTransaksi, kodePelanggan, tanggal) VALUES ('$kodeTransaksi','$kodePelanggan','$tanggal')");

// Flag untuk pengecekan detail simpan
$sukses_detail = true;

foreach ($_POST['pilih'] as $idVarian) {
    // Ambil jumlah dan harga dari POST
    $jumlah = isset($_POST['jumlah'][$idVarian]) ? intval($_POST['jumlah'][$idVarian]) : 0;
    $harga = isset($_POST['harga'][$idVarian]) ? floatval($_POST['harga'][$idVarian]) : 0;

    if ($jumlah <= 0 || $harga <= 0) {
        $sukses_detail = false;
        break; // Jika data tidak valid, keluar dari loop
    }

    $simpan_detail = mysqli_query($kon, "
        INSERT INTO detail_transaksi (kodeTransaksi, idVarian, jumlah, harga)
        VALUES ('$kodeTransaksi', '$idVarian', '$jumlah', '$harga')
    ");

    if (!$simpan_detail) $sukses_detail = false;

    // Hapus dari keranjang
    mysqli_query($kon, "DELETE FROM keranjang WHERE idPengguna='{$_SESSION['idPengguna']}' AND idVarian='$idVarian'");

    // Hapus dari session
    unset($_SESSION['cart_barang'][$idVarian]);
}

if ($simpan_transaksi && $sukses_detail) {
    mysqli_query($kon, "COMMIT");
    header("Location:../index.php?page=booking&kodetransaksi=$kodeTransaksi");
} else {
    mysqli_query($kon, "ROLLBACK");
    header("Location:../index.php?page=booking&add=gagal");
}
?>
