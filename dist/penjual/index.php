<script>
    $('title').text('Data Penjual');S
</script>

<?php
   // $idPenjual= $_SESSION["idPenjual"];

    if ($_SESSION["level"]!='penjual' and $_SESSION["level"]!='admin'):
        echo"<div class='alert alert-danger'>Anda tidak punya hak akses</div>";
        exit;
    endif;
?>
<main>
    <div class="container-fluid">
        <h2 class="mt-4">Data penjual</h2>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Data penjual</li>
        </ol>

        <?php
            //Validasi untuk menampilkan pesan pemberitahuan saat user menambah penjual
            if (isset($_GET['add'])) {
                if ($_GET['add']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> data penjual telah ditambah!</div>";
                }else if ($_GET['add']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data penjual gagal ditambahkan!</div>";
                }    
            }

            if (isset($_GET['edit'])) {
                if ($_GET['edit']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data penjual telah diupdate!</div>";
                }else if ($_GET['edit']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data penjual gagal diupdate!</div>";
                }    
            }
            if (isset($_GET['hapus'])) {
                if ($_GET['hapus']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data penjual telah dihapus!</div>";
                }else if ($_GET['hapus']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data penjual gagal dihapus!</div>";
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
            <!-- Tombol tambah penjual -->
            <button  class="btn-tambah btn btn-dark btn-icon-split"><span class="text">Tambah</span></button>
          </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="tabel_penjual" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <!-- <th>No</th> -->
                                <th>Kode Penjual</th>
                                <!-- <th>Nama</th>
                                <th>Alamat</th>
                                <th>No Telp</th> -->
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // include database
                            include '../config/database.php';
                            
                            $sql="SELECT * FROM penjual ORDER BY  idpenjual DESC";
                            //$sql=" SELECT * FROM  penjual";
                            $hasil=mysqli_query($kon,$sql);
                            $no=0;
                            //Menampilkan data dengan perulangan while
                            while ($data = mysqli_fetch_array($hasil)):
                            $no++;
                        ?>
                        <tr>
                            <!-- <td><?php echo $no; ?></td> -->
                            <td><?php echo $data['kodePenjual']; ?></td>
                            <!-- <td><?php echo $data['namaPenjual']; ?></td>
                            <td><?php echo $data['alamat']; ?></td>
                            <td><?php echo $data['noTelp']; ?></td> -->
                            <td>
                                <button class="detail-akun btn btn-primary btn-circle" kodePenjual="<?php echo $data['kodePenjual']; ?>" ><i class="fas fa-user"></i></button>
                                <!-- <button class="btn-edit btn btn-warning btn-circle" idPenjual="<?php echo $data['idPenjual']; ?>" kodePenjual="<?php echo $data['kodePenjual']; ?>" ><i class="fas fa-edit"></i></button> -->
                                <a href="penjual/hapus.php?idPenjual=<?php echo $data['idPenjual']; ?>&kodePenjual=<?php echo $data['kodePenjual']; ?>" class="btn-hapus btn btn-danger btn-circle" ><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <!-- bagian akhir (penutup) while -->
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

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
    $(document).ready(function(){
        $('#tabel_penjual').DataTable();
    });
</script>

<script>

    // Tambah penjual
    $('.btn-tambah').on('click',function(){
        var level = $(this).attr("level");
        $.ajax({
            url: 'penjual/tambah.php',
            method: 'post',
            data: {level:level},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Tambah penjual';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });


    // fungsi edit penjual
    $('.btn-edit').on('click',function(){

        var idPenjual = $(this).attr("idPenjual");
        var kodePenjual = $(this).attr("kodePenjual");
        $.ajax({
            url: 'penjual/edit.php',
            method: 'post',
            data: {idPenjual:idPenjual},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Edit penjual #'+kodePenjual;
            }
        });
            // Membuka modal
        $('#modal').modal('show');
    });

    // fungsi detail akun penjual
    $('.detail-akun').on('click',function(){

        var kodePenjual = $(this).attr("kodePenjual");
        $.ajax({
            url: 'penjual/detail-akun.php',
            method: 'post',
            data: {kodePenjual:kodePenjual},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Detail Akun';
            }
        });
            // Membuka modal
        $('#modal').modal('show');
    });


   // fungsi hapus penjual
   $('.btn-hapus').on('click',function(){
        konfirmasi=confirm("Yakin ingin menghapus penjual ini?")
        if (konfirmasi){
            return true;
        }else {
            return false;
        }
    });
</script>