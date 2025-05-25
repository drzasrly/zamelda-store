<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['level']) || strtolower($_SESSION['level']) != 'penjual') {
    header("Location: ../../dist/index.php?page=login");
    exit;
}

if (isset($_POST['simpan_profil'])) {
    $idPengguna = intval($_POST['idPengguna']);
    $usernameBaru = mysqli_real_escape_string($kon, $_POST['username_baru']);
    $passwordBaru = $_POST['password'];

    $cekUser = mysqli_query($kon, "SELECT * FROM pengguna WHERE idPengguna = $idPengguna LIMIT 1");
    if (mysqli_num_rows($cekUser) == 0) {
        echo "User tidak ditemukan.";
        exit;
    }

    if (!empty($passwordBaru)) {
        $passwordHash = password_hash($passwordBaru, PASSWORD_DEFAULT);
        $queryUpdate = "UPDATE pengguna SET username = '$usernameBaru', password = '$passwordHash' WHERE idPengguna = $idPengguna";
    } else {
        $queryUpdate = "UPDATE pengguna SET username = '$usernameBaru' WHERE idPengguna = $idPengguna";
    }

    $update = mysqli_query($kon, $queryUpdate);

    if ($update) {
        $_SESSION['username'] = $usernameBaru;
        header("Location: setting-akun.php?pesan=berhasil");
        exit;
    } else {
        echo "Gagal update data: " . mysqli_error($kon);
    }
} else {
    echo "Akses tidak valid.";
}
?>
