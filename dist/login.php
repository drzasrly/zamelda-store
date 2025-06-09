<?php
$pesan = "";

function input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    include "../config/database.php";

    $username = input($_POST["username"]);
    $password = input($_POST["password"]);

    // Cek admin dulu
    $tabel_admin = "SELECT * FROM pengguna WHERE username='$username' AND password='$password' AND level='admin' LIMIT 1";
    $cek_tabel_admin = mysqli_query($kon, $tabel_admin);
    $admin = mysqli_num_rows($cek_tabel_admin);

    // Cek penjual
    $tabel_penjual = "SELECT * FROM pengguna p
        INNER JOIN penjual k ON k.kodePenjual=p.kodePengguna
        WHERE username='$username' AND password='$password' LIMIT 1";
    $cek_tabel_penjual = mysqli_query($kon, $tabel_penjual);
    $penjual = mysqli_num_rows($cek_tabel_penjual);

    // Cek pelanggan
    $tabel_pelanggan = "SELECT * FROM pengguna p
        INNER JOIN pelanggan m ON m.kodePelanggan=p.kodePengguna
        WHERE username='$username' AND password='$password' LIMIT 1";
    $cek_tabel_pelanggan = mysqli_query($kon, $tabel_pelanggan);
    $pelanggan = mysqli_num_rows($cek_tabel_pelanggan);

    if ($admin > 0) {
        $row = mysqli_fetch_assoc($cek_tabel_admin);
        if ($row["status"] == 1) {
            $_SESSION["idPengguna"] = $row["idPengguna"];
            $_SESSION["kodePengguna"] = $row["kodePengguna"];
            $_SESSION["namaAdmin"] = $row["namaAdmin"]; 
            $_SESSION["username"] = $row["username"];
            $_SESSION["level"] = $row["level"];
            $_SESSION["foto"] = $row["foto"];
            header("Location:index.php?page=dashboard");
            exit();
        } else {
            $pesan = "<div class='alert alert-warning'><strong>Gagal!</strong> Status pengguna tidak aktif.</div>";
        }
    } elseif ($penjual > 0) {
        $row = mysqli_fetch_assoc($cek_tabel_penjual);
        if ($row["status"] == 1) {
            $_SESSION["idPengguna"] = $row["idPengguna"];
            $_SESSION["kodePengguna"] = $row["kodePengguna"];
            $_SESSION["namaPenjual"] = $row["namaPenjual"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["level"] = $row["level"];
            $_SESSION["foto"] = $row["foto"];
            header("Location:index.php?page=dashboard");
            exit();
        } else {
            $pesan = "<div class='alert alert-warning'><strong>Gagal!</strong> Status pengguna tidak aktif.</div>";
        }
    } elseif ($pelanggan > 0) {
        $row = mysqli_fetch_assoc($cek_tabel_pelanggan);
        if ($row["status"] == 1) {
            $_SESSION["idPengguna"] = $row["idPengguna"];
            $_SESSION["kodePengguna"] = $row["kodePengguna"];
            $_SESSION["namaPelanggan"] = $row["namaPelanggan"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["level"] = $row["level"];
            $_SESSION["foto"] = $row["foto"];
            header("Location:index.php?page=dashboard");
            exit();
        } else {
            $pesan = "<div class='alert alert-warning'><strong>Gagal!</strong> Status pengguna tidak aktif.</div>";
        }
    } else {
        $pesan = "<div class='alert alert-danger'><strong>Error!</strong> Username dan password salah.</div>";
    }
}
?>

<!DOCTYPE html>
<?php 
include '../config/database.php';
$hasil = mysqli_query($kon, "select * from profil_aplikasi order by nama_aplikasi desc limit 1");
$data = mysqli_fetch_array($hasil); 
?>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?php echo $data['nama_aplikasi']; ?></title>
    <link href="../src/templates/css/styles.css" rel="stylesheet" />
    <script src="../src/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
    <style>
        .login-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #343a40;
            padding: 2rem;
        }
        .login-container {
            display: flex;
            width: 100%;
            max-width: 900px;
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
        .login-logo {
            width: 80%;
            max-width: 300px;
            height: auto;
        }
        .right-side {
            background-color: #ffffff;
        }
        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .welcome-subtitle {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 0.75rem;
            font-weight: bold;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="left-side">
                <img src="aplikasi/logo/<?php echo $data['logo'];?>" class="login-logo" alt="Logo Zamelda">
            </div>
            <div class="right-side">
                <div class="welcome-title">Welcome Back,</div>
                <div class="welcome-subtitle">Log in now to continue</div>

                <?php if ($_SERVER["REQUEST_METHOD"] == "POST") echo $pesan; ?>
                <?php 
                    if (isset($_GET['daftar']) && $_GET['daftar'] == 'berhasil') {
                        echo "<div class='alert alert-success'><strong>Berhasil!</strong> Pendaftaran akun berhasil.</div>";
                    }
                ?>

                <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
                    <div class="form-group">
                        <label class="small mb-1">Username</label>
                        <input class="form-control py-3" name="username" type="text" placeholder="Masukkan Username" />
                    </div>
                    <div class="form-group">
                        <label class="small mb-1">Password</label>
                        <input class="form-control py-3" name="password" type="password" placeholder="Masukkan Password" />
                    </div>
                    <button class="btn btn-primary mt-3" type="submit">Login</button>
                </form>

                <div class="text-center mt-3">
                    <a href="daftar.php">Belum mempunyai akun? Daftar sekarang!</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4 bg-light mt-auto text-center">
        <div class="container-fluid">
            <div class="small text-muted">Copyright &copy; 
                <?php echo $data['nama_aplikasi'];?> <?php echo date('Y');?>
            </div>
        </div>
    </footer>

    <script src="../src/js/jquery/jquery-3.5.1.min.js"></script>
    <script src="../src/plugin/bootstrap/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>
</body>
</html>
