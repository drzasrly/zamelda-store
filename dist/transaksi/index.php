
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data transaksi</title>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../src/templates/css/styles.css">

    <!-- Font Awesome (untuk ikon tombol) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<main>
    <div class="container-fluid">
        <h2 class="mt-4">Data transaksi</h2>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Daftar transaksi</li>
        </ol>

        <?php
        // Notifikasi
        if (isset($_GET['add'])) {
            if ($_GET['add'] == 'berhasil') {
                echo "<div class='alert alert-success'><strong>Berhasil!</strong> Data transaksi telah disimpan</div>";
            } else if ($_GET['add'] == 'gagal') {
                echo "<div class='alert alert-danger'><strong>Gagal!</strong> Data transaksi gagal disimpan</div>";
            }
        }

        if (isset($_GET['hapus'])) {
            if ($_GET['hapus'] == 'berhasil') {
                echo "<div class='alert alert-success'><strong>Berhasil!</strong> Data transaksi telah dihapus</div>";
            } else if ($_GET['hapus'] == 'gagal') {
                echo "<div class='alert alert-danger'><strong>Gagal!</strong> Data transaksi gagal dihapus</div>";
            }
        }

        if (isset($_GET['hapus-transaksi'])) {
            if ($_GET['hapus-transaksi'] == 'berhasil') {
                echo "<div class='alert alert-success'><strong>Berhasil!</strong> Data transaksi telah dihapus</div>";
            } else if ($_GET['hapus-transaksi'] == 'gagal') {
                echo "<div class='alert alert-danger'><strong>Gagal!</strong> Data transaksi gagal dihapus</div>";
            }
        }
        ?>

        <div class="card mb-4">
            <div class="card-header">
                <?php if ($_SESSION["level"] != "Pelanggan"): ?>
                    <a href="index.php?page=input-transaksi" class="btn btn-primary" role="button">Input transaksi</a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tabel_transaksi" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Transaksi</th>
                                <th>Tanggal</th>
                                <th>Nama Anggota</th>
                                <th>Jumlah</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include '../config/database.php';

                            $sql = "SELECT p.kodeTransaksi, an.namaPelanggan, COUNT(*) AS jumlah, p.tanggal
                                    FROM transaksi p
                                    INNER JOIN pelanggan an ON an.kodePelanggan = p.kodePelanggan
                                    INNER JOIN detail_transaksi dp ON dp.kodeTransaksi = p.kodeTransaksi
                                    INNER JOIN barang pk ON pk.kodeBarang = dp.kodeBarang
                                    GROUP BY an.namaPelanggan, p.kodeTransaksi
                                    ORDER BY p.kodeTransaksi DESC";

                            $hasil = mysqli_query($kon, $sql);
                            $no = 0;

                            while ($data = mysqli_fetch_array($hasil)) {
                                $no++;
                            ?>
                            <tr>
                                <td><?= $no; ?></td>
                                <td><?= $data['kodeTransaksi']; ?></td>
                                <td><?= tanggal(date('Y-m-d', strtotime($data['tanggal']))); ?></td>
                                <td><?= $data['namaPelanggan']; ?></td>
                                <td><?= $data['jumlah']; ?> Barang</td>
                                <td>
                                    <a href="index.php?page=detail-transaksi&kodeTransaksi=<?= $data['kodeTransaksi']; ?>" class="btn btn-success btn-circle">
                                        <i class="fas fa-mouse-pointer"></i>
                                    </a>
                                    <a href="transaksi/hapus-transaksi.php?kodeTransaksi=<?= $data['kodeTransaksi']; ?>" class="btn-hapus-transaksi btn btn-danger btn-circle">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="judul"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div id="tampil_data"><!-- Data dari AJAX --></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery & Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        $('#tabel_transaksi').DataTable();

        $('.btn-hapus-transaksi').on('click', function () {
            return confirm("Yakin ingin menghapus data transaksi ini?");
        });
    });
</script>

<?php
// Fungsi ubah format tanggal
function tanggal($tanggal)
{
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April',
        'Mei', 'Juni', 'Juli', 'Agustus',
        'September', 'Oktober', 'November', 'Desember'
    );
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}
?>

</body>
</html>
