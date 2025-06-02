<?php
session_start();
include '../config/database.php';

$query = mysqli_query($kon, "SELECT max(idPelanggan) as kodeTerbesar FROM pelanggan");
$data = mysqli_fetch_array($query);
$idPelanggan = $data['kodeTerbesar'] + 1;
$kodePelanggan = "plg" . sprintf("%03s", $idPelanggan);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Buat Akun Baru - Zamelda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="../src/templates/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="../src/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
    <style>
        .register-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #343a40;
            padding: 2rem;
        }
        .register-container {
            display: flex;
            width: 100%;
            max-width: 800px;
            background-color: #fff;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        .left-side, .right-side {
            flex: 1;
            padding: 3rem;
        }
        .left-side {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-logo {
            width: 80%;
            max-width: 300px;
            height: auto;
        }
        .right-side {
            background-color: #ffffff;
        }
        .register-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .btn-primary {
            font-weight: bold;
            width: 100%;
            padding: 0.75rem;
        }
        .file {
            visibility: hidden;
            position: absolute;
        }
        #preview {
            max-height: 150px;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
<div class="register-wrapper">
    <div class="register-container">
        <div class="left-side">
            <img src="aplikasi/logo/ADS.png" class="register-logo" alt="Zamelda Logo">
        </div>
        <div class="right-side">
            <div class="register-title">Buat Akun Baru</div>
            <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="kodePelanggan" value="<?php echo $kodePelanggan; ?>">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Nama</label>
                        <input name="nama" type="text" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Nomor Telp</label>
                        <input name="noTelp" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input name="email" type="email" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Alamat</label>
                        <input name="alamat" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Username</label>
                        <input name="username" type="text" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Password</label>
                        <input name="password" type="password" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Upload Foto (Opsional)</label>
                    <input type="file" name="foto" class="file" />
                    <div class="input-group my-2">
                        <input type="text" class="form-control" disabled placeholder="Upload Foto" id="file">
                        <div class="input-group-append">
                            <button type="button" id="pilih_foto" class="browse btn btn-dark">Pilih Foto</button>
                        </div>
                    </div>
                    <img src="../src/img/img80.png" id="preview" class="img-thumbnail">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php">Sudah punya akun? Login!</a>
            </div>
        </div>
    </div>
</div>

<script src="../src/js/jquery/jquery-3.5.1.min.js"></script>
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
</body>
</html>