<?php
// session_start(); // Pastikan ini aktif di file utama
include '../config/database.php';

$idPengguna = $_SESSION['idPengguna'];
?>

<!-- Include SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $('title').text('Keranjang Barang');
</script>

<style>
    .highlight-row {
        background-color: #e0ffe0 !important;
    }
</style>

<main>
<div class="container-fluid">
    <h2 class="mt-4">Keranjang</h2>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Keranjang</li>
    </ol>

    <?php
    // Tambah item ke keranjang
    if (isset($_GET['idVarian'])) {
        $idVarian = $_GET['idVarian'];
        $jumlahBaru = isset($_GET['jumlah']) ? intval($_GET['jumlah']) : 1;

        $cek = mysqli_query($kon, "SELECT * FROM keranjang WHERE idPengguna='$idPengguna' AND idVarian='$idVarian'");
        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($kon, "UPDATE keranjang SET jumlah = jumlah + $jumlahBaru WHERE idPengguna='$idPengguna' AND idVarian='$idVarian'");
        } else {
            mysqli_query($kon, "INSERT INTO keranjang (idPengguna, idVarian, jumlah) VALUES ('$idPengguna', '$idVarian', $jumlahBaru)");
        }

        $_SESSION['baru_ditambahkan'] = $idVarian;
    }

    // Hapus item dari keranjang
    if (isset($_GET['aksi']) && $_GET['aksi'] === "hapus_barang" && isset($_GET['idVarian'])) {
        $idVarian = $_GET['idVarian'];
        mysqli_query($kon, "DELETE FROM keranjang WHERE idPengguna='$idPengguna' AND idVarian='$idVarian'");
        $_SESSION['barang_dihapus'] = true;
    }

    // Update jumlah
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_jumlah'])) {
        foreach ($_POST['jumlah'] as $idVarian => $jumlah) {
            $jumlah = max(1, intval($jumlah));
            mysqli_query($kon, "UPDATE keranjang SET jumlah=$jumlah WHERE idPengguna='$idPengguna' AND idVarian='$idVarian'");
        }
    }
    ?>

    <form method="POST" action="keranjang/proses-checkout.php">
        <div class="card mb-4">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
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
                        $query = mysqli_query($kon, "
                            SELECT k.idVarian, k.jumlah, 
                                   b.kodeBarang, b.namaBarang, 
                                   v.typeVarian, v.size, v.harga, v.stok, 
                                   g.gambarvarian 
                            FROM keranjang k
                            INNER JOIN varianbarang v ON k.idVarian = v.idVarian
                            INNER JOIN barang b ON v.kodeBarang = b.kodeBarang
                            LEFT JOIN gambarvarian g ON v.idGambarVarian = g.idGambarVarian
                            WHERE k.idPengguna = '$idPengguna'
                        ");

                        if (mysqli_num_rows($query) > 0):
                            while ($item = mysqli_fetch_assoc($query)):
                                $no++;
                                $idVarian = $item['idVarian'];
                                $jumlah = $item['jumlah'];
                                $total = $item['harga'] * $jumlah;
                                $isBaru = isset($_SESSION['baru_ditambahkan']) && $_SESSION['baru_ditambahkan'] == $idVarian;
                        ?>
                        <tr class="<?= $isBaru ? 'highlight-row' : '' ?>">
                            <td><input type="checkbox" class="pilih-item" name="pilih[]" value="<?= $idVarian ?>" checked></td>
                            <td><?= $no ?></td>
                            <td>
                                <?php if (!empty($item['gambarvarian'])): ?>
                                    <img src="../dist/barang/gambar/<?= htmlspecialchars($item['gambarvarian']) ?>" style="width: 80px;">
                                <?php else: ?>
                                    <span>Tidak ada gambar</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['namaBarang']) ?></td>
                            <td><?= htmlspecialchars($item['typeVarian'] . ' / ' . ($item['size'] ?: '-')) ?></td>
                            <td>Rp<?= number_format($item['harga'], 0, ',', '.') ?></td>
                            <td class="text-center">
                                <div class="input-group" style="max-width: 120px; margin: auto;">
                                    <div class="input-group-prepend">
                                        <button type="button" class="btn btn-outline-secondary btn-sm minus-btn" data-id="<?= $idVarian ?>">-</button>
                                    </div>
                                    <input type="number" name="jumlah[<?= $idVarian ?>]" value="<?= $jumlah ?>" class="form-control form-control-sm jumlah-input" min="1">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary btn-sm plus-btn" data-id="<?= $idVarian ?>">+</button>
                                    </div>
                                </div>
                            </td>
                            <td class="total-per-item" data-harga="<?= $item['harga'] ?>" data-id="<?= $idVarian ?>">
                                Rp<?= number_format($total, 0, ',', '.') ?>
                            </td>
                            <td>
                                <a href="index.php?page=keranjang&idVarian=<?= $idVarian ?>&aksi=hapus_barang" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr><td colspan="9" class="text-center">Keranjang masih kosong.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <button type="submit" name="update_jumlah" class="btn btn-primary">Perbarui Jumlah</button>
            </div>
        </div>

        <div class="text-right">
            <h5>Total Dipilih: <span id="total-dipilih">Rp0</span></h5>
            <button type="submit" class="btn btn-success" onclick="return konfirmasiCheckout();">Checkout</button>
        </div>
    </form>

    <?php
    // Bersihkan session notifikasi
    if (!isset($_GET['idVarian'])) {
        unset($_SESSION['baru_ditambahkan']);
    }
    ?>
</div>
</main>

<!-- NOTIFIKASI SweetAlert -->
<?php if (isset($_SESSION['baru_ditambahkan'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Barang berhasil ditambahkan ke keranjang.',
        timer: 2000,
        showConfirmButton: false
    });
});
</script>
<?php unset($_SESSION['baru_ditambahkan']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['barang_dihapus'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'success',
        title: 'Dihapus!',
        text: 'Barang telah dihapus dari keranjang.',
        timer: 2000,
        showConfirmButton: false
    });
});
</script>
<?php unset($_SESSION['barang_dihapus']); ?>
<?php endif; ?>

<!-- Script JS Update Total Harga dan Fungsi Tombol -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    function updateTotalDipilih() {
        let total = 0;

        document.querySelectorAll('.pilih-item:checked').forEach(function (checkbox) {
            const id = checkbox.value;
            const jumlah = parseInt(document.querySelector(`input[name='jumlah[${id}]']`)?.value || 0);
            const harga = parseInt(document.querySelector(`.total-per-item[data-id='${id}']`)?.dataset.harga || 0);
            total += harga * jumlah;
        });

        document.getElementById('total-dipilih').textContent = 'Rp' + total.toLocaleString('id-ID');
    }

    document.querySelectorAll('.pilih-item').forEach(function (checkbox) {
        checkbox.addEventListener('change', updateTotalDipilih);
    });

    document.querySelectorAll('.jumlah-input').forEach(function (input) {
        input.addEventListener('input', updateTotalDipilih);
    });

    document.querySelectorAll('.minus-btn, .plus-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const input = document.querySelector(`input[name='jumlah[${id}]']`);
            let jumlah = parseInt(input.value);

            if (this.classList.contains('minus-btn')) {
                jumlah = Math.max(1, jumlah - 1);
            } else {
                jumlah++;
            }

            input.value = jumlah;
            updateTotalDipilih();
        });
    });

    document.getElementById('pilih-semua')?.addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('.pilih-item').forEach(cb => cb.checked = checked);
        updateTotalDipilih();
    });

    updateTotalDipilih();
});

function konfirmasiCheckout() {
    return confirm("Yakin ingin melanjutkan ke checkout?");
}
</script>
