<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <!-- Custom styles -->
  <link href="../../../src/templates/css/styles.css" rel="stylesheet">
  <link href="../../../src/plugin/bootstrap/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
</head>
<body onload="window.print();">
<?php
include '../../../config/database.php';
$query = mysqli_query($kon, "SELECT * FROM profil_aplikasi ORDER BY nama_aplikasi DESC LIMIT 1");
$row = mysqli_fetch_array($query);
?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-sm-2 float-left">
                    <img src="../../aplikasi/logo/<?php echo $row['logo']; ?>" width="95px" alt="brand"/>
                </div>
                <div class="col-sm-10 float-left">
                    <h3><?php echo strtoupper($row['nama_aplikasi']);?></h3>
                    <h6><?php echo $row['alamat'].', Telp '.$row['no_telp'];?></h6>
                    <h6><?php echo $row['website'];?></h6>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- rows -->
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead class="text-center">
                            <tr>
                                <th>No</th>
                                <th>Kode Transaksi</th>
                                <th>Nama Pelanggan</th>
                                <th>Nama Barang</th>
                                <th>Waktu Transaksi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $kondisi = "";
                        if (!empty($_GET["dari_tanggal"]) && empty($_GET["sampai_tanggal"])) {
                            $kondisi = "WHERE DATE(tglTransaksi) = '" . $_GET['dari_tanggal'] . "'";
                        }
                        if (!empty($_GET["dari_tanggal"]) && !empty($_GET["sampai_tanggal"])) {
                            $kondisi = "WHERE DATE(tglTransaksi) BETWEEN '" . $_GET['dari_tanggal'] . "' AND '" . $_GET['sampai_tanggal'] . "'";
                        }

                        if ($_SESSION["level"] == "admin") {
                            $idPengguna = $_SESSION["idPengguna"];
                            $sql = "SELECT p.kodeTransaksi, an.namaPelanggan, pk.namaBarang, dp.tglTransaksi, dp.status
                                    FROM transaksi p
                                    INNER JOIN pelanggan an ON an.kodePelanggan = p.kodePelanggan
                                    INNER JOIN detail_transaksi dp ON dp.kodeTransaksi = p.kodeTransaksi
                                    INNER JOIN barang pk ON pk.kodeBarang = dp.kodeBarang
                                    $kondisi AND status != '0'
                                    ORDER BY dp.tglTransaksi ASC";
                        } else {
                            $sql = "SELECT p.kodeTransaksi, an.namaPelanggan, pk.namaBarang, dp.tglTransaksi, dp.status
                                    FROM transaksi p
                                    INNER JOIN pelanggan an ON an.kodePelanggan = p.kodePelanggan
                                    INNER JOIN detail_transaksi dp ON dp.kodeTransaksi = p.kodeTransaksi
                                    INNER JOIN barang pk ON pk.kodeBarang = dp.kodeBarang
                                    $kondisi AND status != '0'
                                    ORDER BY dp.tglTransaksi ASC";
                        }

                        $hasil = mysqli_query($kon, $sql);
                        $no = 0;
                        while ($data = mysqli_fetch_array($hasil)):
                            $no++;

                            // Status label
                            if ($data['status'] == 0) {
                                $status = "Belum Dibayar";
                            } elseif ($data['status'] == 1) {
                                $status = "Dikemas";
                            } elseif ($data['status'] == 2) {
                                $status = "Dikirim";
                            } elseif ($data['status'] == 3) {
                                $status = "Selesai";
                            } elseif ($data['status'] == 4) {
                                $status = "Batal";
                            }

                            $tglTransaksi = ($data['tglTransaksi'] == '0000-00-00') ? '' : date("d/m/Y", strtotime($data['tglTransaksi']));
                        ?>
                        <tr>
                            <td><?php echo $no; ?></td>
                            <td><?php echo $data['kodeTransaksi']; ?></td>
                            <td><?php echo $data['namaPelanggan']; ?></td>
                            <td><?php echo $data['namaBarang']; ?></td>
                            <td class="text-center"><?php echo $tglTransaksi; ?></td>
                            <td><?php echo $status; ?></td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
