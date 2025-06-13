<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>No</th>
        <th>nama Barang</th>
        <th>kodeBarang</th>
        <th>Tanggal</th>
    </tr>
    </thead>
    <tbody>
    <?php
      include '../../../config/database.php';
        $kodePelanggan=$_POST['kodePelanggan'];
        // Menampilkan detail penyewaan
        $sql1="select * from detail_transaksi inner join transaksi on transaksi.kodeTransaksi=detail_transaksi.kodeTransaksi
        inner join barang on barang.kodeBarang=detail_transaksi.kodeBarang where transaksi.kodePelanggan='$kodePelanggan' and 
        
        
        .status='1'";
        $result=mysqli_query($kon,$sql1);
        $no=0;
        $status="";
        $jenis_denda="";
        
        //Menampilkan data dengan perulangan while
        while ($ambil = mysqli_fetch_array($result)):
        $no++;

    ?>
    <tr>
        <td><?php echo $no; ?></td>
        <td><?php echo $ambil['namaBarang']; ?></td>
        <td><?php echo $ambil['kodeTransaksi']; ?></td>
        <td class="text-center"><?php echo tanggal(date("Y-m-d",strtotime($ambil['tanggal_pinjam']))); ?></td>
    </tr>
        <?php endwhile;?>
    </tbody>
</table>


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