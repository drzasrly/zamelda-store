<?php
session_start();
?>
<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Barang</th>
                        <th>Kategori</th>
                        <th>Jumlah Stok</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // include database
                include '../../../config/database.php';

                $kondisi = "";
                $kata_kunci = isset($_POST['kata_kunci']) ? mysqli_real_escape_string($kon, $_POST['kata_kunci']) : '';

                $sql = "SELECT p.kodeBarang, p.namaBarang, k.namaKategori, s.stok
                        FROM barang p
                        INNER JOIN kategoriBarang k ON k.kodeKategori = p.kodeKategori
                        INNER JOIN varianbarang s ON s.kodeBarang = p.kodeBarang
                        WHERE p.namaBarang LIKE '%$kata_kunci%'
                        ORDER BY p.namaBarang ASC";

                $hasil = mysqli_query($kon, $sql);
                $no = 0;

                while ($data = mysqli_fetch_array($hasil)):
                    $no++;
                ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $data['kodeBarang']; ?></td>
                    <td><?php echo $data['namaBarang']; ?></td>
                    <td><?php echo $data['namaKategori']; ?></td>
                    <td><?php echo $data['stok']; ?></td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Tombol Ekspor -->
            <a href="laporan/barang/cetak-laporan.php?kata_kunci=<?php echo urlencode($kata_kunci); ?>" target="_blank" class="btn btn-primary btn-icon-split">
                <span class="text"><i class="fas fa-print fa-sm"></i> Cetak Invoice</span>
            </a>
            <a href="laporan/barang/cetak-pdf.php?kata_kunci=<?php echo urlencode($kata_kunci); ?>" target="_blank" class="btn btn-danger btn-icon-pdf">
                <span class="text"><i class="fas fa-file-pdf fa-sm"></i> Export PDF</span>
            </a>
            <a href="laporan/barang/cetak-excel.php?kata_kunci=<?php echo urlencode($kata_kunci); ?>" target="_blank" class="btn btn-success btn-icon-pdf">
                <span class="text"><i class="fas fa-file-excel fa-sm"></i> Export Excel</span>
            </a>
        </div>
    </div>
</div>
