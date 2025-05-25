<?php
session_start();

if (isset($_POST['simpan_pelanggan'])) {
    include '../../config/database.php';
    mysqli_query($kon, "START TRANSACTION");

    function input($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    // ========================================
    // ========== PELANGGAN ===============
    // ========================================
    if ($_SESSION["level"] == 'Pelanggan' || $_SESSION["level"] == 'pelanggan') {

        $idPelanggan = $_POST["idPelanggan"];
        $namaPelanggan = input($_POST["namaPelanggan"]);
        $noTelp = input($_POST["noTelp"]);
        $email = input($_POST["email"]);
        $alamat = input($_POST["alamat"]);
        $foto_saat_ini = $_POST['foto_saat_ini'];

        // Upload Foto
        $foto_baru = $_FILES['foto_baru']['name'];
        $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg', 'gif');
        $x = explode('.', $foto_baru);
        $ekstensi = strtolower(end($x));
        $ukuran = $_FILES['foto_baru']['size'];
        $file_tmp = $_FILES['foto_baru']['tmp_name'];

        if (!empty($foto_baru)) {
            if (in_array($ekstensi, $ekstensi_diperbolehkan) && $ukuran <= 2000000) {
                move_uploaded_file($file_tmp, '../pelanggan/foto/' . $foto_baru);
                if ($foto_saat_ini != 'foto_default.png') {
                    unlink("../pelanggan/foto/" . $foto_saat_ini);
                }

                $sql = "UPDATE pelanggan SET
                    namaPelanggan='$namaPelanggan',
                    noTelp='$noTelp',
                    email='$email',
                    alamat='$alamat',
                    foto='$foto_baru'
                    WHERE idPelanggan=$idPelanggan";
            }
        } else {
            $sql = "UPDATE pelanggan SET
                namaPelanggan='$namaPelanggan',
                noTelp='$noTelp',
                email='$email',
                alamat='$alamat'
                WHERE idPelanggan=$idPelanggan";
        }

        $update = mysqli_query($kon, $sql);

        // Update pengguna
        $idPengguna = $_POST["idPengguna"];
        $username = input($_POST["username_baru"]);

        $ambil_password = mysqli_query($kon, "SELECT password FROM pengguna WHERE idPengguna=$idPengguna LIMIT 1");
        $data = mysqli_fetch_array($ambil_password);

        if ($data['password'] == $_POST["password"]) {
            $password = input($_POST["password"]);
        } else {
            $password = md5(input($_POST["password"]));
        }

        $sql_pengguna = "UPDATE pengguna SET
            username='$username',
            password='$password'
            WHERE idPengguna=$idPengguna";
        $update_pengguna = mysqli_query($kon, $sql_pengguna);
    }

    // ========================================
    // ========== PENJUAL ===============
    // ========================================
    elseif ($_SESSION["level"] == 'Penjual' || $_SESSION["level"] == 'penjual') {

        $idPenjual = $_POST["idPenjual"];
        $namaPenjual = input($_POST["nama"]);
        $noTelp = input($_POST["noTelp"] ?? '');
        $alamat = input($_POST["alamat"] ?? '');
        $foto_saat_ini = $_POST['foto_saat_ini'];

        // Upload Foto
        $foto_baru = $_FILES['foto_baru']['name'];
        $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg', 'gif');
        $x = explode('.', $foto_baru);
        $ekstensi = strtolower(end($x));
        $ukuran = $_FILES['foto_baru']['size'];
        $file_tmp = $_FILES['foto_baru']['tmp_name'];

        if (!empty($foto_baru)) {
            if (in_array($ekstensi, $ekstensi_diperbolehkan) && $ukuran <= 2000000) {
                move_uploaded_file($file_tmp, '../penjual/foto/' . $foto_baru);
                if ($foto_saat_ini != 'foto_default.png') {
                    unlink("../penjual/foto/" . $foto_saat_ini);
                }

                $sql = "UPDATE penjual SET
                    namaPenjual='$namaPenjual',
                    noTelp='$noTelp',
                    alamat='$alamat',
                    foto='$foto_baru'
                    WHERE idPenjual=$idPenjual";
            }
        } else {
            $sql = "UPDATE penjual SET
                namaPenjual='$namaPenjual',
                noTelp='$noTelp',
                alamat='$alamat'
                WHERE idPenjual=$idPenjual";
        }

        $update = mysqli_query($kon, $sql);

        // Update pengguna
        $idPengguna = $_POST["idPengguna"];
        $username = input($_POST["username_baru"]);

        $ambil_password = mysqli_query($kon, "SELECT password FROM pengguna WHERE idPengguna=$idPengguna LIMIT 1");
        $data = mysqli_fetch_array($ambil_password);

        if ($data['password'] == $_POST["password"]) {
            $password = input($_POST["password"]);
        } else {
            $password = md5(input($_POST["password"]));
        }

        $sql_pengguna = "UPDATE pengguna SET
            username='$username',
            password='$password'
            WHERE idPengguna=$idPengguna";
        $update_pengguna = mysqli_query($kon, $sql_pengguna);
    }

    // ========================================
    // ========== CHECK COMMIT/ROLLBACK ==========
    // ========================================
    if (isset($update) && isset($update_pengguna) && $update && $update_pengguna) {
        mysqli_query($kon, "COMMIT");
        header("Location:../../dist/index.php?page=profil&edit=berhasil");
    } else {
        mysqli_query($kon, "ROLLBACK");
        header("Location:../../dist/index.php?page=profil&edit=gagal");
    }
}
?>
