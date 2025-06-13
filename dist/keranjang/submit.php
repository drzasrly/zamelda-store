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
$query = mysqli_query($kon, "SELECT MAX(idTransaksi) as idTerbesar FROM transaksi");
$data = mysqli_fetch_array($query);
$idBaru = $data['idTerbesar'] + 1;
$kodeTransaksi = sprintf("%03s", $idBaru);

$tanggal = date('Y-m-d');
$kodePelanggan = $_SESSION['kodePengguna'];

// Simpan transaksi utama
$simpan_transaksi = mysqli_query($kon, "INSERT INTO transaksi (kodeTransaksi, kodePelanggan, tanggal) VALUES ('$kodeTransaksi','$kodePelanggan','$tanggal')");

$sukses_detail = true;

foreach ($_POST['pilih'] as $idVarian) {
    $jumlah = isset($_POST['jumlah'][$idVarian]) ? intval($_POST['jumlah'][$idVarian]) : 0;
    $harga = isset($_POST['harga'][$idVarian]) ? floatval($_POST['harga'][$idVarian]) : 0;

    if ($jumlah <= 0 || $harga <= 0) {
        $sukses_detail = false;
        break;
    }

    // Ambil kodeBarang dari varianBarang
    $result = mysqli_query($kon, "SELECT kodeBarang FROM varianBarang WHERE idVarian='$idVarian'");
    $row = mysqli_fetch_assoc($result);
    $kodeBarang = $row ? $row['kodeBarang'] : '';

    if (empty($kodeBarang)) {
        $sukses_detail = false;
        break;
    }

    $tglTransaksi = date('Y-m-d H:i:s');

    $simpan_detail = mysqli_query($kon, "
        INSERT INTO detail_transaksi 
        (kodeTransaksi, kodeBarang, idVarian, jumlah, harga, tglTransaksi, status)
        VALUES 
        ('$kodeTransaksi', '$kodeBarang', '$idVarian', '$jumlah', '$harga', '$tglTransaksi', 'belum dibayar')
    ");

    if (!$simpan_detail) {
        $sukses_detail = false;
        echo "<p style='color:red;'>".mysqli_error($kon)."</p>"; // tampilkan error SQL
        break;
    }

    // Hapus dari keranjang database dan session
    mysqli_query($kon, "DELETE FROM keranjang WHERE idPengguna='{$_SESSION['idPengguna']}' AND idVarian='$idVarian'");
    unset($_SESSION['cart_barang'][$idVarian]);
}

if ($simpan_transaksi && $sukses_detail) {
    mysqli_query($kon, "COMMIT");
    echo "<div style='text-align:center; padding:50px;'>
        <h3>Transaksi berhasil disimpan!</h3>
        <a href='../../transaksi/detail-transaksi/index.php?kodeTransaksi=$kodeTransaksi' class='btn btn-success'>Lihat Detail Transaksi</a>
        <a href='../../index.php?page=transaksi' class='btn btn-secondary'>Kembali ke Daftar Transaksi</a>
    </div>";
} else {
    mysqli_query($kon, "ROLLBACK");
    echo "<div style='text-align:center; padding:50px;'>
        <h3>Transaksi gagal disimpan.</h3>
        <a href='../index.php?page=keranjang' class='btn btn-danger'>Kembali ke Keranjang</a>
    </div>";
}
?>
