<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>LAPORAN DATA BARANG</title>
  <link href="../../../src/templates/css/styles.css" rel="stylesheet">
  <link href="../../../src/plugin/bootstrap/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <style>
    @media print {
        .no-print {
            display: none;
        }
    }
    th, td {
        font-size: 14px;
    }
  </style>
</head>
<body onload="window.print();">

<?php
include '../../../config/database.php';

// Informasi aplikasi
$query = mysqli_query($kon, "SELECT * FROM profil_aplikasi ORDER BY nama_aplikasi DESC LIMIT 1");
$row = mysqli_fetch_array($query);
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-sm-2">
                    <img src="../../aplikasi/logo/<?php echo $row['logo']; ?>" width="95px" alt="Logo">
                </div>
                <div class="col-sm-10 text-center">
                    <h3 class="mb-0"><?php echo strtoupper($row['nama_aplikasi']); ?></h3>
                    <p class="mb-0"><?php echo $row['alamat']; ?>, Telp: <?php echo $row['no_telp']; ?></p>
                    <p class="mb-0"><?php echo $row['website']; ?></p>
                </div>
            </div>
            <hr>
            <h5 class="text-center mt-2">LAPORAN DATA BARANG</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Jumlah Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $kata_kunci = isset($_GET['kata_kunci']) ? mysqli_real_escape_string($kon, $_GET['kata_kunci']) : '';
                    $sql = "SELECT p.kodeBarang, p.namaBarang, k.namaKategori, s.stok
                            FROM barang p
                            INNER JOIN kategoriBarang k ON k.kodeKategori = p.kodeKategori
                            INNER JOIN varianbarang s ON s.kodeBarang = p.kodeBarang
                            WHERE p.namaBarang LIKE '%$kata_kunci%'
                            ORDER BY p.namaBarang ASC";

                    $hasil = mysqli_query($kon, $sql);
                    $no = 1;
                    while ($data = mysqli_fetch_array($hasil)) {
                        echo "<tr>
                            <td class='text-center'>{$no}</td>
                            <td>{$data['kodeBarang']}</td>
                            <td>{$data['namaBarang']}</td>
                            <td>{$data['namaKategori']}</td>
                            <td class='text-center'>{$data['stok']}</td>
                        </tr>";
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
