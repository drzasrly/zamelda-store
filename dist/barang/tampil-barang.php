<?php
session_start();
include '../../config/database.php';

$kategori = "";

if (isset($_POST['kategoriBarang']) && is_array($_POST['kategoriBarang'])) {
    foreach ($_POST['kategoriBarang'] as $value) {
        // Escape input untuk keamanan SQL injection
        $kategori .= "'" . mysqli_real_escape_string($kon, $value) . "',";
    }
    $kategori = rtrim($kategori, ',');
} else {
    $kategori = "0"; // default supaya query valid
}

// Buat query utama
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

$barangs = mysqli_fetch_all($hasil, MYSQLI_ASSOC);
?>

<!-- Lanjut dengan HTML & PHP seperti sebelumnya -->
<?php if (strtolower($_SESSION['level']) === 'penjual'): ?>
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
                <?php foreach ($barangs as $no => $data): ?>
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
    <div class="row">
        <?php foreach ($barangs as $data): ?>
            <div class="col-sm-2">
                <div class="card">
                    <img class="card-img-top img-fluid" src="../dist/barang/gambar/<?= htmlspecialchars($data['gambarBarang']) ?>" alt="<?= htmlspecialchars($data['namaBarang']) ?>">
                    <div class="card-body text-center">
                        <button type="button" class="btn-detail-barang btn btn-warning btn-block"
                                idBarang="<?= $data['idBarang'] ?>" 
                                kodeBarang="<?= $data['kodeBarang'] ?>">
                            Lihat
                        </button>
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

        <!-- Bagian body -->
        <div class="modal-body">
            <div id="tampil_data">

            </div>  
        </div>
        <!-- Bagian footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

        </div>
    </div>
</div>


<script>
    $('#btn-tambah-barang').on('click',function(){
        $.ajax({
            url: 'barang/tambah.php',
            method: 'post',
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("namaBarang").innerHTML='Tambah Barang Baru';
            }
        });
        $('#modal').modal('show');
    });

    $('.btn-detail-barang').on('click',function(){
		var idBarang = $(this).attr("idBarang");
        var kodeBarang = $(this).attr("kodeBarang");
        $.ajax({
            url: 'barang/detail.php',
            method: 'post',
			data: {idBarang:idBarang},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("namaBarang").innerHTML='Detail Barang #'+kodeBarang;
            }
        });
        $('#modal').modal('show');
    });


$(document).ready(function () {
  $(document).on('click', '.btn-edit-barang', function () {
    var idBarang = $(this).attr("idBarang");
    var kodeBarang = $(this).attr("kodeBarang");

    $.ajax({
      url: 'barang/edit.php',
      method: 'POST',
      data: { idBarang: idBarang },
      success: function (data) {
        $('#tampil_data').html(data);
        $('#namaBarang').text('Edit barang #' + kodeBarang);
        $('#modal').modal('show');
      },
      error: function () {
        alert('Gagal mengambil data.');
      }
    });
  });
});

    $('.btn-hapus').on('click',function(){
        konfirmasi=confirm("Yakin ingin menghapus barang ini?")
        if (konfirmasi){
            return true;
        }else {
            return false;
        }
    });
</script>