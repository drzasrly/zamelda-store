<script>
    $('title').text('Dashboard');
</script>

<style>
.bg-custom-status {
    background-color:rgb(31, 124, 161) !important;
    color: white !important;
}
</style>

<main>
    <div class="container-fluid">
        <h2 class="mt-4">Dashboard</h2>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>

        <?php if ($_SESSION["level"] == 'Penjual' || $_SESSION["level"] == 'penjual' || $_SESSION["level"] == 'admin' || $_SESSION["level"] == 'Admin'): ?>
        <div class="row">
            <?php
                include '../config/database.php';

                // ✅ Total Nilai Transaksi (akumulasi nominal)
                $q = mysqli_query($kon, "SELECT SUM(v.harga * d.jumlah) AS totalBayar 
                                        FROM detail_transaksi d 
                                        JOIN varianBarang v ON v.idVarian = d.idVarian");
                $t = mysqli_fetch_assoc($q);
                $totalBayar = $t['totalBayar'] ?? 0;
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-custom-status text-white mb-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs text-uppercase mb-1">Total Transaksi</div>
                                <div class="h5 mb-0 font-weight-bold">Rp <?= number_format($totalBayar, 0, ',', '.') ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-grip-horizontal fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                // Jumlah Pelanggan
                $hasil = mysqli_query($kon, "SELECT kodePelanggan FROM pelanggan");
                $jumlah_pelanggan = mysqli_num_rows($hasil);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-custom-status text-white mb-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs text-uppercase mb-1">Jumlah Pelanggan</div>
                                <div class="h5 mb-0 font-weight-bold"><?= $jumlah_pelanggan ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                // Jumlah Barang
                $hasil = mysqli_query($kon, "SELECT kodeBarang FROM barang");
                $jumlah_barang = mysqli_num_rows($hasil);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-custom-status text-white mb-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs text-uppercase mb-1">Jumlah Barang</div>
                                <div class="h5 mb-0 font-weight-bold"><?= $jumlah_barang ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($_SESSION["level"] == 'Pelanggan' || $_SESSION["level"] == 'pelanggan'): ?>
        <div class="row">
            <?php
                include '../config/database.php';
                $kodePelanggan = isset($_SESSION["kodePelanggan"]) ? $_SESSION["kodePelanggan"] : null;

                function hitungStatus($kon, $kodePelanggan, $status) {
                    $sql = "SELECT p.kodeTransaksi FROM detail_transaksi d
                            INNER JOIN transaksi p ON p.kodeTransaksi = d.kodeTransaksi
                            WHERE p.kodePelanggan='$kodePelanggan' AND d.status='$status'";
                    $hasil = mysqli_query($kon, $sql);
                    return mysqli_num_rows($hasil);
                }

                $belum_bayar = hitungStatus($kon, $kodePelanggan, '0');
                $dikemas = hitungStatus($kon, $kodePelanggan, '1');
                $dikirim = hitungStatus($kon, $kodePelanggan, '2');
                $selesai = hitungStatus($kon, $kodePelanggan, '3');
                $dibatalkan = hitungStatus($kon, $kodePelanggan, '4');

                $statusList = [
                    ['label' => 'Belum Bayar', 'jumlah' => $belum_bayar, 'icon' => 'wallet'],
                    ['label' => 'Dikemas', 'jumlah' => $dikemas, 'icon' => 'box'],
                    ['label' => 'Dikirim', 'jumlah' => $dikirim, 'icon' => 'truck'],
                    ['label' => 'Selesai', 'jumlah' => $selesai, 'icon' => 'box-open'],
                    ['label' => 'Dibatalkan', 'jumlah' => $dibatalkan, 'icon' => 'times-circle'],
                ];
            ?>

            <?php foreach ($statusList as $status): ?>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-custom-status text-white mb-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs text-uppercase mb-1"><?php echo $status['label']; ?></div>
                                    <div class="h5 mb-0 font-weight-bold"><?php echo $status['jumlah']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-<?php echo $status['icon']; ?> fa-3x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- GRAFIK -->
        <div class="row">
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Total Transaksi Tahun <?php echo date('Y'); ?>
                    </div>
                    <div class="card-body">
                        <div id="tampil_grafik_transaksi_per_bulan"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Jumlah Transaksi Berdasarkan Kategori
                    </div>
                    <div class="card-body">
                        <div id="tampil_grafik_transaksi_per_kategori"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER DAN TABEL TRANSAKSI -->
        <div class="card mb-4">
            <div class="card-header">
                <strong>Daftar Transaksi Berdasarkan Tanggal</strong>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <input type="hidden" name="page" value="dashboard">
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label for="tgl_awal">Tanggal Awal</label>
                            <input type="date" name="tgl_awal" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label for="tgl_akhir">Tanggal Akhir</label>
                            <input type="date" name="tgl_akhir" class="form-control" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn w-100" style="background-color: rgb(35, 126, 162); color: white; border: none;">Tampilkan</button>
                        </div>
                    </div>
                </form>

                <?php
                if (isset($_GET['tgl_awal']) && isset($_GET['tgl_akhir'])) {
                    $tgl_awal = $_GET['tgl_awal'];
                    $tgl_akhir = $_GET['tgl_akhir'];

                    include '../config/database.php';

                    $sql = "SELECT t.kodeTransaksi, t.tanggal, p.namaPelanggan AS nama, SUM(d.jumlah * vb.harga) AS total
                            FROM transaksi t
                            INNER JOIN pelanggan p ON p.kodePelanggan = t.kodePelanggan
                            INNER JOIN detail_transaksi d ON d.kodeTransaksi = t.kodeTransaksi
                            INNER JOIN varianBarang vb ON vb.idVarian = d.idVarian
                            WHERE t.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
                            GROUP BY t.kodeTransaksi, t.tanggal, p.namaPelanggan
                            ORDER BY t.tanggal ASC";

                    $hasil = mysqli_query($kon, $sql);

                    echo "<div class='table-responsive'>";
                    echo "<table class='table table-bordered'>";
                    echo "<thead><tr><th>No</th><th>Kode Transaksi</th><th>Tanggal</th><th>Nama Pembeli</th><th>Total</th></tr></thead><tbody>";
                    $no = 1;
                    while ($data = mysqli_fetch_array($hasil)) {
                        echo "<tr>";
                        echo "<td>$no</td>";
                        echo "<td>" . $data['kodeTransaksi'] . "</td>";
                        echo "<td>" . $data['tanggal'] . "</td>";
                        echo "<td>" . $data['nama'] . "</td>";
                        echo "<td>Rp " . number_format($data['total'], 0, ',', '.') . "</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody></table>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>
</main>

<!-- AJAX UNTUK GRAFIK -->
<script>
    $(document).ready(function() {
        $.ajax({
            url: 'dashboard/transaksi_per_bulan.php',
            method: 'POST',
            success: function(data) {
                $('#tampil_grafik_transaksi_per_bulan').html(data);
            }
        });

        $.ajax({
            url: 'dashboard/transaksi_per_kategori.php',
            method: 'POST',
            success: function(data) {
                $('#tampil_grafik_transaksi_per_kategori').html(data);
            }
        });
    });
</script>
