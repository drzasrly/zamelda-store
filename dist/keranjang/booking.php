<!-- <script>
    $('title').text('Checkout');
</script>
<?php
    $tanggal = date('Y-m-d');
    function tanggal($tanggal) {
        $bulan = array(
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );
        $split = explode('-', $tanggal);
        return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
    }

    include '../config/database.php';
    
    if (isset($_POST['kodeTransaksi'])) {
        $kodeTransaksi = mysqli_real_escape_string($kon, $_POST['kodeTransaksi']);
    } else {
        echo "<div class='alert alert-danger'>Kode transaksi tidak ditemukan!</div>";
        exit;
    }

    // Ambil detail pelanggan
    $kodePelanggan = $_SESSION['kodePengguna'];
    $sql_pelanggan = "SELECT * FROM pelanggan WHERE kodePelanggan = '$kodePelanggan'";
    $query_pelanggan = mysqli_query($kon, $sql_pelanggan);
    $pelanggan = mysqli_fetch_array($query_pelanggan);
?>
<main>
    <div class="container-fluid">
        <h2 class="mt-4">Checkout</h2>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Checkout</li>
        </ol>

        <div class="card mb-4">
            <div class="card-body">
                <h3 class="mt-4">Detail Alamat - <?php echo $_SESSION['namaPelanggan']; ?></h3>
                <h3><span class="badge badge-primary">#<?php echo $kodeTransaksi; ?></span></h3>
                <p><strong>Nama:</strong> <?php echo $pelanggan['namaPelanggan']; ?></p>
                <p><strong>No. Telp:</strong> <?php echo $pelanggan['noTelp']; ?></p>
                <p><strong>Email:</strong> <?php echo $pelanggan['email']; ?></p>
                <p><strong>Alamat:</strong> <?php echo $pelanggan['alamat']; ?></p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <?php
                        // Ambil data barang yang di-submit (dari detail_transaksi)
                        $sql = "
                            SELECT 
                                d.*, 
                                v.gambarBarang, 
                                v.kodeBarang,
                                b.namaBarang
                            FROM detail_transaksi d
                            INNER JOIN varianBarang v ON d.idVarian = v.idVarian
                            INNER JOIN barang b ON v.kodeBarang = b.kodeBarang
                            WHERE d.kodeTransaksi = '$kodeTransaksi'
                        ";
                        $hasil = mysqli_query($kon, $sql);
                        while ($data = mysqli_fetch_array($hasil)):
                    ?>
                    <div class="col-sm-3 mb-3">
                        <div class="card h-100">
                            <img class="card-img-top" src="../dist/barang/gambar/<?php echo $data['gambarBarang']; ?>" alt="<?php echo $data['namaBarang']; ?>">
                            <div class="card-body text-center">
                                <p class="card-text font-weight-bold"><?php echo $data['namaBarang']; ?></p>
                                <p class="card-text">Jumlah: <?php echo $data['jumlah']; ?></p>
                                <p class="card-text">Harga: Rp<?php echo number_format($data['harga'], 0, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</main> -->
