<?php
session_start();
    if (isset($_POST['simpan_tambah'])) {
        
        //Include file koneksi, untuk koneksikan ke database
        include '../../config/database.php';
        
        //Fungsi untuk mencegah inputan karakter yang tidak sesuai
        function input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        // Generate kodePelanggan otomatis
        $query = mysqli_query($kon, "SELECT max(idPelanggan) as kodeTerbesar FROM pelanggan");
        $data = mysqli_fetch_array($query);
        $idPelanggan = $data['kodeTerbesar'];
        $idPelanggan++;
        $huruf = "A";
        $kodePelanggan = $huruf . sprintf("%03s", $idPelanggan);

        //Cek apakah ada kiriman form dari method post
        if (isset($_POST['simpan_tambah'])) {
            mysqli_query($kon,"START TRANSACTION");

            $kodePelanggan=input($_POST["kodePelanggan"]);
            $namaPelanggan=input($_POST["namaPelanggan"]);
            $email=input($_POST["email"]);
            $noTelp=input($_POST["noTelp"]);
            $alamat=input($_POST["alamat"]);
            $status=input($_POST["status"]);

              // Buat username otomatis dari nama
    $username = strtolower(str_replace(' ', '_', $namaPelanggan));
    $cek = mysqli_query($kon, "SELECT * FROM pengguna WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $username .= rand(100, 999);
    }
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
// Tambahkan ini sebelum query INSERT pengguna
$kodePengguna = $kodePelanggan; // gunakan kode yang sama, bisa dibedakan jika perlu
//$password = password("password", PASSWORD_DEFAULT); // password default, bisa diganti

// Simpan ke pelanggan
$sql = "INSERT INTO pelanggan (kodePelanggan, namaPelanggan, email,foto, alamat, noTelp) 
        VALUES ('$kodePelanggan', '$namaPelanggan','$email', '$foto_name', '$alamat', '$noTelp')";
$simpan_pelanggan = mysqli_query($kon, $sql);

// Simpan ke pengguna
$level = "Pelanggan";
$sql1 = "INSERT INTO pengguna (kodepengguna, username, password, status, level) 
         VALUES ('$kodePengguna', '$username', '$password', '$status', '$level')";
$simpan_pengguna = mysqli_query($kon, $sql1);

// Cek simpanan
if ($simpan_pelanggan && $simpan_pengguna) {
    mysqli_query($kon, "COMMIT");
    header("Location:../../dist/index.php?page=pelanggan&add=berhasil");
} else {
    mysqli_query($kon, "ROLLBACK");
    header("Location:../../dist/index.php?page=pelanggan&add=gagal");
}
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
    $huruf = "A";
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

