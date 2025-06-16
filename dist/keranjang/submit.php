<?php
session_start();
include '../../config/database.php';
require_once '../../config/midtrans_config.php';

if (!isset($_SESSION['kodePengguna'])) {
    header('Location: ../login.php');
    exit;
}

$kodePengguna = $_SESSION['kodePengguna'];
$idPengguna = $_SESSION['idPengguna'];

// Validasi input
if (!isset($_POST['pilih']) || empty($_POST['pilih']) || !isset($_POST['metode'])) {
    header("Location: ../index.php?page=keranjang&error=input_invalid");
    exit;
}

$metode = mysqli_real_escape_string($kon, $_POST['metode']);
$tanggal = date('Y-m-d H:i:s');

// Ambil data pelanggan lengkap
$pelanggan_query = mysqli_query($kon, "SELECT * FROM pelanggan WHERE kodePelanggan='$kodePengguna'");
if (mysqli_num_rows($pelanggan_query) == 0) {
    die("Data pelanggan tidak ditemukan.");
}
$pelanggan = mysqli_fetch_assoc($pelanggan_query);

$kodePelanggan = $pelanggan['kodePelanggan'];
$namaPelanggan = $pelanggan['namaPelanggan'];
$email = $pelanggan['email'];
$noTelp = $pelanggan['noTelp'];
$alamat = $pelanggan['alamat'];

// Generate kodeTransaksi unik
function generateKodeTransaksi($prefix = 'tr') {
    global $kon;
    $query = mysqli_query($kon, "SELECT MAX(idTransaksi) AS idTerbesar FROM transaksi");
    $data = mysqli_fetch_array($query);
    $idBaru = $data['idTerbesar'] + 1;
    return $prefix . sprintf("%03s", $idBaru);
}
$kodeTransaksi = generateKodeTransaksi();

mysqli_query($kon, "START TRANSACTION");

// Simpan transaksi ke database
$simpan_transaksi = mysqli_query($kon, "
    INSERT INTO transaksi (kodeTransaksi, kodePelanggan, tanggal, metode)
    VALUES ('$kodeTransaksi', '$kodePelanggan', '$tanggal', '$metode')
");

if (!$simpan_transaksi) {
    mysqli_query($kon, "ROLLBACK");
    die("Gagal menyimpan transaksi: " . mysqli_error($kon));
}

$total_harga = 0;
$item_details = [];

foreach ($_POST['pilih'] as $idVarian) {
    $idVarian = intval($idVarian);
    $query = mysqli_query($kon, "
        SELECT k.jumlah, v.harga, v.stok, v.kodeBarang, b.namaBarang
        FROM keranjang k
        JOIN varianBarang v ON k.idVarian = v.idVarian
        JOIN barang b ON v.kodeBarang = b.kodeBarang
        WHERE k.idPengguna='$idPengguna' AND k.idVarian='$idVarian'
    ");
    if (mysqli_num_rows($query) == 0) {
        mysqli_query($kon, "ROLLBACK");
        die("Barang dengan ID $idVarian tidak ditemukan di keranjang.");
    }

    $data = mysqli_fetch_assoc($query);
    $jumlah = $data['jumlah'];
    $harga = $data['harga'];
    $stok = $data['stok'];
    $kodeBarang = $data['kodeBarang'];
    $namaBarang = $data['namaBarang'];

    if ($jumlah > $stok) {
        mysqli_query($kon, "ROLLBACK");
        die("Stok tidak mencukupi untuk varian ID $idVarian.");
    }

    $simpan_detail = mysqli_query($kon, "
        INSERT INTO detail_transaksi (kodeTransaksi, kodeBarang, idVarian, jumlah, status)
        VALUES ('$kodeTransaksi', '$kodeBarang', '$idVarian', $jumlah, '1')
    ");
    if (!$simpan_detail) {
        mysqli_query($kon, "ROLLBACK");
        die("Gagal menyimpan detail transaksi.");
    }

    $update_stok = mysqli_query($kon, "
        UPDATE varianBarang SET stok = stok - $jumlah WHERE idVarian = '$idVarian'
    ");
    if (!$update_stok) {
        mysqli_query($kon, "ROLLBACK");
        die("Gagal memperbarui stok.");
    }

    mysqli_query($kon, "
        DELETE FROM keranjang WHERE idPengguna = '$idPengguna' AND idVarian = '$idVarian'
    ");

    $total_harga += $harga * $jumlah;

    $item_details[] = [
        'id' => $idVarian,
        'price' => $harga,
        'quantity' => $jumlah,
        'name' => $namaBarang
    ];
}

mysqli_query($kon, "COMMIT");

// MIDTRANS SNAP
if ($metode == '1') {
    $transaction = [
        'transaction_details' => [
            'order_id' => $kodeTransaksi,
            'gross_amount' => $total_harga
        ],
        'item_details' => $item_details,
        'customer_details' => [
            'first_name' => $namaPelanggan,
            'email' => $email,
            'phone' => $noTelp,
            'billing_address' => [
                'first_name' => $namaPelanggan,
                'email' => $email,
                'phone' => $noTelp,
                'address' => $alamat,
                'city' => 'Jakarta',
                'postal_code' => '12345',
                'country_code' => 'IDN'
            ],
            'shipping_address' => [
                'first_name' => $namaPelanggan,
                'email' => $email,
                'phone' => $noTelp,
                'address' => $alamat,
                'city' => 'Jakarta',
                'postal_code' => '12345',
                'country_code' => 'IDN'
            ]
        ]
    ];

    $snapToken = \Midtrans\Snap::getSnapToken($transaction);

    $_SESSION['snapToken'] = $snapToken;
    $_SESSION['kodeTransaksi'] = $kodeTransaksi;

    header("Location: bayar.php");
    exit;
} else {
    $_SESSION['kodeTransaksi'] = $kodeTransaksi;
    header("Location: ../transaksi-sukses.php");
    exit;
}
?>
