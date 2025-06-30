<?php
session_start();
include '../../config/database.php';

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
$barang_admin = mysqli_fetch_all($hasil, MYSQLI_ASSOC);

$barang_query = mysqli_query($kon, "
    SELECT 
        b.idBarang,
        b.kodeBarang,
        b.namaBarang,
        (SELECT gv.gambarvarian
         FROM varianbarang vb
         JOIN gambarvarian gv ON gv.idGambarVarian = vb.idGambarVarian
         WHERE vb.kodeBarang = b.kodeBarang
         ORDER BY vb.idVarian ASC
         LIMIT 1) AS gambarBarang,
        MIN(v.harga) as harga_min,
        MAX(v.harga) as harga_max,
        SUM(v.stok) as total_stok
    FROM barang b
    JOIN varianBarang v ON b.kodeBarang = v.kodeBarang
    GROUP BY b.kodeBarang
");


$barang_pelanggan = [];
while ($row = mysqli_fetch_assoc($barang_query)) {
    $barang_pelanggan[] = $row;
}
?>

<?php if (strtolower($_SESSION['level']) === 'penjual' or strtolower($_SESSION['level']) === 'admin'): ?>
    <!-- Admin / Penjual -->
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
                <button type="button" id="btn-tambah-barang" class="btn" style="background-color:rgb(31, 124, 161); color: white;">
                    <i class="fas fa-book fa-sm"></i> Tambah barang
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive col-sm-12">
        <table class="table table-hover table-striped table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Gambar</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($barang_admin as $no => $data): ?>
                    <tr>
                        <td><?= $no + 1 ?></td>
                        <td>
                            <img src="../dist/barang/gambar/<?= htmlspecialchars($data['gambarBarang']) ?>" width="60" alt="<?= htmlspecialchars($data['namaBarang']) ?>">
                        </td>
                        <td><?= htmlspecialchars($data['namaBarang']) ?></td>
                        <td>
                            <button type="button" class="btn-detail-barang btn btn-sm btn-info" idBarang="<?= $data['idBarang'] ?>" kodeBarang="<?= $data['kodeBarang'] ?>"><i class="fas fa-eye"></i></button>
                            <button type="button" class="btn-edit-barang btn btn-sm btn-warning" idBarang="<?= $data['idBarang'] ?>" kodeBarang="<?= $data['kodeBarang'] ?>"><i class="fas fa-edit"></i></button>
                            <a href="barang/hapus.php?idBarang=<?= $data['idBarang'] ?>&gambarBarang=<?= urlencode($data['gambarBarang']) ?>" class="btn-hapus btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="row row-cols-2 row-cols-md-5 gx-3 gy-3 justify-content-start px-2">
        <?php foreach ($barang_pelanggan as $data): ?>
            <div class="col">
                <div class="card card-barang h-100 shadow-sm border rounded-4 p-2 <?= $data['total_stok'] > 0 ? 'btn-detail-barang' : '' ?>"
                    style="<?= $data['total_stok'] > 0?>"
                    <?= $data['total_stok'] > 0 ? 'idBarang="' . $data['idBarang'] . '" kodeBarang="' . $data['kodeBarang'] . '"' : '' ?>>
                    <div class="img-container" style="position: relative;">
                        <img class="card-img-top product-img" 
                            src="../dist/barang/gambar/<?= htmlspecialchars($data['gambarBarang']) ?>" 
                            alt="<?= htmlspecialchars($data['namaBarang']) ?>">

                        <?php if ($data['total_stok'] == 0): ?>
                            <div class="stok-habis-overlay">
                                <span>Stok Habis</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body text-center">
                        <h6 class="card-title mb-1"><?= htmlspecialchars($data['namaBarang']) ?></h6>
                        <p class="text-danger fw-bold mb-0">
                            <?php
                            if ($data['harga_min'] == $data['harga_max']) {
                                echo "Rp" . number_format($data['harga_min'], 0, ',', '.');
                            } else {
                                echo "Rp" . number_format($data['harga_min'], 0, ',', '.') . 
                                     " - Rp" . number_format($data['harga_max'], 0, ',', '.');
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Modal -->
<div class="modal fade" id="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="namaBarang"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="tampil_data"></div>  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#btn-tambah-barang').on('click', function () {
        $.ajax({
            url: 'barang/tambah.php',
            method: 'post',
            success: function (data) {
                $('#tampil_data').html(data);
                $('#namaBarang').text('Tambah Barang Baru');
                $('#modal').modal('show');
            }
        });
    });

    $(document).on('click', '.btn-detail-barang', function () {
        var idBarang = $(this).attr("idBarang");
        var kodeBarang = $(this).attr("kodeBarang");

        $.ajax({
            url: 'barang/detail.php',
            method: 'post',
            data: { idBarang: idBarang },
            success: function (data) {
                $('#tampil_data').html(data);
                $('#namaBarang').text('Detail Barang #' + kodeBarang);
                $('#modal').modal('show');
            }
        });
    });

    $(document).on('click', '.btn-edit-barang', function () {
        var idBarang = $(this).attr("idBarang");
        var kodeBarang = $(this).attr("kodeBarang");

        $.ajax({
            url: 'barang/edit.php',
            method: 'POST',
            data: { idBarang: idBarang },
            success: function (data) {
                $('#tampil_data').html(data);
                $('#namaBarang').text('Edit Barang #' + kodeBarang);
                $('#modal').modal('show');
            },
            error: function () {
                alert('Gagal mengambil data.');
            }
        });
    });

    $(document).on('click', '.btn-hapus', function () {
        return confirm("Yakin ingin menghapus barang ini?");
    });
});
</script>

<style>
.card-barang {
    max-width: 220px;
    width: 100%;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
    border-radius: 16px;
    background-color: #fff;
    overflow: hidden;
    height: 100%;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    border: 1px solid #e0e0e0;

    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.card-barang:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.card-barang .img-container {
    width: 100%;
    aspect-ratio: 1 / 1;
    position: relative;
    overflow: hidden;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    background-color: #f8f9fa;
}

.card-barang .product-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    position: absolute;
    top: 0;
    left: 0;
}

.card-barang .card-body {
    padding: 10px;
    text-align: center;

    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    min-height: 90px;
}

.card-barang h6.card-title {
    font-size: 13px;
    font-weight: 600;
    min-height: 38px;
    margin-bottom: 6px;
    color: #333;
    line-height: 1.3;

    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.card-barang p {
    font-size: 12px;
    color: #C0392B;
    margin-bottom: 0;
}

/* Jarak antar card */
.row.row-cols-2.row-cols-md-4.gx-2.gy-4.justify-content-start.px-2 {
    --bs-gutter-x: 1rem;
    --bs-gutter-y: 2rem; /* Tambah jarak vertikal antar baris */
    margin-top: 1rem;
    margin-bottom: 2rem;
}

/* Pastikan setiap .col punya jarak bawah */
.col {
    margin-bottom: 20px;
}

.stok-habis-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(81, 81, 81, 0.6); 
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
    border-radius: 0.5rem;
    text-transform: uppercase;
}

</style>


