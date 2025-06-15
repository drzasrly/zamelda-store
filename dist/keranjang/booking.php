<script>
    $('title').text('Pengajuan Berhasil');
</script>
<?php
    $tanggal = date('Y-m-d');
    function tanggal($tanggal)
    {
        $bulan = array (1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $split = explode('-', $tanggal);
        return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
    }
?>
<main>
    <div class="container-fluid">
        <h2 class="mt-4">Pengajuan Berhasil</h2>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Pengajuan Berhasil</li>
        </ol>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="mt-4">Selamat <?php echo htmlspecialchars($_SESSION['namaPelanggan']); ?></h3>
                        <h3><span class="badge badge-primary">#<?php echo htmlspecialchars($_GET['kodeTransaksi']); ?></span></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <?php
                        include '../config/database.php';

                        $kodeTransaksi = mysqli_real_escape_string($kon, $_GET['kodeTransaksi']);

                        // Query untuk ambil detail transaksi lengkap dengan varian, barang, dan gambar varian
                        $sql = "SELECT p.namaBarang, v.typeVarian, v.size, g.gambarvarian
                                FROM detail_transaksi d
                                INNER JOIN varianbarang v ON d.idVarian = v.idVarian
                                INNER JOIN barang p ON v.kodeBarang = p.kodeBarang
                                LEFT JOIN gambarvarian g ON v.idGambarVarian = g.idGambarVarian
                                WHERE d.kodeTransaksi = '$kodeTransaksi'";

                        $hasil = mysqli_query($kon, $sql);

                        if (mysqli_num_rows($hasil) > 0):
                            while ($data = mysqli_fetch_assoc($hasil)):
                    ?>
                        <div class="col-sm-2">
                            <div class="card">
                                <div class="card bg-basic">
                                    <?php if (!empty($data['gambarvarian'])): ?>
                                    <img class="card-img-top" src="../dist/barang/varian/<?php echo htmlspecialchars($data['gambarvarian']); ?>" alt="Gambar Varian">
                                    <?php else: ?>
                                    <div style="width: 100%; height: 150px; display: flex; align-items: center; justify-content: center; background: #f0f0f0; color: #888;">
                                        Tidak ada gambar
                                    </div>
                                    <?php endif; ?>
                                    <div class="card-body text-center">
                                        <h6 class="card-title"><?php echo htmlspecialchars($data['namaBarang']); ?></h6>
                                        <p class="card-text"><?php echo htmlspecialchars($data['typeVarian'] . ' / ' . $data['size']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                            endwhile;
                        else:
                    ?>
                    <div class="col-12">
                        <p class="text-center">Tidak ada detail transaksi ditemukan.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
