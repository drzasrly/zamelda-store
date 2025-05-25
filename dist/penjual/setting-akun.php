<?php
session_start();
include '../../config/database.php';

// cek login & ambil data pengguna
if (!isset($_SESSION['level']) || strtolower($_SESSION['level']) != 'penjual') {
    header("Location: ../../dist/index.php?page=login");
    exit;
}

$idPengguna = $_SESSION['idPengguna'] ?? 0;

$query = "SELECT * FROM pengguna WHERE idPengguna = $idPengguna LIMIT 1";
$result = mysqli_query($kon, $query);
$data = mysqli_fetch_assoc($result);
?>

<form method="POST" action="proses_update.php">
    <input type="hidden" name="idPengguna" value="<?= htmlspecialchars($data['idPengguna']) ?>">
    
    <label>Username</label><br>
    <input type="text" name="username_baru" value="<?= htmlspecialchars($data['username']) ?>" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin ganti"><br><br>

    <button type="submit" name="simpan_profil">Simpan</button>
</form>