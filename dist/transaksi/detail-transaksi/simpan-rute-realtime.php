<?php
include '../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kodeTransaksi = mysqli_real_escape_string($kon, $_POST['kodeTransaksi']);
    $lat = mysqli_real_escape_string($kon, $_POST['lat']);
    $lng = mysqli_real_escape_string($kon, $_POST['lng']);

    // Misal, simpan log ke tabel `rute_pengiriman`
    $sql = "INSERT INTO tracking_realtime (kodeTransaksi, latitude, longitude, waktu) VALUES ('$kodeTransaksi', '$lat', '$lng', NOW())";

    if (mysqli_query($kon, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false]);
    }
}
?>
