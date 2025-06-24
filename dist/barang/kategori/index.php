<script>
    $('title').text('Data kategori_barang');
</script>


<main>
    <div class="container-fluid">
        <h2 class="mt-4">Data Kategori Barang</h2>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Data Kategori Barang</li>
        </ol>

        <?php
            if (isset($_GET['add'])) {
                if ($_GET['add']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data Kategori Barang telah ditambah!</div>";
                }else if ($_GET['add']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data Kategori Barang gagal ditambahkan!</div>";
                }    
            }

            if (isset($_GET['edit'])) {
                if ($_GET['edit']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data Kategori Barang telah diupdate!</div>";
                }else if ($_GET['edit']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data Kategori Barang gagal diupdate!</div>";
                }    
            }
            if (isset($_GET['hapus'])) {
                if ($_GET['hapus']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data Kategori Barang telah dihapus!</div>";
                }else if ($_GET['hapus']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data Kategori Barang gagal dihapus!</div>";
                }    
            }
        ?>

        <div class="card mb-4">
          <div class="card-header py-3">
            <button type="button" class="btn btn-tambah" style="background-color: rgb(31, 124, 161); color: white;">Tambah</button>
  <span class="text" style="color: white;">Tambah</span>
</button>


          </div>
            <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered" id="tabel_kategori" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Kode</th>
                          <th>Nama Kategori</th>
                          <th width="10%">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php
                              include '../config/database.php';
                             $sql="select * from kategoribarang order by kodeKategori asc";
                              $hasil=mysqli_query($kon,$sql);
                              $no=0;
                              while ($data = mysqli_fetch_array($hasil)):
                              $no++;
                          ?>
                          <tr>
                              <td><?php echo $no; ?></td>
                              <td><?php echo $data['kodeKategori']; ?></td>
                              <td><?php echo $data['namaKategori']; ?></td>
                              <td>
                                  <button class="btn-edit btn btn-warning btn-circle" idKategori="<?php echo $data['idKategori']; ?>" kodeKategori="<?php echo $data['kodeKategori']; ?>"><i class="fas fa-edit"></i></button>
                                  <a href="barang/kategori/hapus-kategori.php?idKategori=<?php echo $data['idKategori']; ?>" class="btn-hapus btn btn-danger btn-circle" ><i class="fa fa-trash"></i></a>
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
        $('#tabel_kategori').DataTable();
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
            </div>  
        </div>
  
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

        </div>
    </div>
</div>

<script>

    $('.btn-tambah').on('click',function(){
        $.ajax({
            url: 'barang/kategori/tambah-kategori.php',
            method: 'post',
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("namaBarang").innerHTML='Tambah Kategori Barang Baru';
            }
        });
        $('#modal').modal('show');
    });


    $('.btn-edit').on('click',function(){

        var idKategori = $(this).attr("idKategori");
        var kodeKategori = $(this).attr("kodeKategori");
        $.ajax({
            url: 'barang/kategori/edit-kategori.php',
            method: 'post',
            data: {idKategori:idKategori},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("namaBarang").innerHTML='Edit Kategori Pustaka #'+kodeKategori;
            }
        });
        $('#modal').modal('show');
    });


    $('.btn-hapus').on('click',function(){
        konfirmasi=confirm("Yakin ingin menghapus kategori barang ini?")
        if (konfirmasi){
            return true;
        }else {
            return false;
        }
    });
</script>

