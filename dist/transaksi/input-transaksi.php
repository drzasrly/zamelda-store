<?php
// Set judul halaman
?>
<script>
    $('title').text('Input Transaksi');
</script>

<?php
include '../config/database.php';

// Ambil ID transaksi terakhir dan buat kode transaksi baru
$query = mysqli_query($kon, "SELECT MAX(idTransaksi) AS idTerbesar FROM transaksi");
$data = mysqli_fetch_array($query);
$idBaru = $data['idTerbesar'] + 1;
$kodeTransaksi = "tr" . sprintf("%03s", $idBaru);
$tanggal = date("Y-m-d");
?>

<main>
    <input type="hidden" name="kodeTransaksi" value="<?php echo $kodeTransaksi; ?>"/>
    <div class="container-fluid">
        <h2 class="mt-4">Riwayat Transaksi #<?php echo $kodeTransaksi; ?></h2>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Riwayat Transaksi #<?php echo $kodeTransaksi; ?></li>
        </ol>

        <!-- Pilih Pelanggan -->
        <div class="card shadow mb-1">
            <div class="card-body">
                <div class="collapse show">
                    <?php if (!isset($_GET['pelanggan'])): ?>
                        <div class="alert alert-info">Silakan pilih pelanggan di bawah ini:</div>
                    <?php endif; ?>
                    <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="get">
                        <input type="hidden" name="page" value="input-transaksi"/>
                        <div class="form-row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select class="form-control" name="pelanggan" id="pelanggan">
                                        <?php
                                        $sql = "SELECT * FROM pelanggan";
                                        $hasil = mysqli_query($kon, $sql);
                                        while ($data = mysqli_fetch_array($hasil)) {
                                            $selected = (isset($_GET['pelanggan']) && $_GET['pelanggan'] == $data['kodePelanggan']) ? "selected" : "";
                                            echo "<option value='" . $data['kodePelanggan'] . "' $selected>" . $data['kodePelanggan'] . " - " . $data['namaPelanggan'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button type="submit" class="btn btn-dark">
                                    <i class="fas fa-search fa-sm"></i> Pilih Pelanggan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['pelanggan'])):
            $kodePelanggan = addslashes(trim($_GET['pelanggan']));
            $query1 = mysqli_query($kon, "SELECT * FROM pelanggan WHERE kodePelanggan='$kodePelanggan'");
            if (mysqli_num_rows($query1) > 0):
                $data1 = mysqli_fetch_array($query1);
        ?>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Profil Pelanggan</h6>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tbody>
                                        <tr><td>Kode</td><td>: <?php echo $data1['kodePelanggan']; ?></td></tr>
                                        <tr><td>Nama</td><td>: <?php echo $data1['namaPelanggan']; ?></td></tr>
                                        <tr><td>No Telp</td><td>: <?php echo $data1['noTelp']; ?></td></tr>
                                        <tr><td>Email</td><td>: <?php echo $data1['email']; ?></td></tr>
                                    </tbody>
                                </table>
                                <button type="button" kodePelanggan="<?php echo $data1['kodePelanggan']; ?>" class="btn btn-dark" id="lihat_riwayat_transaksi">Lihat Riwayat Transaksi</button>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="col-sm-8">
                        <div id="tampil_cart"></div>
                        <div class="form-group mt-2">
                            <span class="badge badge-info">Tanggal: <?php echo $tanggal; ?></span>
                        </div>
                        <div class="form-group">
                            <a href="transaksi/simpan.php?kodePelanggan=<?php echo $_GET['pelanggan']; ?>" id="tombol_simpan_transaksi" class="btn btn-success float-right">Simpan</a>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
        <?php endif; endif; ?>
    </div>
</main>

<!-- Modal Riwayat -->
<div class="modal fade" id="modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="judul"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="tampil_data"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#lihat_riwayat_transaksi').on('click', function() {
            var kodePelanggan = $(this).attr("kodePelanggan");
            $.ajax({
                url: 'transaksi/riwayat-transaksi.php',
                method: 'POST',
                data: { kodePelanggan: kodePelanggan },
                success: function(data) {
                    $('#tampil_data').html(data);
                    $('#judul').text('Lihat Riwayat Transaksi');
                    $('#modal').modal('show');
                }
            });
        });

        // Tampilkan isi cart
        $.ajax({
            url: 'transaksi/cart.php',
            method: 'POST',
            success: function(data) {
                $('#tampil_cart').html(data);
            }
        });
    });
</script>
