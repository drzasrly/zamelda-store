<?php
include '../config/database.php';

$kodeTransaksi = $_GET['kodeTransaksi'];

$sql = "SELECT * FROM detail_transaksi 
        INNER JOIN transaksi ON transaksi.kodeTransaksi = detail_transaksi.kodeTransaksi
        INNER JOIN barang ON barang.kodeBarang = detail_transaksi.kodeBarang 
        WHERE transaksi.kodeTransaksi = '$kodeTransaksi'";

$result = mysqli_query($kon, $sql);
?>

<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th rowspan="2">No</th>
        <th rowspan="2">Judul Barang</th>
        <th colspan="2" class="text-center">Waktu Transaksi</th>
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
    $no = 0;
    while ($ambil = mysqli_fetch_array($result)):
        $no++;

        // Format status
        if ($ambil['status'] == 0) {
            $status = "<span class='badge badge-dark'>Belum diambil</span>";
        } else if ($ambil['status'] == 1) {
            $status = "<span class='badge badge-primary'>Sedang Dipinjam</span>";
        } else if ($ambil['status'] == 2) {
            $status = "<span class='badge badge-success'>Telah Selesai</span>";
        } else if ($ambil['status'] == 3) {
            $status = "<span class='badge badge-danger'>Batal</span>";
        } else {
            $status = "<span class='badge badge-secondary'>Tidak diketahui</span>";
        }

        // Format tanggal mulai
        $tanggal_mulai = ($ambil['tanggal'] != '0000-00-00') 
            ? tanggal(date("Y-m-d", strtotime($ambil['tanggal']))) 
            : '';

        // Format tanggal selesai
        $tanggal_selesai = (isset($ambil['tanggal_selesai']) && $ambil['tanggal_selesai'] != '0000-00-00') 
            ? tanggal(date("Y-m-d", strtotime($ambil['tanggal_selesai']))) 
            : '';
    ?>
    <tr>
        <td><?php echo $no; ?></td>
        <td><?php echo $ambil['namaBarang']; ?></td>
        <td class="text-center"><?php echo $tanggal_mulai; ?></td>
        <td class="text-center"><?php echo $tanggal_selesai; ?></td>
        <td><?php echo $status; ?></td>
        <td>
            <button class="tombol_konfirmasi btn btn-primary btn-circle"
                kodePelanggan="<?php echo $kodePelanggan; ?>"
                kodeBarang="<?php echo $ambil['kodeBarang']; ?>"
                id_detail_transaksi="<?php echo $ambil['id_detail_transaksi']; ?>"
                kodeTransaksi="<?php echo $_GET['kodeTransaksi']; ?>"
                tanggal="<?php echo $ambil['tanggal']; ?>"
                status="<?php echo $ambil['status']; ?>">
                <i class="fas fa-check"></i>
            </button>
            <button class="tombol_edit_transaksi btn btn-warning btn-circle"
                id_detail_transaksi="<?php echo $ambil['id_detail_transaksi']; ?>"
                kodeTransaksi="<?php echo $_GET['kodeTransaksi']; ?>"
                kodeBarang="<?php echo $ambil['kodeBarang']; ?>">
                <i class="fas fa-edit"></i>
            </button>
            <a href="transaksi/detail-transaksi/hapus-transaksi.php?kodeTransaksi=<?php echo $_GET['kodeTransaksi']; ?>&id_detail_transaksi=<?php echo $ambil['id_detail_transaksi'];?>"
                class="btn-hapus-transaksi btn btn-danger btn-circle">
                <i class="fas fa-trash"></i>
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php
// Fungsi untuk format tanggal Indonesia
function tanggal($tgl) {
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
    $pecahkan = explode('-', $tgl);
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}
?>
