<?php

session_start();

if (!isset($_SESSION['kodePengguna']) || $_SESSION['level'] !== 'penjual' and $_SESSION['level'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../../config/database.php';

$kodePelanggan = $_POST['kodePelanggan'] ?? $_GET['kodePelanggan'] ?? null;

if (!$kodePelanggan) {
    echo "Kode pelanggan tidak ditemukan.";
    exit;
}
if (isset($_POST['simpan_profil'])) {
    $idPelanggan = $_POST['idPelanggan'];
    $idPengguna = $_POST['idPengguna'];
    $namaPelanggan = $_POST['namaPelanggan'];
    $noTelp = $_POST['noTelp'];
    $alamat = $_POST['alamat'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $fotoLama = $_POST['foto_saat_ini'];

    // Cek apakah ada foto baru
    if (!empty($_FILES['foto_baru']['name'])) {
        $fotoBaru = $_FILES['foto_baru']['name'];
        $tmp = $_FILES['foto_baru']['tmp_name'];
        $folder = "pelanggan/foto/";
        $path_foto = $folder . $fotoBaru;

        move_uploaded_file($tmp, $path_foto);

        // Hapus foto lama jika bukan default
        if ($fotoLama != 'foto_default.png' && file_exists($folder . $fotoLama)) {
            unlink($folder . $fotoLama);
        }
    } else {
        $fotoBaru = $fotoLama;
    }

    // Update data pelanggan
    $update_Pelanggan = mysqli_query($kon, "UPDATE pelanggan SET
        namaPelanggan='$namaPelanggan',
        noTelp='$noTelp',
        alamat='$alamat',
        foto='$fotoBaru'
        WHERE idPelanggan='$idPelanggan'");

    // Ambil password lama dan bandingkan
    $cek_password = mysqli_query($kon, "SELECT password FROM pengguna WHERE idPengguna='$idPengguna' LIMIT 1");
    $data_password = mysqli_fetch_array($cek_password);

    if ($data_password['password'] !== $password) {
        $password = md5($password); // Atau password_hash()
    }

    // Update data pengguna
    $update_pengguna = mysqli_query($kon, "UPDATE pengguna SET
        username='$username',
        password='$password'
        WHERE idPengguna='$idPengguna'");

    $_SESSION['pesan'] = "Profil berhasil diperbarui.";
     header("Location:detail-akun.php?kodePelanggan=$kodePelanggan");
     
    exit;
}


// Ambil data pelanggan setelah update
$sql = "SELECT * FROM pelanggan p
        INNER JOIN pengguna u ON u.kodePengguna = p.kodePelanggan
        WHERE p.kodePelanggan = '$kodePelanggan'";
$hasil = mysqli_query($kon, $sql);

if (mysqli_num_rows($hasil) == 0) {
    echo "Pelanggan tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_array($hasil);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Pelanggan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">

    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Profil Pelanggan</div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-3">
                    <img src="pelanggan/foto/<?= htmlspecialchars($data['foto']) ?>" alt="Foto Profil" class="img-thumbnail">
                </div>
                <div class="col-sm-9">
                    <table class="table">
                        <tr><td>Kode</td><td>: <?= $data['kodePelanggan'] ?></td></tr>
                        <tr><td>Nama</td><td>: <?= $data['namaPelanggan'] ?></td></tr>
                        <tr><td>No Telp</td><td>: <?= $data['noTelp'] ?></td></tr>
                        <tr><td>Alamat</td><td>: <?= $data['alamat'] ?></td></tr>
                        <tr><td>Username</td><td>: <?= $data['username'] ?></td></tr>
                        <tr><td>Status</td><td>: <?= $data['status'] == 1 ? 'Aktif' : 'Tidak Aktif' ?></td></tr>
                    </table>
                    <!-- <button class="btn btn-dark" data-toggle="modal" data-target="#modalEditProfil">Edit Profil</button>
                </div> -->
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEditProfil">
        <div class="modal-dialog modal-lg">
            <form action="" method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profil Pelanggan</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="idPelanggan" value="<?= $data['idPelanggan'] ?>">
                    <input type="hidden" name="idPengguna" value="<?= $data['idPengguna'] ?>">
                    <input type="hidden" name="kodePelanggan" value="<?= $data['kodePelanggan'] ?>">

                    <div class="form-group">
                        <label>Nama</label>
                        <input name="namaPelanggan" value="<?= $data['namaPelanggan'] ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>No Telp</label>
                        <input name="noTelp" value="<?= $data['noTelp'] ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"><?= $data['alamat'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input name="username" value="<?= $data['username'] ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input name="password" value="<?= $data['password'] ?>" type="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Foto</label><br>
                        <input type="hidden" name="foto_saat_ini" value="<?= $data['foto'] ?>">
                        <input type="file" name="foto_baru" class="file" hidden>
                        <div class="input-group">
                            <input type="text" class="form-control" disabled placeholder="Upload file" id="file">
                            <div class="input-group-append">
                                <button type="button" id="pilih_foto" class="btn btn-dark">Pilih Foto</button>
                            </div>
                        </div>
                        <br>
                        <img src="pelanggan/foto/<?= htmlspecialchars($data['foto']) ?>" width="50%" id="preview" class="img-thumbnail">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan_profil" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).on("click", "#pilih_foto", function () {
    $(this).closest(".form-group").find("input[type='file']").trigger("click");
});
$('input[type="file"]').change(function (e) {
    var fileName = e.target.files[0].name;
    $("#file").val(fileName);

    var reader = new FileReader();
    reader.onload = function (e) {
        $("#preview").attr("src", e.target.result);
    };
    reader.readAsDataURL(this.files[0]);
});
</script>
</body>
</html>
