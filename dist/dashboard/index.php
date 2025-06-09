<script>
    $('title').text('Dashboard');
</script>

<main>
    <div class="container-fluid">
        <h2 class="mt-4">Dashboard</h2>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>

        <?php if ($_SESSION["level"] == 'Penjual' || $_SESSION["level"] == 'penjual' or $_SESSION["level"] == 'admin' || $_SESSION["level"] == 'Admin'): ?>
        <div class="row">
            <?php
                include '../config/database.php';

                // Total Transaksi
                $hasil = mysqli_query($kon, "SELECT kodeTransaksi FROM detail_transaksi");
                $total_transaksi = mysqli_num_rows($hasil);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-dark text-white mb-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs text-white text-uppercase mb-1">Total Transaksi</div>
                                <div class="h5 mb-0 font-weight-bold text-dark-800"><?php echo $total_transaksi; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-grip-horizontal fa-2x text-dark-300"></i>
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
                <div class="card bg-warning text-white mb-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs text-white text-uppercase mb-1">Jumlah Pelanggan</div>
                                <div class="h5 mb-0 font-weight-bold text-dark-800"><?php echo $jumlah_pelanggan; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user fa-2x text-dark-300"></i>
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
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs text-white text-uppercase mb-1">Jumlah Barang</div>
                                <div class="h5 mb-0 font-weight-bold text-dark-800"><?php echo $jumlah_barang; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book fa-2x text-dark-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php 
        if ($_SESSION["level"] == 'Pelanggan' || $_SESSION["level"] == 'pelanggan'): ?>
        <div class="row">
             <?php
                include '../config/database.php';
                $kodePelanggan = isset($_SESSION["kodePelanggan"]) ? $_SESSION["kodePelanggan"] : null;

            
                include '../config/database.php';
                // STATUS: 0 = Belum Bayar, 1 = Dikemas, 2 = Dikirim, 3 = Selesai, 4 = Dibatalkan
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
                    ['label' => 'Belum Bayar', 'jumlah' => $belum_bayar, 'bg' => 'warning', 'icon' => 'wallet'],
                    ['label' => 'Dikemas', 'jumlah' => $dikemas, 'bg' => 'primary', 'icon' => 'box'],
                    ['label' => 'Dikirim', 'jumlah' => $dikirim, 'bg' => 'success', 'icon' => 'truck'],
                    ['label' => 'Selesai', 'jumlah' => $selesai, 'bg' => 'info', 'icon' => 'box-open'],
                    ['label' => 'Dibatalkan', 'jumlah' => $dibatalkan, 'bg' => 'secondary', 'icon' => 'times-circle'],
                ];
            ?>

            <?php foreach ($statusList as $status): ?>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card bg-<?php echo $status['bg']; ?> text-white mb-4">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs text-white text-uppercase mb-1"><?php echo $status['label']; ?></div>
                                    <div class="h5 mb-0 font-weight-bold text-dark-800"><?php echo $status['jumlah']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-<?php echo $status['icon']; ?> fa-3x text-dark-300"></i>
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
