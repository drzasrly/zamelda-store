<?php
session_start();
include '../../config/database.php';

$kodeBarang = $_POST['kodeBarang'] ?? '';
$aksi = $_POST['aksi'] ?? '';

if ($kodeBarang != '') {
    $sql = "SELECT * FROM barang p
        INNER JOIN varianbarang s ON s.kodeBarang = p.kodeBarang
        INNER JOIN gambarvarian g ON g.kodeBarang = p.kodeBarang
        WHERE s.stok > 0 AND p.kodeBarang = '$kodeBarang'";

    $query = mysqli_query($kon, $sql);
    $data = mysqli_fetch_array($query);

    $itemArray = array(
        $data['kodeBarang'] => array(
            'kodeBarang' => $data['kodeBarang'],
            'idVarian' => $data['idVarian'], 
            'namaBarang' => $data['namaBarang'],
            'varian' => $data['typeVarian'], 
            'harga' => $data['harga'],
            'jumlah' => 1,
            'gambarvarian' => $data['gambarvarian']
        )
    );
}

switch ($aksi) {
    case "pilih_barang":
        if (!empty($_SESSION["cart_barang"])) {
            if (array_key_exists($kodeBarang, $_SESSION["cart_barang"])) {
                $_SESSION["cart_barang"][$kodeBarang]['jumlah'] += 1;
            } else {
                $_SESSION["cart_barang"] = array_merge($_SESSION["cart_barang"], $itemArray);
            }
        } else {
            $_SESSION["cart_barang"] = $itemArray;
        }
        break;

    case "hapus_barang":
        if (!empty($_SESSION["cart_barang"])) {
            unset($_SESSION["cart_barang"][$kodeBarang]);
            if (empty($_SESSION["cart_barang"])) {
                unset($_SESSION["cart_barang"]);
            }
        }
        break;
}
?>

<div class="card mb-4">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-sm-3">
                <button type="button" id="tombol_pilih_barang" class="btn btn-primary">Pilih Barang</button>
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th><input type="checkbox" id="pilih-semua"></th>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama Barang</th>
                    <th>Varian</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 0;
                $jumlah = 0;

                if (!empty($_SESSION["cart_barang"])):
                    foreach ($_SESSION["cart_barang"] as $item):
                        $no++;
                        $jumlah++;
                        $totalHarga = $item["harga"] * $item["jumlah"];
                ?>
                <input type="hidden" name="kodeBarang[]" class="kodeBarang" value="<?= $item["kodeBarang"]; ?>">
                <tr>
                    <td><input type="checkbox" class="cek-barang" value="<?= $item["kodeBarang"]; ?>"></td>
                    <td><?= $no; ?></td>
                    <td>
                        <?php if (!empty($item["gambarvarian"])): ?>
                            <img src="barang/gambar/<?= $item["gambarvarian"]; ?>" width="50">
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $item["namaBarang"]; ?></td>
                    <td><?= $item["varian"]; ?></td>
                    <td>Rp <?= number_format($item["harga"], 0, ',', '.'); ?></td>
                    <td><?= $item["jumlah"]; ?></td>
                    <td>Rp <?= number_format($totalHarga, 0, ',', '.'); ?></td>
                    <td>
                        <button type="button" kodeBarang="<?= $item["kodeBarang"]; ?>" class="hapus_barang btn btn-danger btn-circle">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>

        <?php 
        if (isset($_SESSION["maksimal_transaksi"]) && $_SESSION["maksimal_transaksi"] <= $jumlah) {
            echo "<script>document.getElementById('tombol_pilih_barang').disabled = true;</script>";
            echo "<span class='text-danger'>Telah mencapai batas maksimal transaksi</span>";
        }
        ?>
    </div>
</div>

<script>
$(document).ready(function() {
    // Hapus barang dari cart
    $('.hapus_barang').on('click', function() {
        var kodeBarang = $(this).attr("kodeBarang");
        $.post('transaksi/cart.php', { kodeBarang: kodeBarang, aksi: 'hapus_barang' }, function(data) {
            $('#tampil_cart').html(data);
        });
    });

    // Validasi saat klik simpan
    $('#simpan_transaksi').on('click', function() {
        if ($(".kodeBarang").length == 0) {
            alert('Belum ada barang yang dipilih!');
            return false;
        }
    });

    // Tampilkan modal daftar barang
    $('#tombol_pilih_barang').on('click', function() {
        $.post('transaksi/daftar-barang.php', {}, function(data) {
            $('#tampil_data').html(data);
            $('#judul').text('Pilih Barang');
            $('#modal').modal('show');
        });
    });

    // Pilih semua
    $('#pilih-semua').on('click', function() {
        $('.cek-barang').prop('checked', this.checked);
    });
});
</script>
