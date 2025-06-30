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
                <th rowspan="2">Kurir</th>
                <th rowspan="2">Ongkir</th>
                <th rowspan="2">Total</th>
                <th rowspan="2">Total Akhir</th>
            </tr>
        </thead>

        <tbody>
        <?php
            include '../../config/database.php';

            $kodePelanggan = $_POST['kodePelanggan'];
            $sql = "SELECT 
                        p.kodeTransaksi, 
                        p.ongkir, 
                        p.kurir, 
                        an.namaPelanggan, 
                        b.namaBarang, 
                        v.typeVarian, 
                        dp.tglTransaksi, 
                        dp.status,
                        dp.jumlah,
                        v.harga
                    FROM transaksi p
                    INNER JOIN pelanggan an ON an.kodePelanggan = p.kodePelanggan
                    INNER JOIN detail_transaksi dp ON dp.kodeTransaksi = p.kodeTransaksi
                    INNER JOIN barang b ON b.kodeBarang = dp.kodeBarang
                    INNER JOIN varianBarang v ON v.idVarian = dp.idVarian
                    WHERE an.kodePelanggan = '$kodePelanggan'";

            $hasil = mysqli_query($kon, $sql);
            $jumlah = mysqli_num_rows($hasil);

            if ($jumlah == 0) {
                echo "<div class='alert alert-info'>Pelanggan ini tidak memiliki riwayat transaksi sebelumnya.</div>";
            }

            $no = 0;

            while ($data = mysqli_fetch_array($hasil)):
                $no++;
                $total = $data['harga'] * $data['jumlah'];
                $totalAkhir = $total + $data['ongkir'];

                if ($data['status'] == 0) {
                    $status = "<span class='badge badge-dark'>Belum Dibayar</span>";
                } else if ($data['status'] == 1) {
                    $status = "<span class='badge badge-primary'>Dikemas</span>";
                } else if ($data['status'] == 2) {
                    $status = "<span class='badge badge-success'>Dikirim</span>";
                } else if ($data['status'] == 3) {
                    $status = "<span class='badge badge-danger'>Selesai</span>";
                } else if ($data['status'] == 4) {
                    $status = "<span class='badge badge-danger'>Batal</span>";
                }

                if ($data['tglTransaksi'] == '0000-00-00') {
                    $tglTransaksi = "";
                } else {
                    $tglTransaksi = date("d/m/Y", strtotime($data['tglTransaksi']));
                }
        ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $data['kodeTransaksi']; ?></td>
            <td><?php echo $data['namaBarang']; ?></td>
            <td><?php echo $data['typeVarian']; ?></td>
            <td><?php echo $tglTransaksi; ?></td>
            <td><?php echo $status; ?></td>
            <td><?php echo strtoupper($data['kurir']); ?></td>
            <td>Rp<?php echo number_format($data['ongkir'], 0, ',', '.'); ?></td>
            <td>Rp<?php echo number_format($total, 0, ',', '.'); ?></td>
            <td>Rp<?php echo number_format($totalAkhir, 0, ',', '.'); ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
