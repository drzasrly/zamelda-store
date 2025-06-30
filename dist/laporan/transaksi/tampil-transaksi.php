<?php
session_start();
?>
<div class="card mb-4">
    <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="text-center">
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Kode transaksi</th>
                            <th rowspan="2">Nama pelanggan</th>
                            <th rowspan="2">Judul barang</th>
                            <th rowspan="2">Waktu transaksi</th>
                            <th rowspan="2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        // include database
                        include '../../../config/database.php';
                        $kondisi="";

                        if (!empty($_POST["dari_tanggal"]) && empty($_POST["sampai_tanggal"])) $kondisi= "where date(tglTransaksi)='".$_POST['dari_tanggal']."' ";
                        if (!empty($_POST["dari_tanggal"]) && !empty($_POST["sampai_tanggal"])) $kondisi= "where date(tglTransaksi) between '".$_POST['dari_tanggal']."' and '".$_POST['sampai_tanggal']."'";
                       
                        // perintah sql untuk menampilkan laporan transaksi jika level admin maka sistem hanya akan menampilkan transaksi yang dilakukan admin tersebut
                        if ($_SESSION["level"]=="admin"){
                                $idPengguna=$_SESSION["idPengguna"];
                                $sql="select p.kodeTransaksi,an.namaPelanggan,pk.namaBarang,dp.tglTransaksi,dp.status
                                from transaksi p
                                inner join pelanggan an  on an.kodePelanggan=p.kodePelanggan
                                inner join detail_transaksi dp on dp.kodeTransaksi=p.kodeTransaksi
                                inner join barang pk on pk.kodeBarang=dp.kodeBarang
                                $kondisi and status!='0'
                                order by dp.tglTransaksi asc";
                            }else {
                                $sql="select p.kodeTransaksi,an.namaPelanggan,pk.namaBarang,dp.tglTransaksi,dp.status
                                from transaksi p
                                inner join pelanggan an  on an.kodePelanggan=p.kodePelanggan
                                inner join detail_transaksi dp on dp.kodeTransaksi=p.kodeTransaksi
                                inner join barang pk on pk.kodeBarang=dp.kodeBarang
                                $kondisi and status!='0'
                                order by dp.tglTransaksi asc";
                            }
                        
                        $hasil=mysqli_query($kon,$sql);
                        $no=0;
                        $status='';
                        //Menampilkan data dengan perulangan while
                        while ($data = mysqli_fetch_array($hasil)):
                        $no++;
                        if ($data['status'] == 0) {
                                            $status = "<span class='badge badge-dark'>Belum Dibayar</span>";
                                        } else if ($data['status'] == 1) {
                                            $status = "<span class='badge badge-primary'>Dikemas</span>";
                                        } else if ($data['status'] == 2) {
                                            $status = "<span class='badge badge-success'>Dikirim</span>";
                                        } else if ($data['status'] == 3) {
                                            $status = "<span class='badge badge-success'>Selesai</span>";
                                        } else if ($data['status'] == 4) {
                                            $status = "<span class='badge badge-danger'>Batal</span>";
                                        }


                        if ($data['tglTransaksi']=='0000-00-00'){
                            $tglTransaksi="";
                        }else {
                            $tglTransaksi=date("d/m/Y",strtotime($data['tglTransaksi']));
                        }
                    ?>
                    <tr>
                        <td><?php echo $no; ?></td>
                        <td><?php echo $data['kodeTransaksi']; ?> </td>
                        <td><?php echo $data['namaPelanggan']; ?> </td>
                        <td><?php echo $data['namaBarang']; ?> </td>
                        <td class="text-center"><?php echo $tglTransaksi; ?></td>
                        <td><?php echo $status; ?></td>
                        
                    </tr>
                    <!-- bagian akhir (penutup) while -->
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <a href="laporan/transaksi/cetak-laporan.php?dari_tanggal=<?php if (!empty($_POST["dari_tanggal"])) echo $_POST["dari_tanggal"]; ?>&sampai_tanggal=<?php if (!empty($_POST["sampai_tanggal"])) echo $_POST["sampai_tanggal"]; ?>" target='blank' class="btn btn-primary btn-icon-split"><span class="text"><i class="fas fa-print fa-sm"></i> Cetak Invoice</span></a>
            <a href="laporan/transaksi/cetak-pdf.php?dari_tanggal=<?php if (!empty($_POST["dari_tanggal"])) echo $_POST["dari_tanggal"]; ?>&sampai_tanggal=<?php if (!empty($_POST["sampai_tanggal"])) echo $_POST["sampai_tanggal"]; ?>" target='blank' class="btn btn-danger btn-icon-pdf"><span class="text"><i class="fas fa-file-pdf fa-sm"></i> Export PDF</span></a>
	        <a href="laporan/transaksi/cetak-excel.php?dari_tanggal=<?php if (!empty($_POST["dari_tanggal"])) echo $_POST["dari_tanggal"]; ?>&sampai_tanggal=<?php if (!empty($_POST["sampai_tanggal"])) echo $_POST["sampai_tanggal"]; ?>" target='blank' class="btn btn-success btn-icon-pdf"><span class="text"><i class="fas fa-file-excel fa-sm"></i> Export Excel</span></a>
        </div>
    </div>
</div>