<?php
session_start();
if (isset($_POST['simpan_tambah'])){

include '../../config/database.php';

function input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Generate kodePenjual otomatis
$query = mysqli_query($kon, "SELECT max(idPenjual) as kodeTerbesar FROM penjual");
$data = mysqli_fetch_array($query);
$idPenjual = $data['kodeTerbesar'];
$idPenjual++;
$huruf = "A";
$kodePenjual = $huruf . sprintf("%03s", $idPenjual);

// Simpan jika form disubmit
if (isset($_POST['simpan_tambah'])) {
    mysqli_query($kon, "START TRANSACTION");

    $kodePenjual = input($_POST["kodePenjual"]);
    $namaPenjual = input($_POST["namaPenjual"]);
    $noTelp = input($_POST["noTelp"]);
    $alamat = input($_POST["alamat"]);
    $status = input($_POST["status"]);
    $password = input($_POST["password"]);

    // Buat username otomatis dari nama
    $username = strtolower(str_replace(' ', '_', $namaPenjual));
    $cek = mysqli_query($kon, "SELECT * FROM pengguna WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $username .= rand(100, 999);
    }

    //$password = password($password, PASSWORD_DEFAULT);

    // Upload foto
    $foto = $_FILES['foto']['name'];
    $x = explode('.', $foto);
    $ekstensi = strtolower(end($x));
    $file_tmp = $_FILES['foto']['tmp_name'];
    $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg', 'gif');

    class FileHandler {
        private $file_tmp;
        private $file_name;

        public function __construct($file_tmp, $file_name) {
            $this->file_tmp = $file_tmp;
            $this->file_name = $file_name;
        }

        public function saveFile($destination) {
            return move_uploaded_file($this->file_tmp, $destination . $this->file_name);
        }

        public function getFileName() {
            return $this->file_name;
        }
    }

    if (!empty($foto) && in_array($ekstensi, $ekstensi_diperbolehkan)) {
        $fileHandler = new FileHandler($file_tmp, $foto);
        $foto_name = $fileHandler->saveFile('foto/') ? $fileHandler->getFileName() : 'foto_default.png';
    } else {
        $foto_name = 'foto_default.png';
    }

    // Simpan ke penjual
    $sql = "INSERT INTO penjual (kodePenjual, namaPenjual, foto, alamat, noTelp) 
            VALUES ('$kodePenjual', '$namaPenjual', '$foto_name', '$alamat', '$noTelp')";
    $simpan_penjual = mysqli_query($kon, $sql);

    // Simpan ke pengguna
    $level = "penjual";
    $sql1 = "INSERT INTO pengguna (kodePengguna, username, password, status, level) 
             VALUES ('$kodePenjual', '$username', '$password', '$status', '$level')";
    $simpan_pengguna = mysqli_query($kon, $sql1);

    if ($simpan_penjual && $simpan_pengguna) {
        mysqli_query($kon, "COMMIT");
        header("Location:../../dist/index.php?page=penjual&add=berhasil");
    } else {
        mysqli_query($kon, "ROLLBACK");
        header("Location:../../dist/index.php?page=penjual&add=gagal");
    }
        }
}
?>

<?php
    include '../../config/database.php';
    $query = mysqli_query($kon, "SELECT max(idPenjual) as kodeTerbesar FROM penjual");
    $data = mysqli_fetch_array($query);
    $idPenjual = $data['kodeTerbesar'];
    $idPenjual++;
    $huruf = "A";
    $kodePenjual = $huruf . sprintf("%03s", $idPenjual);
?>

<form action="penjual/tambah.php" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label>Kode Penjual:</label>
        <h3><?php echo $kodePenjual; ?></h3>
        <input name="kodePenjual" value="<?php echo $kodePenjual; ?>" type="hidden" class="form-control">
    </div>

    <div class="form-group">
        <label>Nama Penjual:</label>
        <input name="namaPenjual" type="text" class="form-control" placeholder="Masukkan nama" required>
    </div>

    <div class="form-group">
        <label>No Telp:</label>
        <input name="noTelp" type="text" class="form-control" placeholder="Masukkan no telp" required>
    </div>

    <div class="form-group">
        <label>Alamat:</label>
        <textarea class="form-control" name="alamat" rows="2"></textarea>
    </div>

    <div class="form-group">
        <label>Status:</label>
        <select name="status" class="form-control">
            <option value="1">Aktif</option>
            <option value="0">Tidak Aktif</option>
        </select>
    </div>

    <div class="form-group">
        <label>Password:</label>
        <input name="password" type="password" class="form-control" placeholder="Masukkan password" required>
    </div>

    <div class="form-group">
        <label>Foto:</label>
        <input type="file" name="foto" class="file">
        <div class="input-group my-3">
            <input type="text" class="form-control" disabled placeholder="Upload Foto" id="file">
            <div class="input-group-append">
                <button type="button" id="pilih_foto" class="browse btn btn-dark">Pilih Foto</button>
            </div>
        </div>
        <img src="../src/img/img80.png" id="preview" class="img-thumbnail">
    </div>

    <button type="submit" name="simpan_tambah" class="btn btn-dark">Tambah</button>
</form>

<style>
    .file {
        visibility: hidden;
        position: absolute;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on("click", "#pilih_foto", function () {
        var file = $(this).parents().find(".file");
        file.trigger("click");
    });

    $('input[type="file"]').change(function (e) {
        var fileName = e.target.files[0].name;
        $("#file").val(fileName);

        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById("preview").src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    });
</script>