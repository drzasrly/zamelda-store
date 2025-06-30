<?php
include '../../../config/database.php';
file_put_contents('log.txt', json_encode($_POST));


if (isset($_POST['kodeTransaksi']) && isset($_POST['status'])) {
    $kodeTransaksi = mysqli_real_escape_string($kon, $_POST['kodeTransaksi']);
    $status = intval($_POST['status']);

    $query = "UPDATE detail_transaksi SET status = $status WHERE kodeTransaksi = '$kodeTransaksi'";
    if (mysqli_query($kon, $query)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => mysqli_error($kon)]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Parameter tidak lengkap']);
}
?>
