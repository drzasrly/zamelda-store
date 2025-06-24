<?php
require_once 'midtrans/midtrans.php'; // ini akan memuat konfigurasi Midtrans

// Aktifkan error display untuk debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Pastikan data POST diterima
if (!isset($_POST['total_barang']) || !isset($_POST['ongkir'])) {
    echo json_encode(['error' => 'Data tidak lengkap']);
    exit;
}

$order_id = "ORDER-" . time();
$total = intval($_POST['total_barang']) + intval($_POST['ongkir']);

$transaction = [
    'transaction_details' => [
        'order_id' => $order_id,
        'gross_amount' => $total,
    ],
    'customer_details' => [
        'first_name' => "Nama Pelanggan", // Optional: bisa ambil dari session
        'email' => "email@contoh.com",
        'phone' => "08123456789"
    ]
];

try {
    $snapToken = \Midtrans\Snap::getSnapToken($transaction);
    echo json_encode(['token' => $snapToken]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
