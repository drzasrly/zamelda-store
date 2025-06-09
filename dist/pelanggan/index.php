<script>
    $('title').text('Data pelanggan');
</script>

<?php
    //$kodePelanggan= $_SESSION["kodepelanggan"];

    if ($_SESSION["level"]!='penjual' and $_SESSION["level"]!='Penjual' and $_SESSION["level"]!='admin' and $_SESSION["level"]!='Admin'):
        echo"<div class='alert alert-danger'>Anda tidak punya hak akses</div>";
        exit;
    endif;
?>
<main>
    <div class="container-fluid">
        <h2 class="mt-4">Data pelanggan</h2>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Data Pelanggan</li>
        </ol>

    <?php
class DashboardBase {
    protected $total_transaksi = 0;
    protected $jumlah_pelanggan = 0;
    protected $jumlah_barang = 0;

    public function getSummary() {
        return [
            'total_transaksi' => $this->total_transaksi,
            'jumlah_pelanggan' => $this->jumlah_pelanggan,
            'jumlah_barang' => $this->jumlah_barang,
        ];
    }
}

            if (isset($_GET['add'])) {
                if ($_GET['add']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> data pelanggan telah ditambah!</div>";
                }else if ($_GET['add']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data pelanggan gagal ditambahkan!</div>";
                }    
            }

            if (isset($_GET['edit'])) {
                if ($_GET['edit']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data pelanggan telah diupdate!</div>";
                }else if ($_GET['edit']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data pelanggan gagal diupdate!</div>";
                }    
            }
            if (isset($_GET['hapus'])) {
                if ($_GET['hapus']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data pelanggan telah dihapus!</div>";
                }else if ($_GET['hapus']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data pelanggan gagal dihapus!</div>";
                }    
            }

            if (isset($_GET['detail-akun'])) {
                if ($_GET['detail-akun']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Akun pengguna telah disetting!</div>";
                }else if ($_GET['detail-akun']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Akun pengguna gagal disetting!</div>";
                }    
            }
        ?>

        <div class="card mb-4">
          <div class="card-header py-3">
            <!-- Tombol tambah pelanggan -->
            <button  class="btn-tambah btn btn-dark btn-icon-split"><span class="text">Tambah</span></button>
          </div>
            <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered" id="tabel_pelanggan" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <!-- <th>No</th> -->
                          <th>Kode</th>
                          <!-- <th>Nama</th>
                          <th>Email</th>
                          <th>No Telp</th>
                          <th>Alamat</th> -->
                          <th width="15%">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php
                              include '../config/database.php';
                              $sql="SELECT * FROM pelanggan ORDER BY  idpelanggan DESC";
                              $hasil=mysqli_query($kon,$sql);
                              $no=0;
                            
                              while ($data = mysqli_fetch_array($hasil)):
                              $no++;
                          ?>
                          <tr>
                              <!-- <td><?php echo $no; ?></td> -->
                              <td><?php echo $data['kodePelanggan']; ?></td>
                              <!-- <td><?php echo $data['namaPelanggan']; ?></td>
                              <td><?php echo $data['email']; ?></td>
                              <td><?php echo $data['noTelp']; ?></td>
                              <td><?php echo $data['alamat']; ?></td> -->
                              <td>
                                    <button class="detail-akun btn btn-primary btn-circle" data-kode-pelanggan="<?php echo $data['kodePelanggan']; ?>" ><i class="fas fa-user"></i></button>
                                    <!-- <button class="btn-edit btn btn-warning btn-circle" idPelanggan="<?php echo $data['idPelanggan']; ?>" kodePelanggan="<?php echo $data['kodePelanggan']; ?>" ><i class="fas fa-edit"></i></button> -->
                                    <a href="pelanggan/hapus.php?idPelanggan=<?php echo $data['idPelanggan']; ?>&kodePelanggan=<?php echo $data['kodePelanggan']; ?>" class="btn-hapus btn btn-danger btn-circle" ><i class="fa fa-trash"></i></a>
                              </td>
                          </tr>
                          <?php endwhile; ?>
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    $(document).ready(function(){
        $('#tabel_pelanggan').DataTable();
    });
</script>

<!-- Modal -->
<div class="modal fade" id="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        <div class="modal-header">
            <h4 class="modal-title" id="judul"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
            <div id="tampil_data">
                 <!-- Data akan di load menggunakan AJAX -->                   
            </div>  
        </div>
  
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

        </div>
    </div>
</div>

<script>

    // Tambah pelanggan
    $('.btn-tambah').on('click',function(){
        var level = $(this).attr("level");
        $.ajax({
            url: 'pelanggan/tambah.php',
            method: 'post',
            data: {level:level},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Tambah pelanggan';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });


    // fungsi edit pelanggan
    $('.btn-edit').on('click',function(){

        var idPelanggan = $(this).attr("idPelanggan");
        var kodePelanggan = $(this).attr("kodePelanggan");
        $.ajax({
            url: 'pelanggan/edit.php',
            method: 'post',
            data: {idPelanggan:idPelanggan, kodePelanggan:kodePelanggan},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Edit pelanggan #'+kodePelanggan;
            }
        });
            // Membuka modal
        $('#modal').modal('show');
    });

    // Untuk detail username dan password
$('.detail-akun').on('click', function () {
    var kodePelanggan = $(this).data("kodePelanggan");
    console.log("Kode pelanggan:", kodePelanggan);  // Debug: pastikan muncul di console
    $.ajax({
        url: 'pelanggan/detail-akun.php',
        method: 'POST',
        data: { kodePelanggan: kodePelanggan },
        success: function (data) {
            $('#tampil_data').html(data);
            document.getElementById("judul").innerHTML = 'Detail Akun';
            $('#modal').modal('show');
        }
    });
});



   // fungsi hapus pelanggan
   $('.btn-hapus').on('click',function(){
        konfirmasi=confirm("Yakin ingin menghapus pelanggan ini?")
        if (konfirmasi){
            return true;
        }else {
            return false;
        }
    });
</script>