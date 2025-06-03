<?php
session_start();
include "../config/database.php";

$base_url = "http://localhost/zamelda-store/";
$query = mysqli_query($kon, "SELECT gambarAwal FROM tampilanawal LIMIT 4");
$gambar = [];
while ($row = mysqli_fetch_assoc($query)) {
    $gambar[] = $row['gambarAwal'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Zamelda Store - Selamat Datang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css"
      integrity="sha384-sVZpIOzfvFV1XoLz1vMeHWK6y94E7Oa+FFa4svZJGRw1bPvS+fCEXnYQ6vUu6fwN"
      crossorigin="anonymous"
    />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #b6afac;
        }

        .slider-container {
            position: relative;
            overflow: hidden;
            width: 100%;
            height: 90vh;
        }

        .slider-track {
            display: flex;
            width: 100%;
            height: 100%;
            transition: transform 0.6s ease;
        }

        .slider-track img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            image-rendering: auto;
            image-rendering: -webkit-optimize-contrast;
            flex-shrink: 0;
        }

        .slider-header {
            position: absolute;
            top: 30px;
            left: 40px;
            z-index: 10;
        }

        .slider-header img {
            width: 150px;
            height: auto;
        }

        .login-button {
            position: absolute;
            top: 30px;
            right: 40px;
            background-color: rgb(50, 49, 48);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 16px;
            z-index: 10;
        }

        .login-button:hover {
            background-color: rgb(103, 101, 99);
        }

        footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .social-icons {
            margin-top: 15px;
        }

        .social-icons a {
            color: white;
            font-size: 24px;
            margin: 0 15px;
            text-decoration: none;
            transition: 0.3s ease;
        }

        .social-icons a:hover {
            color: rgb(200, 200, 200);
            transform: scale(1.1);
        }
    </style>
</head>
<body>

<div class="slider-container">
    <div class="slider-header">
        <img src="aplikasi/logo/ADS.png" alt="Zamelda Logo" />
    </div>
    <a href="login.php" class="login-button">Login</a>

    <div class="slider-track">
        <?php foreach ($gambar as $g): ?>
            <img src="<?php echo $base_url . 'dist/barang/gambar/' . htmlspecialchars($g); ?>" alt="Slider Gambar" />
        <?php endforeach; ?>
    </div>
</div>

<footer>
    <p>&copy; 2025 Zamelda Official Store. All rights reserved.</p>
    <div class="social-icons">
        <a href="https://www.facebook.com/zamelda" target="_blank" aria-label="Facebook">
            <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/facebook.svg" alt="Facebook" width="24" />
        </a>
        <a href="https://twitter.com/zamelda" target="_blank" aria-label="Twitter">
            <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/twitter.svg" alt="Twitter" width="24" />
        </a>
        <a href="https://www.instagram.com/zamelda" target="_blank" aria-label="Instagram">
            <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/instagram.svg" alt="Instagram" width="24" />
        </a>
        <a href="https://www.linkedin.com/company/zamelda" target="_blank" aria-label="LinkedIn">
            <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/linkedin.svg" alt="LinkedIn" width="24" />
        </a>
    </div>
</footer>

<script>
    const track = document.querySelector('.slider-track');
    const totalSlides = <?php echo count($gambar); ?>;
    let current = 0;

    setInterval(() => {
        current = (current + 1) % totalSlides;
        track.style.transform = `translateX(-${current * 100}%)`;
    }, 4000);
</script>

</body>
</html>
