<?php
session_start();
?>
<script>
    $('title').text('Data transaksi');
</script>
<main>
    <div class="container-fluid">
        <h2 class="mt-4">Data transaksi exacta</h2>
        <ol class="breadcrumb mb-4">
            <!-- <li class="breadcrumb-item active">Daftar Transaksi</li> -->
        </ol>
        <?php
            //Validasi untuk menampilkan pesan pemberitahuan saat user menambah transaksi
            if (isset($_GET['add'])) {
                if ($_GET['add']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data transaksi telah disimpan</div>";
                }else if ($_GET['add']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data transaksi gagal disimpan</div>";
                }    
            }

            //Validasi untuk menampilkan pesan pemberitahuan saat user menghapus transaksi
            if (isset($_GET['hapus'])) {
                if ($_GET['hapus']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data transaksi telah dihapus</div>";
                }else if ($_GET['hapus']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data transaksi gagal dihapus</div>";
                }    
            }

            if (isset($_GET['hapus-transaksi'])) {
                if ($_GET['hapus-transaksi']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data transaksi telah dihapus</div>";
                }else if ($_GET['hapus-transaksi']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data transaksi gagal dihapus</div>";
                }    
            }
        ?>

        <div class="card mb-4">
            <div class="card-header">
            <?php if ($_SESSION["level"]!="Pelanggan"): ?>
            <a href="index.php?page=input-transaksi" class="btn btn-primary" role="button">Input transaksi</a>
            <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tabel_transaksi" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode transaksi</th>
                                <th>Tanggal </th>
                                <th>Nama Anggota</th>
                                <th>Jumlah</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
        
                        <tbody>
                        <?php
                            // include database
                            include '../../config/database.php';
                            $sql="select p.kodeTransaksi,an.namaPelanggan,count(*) as jumlah, p                   .tanggal
                            from transaksi p
                            inner join pelanggan an on an.kodePelanggan=p.kodePelanggan
                            inner join detail_transaksi dp on dp.kodeTransaksi=p.kodeTransaksi
                            inner join barang pk on pk.kodeBarang=dp.kodeBarang
                            group by an.namaPelanggan,p.kodeTransaksi
                            order by p.kodeTransaksi desc";

                            $hasil=mysqli_query($kon,$sql);
                            $no=0;
                            //Menampilkan data dengan perulangan while
                            while ($data = mysqli_fetch_array($hasil)):
                            $no++;
                        ?>
                        <tr>
                            <td><?php echo $no; ?></td>
                            <td><?php echo $data['kodeTransaksi']; ?></td>
                            <td>
                                <?php
                                    
                                        echo  tanggal(date('Y-m-d', strtotime($data['tanggal']))); 
                                   
                                ?>
                            </td>
                            <td><?php echo $data['namaPelanggan']; ?></td>
                            <td><?php echo $data['jumlah']; ?> Barang</td>
                            <td>
                                <a href="index.php?page=detail-transaksi&kodeTransaksi=<?php echo $data['kodeTransaksi']; ?>" class="btn btn-success btn-circle"><i class="fas fa-mouse-pointer"></i></a>
                                <a href="transaksi/hapus-transaksi.php?kodeTransaksi=<?php echo $data['kodeTransaksi']; ?>" class="btn-hapus-transaksi btn btn-danger btn-circle" ><i class="fas fa-trash"></i></a>
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
  <div class="modal-dialog modal-sm">
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
    $(document).ready(function(){
        $('#tabel_transaksi').DataTable();
    });
</script>

<script>

   // fungsi hapus transaksi
   $('.btn-hapus-transaksi').on('click',function(){
        konfirmasi=confirm("Yakin ingin menghapus data transaksi ini?")
        if (konfirmasi){
            return true;
        }else {
            return false;
        }
    });
</script>

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