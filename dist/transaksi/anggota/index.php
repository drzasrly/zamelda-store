<script>
    $('title').text('Transaksi Saya');
</script>
<main>
    <div class="container-fluid">
        <h2 class="mt-4">Transaksi Saya</h2>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Daftar Transaksi</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                <div class="collapse show">
                    <!-- form -->
                    <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="get">
                        <input type="hidden" name="page" value="Transaksi-saya"/>
                        <div class="form-row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select class="form-control" name="kodeTransaksi" id="kodeTransaksi">
                                        <option value="">Pilih Kode</option>
                                        <?php
                                            include '../config/database.php';
                                            $kodePelanggan = $_SESSION['kodePengguna'];
                                            $sql = "SELECT kodeTransaksi FROM Transaksi WHERE kodePelanggan='$kodePelanggan'";
                                            $ket = "";
                                            $hasil = mysqli_query($kon, $sql);
                                            $no = 0;
                                            while ($data = mysqli_fetch_array($hasil)):
                                                $no++;
                                                if (isset($_GET['kodeTransaksi'])) {
                                                    $kodeTransaksi = trim($_GET['kodeTransaksi']);
                                                    $ket = ($kodeTransaksi == $data['kodeTransaksi']) ? "selected" : "";
                                                }
                                        ?>
                                            <option <?php echo $ket; ?> value="<?php echo $data['kodeTransaksi'];?>"><?php echo $data['kodeTransaksi'];?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button type="submit" class="btn btn-dark">
                                    <span class="text"><i class="fas fa-search fa-sm"></i> Pilih</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="text-center">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Kode</th>
                                <th rowspan="2">barang</th>
                                <th colspan="2">Waktu Transaksi</th>
                                <th rowspan="2">Status</th>
                                <th rowspan="2">Aksi</th>
                            </tr>
                            <tr>
                                <th>Mulai</th>
                                <th>Selesai</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                                include '../config/database.php';
                                $kodePelanggan = $_SESSION["kodePengguna"];
                                
                                if (isset($_GET['kodeTransaksi']) && $_GET['kodeTransaksi'] != '') {
                                    $kodeTransaksi = $_GET['kodeTransaksi'];
                                    $sql = "SELECT *
                                            FROM Transaksi p
                                            INNER JOIN detail_Transaksi d ON d.kodeTransaksi = p.kodeTransaksi
                                            INNER JOIN barang k ON k.kodeBarang = d.kodeBarang
                                            WHERE p.kodePelanggan = '$kodePelanggan' AND p.kodeTransaksi = '$kodeTransaksi'
                                            ORDER BY p.kodeTransaksi DESC";
                                } else {
                                    $sql = "SELECT *
                                            FROM Transaksi p
                                            INNER JOIN detail_Transaksi d ON d.kodeTransaksi = p.kodeTransaksi
                                            INNER JOIN barang k ON k.kodeBarang = d.kodeBarang
                                            WHERE p.kodePelanggan = '$kodePelanggan'
                                            ORDER BY p.kodeTransaksi DESC";
                                }

                                $hasil = mysqli_query($kon, $sql);
                                $no = 0;
                                $jum = 0;
                                while ($data = mysqli_fetch_array($hasil)):
                                    $no++;
                                    $jum += 1;

                                    if ($data['status'] == 0) {
                                        $status = "<span class='badge badge-dark'>Belum diambil</span>";
                                    } elseif ($data['status'] == 1) {
                                        $status = "<span class='badge badge-primary'>Sedang Dipinjam</span>";
                                    } elseif ($data['status'] == 2) {
                                        $status = "<span class='badge badge-success'>Telah Selesai</span>";
                                    } elseif ($data['status'] == 3) {
                                        $status = "<span class='badge badge-danger'>Batal</span>";
                                    }

                                    if ($data['tanggal'] == '0000-00-00') {
                                        $tanggal = "";
                                    } else {
                                        $tanggal = tanggal(date("Y-m-d", strtotime($data['tanggal'])));
                                    }
                                    if ($data['tanggal_kembali'] == '0000-00-00') {
                                        $tanggal_kembali = "";
                                    } else {
                                        $tanggal_kembali = tanggal(date("Y-m-d", strtotime($data['tanggal_kembali'])));
                                    }
                            ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $data['kodeTransaksi']; ?></td>
                                    <td><?php echo $data['namaBarang']; ?></td>
                                    <td><?php echo $tanggal; ?></td>
                                    <td><?php echo $tanggal_kembali; ?></td>
                                    <td><?php echo $status; ?></td>

                                    <td>
                                        <?php if ($data['status'] == 1): ?>
                                            <form action="Transaksi/detail-Transaksi/konfirmasi.php" method="post">
                                                <input type="hidden" name="kodeTransaksi" value="<?php echo $data['kodeTransaksi']; ?>"/>
                                                <input type="hidden" name="kodePelanggan" value="<?php echo $kodePelanggan; ?>"/>
                                                <input type="submit" class="btn btn-warning" name="ajukan_pengembalian" value="Ajukan Pengembalian">
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($jum != 0): ?>
                    <a href="Transaksi/detail-Transaksi/invoice.php?kodeTransaksi=<?php if (isset($_GET['kodeTransaksi']) && $_GET['kodeTransaksi'] != '') echo $_GET['kodeTransaksi']; ?>&kodePelanggan=<?php echo $kodePelanggan; ?>" target="_blank" class="btn btn-dark btn-icon-pdf">
                        <span class="text"><i class="fas fa-print fa-sm"></i> Cetak</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php 
    // Membuat format tanggal
    function tanggal($tanggal)
    {
        $bulan = array(
            1 => 'Januari',
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
        return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
    }
?>
