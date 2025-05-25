<?php
session_start();
if (isset($_POST['simpan_edit'])) {
    include '../../config/database.php';

    function input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        mysqli_query($kon,"START TRANSACTION");

        $idPenjual = input($_POST["idPenjual"]);
        $kodePenjual = input($_POST["kodePenjual"]);
        $nama = input($_POST["nama"]);
        $nip = input($_POST["nip"]);
        $jk = input($_POST["jk"]);
        $email = input($_POST["email"]);
        $no_telp = input($_POST["no_telp"]);
        $alamat = input($_POST["alamat"]);
        $status = input($_POST["status"]);

        $foto_saat_ini = $_POST['foto_saat_ini'];
        $foto_baru = $_FILES['foto_baru']['name'];
        $ekstensi_diperbolehkan = array('png','jpg','jpeg','gif');
        $x = explode('.', $foto_baru);
        $ekstensi = strtolower(end($x));
        $ukuran = $_FILES['foto_baru']['size'];
        $file_tmp = $_FILES['foto_baru']['tmp_name'];

        if (!empty($foto_baru)) {
            if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
                move_uploaded_file($file_tmp, 'foto/'.$foto_baru);

                if ($foto_saat_ini != 'foto_default.png') {
                    unlink("foto/".$foto_saat_ini);
                }

                $sql = "UPDATE penjual SET
                    namaPenjual='$nama',
                    nip='$nip',
                    jk='$jk',
                    email='$email',
                    noTelp='$no_telp',
                    alamat='$alamat',
                    foto='$foto_baru'
                    WHERE idPenjual=$idPenjual";
            }
        } else {
            $sql = "UPDATE penjual SET
                namaPenjual='$nama',
                nip='$nip',
                jk='$jk',
                email='$email',
                noTelp='$no_telp',
                alamat='$alamat'
                WHERE idPenjual=$idPenjual";
        }

        $edit_penjual = mysqli_query($kon, $sql);
        $edit_status = mysqli_query($kon, "UPDATE pengguna SET status='$status' WHERE kodepengguna='$kodePenjual'");

        if ($edit_penjual && $edit_status) {
            mysqli_query($kon, "COMMIT");
            header("Location:../../dist/index.php?page=penjual&edit=berhasil");
        } else {
            mysqli_query($kon, "ROLLBACK");
            header("Location:../../dist/index.php?page=penjual&edit=gagal");
        }
    }
}
?>
