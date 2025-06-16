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

// Data pelanggan
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
        MAX(v.harga) as harga_max
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
                <button type="button" id="btn-tambah-barang" class="btn btn-warning">
                    <i class="fas fa-book fa-sm"></i> Tambah barang
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive col-sm-12">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama Barang</th>
                    <th>Aksi</th>
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
    <!-- Pelanggan -->
    <div class="row">
        <?php foreach ($barang_pelanggan as $data): ?>
            <div class="col-sm-2 mb-4">
                <div class="card card-barang h-100 shadow-sm btn-detail-barang" style="cursor:pointer"
                    idBarang="<?= $data['idBarang'] ?>" 
                    kodeBarang="<?= $data['kodeBarang'] ?>">
                    
                    <img class="card-img-top img-fluid" src="../dist/barang/gambar/<?= htmlspecialchars($data['gambarBarang']) ?>" alt="<?= htmlspecialchars($data['namaBarang']) ?>">
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
    // Tambah barang
    $('#btn-tambah-barang').on('click',function(){
        $.ajax({
            url: 'barang/tambah.php',
            method: 'post',
            success:function(data){
                $('#tampil_data').html(data);  
                $("#namaBarang").text('Tambah Barang Baru');
                $('#modal').modal('show');
            }
        });
    });

    // Lihat detail barang (semua user)
    $(document).on('click', '.btn-detail-barang', function(){
        var idBarang = $(this).attr("idBarang");
        var kodeBarang = $(this).attr("kodeBarang");

        $.ajax({
            url: 'barang/detail.php',
            method: 'post',
            data: {idBarang: idBarang},
            success:function(data){
                $('#tampil_data').html(data);  
                $("#namaBarang").text('Detail Barang #' + kodeBarang);
                $('#modal').modal('show');
            }
        });
    });

    // Edit barang
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

    // Konfirmasi hapus
    $(document).on('click', '.btn-hapus', function(){
        return confirm("Yakin ingin menghapus barang ini?");
    });
</script>
<style>
    .card-barang {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        border: none;
        border-radius: 12px;
    }

    .card-barang:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        z-index: 5;
    }

    .card-barang img {
        border-top-left-radius: 12px;
        border-top-right-radius: 15px;
        height: 150px;
        object-fit: cover;
    }

    .card-barang .card-body {
        padding: 10px;
    }

    .card-barang h6 {
        font-size: 14px;
        font-weight: 600;
    }

    .card-barang p {
        font-size: 13px;
        margin-bottom: 4px;
    }
</style>

