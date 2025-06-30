<?php
session_start();
// Koneksi database
include '../../../config/database.php';

// Mengambil nama aplikasi
$query = mysqli_query($kon, "SELECT nama_aplikasi FROM profil_aplikasi ORDER BY nama_aplikasi DESC LIMIT 1");
$row = mysqli_fetch_array($query);

// Ambil kata kunci pencarian
$kata_kunci = isset($_GET['kata_kunci']) ? mysqli_real_escape_string($kon, $_GET['kata_kunci']) : '';

// Set header untuk file Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=LAPORAN BARANG " . strtoupper($row['nama_aplikasi']) . ".xls");
?>

<h2><center>LAPORAN DATA BARANG <?php echo strtoupper($row['nama_aplikasi']); ?></center></h2>

<table border="1">
    <thead style="text-align: center;">
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
        // Query untuk mengambil data barang sesuai kata kunci
        $sql = "SELECT p.kodeBarang, p.namaBarang, k.namaKategori, s.stok
                FROM barang p
                INNER JOIN kategoriBarang k ON k.kodeKategori = p.kodeKategori
                INNER JOIN varianbarang s ON s.kodeBarang = p.kodeBarang
                WHERE p.namaBarang LIKE '%$kata_kunci%'
                ORDER BY p.namaBarang ASC";

        $hasil = mysqli_query($kon, $sql);
        $no = 1;
        while ($data = mysqli_fetch_array($hasil)) {
            echo "
            <tr>
                <td align='center'>{$no}</td>
                <td>{$data['kodeBarang']}</td>
                <td>{$data['namaBarang']}</td>
                <td>{$data['namaKategori']}</td>
                <td align='center'>{$data['stok']}</td>
            </tr>";
            $no++;
        }
        ?>
    </tbody>
</table>
