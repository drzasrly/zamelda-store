<?php
session_start();
require('../../../src/plugin/fpdf/fpdf.php');
$pdf = new FPDF('L', 'mm', 'Letter');

// Koneksi ke database
include '../../../config/database.php';

// Informasi aplikasi
$query = mysqli_query($kon, "SELECT * FROM profil_aplikasi ORDER BY nama_aplikasi DESC LIMIT 1");
$row = mysqli_fetch_array($query);
$pdf->SetTitle("DATA BARANG " . strtoupper($row['nama_aplikasi']));
$pimpinan = $row['nama_pimpinan'];

// Header
$pdf->AddPage();
$pdf->Image('../../aplikasi/logo/' . $row['logo'], 15, 5, 30, 30);
$pdf->SetFont('Arial', 'B', 21);
$pdf->Cell(0, 7, strtoupper($row['nama_aplikasi']), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, $row['alamat'] . ', Telp ' . $row['no_telp'], 0, 1, 'C');
$pdf->Cell(0, 7, $row['website'], 0, 1, 'C');
$pdf->Cell(10, 7, '', 0, 1);
$pdf->SetLineWidth(1);
$pdf->Line(10, 31, 270, 31);
$pdf->SetLineWidth(0);
$pdf->Line(10, 32, 270, 32);

// Judul
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 7, 'LAPORAN DATA BARANG', 0, 1, 'C');
$pdf->Cell(10, 4, '', 0, 1);

// Table header
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 6, 'No', 1, 0, 'C');
$pdf->Cell(25, 6, 'Kode', 1, 0, 'C');
$pdf->Cell(90, 6, 'Nama Barang', 1, 0, 'C');
$pdf->Cell(70, 6, 'Kategori', 1, 0, 'C');
$pdf->Cell(20, 6, 'Stok', 1, 1, 'C');

// Ambil data
$kata_kunci = isset($_GET['kata_kunci']) ? mysqli_real_escape_string($kon, $_GET['kata_kunci']) : '';

$sql = "SELECT p.kodeBarang, p.namaBarang, k.namaKategori, 
               SUM(s.stok) as stok
        FROM barang p
        INNER JOIN kategoriBarang k ON k.kodeKategori = p.kodeKategori
        INNER JOIN varianbarang s ON s.kodeBarang = p.kodeBarang
        WHERE p.namaBarang LIKE '%$kata_kunci%'
        GROUP BY p.kodeBarang
        ORDER BY p.namaBarang ASC";

$hasil = mysqli_query($kon, $sql);
$pdf->SetFont('Arial', '', 9);
$no = 1;

while ($data = mysqli_fetch_array($hasil)) {
    $pdf->Cell(10, 6, $no, 1, 0, 'C');
    $pdf->Cell(25, 6, $data['kodeBarang'], 1, 0);
    $pdf->Cell(90, 6, substr($data['namaBarang'], 0, 60), 1, 0);
    $pdf->Cell(70, 6, $data['namaKategori'], 1, 0);
    $pdf->Cell(20, 6, $data['stok'], 1, 1, 'C');
    $no++;
}

// Tambahan tanda tangan
function tanggal($tanggal)
{
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

$tanggal = date('Y-m-d');
$pdf->Cell(460, 10, '', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(460, 7, 'Tanggal: ' . tanggal($tanggal), 0, 1, 'C');
$pdf->Cell(460, 6, 'Mengetahui,', 0, 1, 'C');
$pdf->Cell(460, 20, '', 0, 1, 'C');
$pdf->Cell(460, 6, $pimpinan, 0, 1, 'C');

$pdf->Output();
