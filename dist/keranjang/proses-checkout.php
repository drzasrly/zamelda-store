<?php
session_start();
include '../../config/database.php';

if (isset($_POST['tambah_alamat'])) {
    $kodePelanggan = $_POST['kodePelanggan'];
    $label = $_POST['label_alamat'];
    $nama = $_POST['nama_penerima'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat_detail'];
    $kota = $_POST['kota'];
    $provinsi = $_POST['provinsi'];

    $query = "INSERT INTO alamat_pelanggan (kodePelanggan, label_alamat, nama_penerima, no_hp, alamat_detail, kota, provinsi) 
              VALUES ('$kodePelanggan', '$label', '$nama', '$no_hp', '$alamat', '$kota', '$provinsi')";

    if (mysqli_query($kon, $query)) {
        header("Location: proses-checkout.php"); // Kembali ke halaman checkout TANPA memproses ulang
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan alamat!');window.location.href='proses-checkout.php';</script>";
        exit;
    }
}

if (!isset($_POST['pilih']) || empty($_POST['pilih'])) {
    if (!isset($_SESSION['checkout'])) {
        header("Location:../index.php?page=keranjang&error=tidak_ada_pilihan");
        exit;
    }
    $pilih = $_SESSION['checkout']['pilih'];
    $jumlah = $_SESSION['checkout']['jumlah'];
} else {
    $_SESSION['checkout'] = [
        'pilih' => $_POST['pilih'],
        'jumlah' => $_POST['jumlah'],
    ];
    $pilih = $_POST['pilih'];
    $jumlah = $_POST['jumlah'];
}

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

$_SESSION['total_bayar'] = $total + $ongkir;
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
            background-color: #f2bb16;        
            color: #1b1b1b;                   
            border: none;
            padding: 10px;
            border-radius: 10px;           
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(255, 145, 77, 0.3); 
            transition: all 0.3s ease;
            text-transform: uppercase;        
        }

        .btn-pesan:hover {
            background-color: #ff7f2a;       
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
                <option value="jnt">JNT</option>
                <option value="anteraja">Anter Aja</option>
                <option value="sicepat">SICEPAT</option>
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

<!-- Modal Tambah Alamat -->
<div class="modal fade" id="modalTambahAlamat" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="proses-checkout.php" method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Alamat Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="kodePelanggan" value="<?= $kodePelanggan ?>">

        <div class="mb-3">
  <label class="form-label">Label Alamat</label>
  <input type="text" name="label_alamat" class="form-control" required>
</div>

<div class="mb-3">
  <label class="form-label">Nama Penerima</label>
  <input type="text" name="nama_penerima" class="form-control" required>
</div>

<div class="mb-3">
  <label class="form-label">No HP</label>
  <input type="text" name="no_hp" class="form-control" required>
</div>

<!-- ALAMAT MANUAL -->
<div class="mb-3">
  <label class="form-label">Detail Alamat (Jalan, RT/RW)</label>
  <textarea name="alamat_detail_manual" class="form-control" required></textarea>
</div>

<!-- PROVINSI -->
<div class="mb-3">
  <label class="form-label">Provinsi</label>
  <select id="provinsi" name="provinsi" class="form-select" required>
    <option value="">-- Pilih Provinsi --</option>
  </select>
</div>

<!-- KOTA -->
<div class="mb-3">
  <label class="form-label">Kota/Kabupaten</label>
  <select id="kota" name="kota" class="form-select" required>
    <option value="">-- Pilih Kota --</option>
  </select>
</div>

<!-- KECAMATAN -->
<div class="mb-3">
  <label class="form-label">Kecamatan</label>
  <select id="kecamatan" class="form-select" required>
    <option value="">-- Pilih Kecamatan --</option>
  </select>
</div>

<!-- KELURAHAN -->
<div class="mb-3">
  <label class="form-label">Kelurahan</label>
  <select id="kelurahan" class="form-select" required>
    <option value="">-- Pilih Kelurahan --</option>
  </select>
</div>

<!-- HIDDEN COMBINED FIELD -->
<input type="hidden" name="alamat_detail" id="alamat_detail">


      </div>
      <div class="modal-footer">
        <button type="submit" name="tambah_alamat" class="btn btn-success">Simpan Alamat</button>
      </div>
    </form>
  </div>
</div>

<script>
function hitungOngkir() {
    const provinsi = $('#idAlamat').data('provinsi');
    const kurir = $('#kurirSelect').val();
    const subtotal = <?= $total ?>;

    $.getJSON('ongkir_kurir.json', function(json) {
        const match = json.ongkir_per_kurir.find(
            o => o.provinsi === provinsi && o.kurir === kurir
        );
        const ongkir = match ? match.ongkir : 0;

        $('#ongkirText').text('Rp' + ongkir.toLocaleString('id-ID'));
        $('#totalBayar').text('Rp' + (subtotal + ongkir).toLocaleString('id-ID'));
        $('#ongkirInput').val(ongkir);
    });
}

$('#kurirSelect').on('change', hitungOngkir);

function pilihAlamat(radio) {
    const id = radio.value;
    const provinsi = radio.dataset.provinsi;

    $('#idAlamat').val(id).data('provinsi', provinsi); 
    $('#alamatTerpilih').html(
        radio.nextElementSibling.innerHTML + 
        `<input type="hidden" name="idAlamat" id="idAlamat" value="${id}" data-provinsi="${provinsi}">`
    );
    
    $('#modalAlamat').modal('hide');

    hitungOngkir(); 
}
</script>

</script>
<script>
function toTitleCase(str) {
  return str.toLowerCase().split(' ').map(function(word) {
    if (["dan", "di", "ke", "dari", "yang", "untuk"].includes(word)) {
      return word;
    }
    return word.charAt(0).toUpperCase() + word.slice(1);
  }).join(' ');
}

function removePrefixKotaKabupaten(name) {
  return name.replace(/^(Kota|Kabupaten)\s+/i, '').trim();
}

$(document).ready(function () {
    $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json", function(data) {
        data.forEach(function (prov) {
            const provName = toTitleCase(prov.name);
            $('#provinsi').append(`<option value="${provName}" data-id="${prov.id}">${provName}</option>`);
        });
    });

    $('#provinsi').on('change', function () {
        const id = $(this).find(':selected').data('id');
        $('#kota').html('<option>Loading...</option>');
        $('#kecamatan').html('<option value="">-- Pilih Kecamatan --</option>');
        $('#kelurahan').html('<option value="">-- Pilih Kelurahan --</option>');

        $.getJSON(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${id}.json`, function (data) {
            $('#kota').html('<option value="">-- Pilih Kota --</option>');
            data.forEach(kota => {
                const namaKota = toTitleCase(removePrefixKotaKabupaten(kota.name));
                $('#kota').append(`<option value="${namaKota}" data-id="${kota.id}">${namaKota}</option>`);
            });
        });
    });

    $('#kota').on('change', function () {
        const id = $(this).find(':selected').data('id');
        $('#kecamatan').html('<option>Loading...</option>');
        $('#kelurahan').html('<option value="">-- Pilih Kelurahan --</option>');

        $.getJSON(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${id}.json`, function (data) {
            $('#kecamatan').html('<option value="">-- Pilih Kecamatan --</option>');
            data.forEach(kec => {
                const namaKec = toTitleCase(kec.name);
                $('#kecamatan').append(`<option value="${namaKec}" data-id="${kec.id}">${namaKec}</option>`);
            });
        });
    });

    $('#kecamatan').on('change', function () {
        const id = $(this).find(':selected').data('id');
        $('#kelurahan').html('<option>Loading...</option>');

        $.getJSON(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${id}.json`, function (data) {
            $('#kelurahan').html('<option value="">-- Pilih Kelurahan --</option>');
            data.forEach(kel => {
                const namaKel = toTitleCase(kel.name);
                $('#kelurahan').append(`<option value="${namaKel}">${namaKel}</option>`);
            });
        });
    });

    $('form').on('submit', function () {
        const jalan = toTitleCase($('textarea[name="alamat_detail_manual"]').val());
        const kel = toTitleCase($('#kelurahan').val());
        const kec = toTitleCase($('#kecamatan').val());
        const kota = toTitleCase($('#kota').val());
        const prov = toTitleCase($('#provinsi').val());

        $('#alamat_detail').val(`${jalan}, Kel. ${kel}, Kec. ${kec}, ${kota}, ${prov}`);
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>