<?php
session_start();
require('../../../src/plugin/fpdf/fpdf.php');
$pdf = new FPDF('L', 'mm', 'Letter');

// Koneksi database
include '../../../config/database.php';

// Ambil profil aplikasi
$query = mysqli_query($kon, "SELECT * FROM profil_aplikasi ORDER BY nama_aplikasi DESC LIMIT 1");
$row = mysqli_fetch_array($query);
$pimpinan = $row['nama_pimpinan'];

// Header laporan
$pdf->AddPage();
$pdf->Image('../../aplikasi/logo/' . $row['logo'], 15, 5, 30, 30);
$pdf->SetFont('Arial', 'B', 21);
$pdf->Cell(0, 7, strtoupper($row['nama_aplikasi']), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, $row['alamat'] . ', Telp ' . $row['no_telp'], 0, 1, 'C');
$pdf->Cell(0, 7, $row['website'], 0, 1, 'C');
$pdf->Cell(10, 7, '', 0, 1);

// Garis pembatas
$pdf->SetLineWidth(1);
$pdf->Line(10, 31, 270, 31);
$pdf->SetLineWidth(0);
$pdf->Line(10, 32, 270, 32);

// Judul laporan
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 7, 'LAPORAN TRANSAKSI', 0, 1, 'C');

// Tanggal filter
$tanggal = '';
if (!empty($_GET["dari_tanggal"]) && empty($_GET["sampai_tanggal"])) {
    $tanggal = date("d/m/Y", strtotime($_GET["dari_tanggal"]));
}
if (!empty($_GET["dari_tanggal"]) && !empty($_GET["sampai_tanggal"])) {
    $tanggal = date("d/m/Y", strtotime($_GET["dari_tanggal"])) . " - " . date("d/m/Y", strtotime($_GET["sampai_tanggal"]));
}

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(20, 6, 'Tanggal :', 0, 0);
$pdf->Cell(50, 6, $tanggal, 0, 1);
$pdf->Cell(10, 3, '', 0, 1);

// Table header
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 7, 'No', 1, 0, 'C');
$pdf->Cell(30, 7, 'Kode', 1, 0, 'C');
$pdf->Cell(55, 7, 'Nama Pelanggan', 1, 0, 'C');
$pdf->Cell(100, 7, 'Judul Barang', 1, 0, 'C');
$pdf->Cell(35, 7, 'Waktu Transaksi', 1, 0, 'C');
$pdf->Cell(35, 7, 'Status', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);

// Filter
$kondisi = "";
if (!empty($_GET["dari_tanggal"]) && empty($_GET["sampai_tanggal"])) {
    $kondisi = "WHERE DATE(tglTransaksi) = '" . $_GET['dari_tanggal'] . "'";
}
if (!empty($_GET["dari_tanggal"]) && !empty($_GET["sampai_tanggal"])) {
    $kondisi = "WHERE DATE(tglTransaksi) BETWEEN '" . $_GET['dari_tanggal'] . "' AND '" . $_GET['sampai_tanggal'] . "'";
}

// Query transaksi
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
$no = 1;

// Loop data transaksi
while ($data = mysqli_fetch_array($hasil)) {
    $statusText = '';
    switch ($data['status']) {
        case '0': $statusText = 'Belum Dibayar'; break;
        case '1': $statusText = 'Dikemas'; break;
        case '2': $statusText = 'Dikirim'; break;
        case '3': $statusText = 'Selesai'; break;
        case '4': $statusText = 'Batal'; break;
    }

    $tgl = ($data['tglTransaksi'] == '0000-00-00') ? '' : date("d/m/Y", strtotime($data['tglTransaksi']));

    $pdf->Cell(10, 6, $no++, 1, 0, 'C');
    $pdf->Cell(30, 6, $data['kodeTransaksi'], 1, 0);
    $pdf->Cell(55, 6, substr($data['namaPelanggan'], 0, 35), 1, 0);
    $pdf->Cell(100, 6, substr($data['namaBarang'], 0, 60), 1, 0);
    $pdf->Cell(35, 6, $tgl, 1, 0, 'C');
    $pdf->Cell(35, 6, $statusText, 1, 1);
}

// Format tanggal Indonesia
function tanggalIndo($tanggal)
{
    $bulan = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Footer tanda tangan
$tglSekarang = date('Y-m-d');
$pdf->Cell(10, 10, '', 0, 1);
$pdf->Cell(255, 10, tanggalIndo($tglSekarang), 0, 1, 'C');
$pdf->Cell(255, 10, 'Mengetahui,', 0, 1, 'C');
$pdf->Cell(255, 10, 'Pimpinan', 0, 1, 'C');
$pdf->Cell(255, 20, '', 0, 1);
$pdf->Cell(255, 10, $pimpinan, 0, 1, 'C');

// Output PDF
$pdf->Output();
