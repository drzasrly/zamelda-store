<?php
session_start();
    include '../../config/database.php';
    mysqli_query($kon,"START TRANSACTION");

    $kodeTransaksi=$_GET['kodeTransaksi'];

    $hapus_detail_transaksi=mysqli_query($kon,"delete from detail_transaksi where kodeTransaksi='$kodeTransaksi'");
    $hapus_transaksi=mysqli_query($kon,"delete from transaksi where kodeTransaksi='$kodeTransaksi'");

    $waktu=date("Y-m-d H:i");
    $log_aktivitas="Hapus Transaksi Kode #$kodeTransaksi";
    $id_pengguna= $_SESSION["id_pengguna"];


    if ($hapus_transaksi && $hapus_detail_transaksi) {
        mysqli_query($kon,"COMMIT");
        header("Location:../../dist/index.php?page=daftar-transaksi&hapus=berhasil");
    }
    else {
        mysqli_query($kon,"ROLLBACK");
        header("Location:../../dist/index.php?page=daftar-transaksi&hapus=berhasil");

    }

?>