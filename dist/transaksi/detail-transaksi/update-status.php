<?php
include '../../../config/database.php';

// Logging buat debug kalau perlu
file_put_contents('log.txt', json_encode($_POST));

$id_detail_transaksi = $_POST['id_detail_transaksi'] ?? '';
$status = $_POST['status'] ?? '';

if (!is_numeric($id_detail_transaksi) || !in_array($status, ['0','1','2','3','4'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Parameter tidak valid']);
    exit;
}

$id_detail_transaksi = intval($id_detail_transaksi);
$status = mysqli_real_escape_string($kon, $status);

$query = "UPDATE detail_transaksi SET status = '$status' WHERE id_detail_transaksi = '$id_detail_transaksi'";
$result = mysqli_query($kon, $query);

if ($result) {
    if (mysqli_affected_rows($kon) > 0) {
        echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Tidak ada perubahan (status mungkin sudah sama)']);
    }
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => mysqli_error($kon)]);
}
?>
