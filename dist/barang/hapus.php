<?php
session_start();
include '../../config/database.php';

mysqli_query($kon, "START TRANSACTION");

$idBarang = $_GET["idBarang"];
$gambarBarang = $_GET["gambarBarang"];

$result = mysqli_query($kon, "SELECT kodeBarang FROM barang WHERE idBarang='$idBarang'");
$data = mysqli_fetch_array($result);
$kodeBarang = $data['kodeBarang'];

$hapus_varian = mysqli_query($kon, "DELETE FROM varianbarang WHERE kodeBarang='$kodeBarang'");
$hapus_gambar_utama = mysqli_query($kon, "DELETE FROM gambarutama WHERE kodeBarang='$kodeBarang'");
$hapus_gambar_varian = mysqli_query($kon, "DELETE FROM gambarvarian WHERE kodeBarang='$kodeBarang'");
$hapus_barang = mysqli_query($kon, "DELETE FROM barang WHERE idBarang='$idBarang'");

if ($gambarBarang != 'gambar_default.png') {
    unlink("gambar/" . $gambarBarang);
}

if ($hapus_barang && $hapus_varian && $hapus_gambar_utama) {
    mysqli_query($kon, "COMMIT");
    header("Location:../../dist/index.php?page=barang&hapus=berhasil");
} else {
    mysqli_query($kon, "ROLLBACK");
    header("Location:../../dist/index.php?page=barang&hapus=gagal");
}
?>
