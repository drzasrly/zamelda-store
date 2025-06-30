
<?php
session_start();

header('Content-Type: application/json');
include '../../config/database.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['idPengguna']) || $_SESSION['level'] !== 'Pelanggan') {
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login sebagai pelanggan.']);
    exit;
}

$idVarian = $_POST['idVarian'] ?? null;
$kodeBarang = $_POST['kodeBarang'] ?? null;

if (!$idVarian || !$kodeBarang) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak lengkap.'
    ]);
    exit;
}

$query = mysqli_query($kon, "SELECT * FROM varianbarang WHERE idVarian = '$idVarian'");
$varian = mysqli_fetch_assoc($query);

if (!$varian) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Varian tidak ditemukan.'
    ]);
    exit;
}

if ($varian['stok'] <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Stok habis.'
    ]);
    exit;
}

$idPengguna = $_SESSION['idPengguna'];
$cek = mysqli_query($kon, "SELECT * FROM keranjang WHERE idPengguna = '$idPengguna' AND idVarian = '$idVarian'");
if (mysqli_num_rows($cek) > 0) {
    mysqli_query($kon, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE idPengguna = '$idPengguna' AND idVarian = '$idVarian'");
} else {
    mysqli_query($kon, "INSERT INTO keranjang (idPengguna, idVarian, jumlah) VALUES ('$idPengguna', '$idVarian', 1)");
}

echo json_encode([
    'status' => 'success',
    'message' => 'Berhasil ditambahkan ke keranjang.'
]);
exit;

