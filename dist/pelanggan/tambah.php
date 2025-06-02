<?php
session_start();
if (isset($_POST['simpan_tambah'])) {

    include '../../config/database.php';

    function input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    mysqli_query($kon, "START TRANSACTION");

    // Ambil data dari form
    $namaPelanggan = input($_POST["namaPelanggan"]);
    $email = input($_POST["email"]);
    $noTelp = input($_POST["noTelp"]);
    $alamat = input($_POST["alamat"]);
    $status = input($_POST["status"]);
    $username = input($_POST["username"]);
    $password_raw = input($_POST["password"]);
    $password = input($_POST["password"]); 
    $level = "Pelanggan";

    // Ambil kode pelanggan terbaru
    $query = mysqli_query($kon, "SELECT MAX(idPelanggan) AS kodeTerbesar FROM pelanggan");
    $data = mysqli_fetch_array($query);
    $idPelanggan = $data['kodeTerbesar'] + 1;
    $kodePelanggan = "plg" . sprintf("%03s", $idPelanggan);

    // Upload foto
    $foto = $_FILES['foto']['name'];
    $x = explode('.', $foto);
    $ekstensi = strtolower(end($x));
    $file_tmp = $_FILES['foto']['tmp_name'];
    $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg', 'gif');
    $foto_name = 'foto_default.png';

    if (!empty($foto) && in_array($ekstensi, $ekstensi_diperbolehkan)) {
        $nama_foto = uniqid() . '.' . $ekstensi;
        if (move_uploaded_file($file_tmp, 'foto/' . $nama_foto)) {
            $foto_name = $nama_foto;
        }
    }

    // Simpan ke tabel pelanggan
    $sql_pelanggan = "INSERT INTO pelanggan (kodePelanggan, namaPelanggan, email, foto, alamat, noTelp) 
                      VALUES ('$kodePelanggan', '$namaPelanggan', '$email', '$foto_name', '$alamat', '$noTelp')";
    $simpan_pelanggan = mysqli_query($kon, $sql_pelanggan);

    // Simpan ke tabel pengguna
    $sql_pengguna = "INSERT INTO pengguna (kodepengguna, username, password, status, level) 
                     VALUES ('$kodePelanggan', '$username', '$password', '$status', '$level')";
    $simpan_pengguna = mysqli_query($kon, $sql_pengguna);

    // Validasi hasil
    if ($simpan_pelanggan && $simpan_pengguna) {
        mysqli_query($kon, "COMMIT");
        header("Location:../../dist/index.php?page=pelanggan&add=berhasil");
    } else {
        mysqli_query($kon, "ROLLBACK");
        header("Location:../../dist/index.php?page=pelanggan&add=gagal");
    }
}
?>


<?php
    // mengambil data barang dengan kode paling besar
    include '../../config/database.php';
    $query = mysqli_query($kon, "SELECT max(idPelanggan) as kodeTerbesar FROM pelanggan");
    $data = mysqli_fetch_array($query);
    $idPelanggan = $data['kodeTerbesar'];
    $idPelanggan++;
    $huruf = "plg";
    $kodePelanggan = $huruf . sprintf("%03s", $idPelanggan);
?>
<form action="pelanggan/tambah.php" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label>Kode pelanggan :</label>
        <h3><?php echo $kodePelanggan; ?></h3>
        <input name="kodePelanggan" value="<?php echo $kodePelanggan; ?>" type="hidden" class="form-control">
    </div>
    <div class="form-group">
        <label>Nama pelanggan :</label>
        <input name="namaPelanggan" type="text" class="form-control" placeholder="Masukan nama" required>
    </div>


    <div class="row">
        <div class="col-sm-6">
        <div class="form-group">
                <label>Email:</label>
                <input name="email" type="email" class="form-control" placeholder="Masukan email" required>
        </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>No Telp:</label>
                <input name="noTelp" type="text" class="form-control" placeholder="Masukan no telp" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Alamat:</label>
                <textarea class="form-control" name="alamat" rows="2" id="alamat"></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Status:</label>
                <select name="status" class="form-control">
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Username:</label>
        <input name="username" type="text" class="form-control" placeholder="Masukan username" required>
    </div>

    <div class="form-group">
        <label>Password:</label>
        <input name="password" type="password" class="form-control" placeholder="Masukan password" required>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <div id="msg"></div>
                <label>Foto:</label>
                <input type="file" name="foto" class="file" >
                    <div class="input-group my-3">
                        <input type="text" class="form-control" disabled placeholder="Upload Foto" id="file">
                        <div class="input-group-append">
                            <button type="button" id="pilih_foto" class="browse btn btn-dark">Pilih Foto</button>
                        </div>
                    </div>
                <img src="../src/img/img80.png" id="preview" class="img-thumbnail">
            </div>
        </div>
    </div>

    <button type="submit" name="simpan_tambah" id="btn-anggota" class="btn btn-dark">Tambah</button>
</form>

<style>
    .file {
    visibility: hidden;
    position: absolute;
    }
</style>

<script>

    $(document).on("click", "#pilih_foto", function() {
    var file = $(this).parents().find(".file");
    file.trigger("click");
    });

    $('input[type="file"]').change(function(e) {
    var fileName = e.target.files[0].name;
    $("#file").val(fileName);

    var reader = new FileReader();
    reader.onload = function(e) {
        // get loaded data and render thumbnail.
        document.getElementById("preview").src = e.target.result;
    };
    // read the image file as a data URL.
    reader.readAsDataURL(this.files[0]);
    });


</script>

