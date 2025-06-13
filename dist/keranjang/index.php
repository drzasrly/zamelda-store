<?php
include '../config/database.php';
//session_start(); 

?>

<script>
    $('title').text('Keranjang Barang');
</script>

<main>
    <div class="container-fluid">
        <h2 class="mt-4">Keranjang</h2>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Keranjang</li>
        </ol>

        <?php
        $idPengguna = $_SESSION['idPengguna'];

        if (!isset($_SESSION['cart_barang']) || empty($_SESSION['cart_barang'])) {
            $_SESSION['cart_barang'] = [];

            $result = mysqli_query($kon, "
                SELECT b.kodeBarang, b.namaBarang, v.idVarian, v.typeVarian, v.size, v.harga, v.stok, g.gambarvarian, k.jumlah
                FROM keranjang k
                INNER JOIN varianbarang v ON k.idVarian = v.idVarian
                INNER JOIN barang b ON v.kodeBarang = b.kodeBarang
                LEFT JOIN gambarvarian g ON v.idGambarVarian = g.idGambarVarian
                WHERE k.idPengguna = '$idPengguna'
            ");

            while ($row = mysqli_fetch_assoc($result)) {
                $idVarian = $row['idVarian'];
                $_SESSION['cart_barang'][$idVarian] = [
                    'kodeBarang' => $row['kodeBarang'],
                    'idVarian' => $idVarian,
                    'namaBarang' => $row['namaBarang'],
                    'typeVarian' => $row['typeVarian'],
                    'size' => $row['size'],
                    'harga' => $row['harga'],
                    'gambar' => $row['gambarvarian'],
                    'jumlah' => $row['jumlah']
                ];
            }
        }

        if (isset($_GET['idVarian'])) {
            $idVarian = $_GET['idVarian'];
            $jumlahBaru = isset($_GET['jumlah']) ? intval($_GET['jumlah']) : 1;

            $query = mysqli_query($kon, "
                SELECT b.kodeBarang, b.namaBarang, v.idVarian, v.typeVarian, v.size, v.harga, v.stok, g.gambarvarian 
                FROM varianbarang v
                INNER JOIN barang b ON v.kodeBarang = b.kodeBarang
                LEFT JOIN gambarvarian g ON v.idGambarVarian = g.idGambarVarian
                WHERE v.idVarian='$idVarian'
            ");
            $data = mysqli_fetch_array($query);

            if ($data) {
                if (!isset($_SESSION["cart_barang"])) $_SESSION["cart_barang"] = [];

                if (array_key_exists($idVarian, $_SESSION["cart_barang"])) {
                    $_SESSION["cart_barang"][$idVarian]['jumlah'] += $jumlahBaru;
                } else {
                    $_SESSION["cart_barang"][$idVarian] = [
                        'kodeBarang' => $data['kodeBarang'],
                        'idVarian' => $data['idVarian'],
                        'namaBarang' => $data['namaBarang'],
                        'typeVarian' => $data['typeVarian'],
                        'size' => $data['size'],
                        'harga' => $data['harga'],
                        'gambar' => $data['gambarvarian'],
                        'jumlah' => $jumlahBaru
                    ];
                }

                $cek = mysqli_query($kon, "SELECT * FROM keranjang WHERE idPengguna='$idPengguna' AND idVarian='$idVarian'");
                if (mysqli_num_rows($cek) > 0) {
                    mysqli_query($kon, "UPDATE keranjang SET jumlah = jumlah + $jumlahBaru WHERE idPengguna='$idPengguna' AND idVarian='$idVarian'");
                } else {
                    mysqli_query($kon, "INSERT INTO keranjang (idPengguna, idVarian, jumlah) VALUES ('$idPengguna', '$idVarian', '$jumlahBaru')");
                }
            }
            $_SESSION['terakhir_ditambahkan'] = $idVarian;

        }

        if (isset($_GET['aksi']) && $_GET['aksi'] === "hapus_barang" && isset($_GET['idVarian'])) {
            $idVarian = $_GET['idVarian'];
            unset($_SESSION["cart_barang"][$idVarian]);
            mysqli_query($kon, "DELETE FROM keranjang WHERE idPengguna='$idPengguna' AND idVarian='$idVarian'");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_jumlah'])) {
            foreach ($_POST['jumlah'] as $idVarian => $jumlah) {
                if (isset($_SESSION['cart_barang'][$idVarian])) {
                    $jumlah = max(1, intval($jumlah));
                    $_SESSION['cart_barang'][$idVarian]['jumlah'] = $jumlah;

                    mysqli_query($kon, "UPDATE keranjang SET jumlah = '$jumlah' WHERE idPengguna='$idPengguna' AND idVarian='$idVarian'");
                }
            }
        }
        ?>

        <div class="mb-3">
            <a href="index.php?page=barang" class="btn btn-dark">Pilih Barang</a>
        </div>

        <form method="POST">
        <div class="card mb-4">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <input type="hidden" name="idVarian[]" value="<?= $varian['idVarian'] ?>">
                    <input type="number" name="jumlah[]" value="<?= $varian['jumlah'] ?>">
                    <input type="hidden" name="harga[]" value="<?= $varian['harga'] ?>">

                    <thead>
                        <tr>
                            <th>Pilih</th>
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
                        if (!empty($_SESSION["cart_barang"])):
                            foreach ($_SESSION["cart_barang"] as $idVarian => $item):
                                $no++;
                                $jumlah = $item['jumlah'];
                                $total = $item['harga'] * $jumlah;
                        ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="pilih-item" name="pilih[]" value="<?php echo $idVarian; ?>"
                                <?php echo (isset($_SESSION['terakhir_ditambahkan']) && $_SESSION['terakhir_ditambahkan'] == $idVarian) ? 'checked' : ''; ?>>
                            </td>
                            <td><?php echo $no; ?></td>
                            <td>
                                <?php if (!empty($item['gambar'])): ?>
                                    <img src="../dist/barang/gambar/<?php echo htmlspecialchars($item['gambar']); ?>" style="width: 80px;">
                                <?php else: ?>
                                    <span>Tidak ada gambar</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['namaBarang']); ?></td>
                            <td><?php echo htmlspecialchars($item['typeVarian'] . ' / ' . ($item['size'] ?: '-')); ?></td>
                            <td>Rp<?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <div class="input-group" style="max-width: 120px; margin: auto;">
                                    <div class="input-group-prepend">
                                        <button type="button" class="btn btn-outline-secondary btn-sm minus-btn" data-id="<?php echo $idVarian; ?>">-</button>
                                    </div>
                                    <input type="number" name="jumlah[<?php echo $idVarian; ?>]" value="<?php echo $jumlah; ?>" class="form-control form-control-sm jumlah-input" min="1" data-id="<?php echo $idVarian; ?>">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary btn-sm plus-btn" data-id="<?php echo $idVarian; ?>">+</button>
                                    </div>
                                </div>
                            </td>
                            <td class="total-per-item" data-harga="<?php echo $item['harga']; ?>" data-id="<?php echo $idVarian; ?>">
                                Rp<?php echo number_format($total, 0, ',', '.'); ?>
                            </td>
                            <td>
                                <a href="index.php?page=keranjang&idVarian=<?php echo $idVarian; ?>&aksi=hapus_barang" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                            endforeach;
                        else:
                            echo '<tr><td colspan="9" class="text-center">Keranjang masih kosong.</td></tr>';
                        endif;
                        ?>
                    </tbody>
                </table>
                <button type="submit" name="update_jumlah" class="btn btn-primary">Perbarui Jumlah</button>
            </div>
        </div>

        <div class="text-right">
            <h5>Total Dipilih: <span id="total-dipilih">Rp0</span></h5>
            <button type="submit" formaction="keranjang/submit.php" class="btn btn-success" onclick="return konfirmasiCheckout();">Checkout</button>
        </div>
        </form>
    </div>
</main>

<script>
    function formatRupiah(angka) {
        return 'Rp' + angka.toLocaleString('id-ID');
    }

    function updateTotalPerItem(id) {
        const harga = parseInt(document.querySelector('.total-per-item[data-id="' + id + '"]').getAttribute('data-harga'));
        const jumlah = parseInt(document.querySelector('input[name="jumlah[' + id + ']"]').value);
        const totalElem = document.querySelector('.total-per-item[data-id="' + id + '"]');
        const total = harga * jumlah;
        totalElem.innerText = formatRupiah(total);
    }

    function hitungTotalDipilih() {
        let total = 0;
        document.querySelectorAll('.pilih-item:checked').forEach(item => {
            const id = item.value;
            const harga = parseInt(document.querySelector('.total-per-item[data-id="' + id + '"]').getAttribute('data-harga'));
            const jumlah = parseInt(document.querySelector('input[name="jumlah[' + id + ']"]').value);
            total += harga * jumlah;
        });
        document.getElementById('total-dipilih').innerText = formatRupiah(total);
    }

    document.querySelectorAll('.plus-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const input = document.querySelector('input[name="jumlah[' + id + ']"]');
            input.value = parseInt(input.value) + 1;
            updateTotalPerItem(id); 
            hitungTotalDipilih();
        });
    });

    document.querySelectorAll('.minus-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const input = document.querySelector('input[name="jumlah[' + id + ']"]');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                updateTotalPerItem(id); 
                hitungTotalDipilih();
            }
        });
    });

    document.querySelectorAll('.pilih-item, .jumlah-input').forEach(elem => {
        elem.addEventListener('input', () => {
            const id = elem.getAttribute('data-id');
            updateTotalPerItem(id); 
            hitungTotalDipilih();
        });

        elem.addEventListener('change', () => {
            const id = elem.getAttribute('data-id');
            updateTotalPerItem(id); 
            hitungTotalDipilih();
        });
    });

    function konfirmasiCheckout() {
        const selected = document.querySelectorAll('.pilih-item:checked');
        if (selected.length === 0) {
            alert('Pilih minimal satu barang untuk checkout.');
            return false;
        }
        return confirm('Lanjutkan ke transaksi untuk barang yang dipilih?');
    }

    window.addEventListener('DOMContentLoaded', hitungTotalDipilih);
</script>
