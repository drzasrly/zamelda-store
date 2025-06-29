<?php
session_start();
include '../../config/database.php';
require_once '../../config/midtrans_config.php';

if (!isset($_SESSION['kodePengguna'])) {
    header("Location: ../login.php");
    exit;
}

$kodePengguna = $_SESSION['kodePengguna'];
$idPengguna = $_SESSION['idPengguna'];

if (!isset($_POST['pilih'], $_POST['jumlah'], $_POST['metode'], $_POST['ongkir'], $_POST['idAlamat'], $_POST['kurir'])) {
    header("Location: ../index.php?page=keranjang&error=input_invalid");
    exit;
}

$pilih     = $_POST['pilih'];
$jumlah    = $_POST['jumlah'];
$metode    = mysqli_real_escape_string($kon, $_POST['metode']);
$kurir     = mysqli_real_escape_string($kon, $_POST['kurir']);
$ongkir    = intval($_POST['ongkir']);
$idAlamat  = intval($_POST['idAlamat']);
$tanggal   = date('Y-m-d H:i:s');

// Ambil data pelanggan
$pelanggan = mysqli_fetch_assoc(mysqli_query($kon, "SELECT * FROM pelanggan WHERE kodePelanggan='$kodePengguna'"));
if (!$pelanggan) die("Data pelanggan tidak ditemukan.");

$kodePelanggan  = $pelanggan['kodePelanggan'];
$namaPelanggan  = $pelanggan['namaPelanggan'];
$email          = $pelanggan['email'];
$noTelp         = $pelanggan['noTelp'];
$alamat         = $pelanggan['alamat'];

// Validasi alamat
$cekAlamat = mysqli_query($kon, "SELECT * FROM alamat_pelanggan WHERE idAlamat='$idAlamat' AND kodePelanggan='$kodePelanggan'");
if (mysqli_num_rows($cekAlamat) == 0) {
    mysqli_query($kon, "ROLLBACK");
    die("Alamat tidak valid atau bukan milik Anda.");
}

// Generate kode transaksi unik
function generateKodeTransaksi($prefix = 'tr') {
    global $kon;
    $q = mysqli_query($kon, "SELECT MAX(idTransaksi) AS idTerbesar FROM transaksi");
    $d = mysqli_fetch_array($q);
    $idBaru = $d['idTerbesar'] + 1;
    return $prefix . sprintf("%03s", $idBaru);
}
$kodeTransaksi = generateKodeTransaksi();

// Simpan transaksi utama
mysqli_query($kon, "START TRANSACTION");

$simpan_transaksi = mysqli_query($kon, "
    INSERT INTO transaksi (kodeTransaksi, kodePelanggan, tanggal, metode, ongkir, kurir)
    VALUES ('$kodeTransaksi', '$kodePelanggan', '$tanggal', '$metode', '$ongkir', '$kurir')
");

if (!$simpan_transaksi) {
    mysqli_query($kon, "ROLLBACK");
    die("Gagal simpan transaksi: " . mysqli_error($kon));
}

$total_harga = 0;
$item_details = [];

foreach ($pilih as $idVarian) {
    $idVarian   = intval($idVarian);
    $jumlahBeli = intval($jumlah[$idVarian]);

    $q = mysqli_query($kon, "
        SELECT v.harga, v.stok, v.kodeBarang, b.namaBarang
        FROM varianBarang v
        JOIN barang b ON v.kodeBarang = b.kodeBarang
        WHERE v.idVarian='$idVarian'
    ");
    if (!$q || mysqli_num_rows($q) == 0) {
        mysqli_query($kon, "ROLLBACK");
        die("Varian tidak ditemukan: $idVarian");
    }

    $data = mysqli_fetch_assoc($q);
    if ($jumlahBeli > $data['stok']) {
        mysqli_query($kon, "ROLLBACK");
        die("Stok tidak cukup untuk {$data['namaBarang']}");
    }

    // Simpan detail transaksi
    $simpan_detail = mysqli_query($kon, "
        INSERT INTO detail_transaksi (kodeTransaksi, kodeBarang, idVarian, jumlah, status, idAlamat)
        VALUES ('$kodeTransaksi', '{$data['kodeBarang']}', '$idVarian', $jumlahBeli, '1', '$idAlamat')
    ");
    if (!$simpan_detail) {
        mysqli_query($kon, "ROLLBACK");
        die("Gagal simpan detail transaksi.");
    }

    // Kurangi stok
    $update_stok = mysqli_query($kon, "
        UPDATE varianBarang SET stok = stok - $jumlahBeli WHERE idVarian = '$idVarian'
    ");
    if (!$update_stok) {
        mysqli_query($kon, "ROLLBACK");
        die("Gagal update stok.");
    }

    // Hapus dari keranjang
    mysqli_query($kon, "
        DELETE FROM keranjang WHERE idPengguna='$idPengguna' AND idVarian='$idVarian'
    ");

    $total_harga += $data['harga'] * $jumlahBeli;

    $item_details[] = [
        'id'       => $idVarian,
        'price'    => $data['harga'],
        'quantity' => $jumlahBeli,
        'name'     => $data['namaBarang']
    ];
}

// Tambahkan ongkir ke Midtrans
if ($ongkir > 0) {
    $item_details[] = [
        'id'       => 'ONGKIR',
        'price'    => $ongkir,
        'quantity' => 1,
        'name'     => 'Ongkos Kirim - ' . strtoupper($kurir)
    ];
}

// Commit semua data
mysqli_query($kon, "COMMIT");

// Integrasi Midtrans jika metode transfer bank
if ($metode == '1') {
    $transaction = [
        'transaction_details' => [
            'order_id'     => $kodeTransaksi,
            'gross_amount' => $total_harga + $ongkir
        ],
        'item_details' => $item_details,
        'customer_details' => [
            'first_name' => $namaPelanggan,
            'email'      => $email,
            'phone'      => $noTelp,
            'billing_address' => [
                'first_name'   => $namaPelanggan,
                'address'      => $alamat,
                'city'         => 'Jakarta',
                'postal_code'  => '12345',
                'phone'        => $noTelp,
                'country_code' => 'IDN'
            ],
            'shipping_address' => [
                'first_name'   => $namaPelanggan,
                'address'      => $alamat,
                'city'         => 'Jakarta',
                'postal_code'  => '12345',
                'phone'        => $noTelp,
                'country_code' => 'IDN'
            ]
        ]
    ];

    try {
        $snapToken = \Midtrans\Snap::getSnapToken($transaction);
        $_SESSION['snapToken'] = $snapToken;
        $_SESSION['kodeTransaksi'] = $kodeTransaksi;
        header("Location: bayar.php");
        exit;
    } catch (Exception $e) {
        die("Gagal memproses Midtrans: " . $e->getMessage());
    }
} else {
    $_SESSION['kodeTransaksi'] = $kodeTransaksi;
    header("Location: ../transaksi-berhasil.php");
    exit;
}
