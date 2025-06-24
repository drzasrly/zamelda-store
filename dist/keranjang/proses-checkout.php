<?php
session_start();
include '../../config/database.php';

if (!isset($_POST['pilih']) || empty($_POST['pilih'])) {
    header("Location:../index.php?page=keranjang&error=tidak_ada_pilihan");
    exit;
}

$_SESSION['checkout'] = [
    'pilih' => $_POST['pilih'],
    'jumlah' => $_POST['jumlah'],
];

$pilih = $_POST['pilih'];
$jumlah = $_POST['jumlah'];
$kodePelanggan = $_SESSION['kodePengguna'];
$pelanggan = mysqli_fetch_assoc(mysqli_query($kon, "SELECT * FROM pelanggan WHERE kodePelanggan='$kodePelanggan'"));
$alamatUtama = mysqli_fetch_assoc(mysqli_query($kon, "SELECT * FROM alamat_pelanggan WHERE kodePelanggan='$kodePelanggan' AND label_alamat='Rumah' LIMIT 1"));
$provinsi = $alamatUtama['provinsi'];

$total = 0;
$beratTotal = 0;
foreach ($pilih as $idVarian) {
    $jumlahBeli = intval($jumlah[$idVarian]);
    $v = mysqli_fetch_assoc(mysqli_query($kon, "SELECT harga, berat FROM varianBarang WHERE idVarian='$idVarian'"));
    $total += $v['harga'] * $jumlahBeli;
    $beratTotal += $v['berat'] * $jumlahBeli;
}

$ongkir = 0;
if (isset($_SESSION['ongkir'])) {
    $ongkir = $_SESSION['ongkir'];
} else {
    $_SESSION['ongkir'] = 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Zamelda Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background-color: #bad9ce; }
        .container { max-width: 1000px; margin: 30px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px #0e6973; }
        .section-title { font-weight: bold; color:rgb(12, 86, 94); margin-bottom: 10px; }
        .produk-item { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .produk-info { display: flex; gap: 10px; }
        .produk-item img { width: 110px; height: 110px; border: 1px solid #ddd; border-radius: 5px; }
        .highlight { color: #f2bb16; font-weight: bold; }
        .btn-pesan {
            background-color: #f2bb16;         /* warna oranye dari palet */
            color: #1b1b1b;                    /* teks gelap agar kontras di tombol terang */
            border: none;
            padding: 10px;
            border-radius: 10px;              /* sudut kotak */
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(255, 145, 77, 0.3); /* bayangan halus */
            transition: all 0.3s ease;
            text-transform: uppercase;        /* biar lebih tegas */
        }

        .btn-pesan:hover {
            background-color: #ff7f2a;        /* sedikit lebih gelap saat hover */
            box-shadow: 0 6px 16px rgba(255, 145, 77, 0.5);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<div class="container">
    <form action="submit.php" method="post" id="checkoutForm">
        <div class="mb-4">
            <div class="section-title d-flex justify-content-between align-items-center">
                <span>Alamat Pengiriman</span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-warning btn-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalAlamat">
                        <i class="bi bi-pencil-square me-1"></i> <span style="color: #ff914d;">Ubah Alamat</span>
                    </button>
                    <button type="button" class="btn btn-sm d-flex align-items-center" style="background-color: #f2bb16; color: #1b1b1b;" data-bs-toggle="modal" data-bs-target="#modalTambahAlamat">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Alamat
                    </button>
                </div>
            </div>
            <div id="alamatTerpilih">
                <?php if ($alamatUtama): ?>
                    <strong><?= $alamatUtama['nama_penerima'] ?></strong> (<?= $alamatUtama['no_hp'] ?>)<br>
                    <?= $alamatUtama['alamat_detail'] . ', ' . $alamatUtama['kota'] . ', ' . $alamatUtama['provinsi']; ?><br>
                    <input type="hidden" name="idAlamat" id="idAlamat" value="<?= $alamatUtama['idAlamat'] ?>">
                <?php else: ?>
                    <p class="text-danger">Tidak ada alamat rumah! Silakan tambahkan.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="mb-4">
            <div class="section-title">Produk Dipesan</div>
            <?php
            foreach ($pilih as $idVarian):
                $jumlahBeli = intval($jumlah[$idVarian]);
                $q = mysqli_query($kon, "SELECT vb.*, b.namaBarang, gv.gambarVarian FROM varianBarang vb 
                                         JOIN barang b ON vb.kodeBarang = b.kodeBarang 
                                         LEFT JOIN gambarVarian gv ON vb.idGambarVarian = gv.idGambarVarian
                                         WHERE vb.idVarian='$idVarian'");
                $v = mysqli_fetch_assoc($q);
                if ($v['stok'] < $jumlahBeli) {
                    echo "<p class='highlight'>Stok tidak cukup untuk: {$v['namaBarang']} ({$v['stok']} tersedia)</p>";
                    continue;
                }
                $subtotal = $v['harga'] * $jumlahBeli;
            ?>
            <div class="produk-item">
                <div class="produk-info">
                    <img src="../barang/gambar/<?= $v['gambarVarian'] ?>" alt="<?= $v['namaBarang'] ?>">
                    <div>
                        <strong><?= $v['namaBarang'] ?></strong><br>
                        Varian: <?= $v['typeVarian'] ?>, Ukuran: <?= $v['size'] ?><br>
                        Harga: Rp<?= number_format($v['harga'], 0, ',', '.') ?><br>
                        Jumlah: <?= $jumlahBeli ?> pcs<br>
                        Subtotal: Rp<?= number_format($subtotal, 0, ',', '.') ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mb-4">
            <div class="section-title">Kurir Pengiriman</div>
            <select name="kurir" id="kurirSelect" class="form-select" required>
                <option value="jne">JNE</option>
                <option value="tiki">TIKI</option>
                <option value="pos">POS Indonesia</option>
            </select>
        </div>

        <div class="mb-4">
            <div class="section-title">Metode Pembayaran</div>
            <select name="metode" class="form-select" required>
                <option value="">-- Pilih Metode --</option>
                <option value="0">Bayar di Tempat (COD)</option>
                <option value="1">Transfer Bank</option>
                <option value="2">ShopeePay</option>
                <option value="3">Bayar di Alfamart</option>
                <option value="4">Bayar di Indomaret</option>
            </select>
        </div>

        <div class="mb-4">
            <div class="section-title">Total Pembayaran</div>
            <div class="d-flex justify-content-between">
                <span>Subtotal Pesanan</span>
                <span class="highlight">Rp<?= number_format($total, 0, ',', '.') ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Biaya Pengiriman</span>
                <span class="highlight" id="ongkirText">Rp<?= number_format($ongkir, 0, ',', '.') ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <strong>Total Pembayaran</strong>
                <strong class="highlight" id="totalBayar">Rp<?= number_format($total + $ongkir, 0, ',', '.') ?></strong>
            </div>
        </div>

        <input type="hidden" name="ongkir" id="ongkirInput" value="<?= $ongkir ?>">
        <input type="hidden" name="berat_total" id="beratTotal" value="<?= $beratTotal ?>">
        <input type="hidden" name="total_barang" value="<?= $total ?>">

        <?php foreach ($pilih as $idVarian): ?>
            <input type="hidden" name="pilih[]" value="<?= $idVarian ?>">
            <input type="hidden" name="jumlah[<?= $idVarian ?>]" value="<?= $jumlah[$idVarian] ?>">
        <?php endforeach; ?>

        <button type="submit" class="btn btn-pesan w-100">Buat Pesanan</button>
    </form>
</div>

<!-- Modal Alamat -->
<div class="modal fade" id="modalAlamat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Alamat Lain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                $daftarAlamat = mysqli_query($kon, "SELECT * FROM alamat_pelanggan WHERE kodePelanggan='$kodePelanggan'");
                while ($a = mysqli_fetch_assoc($daftarAlamat)) {
                    echo "<div class='mb-2'>
                            <input type='radio' name='pilihAlamat' value='{$a['idAlamat']}' data-provinsi='{$a['provinsi']}' onchange=\"pilihAlamat(this)\">
                            <label>
                                <strong>{$a['nama_penerima']}</strong> ({$a['no_hp']})<br>
                                {$a['alamat_detail']}, {$a['kota']}, {$a['provinsi']}
                            </label>
                          </div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
function pilihAlamat(radio) {
    const id = radio.value;
    const provinsi = radio.dataset.provinsi;

    $.getJSON('ongkir_provinsi.json', function (json) {
        const match = json.ongkir_per_provinsi.find(p => p.provinsi === provinsi);
        const ongkir = match ? match.ongkir : 0;
        const subtotal = <?= $total ?>;

        $('#idAlamat').val(id);
        $('#alamatTerpilih').html(
            radio.nextElementSibling.innerHTML + 
            `<input type="hidden" name="idAlamat" id="idAlamat" value="${id}">`
        );
        $('#ongkirText').text('Rp' + ongkir.toLocaleString('id-ID'));
        $('#totalBayar').text('Rp' + (subtotal + ongkir).toLocaleString('id-ID'));
        $('#ongkirInput').val(ongkir);
        $('#modalAlamat').modal('hide');
    });
}

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
