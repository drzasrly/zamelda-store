
<?php
session_start();
include '../../config/database.php';

// Ambil kode penjual dari session login
$kodePenjual = $_SESSION["kodePengguna"];

// Ambil data profil
$sql = "SELECT * FROM pengguna p
        INNER JOIN penjual k ON k.kodePenjual = p.kodePengguna
        WHERE k.kodePenjual = '$kodePenjual' LIMIT 1";
$hasil = mysqli_query($kon, $sql);
$data = mysqli_fetch_array($hasil);

// Proses update jika form disubmit
if (isset($_POST['simpan_profil'])) {
    $idPenjual = $_POST['idPenjual'];
    $idPengguna = $_POST['idPengguna'];
    $namaPenjual = $_POST['namaPenjual'];
    $noTelp = $_POST['noTelp'];
    $alamat = $_POST['alamat'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $fotoLama = $_POST['foto_saat_ini'];

    // Proses foto baru jika ada
    if (!empty($_FILES['foto_baru']['name'])) {
        $fotoBaru = $_FILES['foto_baru']['name'];
        $tmp = $_FILES['foto_baru']['tmp_name'];
        $folder = "penjual/foto/";
        $path_foto = $folder . $fotoBaru;

        // Upload foto baru
        move_uploaded_file($tmp, $path_foto);

        // Hapus foto lama jika berbeda
        if ($fotoLama != '' && file_exists($folder . $fotoLama)) {
            unlink($folder . $fotoLama);
        }
    } else {
        $fotoBaru = $fotoLama;
    }

    // Update ke tabel penjual
    $update_penjual = mysqli_query($kon, "UPDATE penjual SET
        namaPenjual='$namaPenjual',
        noTelp='$noTelp',
        alamat='$alamat',
        foto='$fotoBaru'
        WHERE idPenjual='$idPenjual'");

    // Update ke tabel pengguna
    $update_pengguna = mysqli_query($kon, "UPDATE pengguna SET
        username='$username',
        password='$password'
        WHERE idPengguna='$idPengguna'");

    // Redirect dengan pesan
    echo "<script>alert('Profil berhasil diperbarui.'); window.location='profil_penjual.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Penjual</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css    ">
</head>
<body>
<div class="container mt-4">

    <div class="card mb-4">
        <div class="card-header">Profil Penjual</div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-3">
                    <img src="penjual/foto/<?= htmlspecialchars($data['foto']) ?>" alt="Foto Profil" class="img-thumbnail">
                </div>
                <div class="col-sm-9">
                    <table class="table">
                        <tr><td>Kode</td><td>: <?= $data['kodePenjual'] ?></td></tr>
                        <tr><td>Nama</td><td>: <?= $data['namaPenjual'] ?></td></tr>
                        <tr><td>No Telp</td><td>: <?= $data['noTelp'] ?></td></tr>
                        <tr><td>Alamat</td><td>: <?= $data['alamat'] ?></td></tr>
                        <tr><td>Username</td><td>: <?= $data['username'] ?></td></tr>
                        <tr><td>Status</td><td>: <?= $data['status'] == 1 ? 'Aktif' : 'Tidak Aktif' ?></td></tr>
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
                    <h5 class="modal-title">Edit Profil Penjual</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="idPenjual" value="<?= $data['idPenjual'] ?>">
                    <input type="hidden" name="idPengguna" value="<?= $data['idPengguna'] ?>">
                    <input type="hidden" name="kodePenjual" value="<?= $data['kodePenjual'] ?>">

                    <div class="form-group">
                        <label>Nama</label>
                        <input name="namaPenjual" value="<?= $data['namaPenjual'] ?>" class="form-control" required>
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
                        <img src="penjual/foto/<?= $data['foto'] ?>" width="50%" id="preview" class="img-thumbnail">
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
<script src="https://code.jquery.com/jquery-3.5.1.min.js    "></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js    "></script>
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