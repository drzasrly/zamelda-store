<script>
    $('title').text('Detail transaksi');
</script>


<main>
    <div class="container-fluid">
        <h2 class="mt-4">Detail transaksi</h2>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Detail transaksi</li>
        </ol>
        <?php
            if (isset($_GET['edit-pelanggan'])) {
                if ($_GET['edit-pelanggan']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Pelanggan yang meminjam barang berhasil diupdate</div>";
                } else if ($_GET['edit-pelanggan']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Pelanggan yang meminjam barang gagal diupdate</div>";
                }   
            }
        ?>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary" id="judul_grafik" >Informasi Data Pelanggan</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table">
                                    <tbody>
                                    <?php
                                        include '../config/database.php';
                                        $kodeTransaksi=$_GET['kodeTransaksi'];
                                        $sql="select *
                                        from transaksi p
                                        left join pelanggan an on an.kodePelanggan=p.kodePelanggan
                                        left join detail_transaksi dp on dp.kodeTransaksi=p.kodeTransaksi
                                        left join barang pk on pk.kodeBarang=dp.kodeBarang
                                        where p.kodeTransaksi='$kodeTransaksi'";
                                        $query = mysqli_query($kon,$sql);    
                                        $ambil = mysqli_fetch_array($query);
                                        $kodePelanggan=$ambil['kodePelanggan'];
                                    ?>
            
                                    <tr>
                                        <td>Nama</td>
                                        <td>: <?php echo $ambil['namaPelanggan'];?></td>
                                    </tr>
                                    <tr>
                                        <td>No Telp</td>
                                        <td>: <?php echo $ambil['noTelp'];?></td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>: <?php echo $ambil['email'];?></td>
                                    </tr>
                                    <tr>
                                        <td>Alamat</td>
                                        <td>: <?php echo  $ambil['alamat'];?></td>
                                    </tr>
                                    <tr>
                                        <?php if (strtolower($_SESSION['level'] ?? '') != 'Pelanggan'): ?>
                                        <td colspan="2">
                                            <button class="btn btn-warning btn-circle" id="tombol_edit_pelanggan" kodeTransaksi="<?php echo $_GET['kodeTransaksi'];?>"  kodePelanggan="<?php echo $ambil['kodePelanggan'];?>" ><i class="fas fa-edit"></i></button>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                    </tbody>
                                </table>    
                            </div>
                        </div>
                    </div>


                </div>
                <div class="row" id="bagian_detail_transaksi">
                    <div class="col-sm-12">
                        <div class="card mb-4">
                            <div class="card-body">
                            <?php
                                    //Validasi untuk menampilkan pesan pemberitahuan saat user menambah penyewaan baru
                                    if (isset($_GET['edit-transaksi'])) {
                                        if ($_GET['edit-transaksi']=='berhasil'){
                                            echo"<div class='alert alert-success'><strong>Berhasil!</strong> barang berhasil diupdate</div>";
                                        } else if ($_GET['edit-transaksi']=='gagal'){
                                            echo"<div class='alert alert-danger'><strong>Gagal!</strong> barang gagal diupdate</div>";
                                        }   
                                    }
                                    //Validasi untuk menampilkan pesan pemberitahuan saat user menghapus penyewaan
                                    if (isset($_GET['hapus-transaksi'])) {
                                        if ($_GET['hapus-transaksi']=='berhasil'){
                                            echo"<div class='alert alert-success'><strong>Berhasil!</strong> barang telah dihapus</div>";
                                        } else if ($_GET['hapus-transaksi']=='gagal'){
                                            echo"<div class='alert alert-danger'><strong>Gagal!</strong> barang gagal dihapus</div>";
                                        }    
                                    }

                                    //Validasi untuk menampilkan pesan pemberitahuan saat user menambah penyewaan baru
                                    if (isset($_GET['konfirmasi'])) {
                                        if ($_GET['konfirmasi']=='berhasil'){
                                            echo"<div class='alert alert-success'><strong>Berhasil!</strong> Status transaksi telah ditetapkan</div>";
                                        } else if ($_GET['konfirmasi']=='gagal'){
                                            echo"<div class='alert alert-danger'><strong>Gagal!</strong> Status transaksi gagal ditetapkan</div>";
                                        }   
                                    }

                                    if (isset($_GET['konfirmasi'])) {
                                        if ($_GET['konfirmasi']=='tolak'){
                                            echo"<div class='alert alert-warning'><strong>Gagal!</strong> Tindakan ditolak karena telah mencapai batas maksimal transaksi. <a href='#' kodePelanggan='". $kodePelanggan."' id='lihat_detail_transaksi'>Lihat daftar barang yang sedang dipinjam</a></div>";
                                        } 
                                    }

                                ?>

                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Barang</th>
                                        <th rowspan="2">Waktu transaksi</th>
                                        <th rowspan="2">Status</th>
                                        <?php if (!isset($_SESSION['level']) || $_SESSION['level'] != 'Pelanggan'): ?>
                                            <?php echo "<th rowspan='2'>Aksi</th>";?>
                                        <?php endif; 
                                        ?>
                                        <!-- <th rowspan="2">Aksi</th> -->
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        include '../config/database.php';
                                        $kodeTransaksi=$_GET['kodeTransaksi'];
                                        // Menampilkan detail penyewaan
                                        $sql1="select * from detail_transaksi inner join transaksi on transaksi.kodeTransaksi=detail_transaksi.kodeTransaksi
                                        inner join barang on barang.kodeBarang=detail_transaksi.kodeBarang where transaksi.kodeTransaksi='$kodeTransaksi'";
                                        $result=mysqli_query($kon,$sql1);
                                        
                                        //Menampilkan data dengan perulangan while
                                        while ($ambil = mysqli_fetch_array($result)):
                                        $no = 1;
                                        while ($row = mysqli_fetch_array($hasil)) {
                                            echo $no++;
                                        }


                                        if ($ambil['status']==0){
                                            $status="<span class='badge badge-dark'>Belum Dibayar</span>";
                                        }else if ($ambil['status']==1) {
                                            $status="<span class='badge badge-primary'>Dikemas</span>";
                                        }else if ($ambil['status']==2){
                                            $status="<span class='badge badge-success'>Dikirm</span>";
                                        }
                                        else if ($ambil['status']==3){
                                            $status="<span class='badge badge-danger'>Selesai</span>";
                                        } 
                                        else if ($ambil['status']==4){
                                            $status="<span class='badge badge-danger'>Batal</span>";
                                        }
                                        
                                        


                                        if ($ambil['tanggal']=='0000-00-00'){
                                            $tanggal="";
                                        }else {
                                            $tanggal=tanggal(date("Y-m-d",strtotime($ambil['tanggal'])));
                                        }
                                       
                                
                                    ?>
                                    <tr>
                                        <td><?php echo $no; ?></td>
                                        <td><?php echo $ambil['namaBarang']; ?></td>
                                        <td><?php echo $tanggal; ?></td>
                                        <td><?php echo $status; ?></td>
                                        <?php if (!isset($_SESSION['level']) || $_SESSION['level'] != 'Pelanggan'): ?>
                                        <td>
                                            <button class="tombol_konfirmasi btn btn-primary btn-circle" kodePelanggan="<?php echo $kodePelanggan; ?>" kodeBarang="<?php echo $ambil['kodeBarang']; ?>"  id_detail_transaksi="<?php echo $ambil['id_detail_transaksi']; ?>"  kodeTransaksi="<?php echo $_GET['kodeTransaksi']; ?>"  tanggal="<?php echo $ambil['tanggal']; ?>" status="<?php echo $ambil['status'];?>"><i class="fas fa-check"></i></button>
                                            <button class="tombol_edit_transaksi btn btn-warning btn-circle" id_detail_transaksi="<?php echo $ambil['id_detail_transaksi']; ?>" kodeTransaksi="<?php echo $_GET['kodeTransaksi']; ?>"  kodeBarang="<?php echo $ambil['kodeBarang']; ?>" ><i class="fas fa-edit"></i></button>
                                            <a href="transaksi/detail-transaksi/hapus-transaksi.php?kodeTransaksi=<?php echo $_GET['kodeTransaksi']; ?>&id_detail_transaksi=<?php echo $ambil['id_detail_transaksi'];?>" class="btn-hapus-transaksi btn btn-danger btn-circle" ><i class="fas fa-trash"></i></a>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                        <?php endwhile;?>
                                    </tbody>
                                </table>
                                <a href="transaksi/detail-transaksi/invoice.php?kodeTransaksi=<?php echo $kodeTransaksi; ?>" target='blank' class="btn btn-dark btn-icon-pdf"><span class="text"><i class="fas fa-print fa-sm"></i> Cetak</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php 
    //Membuat format tanggal
    function tanggal($tanggal)
    {
        $bulan = array (1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $split = explode('-', $tanggal);
        return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
    }
?>

<input type="hidden" name="kodeTransaksi" id="kodeTransaksi" value="<?php echo  $_GET['kodeTransaksi'];?>"/>
<!-- Modal -->
<div class="modal fade" id="modal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Bagian header -->
      <div class="modal-header">
        <h4 class="modal-title" id="judul"></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Bagian body -->
      <div class="modal-body">
        
        <div id="tampil_data">
          <!-- Data akan ditampilkan disini dengan AJAX -->                   
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
  
    // edit transaksi
    $('.tombol_edit_transaksi').on('click',function(){
        var id_detail_transaksi = $(this).attr("id_detail_transaksi");
        var kodeTransaksi = $(this).attr("kodeTransaksi");
        var kodeBarang = $(this).attr("kodeBarang");
        var tanggal = $(this).attr("tanggal");
        $.ajax({
            url: 'transaksi/detail-transaksi/edit-transaksi.php',
            method: 'post',
            data: {id_detail_transaksi:id_detail_transaksi,kodeTransaksi:kodeTransaksi,kodeBarang:kodeBarang,tanggal:tanggal},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Edit barang';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });


    // konfirmasi
    $('.tombol_konfirmasi').on('click',function(){
        var kodePelanggan = $(this).attr("kodePelanggan");
        var id_detail_transaksi = $(this).attr("id_detail_transaksi");
        var kodeTransaksi = $(this).attr("kodeTransaksi");
        var kodeBarang = $(this).attr("kodeBarang");
        var tanggal = $(this).attr("tanggal");
        var status = $(this).attr("status");
        

        $.ajax({
            url: 'transaksi/detail-transaksi/konfirmasi.php',
            method: 'post',
            data: {kodePelanggan:kodePelanggan,id_detail_transaksi:id_detail_transaksi,kodeTransaksi:kodeTransaksi,kodeBarang:kodeBarang,tanggal:tanggal,status:status},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Konfirmasi transaksi';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });

    // edit penyewa
     $('#tombol_edit_Pelanggan').on('click',function(){
     
        var kodeTransaksi = $(this).attr("kodeTransaksi");
        var kodePelanggan = $(this).attr("kodePelanggan");
        $.ajax({
            url: 'transaksi/detail-transaksi/edit-pelanggan.php',
            method: 'post',
            data: {kodeTransaksi:kodeTransaksi,kodePelanggan:kodePelanggan},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Edit transaksi barang';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });

    // fungsi hapus transaksi
    $('.btn-hapus-transaksi').on('click',function(){
        konfirmasi=confirm("Yakin ingin menghapus data transaksi ini?")
        if (konfirmasi){
            return true;
        }else {
            return false;
        }
    });


        // lihat transaksi
        $('#lihat_detail_transaksi').on('click',function(){
        var kodePelanggan = $(this).attr("kodePelanggan");
        $.ajax({
            url: 'transaksi/detail-transaksi/data-barang.php',
            method: 'post',
            data: {kodePelanggan:kodePelanggan},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Daftar barang yang Sedang Dipinjam';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });



</script>



