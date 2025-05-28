<?php
include '../../config/database.php';

if (!isset($_GET['idPelanggan'])) {
    die("ID Pelanggan tidak ditemukan.");
}

$idPelanggan = mysqli_real_escape_string($kon, $_GET['idPelanggan']);

$sql = "SELECT p.idPengguna, k.* 
        FROM pengguna p
        INNER JOIN pelanggan k ON p.idPengguna = k.idPengguna
        WHERE k.idPelanggan = '$idPelanggan'
        LIMIT 1";

$hasil = mysqli_query($kon, $sql);
$data = mysqli_fetch_array($hasil);

if (!$data) {
    die("Data pelanggan tidak ditemukan.");
}

// Proses update jika form disubmit
if (isset($_POST['simpan_profil'])) {
    $idPelanggan = $data['idPelanggan'];
    $idPengguna = $data['idPengguna'];
    $namaPelanggan = mysqli_real_escape_string($kon, $_POST['namaPelanggan']);
    $noTelp = mysqli_real_escape_string($kon, $_POST['noTelp']);
    $alamat = mysqli_real_escape_string($kon, $_POST['alamat']);
    $username = mysqli_real_escape_string($kon, $_POST['username']);
    $password = md5($_POST['password']); // Gunakan password_hash() di produksi
    $fotoLama = $data['foto'];

    // Upload foto baru jika ada
    if (!empty($_FILES['foto_baru']['name'])) {
        $fotoBaru = $_FILES['foto_baru']['name'];
        $tmp = $_FILES['foto_baru']['tmp_name'];
        $folder = "../pelanggan/foto/";

        move_uploaded_file($tmp, $folder . $fotoBaru);

        // Hapus foto lama jika bukan default
        if ($fotoLama != 'foto_default.png' && file_exists($folder . $fotoLama)) {
            unlink($folder . $fotoLama);
        }
    } else {
        $fotoBaru = $fotoLama;
    }

    // Update tabel pelanggan
    $update_pelanggan = mysqli_query($kon, "UPDATE pelanggan SET
        namaPelanggan='$namaPelanggan',
        noTelp='$noTelp',
        alamat='$alamat',
        foto='$fotoBaru'
        WHERE idPelanggan='$idPelanggan'");

    // Update tabel pengguna
    $update_pengguna = mysqli_query($kon, "UPDATE pengguna SET
        username='$username',
        password='$password'
        WHERE idPengguna='$idPengguna'");

    if ($update_pelanggan && $update_pengguna) {
        echo "<script>alert('Profil berhasil diperbarui.'); window.location='detail_pelanggan.php?idPelanggan=$idPelanggan';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui profil.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Pelanggan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css ">
</head>
<body>
<div class="container mt-4">

    <div class="card mb-4">
        <div class="card-header">Detail Profil Pelanggan</div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-3">
                    <img src="../pelanggan/foto/<?= htmlspecialchars($data['foto']) ?>" alt="Foto Profil" class="img-thumbnail">
                </div>
                <div class="col-sm-9">
                    <table class="table">
                        <tr><td>Kode</td><td>: <?= $data['kodePelanggan'] ?></td></tr>
                        <tr><td>Nama</td><td>: <?= $data['namaPelanggan'] ?></td></tr>
                        <tr><td>No Telp</td><td>: <?= $data['noTelp'] ?></td></tr>
                        <tr><td>Alamat</td><td>: <?= $data['alamat'] ?></td></tr>
                        <tr><td>Username</td><td>: <?= $data['username'] ?></td></tr>
                    </table>
                    <button class="btn btn-dark" data-toggle="modal" data-target="#modalEditProfil">Edit Profil</button>
                </div>
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
                        <input name="password" type="password" class="form-control" placeholder="Masukkan password baru atau ketik ulang yang lama" required>
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
                        <img src="../pelanggan/foto/<?= $data['foto'] ?>" width="50%" id="preview" class="img-thumbnail">
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

<!-- Script -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js "></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js "></script>
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