<?php
session_start();
include '../../config/database.php';

// Mulai transaksi database
mysqli_query($kon, "START TRANSACTION");

// Generate kode transaksi baru                               nnn                                     b bb v m                              c 
$query = mysqli_query($kon, "SELECT MAX(idTransaksi) AS idTerbesar FROM transaksi");
$data = mysqli_fetch_array($query);
$kodeTransaksi = $data['idTerbesar'] + 1;
$kodeTransaksi = 'tr'.sprintf("%03s", $kodeTransaksi);

// Ambil data utama
$kodePelanggan = isset($_GET['kodePelanggan']) ? $_GET['kodePelanggan'] : '';
$tanggal = date('Y-m-d');
$status = "1";

// Simpan ke tabel transaksi (dengan tanggal di sini saja)
$simpan_tabel_transaksi = mysqli_query($kon, "INSERT INTO transaksi (kodeTransaksi, kodePelanggan, tanggal) VALUES ('$kodeTransaksi','$kodePelanggan','$tanggal')");

// Inisialisasi status semua operasi detail
$semua_detail_berhasil = true;

if (!empty($_SESSION["cart_barang"])) {
    foreach ($_SESSION["cart_barang"] as $item) {
    $kodeBarang = $item['kodeBarang'];
    $status = "1";

    // Ambil idVarian dari tabel varianbarang pakai JOIN
    $queryVarian = mysqli_query($kon, "
        SELECT v.idVarian, v.stok 
        FROM varianbarang v
        INNER JOIN barang b ON v.kodeBarang = b.kodeBarang
        WHERE v.kodeBarang = '$kodeBarang' AND v.stok > 0 LIMIT 1
    ");

    if ($row = mysqli_fetch_array($queryVarian)) {
        $idVarian = $row['idVarian'];
        $stok_baru = $row['stok'] - 1;

        // Simpan detail transaksi dengan idVarian
       $simpan_detail = mysqli_query($kon, "INSERT INTO detail_transaksi 
    (kodeTransaksi, kodeBarang, status, tglTransaksi) 
    VALUES 
    ('$kodeTransaksi', '$kodeBarang', '$status', '$tanggal')");


        if (!$simpan_detail) {
            $semua_detail_berhasil = false;
            break;
        }

        // Update stok
        $update_stok = mysqli_query($kon, "
            UPDATE varianbarang SET stok = $stok_baru WHERE idVarian = '$idVarian'
        ");

        if (!$update_stok) {
            $semua_detail_berhasil = false;
            break;
        }
    } else {
        $semua_detail_berhasil = false;
        break;
    }
}


}

// Finalisasi transaksi
if ($simpan_tabel_transaksi && $semua_detail_berhasil) {
    mysqli_query($kon, "COMMIT");
    unset($_SESSION["cart_barang"]);
    header("Location:../index.php?page=daftar-transaksi&add=berhasil");
} else {
    mysqli_query($kon, "ROLLBACK");
    unset($_SESSION["cart_barang"]);
    header("Location:../index.php?page=daftar-transaksi&add=gagal");
}
?>
