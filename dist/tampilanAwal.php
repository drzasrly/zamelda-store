<?php
session_start();
include "../config/database.php";

$base_url = "http://localhost/zamelda-store/";
$query = mysqli_query($kon, "SELECT gambarAwal FROM tampilanawal LIMIT 4");
$gambar = [];
while ($row = mysqli_fetch_assoc($query)) {
    $gambar[] = $row['gambarAwal'];
}

$kategori = "";
if (isset($_POST['kategoriBarang']) && is_array($_POST['kategoriBarang'])) {
    foreach ($_POST['kategoriBarang'] as $value) {
        $kategori .= "'" . mysqli_real_escape_string($kon, $value) . "',";
    }
    $kategori = rtrim($kategori, ',');
} else {
    $kategori = "0"; 
}

$sql = "SELECT b.idBarang, b.kodeBarang, b.namaBarang,
        (SELECT gv.gambarvarian
         FROM varianbarang vb
         JOIN gambarvarian gv ON gv.idGambarVarian = vb.idGambarVarian
         WHERE vb.kodeBarang = b.kodeBarang
         ORDER BY vb.idVarian ASC
         LIMIT 1) AS gambarBarang
        FROM barang b";

if (isset($_POST['kategoriBarang']) && !empty($kategori)) {
    $sql .= " WHERE b.kodeKategori IN ($kategori)";
}

$sql .= " LIMIT 8"; 

$hasil = mysqli_query($kon, $sql);
if (!$hasil) {
    echo "<div class='alert alert-danger'>Query error: " . mysqli_error($kon) . "</div>";
    exit;
}
$cek = mysqli_num_rows($hasil);
if ($cek <= 0) {
    echo "<div class='col-sm-12'><div class='alert alert-warning'>Data tidak ditemukan!</div></div>";
    exit;
}
$barangs = mysqli_fetch_all($hasil, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Zamelda Store - Selamat Datang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="../src/templates/css/styles.css" rel="stylesheet" />
    <script src="../src/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
        }

        .slider-container {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            box-shadow: 0 8px 12px -6px rgba(0, 0, 0, 0.1);
        }

        .slider-track {
            display: flex;
            height: 100%;
            transition: transform 0.6s ease;
        }

        .slider-track img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .slider-header {
            position: absolute;
            top: 30px;
            left: 40px;
            z-index: 10;
        }

        .login-button {
            position: absolute;
            top: 30px;
            right: 40px;
            background-color: #fff;
            color: #000;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            z-index: 100;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: 0.3s ease;
        }

        .login-button:hover {
            background-color: #f1f1f1;
            color: #333;
        }


        .new-arrivals {
            text-align: center;
            padding: 60px 20px 30px;
            background-color: white;
        }

        .new-arrivals h2 {
            font-size: 2.2rem;
            margin-bottom: 40px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto 60px;
            padding: 0 20px;
        }

        .card {
            border: none;
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .social-icons a {
            margin: 0 10px;
        }

        .btn-detail-barang {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="slider-container">
    <div class="slider-header">
        <img src="aplikasi/logo/ADS.png" alt="Zamelda Logo" width="150">
    </div>
    <a href="login.php" class="login-button">Login</a>
    <div class="slider-track">
        <?php foreach ($gambar as $g): ?>
            <img src="<?= $base_url . 'dist/barang/gambar/' . htmlspecialchars($g) ?>" alt="Slider">
        <?php endforeach; ?>
    </div>
</div>

<section class="new-arrivals">
    <h2>New Arrivals</h2>
    <div class="product-grid">
        <?php foreach ($barangs as $data): ?>
            <div class="card">
                <img src="../dist/barang/gambar/<?= htmlspecialchars($data['gambarBarang']) ?>" alt="<?= htmlspecialchars($data['namaBarang']) ?>">
                <div class="card-body text-center">
                    <h6><?= htmlspecialchars($data['namaBarang']) ?></h6>
                    <!-- <button type="button" class="btn btn-warning btn-detail-barang"
                        idBarang="<?= $data['idBarang'] ?>"
                        kodeBarang="<?= $data['kodeBarang'] ?>">
                        Lihat
                    </button> -->
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<footer>
    <p>&copy; 2025 Zamelda Official Store. All rights reserved.</p>
    <div class="social-icons">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-linkedin"></i></a>
    </div>
</footer>

<script>
    const track = document.querySelector('.slider-track');
    const totalSlides = <?= count($gambar) ?>;
    let current = 0;

    setInterval(() => {
        current = (current + 1) % totalSlides;
        track.style.transform = `translateX(-${current * 100}%)`;
    }, 4000);
</script>

</body>
</html>
