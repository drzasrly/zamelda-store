<script>
    $(document).ready(function () {
        $(".select2").select2();
    });
</script>

<main>
    <div class="container-fluid">
        <!-- Judul Halaman -->
        <?php if (strtolower($_SESSION['level']) === 'admin' || strtolower($_SESSION['level']) === 'penjual'): ?>
            <h2 class="mt-4">Data Barang</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Data Barang</li>
            </ol>
        <?php endif; ?>

        <!-- Notifikasi -->
        <?php
        if (isset($_GET['add'])) {
            if ($_GET['add'] == 'berhasil') {
                echo "<div class='alert alert-success'><strong>Berhasil!</strong> Data Barang telah ditambah!</div>";
            } elseif ($_GET['add'] == 'gagal') {
                echo "<div class='alert alert-danger'><strong>Gagal!</strong> Data Barang gagal ditambahkan!</div>";
            } elseif ($_GET['add'] == 'format_gambar_tidak_sesuai') {
                echo "<div class='alert alert-warning'><strong>Gagal!</strong> Format gambar tidak sesuai!</div>";
            }
        }

        if (isset($_GET['edit'])) {
            if ($_GET['edit'] == 'berhasil') {
                echo "<div class='alert alert-success'><strong>Berhasil!</strong> Data Barang telah diupdate!</div>";
            } elseif ($_GET['edit'] == 'gagal') {
                echo "<div class='alert alert-danger'><strong>Gagal!</strong> Data Barang gagal diupdate!</div>";
            }
        }

        if (isset($_GET['hapus'])) {
            if ($_GET['hapus'] == 'berhasil') {
                echo "<div class='alert alert-success'><strong>Berhasil!</strong> Data Barang telah dihapus!</div>";
            } elseif ($_GET['hapus'] == 'gagal') {
                echo "<div class='alert alert-danger'><strong>Gagal!</strong> Data Barang gagal dihapus!</div>";
            }
        }
        ?>

        <!-- Filter Kategori -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="form_pencarian_barang">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="sel1">Kategori:</label>
                                <select class="form-control select2" multiple="multiple" name="kategoriBarang[]">
                                    <?php
                                    include '../config/database.php';
                                    $sql = "SELECT * FROM kategoribarang";
                                    $hasil = mysqli_query($kon, $sql);
                                    while ($data = mysqli_fetch_array($hasil)):
                                    ?>
                                        <option value="<?= $data['idKategori'] ?>"><?= $data['namaKategori'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2 align-self-end">
                            <button type="button" id="btn-cari" class="btn" style="background-color:rgb(31, 124, 161); color: white;">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kontainer Barang -->
        <div id="tampil_barang"></div>

        <!-- Loading Spinner -->
        <div id="ajax-wait">
            <img alt="loading..." src="../src/img/Rolling-1s-84px.png" />
        </div>

        <style>
            #ajax-wait {
                display: none;
                position: fixed;
                z-index: 1999;
            }
        </style>
    </div>
</main>

<!-- AJAX Script -->
<script>
    $(document).ready(function () {
        // Tampilkan semua barang saat halaman dibuka
        $.ajax({
            type: 'POST',
            url: 'barang/tampil-barang.php',
            data: '',
            cache: false,
            success: function (data) {
                $("#tampil_barang").html(data);
            }
        });

        // Pencarian berdasarkan kategori
        $('#btn-cari').on('click', function () {
            $(document).ajaxStart(function () {
                $("#ajax-wait").css({
                    left: ($(window).width() - 32) / 2 + "px",
                    top: ($(window).height() - 32) / 2 + "px",
                    display: "block"
                });
            }).ajaxComplete(function () {
                $("#ajax-wait").fadeOut();
            });

            var data = $('#form_pencarian_barang').serialize();
            $.ajax({
                type: 'POST',
                url: 'barang/tampil-barang.php',
                data: data,
                cache: false,
                success: function (data) {
                    $("#tampil_barang").html(data);
                }
            });
        });
    });
</script>
