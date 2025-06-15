<?php
require('../../../src/plugin/fpdf/fpdf.php');
include '../../../config/database.php';

$pdf = new FPDF('P', 'mm', 'Letter');

// Ambil data profil aplikasi
$query = mysqli_query($kon, "SELECT * FROM profil_aplikasi ORDER BY nama_aplikasi DESC LIMIT 1");
$row = mysqli_fetch_array($query);
$pimpinan = $row['nama_pimpinan'];

// Header
$pdf->AddPage();
$pdf->Image('../../aplikasi/logo/' . $row['logo'], 15, 5, 25, 25);
$pdf->SetFont('Arial', 'B', 21);
$pdf->Cell(0, 7, strtoupper($row['nama_aplikasi']), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, $row['alamat'] . ', Telp ' . $row['no_telp'], 0, 1, 'C');
$pdf->Cell(0, 7, $row['website'], 0, 1, 'C');
$pdf->Ln(2);

// Garis
$pdf->SetLineWidth(1);
$pdf->Line(10, 31, 206, 31);
$pdf->SetLineWidth(0);
$pdf->Line(10, 32, 206, 32);

// Judul
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'BUKTI TRANSAKSI', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);

// Ambil kode transaksi dari URL
$kode = isset($_GET['kodeTransaksi']) ? $_GET['kodeTransaksi'] : "-";
$pdf->Cell(0, 4, 'Kode : ' . $kode, 0, 1, 'C');
$pdf->Ln(2);

// Ambil data transaksi
if (!empty($_GET['kodeTransaksi'])) {
    $sql = "SELECT * FROM transaksi p
            INNER JOIN pelanggan an ON an.kodePelanggan = p.kodePelanggan
            INNER JOIN detail_transaksi dp ON dp.kodeTransaksi = p.kodeTransaksi
            INNER JOIN barang pk ON pk.kodeBarang = dp.kodeBarang
            WHERE p.kodeTransaksi = '$kode'";
} elseif (!empty($_GET['kodePelanggan'])) {
    $kodePelanggan = $_GET['kodePelanggan'];
    $sql = "SELECT * FROM transaksi p
            INNER JOIN pelanggan an ON an.kodePelanggan = p.kodePelanggan
            INNER JOIN detail_transaksi dp ON dp.kodeTransaksi = p.kodeTransaksi
            INNER JOIN barang pk ON pk.kodeBarang = dp.kodeBarang
            WHERE p.kodePelanggan = '$kodePelanggan'";
}

$hasil = mysqli_query($kon, $sql);
$data = mysqli_fetch_array($hasil);

// Info Pelanggan
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 6, 'Nama', 0, 0);
$pdf->Cell(31, 6, ': ' . $data['namaPelanggan'], 0, 1);
$pdf->Cell(30, 6, 'No Telp', 0, 0);
$pdf->Cell(31, 6, ': ' . $data['noTelp'], 0, 1);
$pdf->Cell(30, 6, 'Email', 0, 0);
$pdf->Cell(31, 6, ': ' . $data['email'], 0, 1);
$pdf->Cell(30, 6, 'Alamat', 0, 0);
$pdf->Cell(31, 6, ': ' . $data['alamat'], 0, 1);
$pdf->Ln(2);

// Header Tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(8, 6, 'No', 1, 0, 'C');
$pdf->Cell(72, 6, 'Nama Barang', 1, 0, 'C');
$pdf->Cell(30, 6, 'Tgl Transaksi', 1, 0, 'C');
$pdf->Cell(60, 6, 'Status', 1, 1, 'C');

// Data Tabel
$pdf->SetFont('Arial', '', 10);
$no = 0;

// Peta status
$statusMap = [
    'belum dibayar' => 'Belum Dibayar',
    'dikemas' => 'Dikemas',
    'dikirim' => 'Dikirim',
    'selesai' => 'Selesai',
    'dibatalkan' => 'Dibatalkan'
];

// Jalankan lagi query untuk isi tabel
$hasil = mysqli_query($kon, $sql);
while ($data = mysqli_fetch_array($hasil)) {
    $no++;

    $status = isset($statusMap[$data['status']]) ? $statusMap[$data['status']] : 'Tidak Diketahui';

    $tanggal = ($data['tglTransaksi'] == '0000-00-00' || $data['tglTransaksi'] == null) 
                ? '-' 
                : date("d/m/Y", strtotime($data['tglTransaksi']));

    $pdf->Cell(8, 6, $no, 1, 0, 'C');
    $pdf->Cell(72, 6, $data['namaBarang'], 1, 0);
    $pdf->Cell(30, 6, $tanggal, 1, 0, 'C');
    $pdf->Cell(60, 6, $status, 1, 1);
}

// Fungsi format tanggal Indonesia
function tanggal($tanggal) {
    $bulan = [
        1 => 'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Tanda tangan pimpinan
$pdf->Ln(10);
$tanggalCetak = date('Y-m-d');
$pdf->Ln(10);
$pdf->SetFont('Arial','',10);
$pdf->SetX(140);
$pdf->Cell(70, 6, 'Surabaya, ' . tanggal($tanggalCetak), 0, 1, 'L');
$pdf->SetX(140);
$pdf->Cell(70, 6, 'Mengetahui,', 0, 1, 'L');
$pdf->Ln(20);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetX(140);
$pdf->Cell(70, 6, $pimpinan, 0, 1, 'L');

$pdf->Output();
?>
