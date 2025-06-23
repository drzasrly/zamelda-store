<?php
session_start();
require_once '../../config/midtrans_config.php'; 

if (!isset($_SESSION['snapToken']) || !isset($_SESSION['kodeTransaksi'])) {
    if (isset($_SESSION['kodeTransaksi'])) {
        header("Location: transaksi-berhasil.php");
    } else {
        header("Location: transaksi-gagal.php?error=token_invalid");
    }
    exit;
}


$snapToken = $_SESSION['snapToken'];
$kodeTransaksi = $_SESSION['kodeTransaksi'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bayar Pesanan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-xxxxx"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right,rgb(168, 204, 209),rgb(45, 117, 113));
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 100px auto;
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .info {
            font-size: 16px;
            margin-bottom: 25px;
            color: #555;
        }
        #pay-button {
            background-color: #ee4d2d;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        #pay-button:hover {
            background-color: #d94326;
            transform: scale(1.05);
        }
        #pay-button:active {
            transform: scale(0.97);
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Konfirmasi Pembayaran</h2>
        <div class="info">
            Silakan klik tombol di bawah untuk menyelesaikan pembayaran pesanan Anda.<br>
            Kode Transaksi: <strong><?= htmlspecialchars($kodeTransaksi) ?></strong>
        </div>
        <button id="pay-button" style="background-color: #118C8C; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Bayar Sekarang</button>
        <div class="footer">Zamelda Store &copy; <?= date('Y') ?></div>
    </div>

    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            snap.pay("<?= $snapToken ?>", {
                onSuccess: function(result){
                    alert("Pembayaran berhasil!");
                    window.location.href = "transaksi-berhasil.php?kodeTransaksi=<?= $kodeTransaksi ?>";
                },
                onPending: function(result){
                    alert("Menunggu pembayaran...");
                    window.location.href = "transaksi-berhasil.php?kodeTransaksi=<?= $kodeTransaksi ?>";
                },
                onError: function(result){
                    alert("Pembayaran gagal!");
                    window.location.href = "transaksi-gagal.php?kodeTransaksi=<?= $kodeTransaksi ?>";
                },
                onClose: function(){
                    alert("Kamu menutup jendela pembayaran.");
                }
            });
        });
    </script>
</body>
</html>
