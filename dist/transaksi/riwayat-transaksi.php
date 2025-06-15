<!-- <div class="table-responsive">
    <table class="table table-striped table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead class="text-center">
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Kode</th>
                <th rowspan="2">Barang</th>
                <th colspan="2">Waktu transaksi</th>
                <th rowspan="2">Status</th>
            </tr>
            <tr>
                <th>Mulai</th>
                <th>Selesai</th>
            </tr>
        </thead>

        <tbody>
        <?php
            include '../../config/database.php';
            
            $kodePelanggan = $_POST['kodePelanggan'];

            $sql = "SELECT 
                        p.kodeTransaksi, 
                        an.kodePelanggan, 
                        pk.namaBarang, 
                        dp.status, 
                        dp.tglTransaksi, 
                        dp.tglSelesai 
                    FROM transaksi p
                    INNER JOIN pelanggan an ON an.kodePelanggan = p.kodePelanggan
                    INNER JOIN detail_transaksi dp ON dp.kodeTransaksi = p.kodeTransaksi
                    INNER JOIN barang pk ON pk.kodeBarang = dp.kodeBarang
                    WHERE an.kodePelanggan = '$kodePelanggan'";

            $hasil = mysqli_query($kon, $sql);
            $jumlah = mysqli_num_rows($hasil);

            if ($jumlah == 0){
                echo "<tr><td colspan='6' class='text-center'><div class='alert alert-info'>Pelanggan ini tidak memiliki riwayat transaksi sebelumnya.</div></td></tr>";
            }

            $no = 0;

            while ($data = mysqli_fetch_array($hasil)):
                $no++;

                // Format status
                if ($data['status'] == 0){
                    $status = "<span class='badge badge-dark'>Belum diambil</span>";
                } else if ($data['status'] == 1) {
                    $status = "<span class='badge badge-primary'>Sedang Dipinjam</span>";
                } else if ($data['status'] == 2){
                    $status = "<span class='badge badge-success'>Telah Selesai</span>";
                } else if ($data['status'] == 3){
                    $status = "<span class='badge badge-danger'>Batal</span>";
                } else {
                    $status = "<span class='badge badge-secondary'>Tidak diketahui</span>";
                }
        ?>
        <tr>
            <td class="text-center"><?php echo $no; ?></td>
            <td><?php echo $data['kodeTransaksi']; ?></td>
            <td><?php echo $data['namaBarang']; ?></td>
            <td><?php echo date('d-m-Y H:i', strtotime($data['tglTransaksi'])); ?></td>
            <td>
                <?php 
                if ($data['tglSelesai'] != null) {
                    echo date('d-m-Y H:i', strtotime($data['tglSelesai']));
                } else {
                    echo "-";
                }
                ?>
            </td>
            <td class="text-center"><?php echo $status; ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div> -->
