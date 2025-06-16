<div class="table-responsive">
    <table class="table table-striped table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead class="text-center">
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Kode</th>
                <th rowspan="2">Barang</th>
                <th rowspan="2">Varian</th>
                <th rowspan="2">tglTransaksi</th>
                <th rowspan="2">Status</th>
            </tr>
        </thead>

        <tbody>
        <?php
            // include database
            include '../../config/database.php';
            
            $kodePelanggan=$_POST['kodePelanggan'];
            $sql="select p.kodeTransaksi,an.namaPelanggan,b.*,v.*,dp.tglTransaksi,dp.status
            from transaksi p
            inner join pelanggan an  on an.kodePelanggan=p.kodePelanggan
            inner join detail_transaksi dp on dp.kodeTransaksi=p.kodeTransaksi
            inner join barang b on b.kodeBarang=dp.kodeBarang
            inner join varianBarang v on v.idVarian=dp.idVarian
            where an.kodePelanggan='$kodePelanggan'";
            
            $hasil=mysqli_query($kon,$sql);
            $jumlah = mysqli_num_rows($hasil);

            if ($jumlah==0){
                echo"<div class='alert alert-info'>pelanggan ini tidak memiliki riwayat transaksi sebelumnya.</div>";
            }

           
            $no=0;
            $status="";

            //Menampilkan data dengan perulangan while
            while ($data = mysqli_fetch_array($hasil)):
            $no++;

            if ($data['status']==0){
                $status="<span class='badge badge-dark'>Belum Dibayar</span>";
            }else if ($data['status']==1) {
                $status="<span class='badge badge-primary'>Dikemas</span>";
            }else if ($data['status']==2){
                $status="<span class='badge badge-success'>Dikirim</span>";
            }
            else if ($data['status']==3){
                $status="<span class='badge badge-danger'>Selesai</span>";
            }
            else if ($data['status']==4){
                $status="<span class='badge badge-danger'>Batal</span>";
            }

            if ($data['tglTransaksi']=='0000-00-00'){
                $tglTransaksi="";
            }else {
                $tglTransaksi=date("d/m/Y",strtotime($data['tglTransaksi']));
            }
        ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $data['kodeTransaksi']; ?></td>
            <td><?php echo $data['namaBarang']; ?></td>
            <td><?php echo $data['typeVarian']; ?></td>
            <td><?php echo $tglTransaksi; ?></td>
            <td><?php echo $status; ?></td>
        </tr>
        <!-- bagian akhir (penutup) while -->
        <?php endwhile; ?>

        </tbody>
    </table>
</div>