<?php
session_start();
// Koneksi database
include '../../../config/database.php';

// Mengambil nama aplikasi
$query = mysqli_query($kon, "SELECT nama_aplikasi FROM profil_aplikasi ORDER BY nama_aplikasi DESC LIMIT 1");    
$row = mysqli_fetch_array($query);

// Ambil dan format tanggal
$tanggal = '';
if (!empty($_GET["dari_tanggal"]) && empty($_GET["sampai_tanggal"])) {
    $tanggal = date("d/m/Y", strtotime($_GET["dari_tanggal"]));
}
if (!empty($_GET["dari_tanggal"]) && !empty($_GET["sampai_tanggal"])) {
    $tanggal = date("d/m/Y", strtotime($_GET["dari_tanggal"])) . " - " . date("d/m/Y", strtotime($_GET["sampai_tanggal"]));
}

// Membuat file format Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=LAPORAN_TRANSAKSI_" . strtoupper($row['nama_aplikasi']) . "_" . $tanggal . ".xls");
?>  
<h2><center>LAPORAN TRANSAKSI <?php echo strtoupper($row['nama_aplikasi']); ?></center></h2>
<h4>Tanggal: <?php echo $tanggal; ?></h4>

<table border="1" cellpadding="5" cellspacing="0">
    <thead class="text-center">
        <tr>
            <th>No</th>
            <th>Kode Transaksi</th>
            <th>Nama Pelanggan</th>
            <th>Nama Barang</th>
            <th>Tanggal Transaksi</th>
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

        if ($_SESSION["level"] == "Admin") {
            $id_pengguna = $_SESSION["id_pengguna"];
            $sql = "SELECT p.kodeTransaksi, an.namaPelanggan, pk.namaBarang, dp.tglTransaksi, dp.status
                    FROM transaksi p
                    INNER JOIN pelanggan an ON an.kodePelanggan = p.kodePelanggan
                    INNER JOIN detail_transaksi dp ON dp.kodeTransaksi = p.kodeTransaksi
                    INNER JOIN barang pk ON pk.kodeBarang = dp.kodeBarang
                    $kondisi AND dp.status != '0'
                    ORDER BY dp.tglTransaksi ASC";
        } else {
            $sql = "SELECT p.kodeTransaksi, an.namaPelanggan, pk.namaBarang, dp.tglTransaksi, dp.status
                    FROM transaksi p
                    INNER JOIN pelanggan an ON an.kodePelanggan = p.kodePelanggan
                    INNER JOIN detail_transaksi dp ON dp.kodeTransaksi = p.kodeTransaksi
                    INNER JOIN barang pk ON pk.kodeBarang = dp.kodeBarang
                    $kondisi AND dp.status != '0'
                    ORDER BY dp.tglTransaksi ASC";
        }

        $hasil = mysqli_query($kon, $sql);
        $no = 1;

        while ($data = mysqli_fetch_array($hasil)) {
            $status = '';
            switch ($data['status']) {
                case 0: $status = "Belum Dibayar"; break;
                case 1: $status = "Dikemas"; break;
                case 2: $status = "Dikirim"; break;
                case 3: $status = "Selesai"; break;
                case 4: $status = "Batal"; break;
            }

            $tglTransaksi = ($data['tglTransaksi'] == '0000-00-00') ? '' : date("d/m/Y", strtotime($data['tglTransaksi']));
            echo "<tr>
                <td>$no</td>
                <td>{$data['kodeTransaksi']}</td>
                <td>{$data['namaPelanggan']}</td>
                <td>{$data['namaBarang']}</td>
                <td align='center'>{$tglTransaksi}</td>
                <td>{$status}</td>
            </tr>";
            $no++;
        }
        ?>
    </tbody>
</table>
