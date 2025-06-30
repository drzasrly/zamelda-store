<script>
    $('title').text('Detail transaksi');
</script>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main>
    <div class="container-fluid">
        <h2 class="mt-4">Detail transaksi</h2>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Detail transaksi</li>
        </ol>
        <?php
            if (isset($_GET['edit-pelanggan'])) {
                if ($_GET['edit-pelanggan']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Pelanggan yang meminjam barang berhasil diupdate</div>";
                } else if ($_GET['edit-pelanggan']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Pelanggan yang meminjam barang gagal diupdate</div>";
                }   
            }
        ?>
        <div class="card mb-4">
            <div class="card-body">
                <div class="container-fluid">
    <div class="row justify-content-right">
        <div class="col-12 col-md-8 col-lg-6 col-xl-4"> <!-- RESPONSIF -->
            <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary" id="judul_grafik">Informasi Data Pelanggan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                        <?php
                            include '../config/database.php';
                            $kodeTransaksi = $_GET['kodeTransaksi'];
                            $sql = "SELECT *, ap.alamat_detail, ap.kota, ap.provinsi 
                                    FROM transaksi p
                                    LEFT JOIN pelanggan an ON an.kodePelanggan = p.kodePelanggan
                                    LEFT JOIN detail_transaksi dp ON dp.kodeTransaksi = p.kodeTransaksi
                                    LEFT JOIN alamat_pelanggan ap ON ap.idAlamat = dp.idAlamat
                                    LEFT JOIN barang pk ON pk.kodeBarang = dp.kodeBarang
                                    WHERE p.kodeTransaksi = '$kodeTransaksi'
                                    LIMIT 1";
                            $query = mysqli_query($kon, $sql);
                            $ambil = mysqli_fetch_array($query);
                            $kodePelanggan = $ambil['kodePelanggan'];
                            $alamatLengkap = trim($ambil['alamat_detail']) ? $ambil['alamat_detail'] . ', ' . $ambil['kota'] . ', ' . $ambil['provinsi'] : 'Alamat belum tersedia';
                        ?>
                        <tr>
                            <td>Nama</td>
                            <td>: <?php echo $ambil['namaPelanggan']; ?></td>
                        </tr>
                        <tr>
                            <td>No Telp</td>
                            <td>: <?php echo $ambil['noTelp']; ?></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>: <?php echo $ambil['email']; ?></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>: <?php echo $alamatLengkap; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

                <div class="row" id="bagian_detail_transaksi">
                    <div class="col-sm-12">
                        <div class="card mb-4">
                            <div class="card-body">
                            <?php
                                    if (isset($_GET['edit-transaksi'])) {
                                        if ($_GET['edit-transaksi']=='berhasil'){
                                            echo"<div class='alert alert-success'><strong>Berhasil!</strong> barang berhasil diupdate</div>";
                                        } else if ($_GET['edit-transaksi']=='gagal'){
                                            echo"<div class='alert alert-danger'><strong>Gagal!</strong> barang gagal diupdate</div>";
                                        }   
                                    }
                                    if (isset($_GET['hapus-transaksi'])) {
                                        if ($_GET['hapus-transaksi']=='berhasil'){
                                            echo"<div class='alert alert-success'><strong>Berhasil!</strong> barang telah dihapus</div>";
                                        } else if ($_GET['hapus-transaksi']=='gagal'){
                                            echo"<div class='alert alert-danger'><strong>Gagal!</strong> barang gagal dihapus</div>";
                                        }    
                                    }

                                    if (isset($_GET['konfirmasi'])) {
                                        if ($_GET['konfirmasi']=='berhasil'){
                                            echo"<div class='alert alert-success'><strong>Berhasil!</strong> Status transaksi telah ditetapkan</div>";
                                        } else if ($_GET['konfirmasi']=='gagal'){
                                            echo"<div class='alert alert-danger'><strong>Gagal!</strong> Status transaksi gagal ditetapkan</div>";
                                        }   
                                    }

                                    if (isset($_GET['konfirmasi'])) {
                                        if ($_GET['konfirmasi']=='tolak'){
                                            echo"<div class='alert alert-warning'><strong>Gagal!</strong> Tindakan ditolak karena telah mencapai batas maksimal transaksi. <a href='#' kodePelanggan='". $kodePelanggan."' id='lihat_detail_transaksi'>Lihat daftar barang yang sedang dipinjam</a></div>";
                                        } 
                                    }

                                ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Barang</th>
                                        <th rowspan="2">Waktu transaksi</th>
                                        <th rowspan="2">Status</th>
                                        <?php if (!isset($_SESSION['level']) || $_SESSION['level'] != 'Pelanggan'): ?>
                                            <?php echo "<th rowspan='2'>Aksi</th>";?>
                                        <?php endif; 
                                        ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                   <?php
                                    include '../config/database.php';
                                    $kodeTransaksi = $_GET['kodeTransaksi'];

                                 $sql1 = "SELECT detail_transaksi.*, 
                                            barang.namaBarang 
                                    FROM detail_transaksi 
                                    INNER JOIN barang ON barang.kodeBarang = detail_transaksi.kodeBarang 
                                    WHERE detail_transaksi.kodeTransaksi = '$kodeTransaksi'";



                                    $result = mysqli_query($kon, $sql1);

                                    $no = 1;
                                    while ($ambil = mysqli_fetch_array($result)):
                                        if ($ambil['status'] == 0) {
                                            $status = "<span class='badge badge-dark'>Belum Dibayar</span>";
                                        } else if ($ambil['status'] == 1) {
                                            $status = "<span class='badge badge-primary'>Dikemas</span>";
                                        } else if ($ambil['status'] == 2) {
                                            $status = "<span class='badge badge-success'>Dikirim</span>";
                                        } else if ($ambil['status'] == 3) {
                                            $status = "<span class='badge badge-success'>Selesai</span>";
                                        } else if ($ambil['status'] == 4) {
                                            $status = "<span class='badge badge-danger'>Batal</span>";
                                        } else {
                                            $status = "<span class='badge badge-secondary'>Tidak Diketahui</span>";
                                        }
                                        if ($ambil['tglTransaksi'] == '0000-00-00' || empty($ambil['tglTransaksi'])) {
                                            $tanggal = "";
                                        } else {
                                           $tanggal = date("d-m-Y", strtotime($ambil['tglTransaksi']));

                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $ambil['namaBarang']; ?></td>
                                        <td><?php echo $tanggal; ?></td>
                                        <td>
                                       
                                        <?php if ($ambil['status'] == 2): ?>
                                            <button class="btn btn-sm btn-info btn-lihat-peta" 
                                                data-alamat="<?= htmlspecialchars($alamatLengkap) ?>"
                                                data-id_detail_transaksi="<?= $ambil['id_detail_transaksi'] ?>" >
                                                Dikirim
                                            </button>
                                        <?php else: ?>
                                            <?= $status ?>
                                        <?php endif; ?>
                                        </td>

                                        <?php if (isset($_SESSION['level']) && $_SESSION['level'] == 'Pelanggan'): ?>
                                        <td>
                                            <?php 
                                                if ($ambil['status'] == 1) { 
                                                    $tanggal_transaksi = strtotime($ambil['tglTransaksi']);
                                                    $selisih_hari = floor((time() - $tanggal_transaksi) / (60 * 60 * 24));
                                                    
                                                    if ($selisih_hari > 7) {
                                                        $pesan = urlencode("Halo Admin,\nSaya ingin komplain karena barang {$ambil['namaBarang']} dengan kode transaksi {$ambil['kodeTransaksi']} belum dikirim lebih dari 7 hari.");
                                                        echo "<a href='https://wa.me/62895397081000?text={$pesan}' target='_blank' class='btn btn-danger btn-sm'>Komplain via WA</a>";
                                                    }
                                                } else {
                                                    echo "-";
                                                }
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if (!isset($_SESSION['level']) || $_SESSION['level'] != 'Pelanggan'): ?>
                                        <td>
                                            <button class="tombol_konfirmasi btn btn-primary btn-circle" 
                                                    kodePelanggan="<?php echo $kodePelanggan; ?>" 
                                                    kodeBarang="<?php echo $ambil['kodeBarang']; ?>"  
                                                    id_detail_transaksi="<?php echo $ambil['id_detail_transaksi']; ?>"  
                                                    kodeTransaksi="<?php echo $_GET['kodeTransaksi']; ?>"  
                                                    tglTransaksi="<?php echo $ambil['tglTransaksi']; ?>" 
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
                                        <?php endif; ?>
                                    </tr>
                                    <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <!-- <a href="transaksi/detail-transaksi/invoice.php?kodeTransaksi=<?php echo $kodeTransaksi; ?>" target="_blank" 
                                    class="btn-icon-pdf" style="background-color:rgb(17, 102, 102); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;"><span class="text"><i class="fas fa-print fa-sm"></i> Cetak</span></a> -->
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php 
    function tanggal($tanggal)
    {
        $bulan = array (1 =>   'Januari',
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
        $split = explode('-', $tanggal);
        return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
    }
?>

<input type="hidden" name="kodeTransaksi" id="kodeTransaksi" value="<?php echo  $_GET['kodeTransaksi'];?>"/>
<!-- Modal -->
<div class="modal fade" id="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="judul"></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div id="tampil_data">                 
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!-- Modal Peta -->
<div class="modal fade" id="modalPeta" tabindex="-1" aria-labelledby="judulPeta" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="judulPeta">Rute Pengiriman Barang</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">×</button>
      </div>
      <div class="modal-body" style="height: 500px;">
        <div id="mapPeta" style="height: 100%; width: 100%;"></div>
      </div>
    </div>
  </div>
</div>


<script>
    $('.tombol_edit_transaksi').on('click',function(){
        var id_detail_transaksi = $(this).attr("id_detail_transaksi");
        var kodeTransaksi = $(this).attr("kodeTransaksi");
        var kodeBarang = $(this).attr("kodeBarang");
        var tanggal = $(this).attr("tanggal");
        $.ajax({
            url: 'transaksi/detail-transaksi/edit-transaksi.php',
            method: 'post',
            data: {id_detail_transaksi:id_detail_transaksi,kodeTransaksi:kodeTransaksi,kodeBarang:kodeBarang,tanggal:tanggal},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Edit barang';
            }
        });
        $('#modal').modal('show');
    });


    $('.tombol_konfirmasi').on('click',function(){
        var kodePelanggan = $(this).attr("kodePelanggan");
        var id_detail_transaksi = $(this).attr("id_detail_transaksi");
        var kodeTransaksi = $(this).attr("kodeTransaksi");
        var kodeBarang = $(this).attr("kodeBarang");
        var tanggal = $(this).attr("tanggal");
        var status = $(this).attr("status");
        

        $.ajax({
            url: 'transaksi/detail-transaksi/konfirmasi.php',
            method: 'post',
            data: {kodePelanggan:kodePelanggan,id_detail_transaksi:id_detail_transaksi,kodeTransaksi:kodeTransaksi,kodeBarang:kodeBarang,tanggal:tanggal,status:status},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Konfirmasi transaksi';
            }
        });
        $('#modal').modal('show');
    });

     $('#tombol_edit_Pelanggan').on('click',function(){
     
        var kodeTransaksi = $(this).attr("kodeTransaksi");
        var kodePelanggan = $(this).attr("kodePelanggan");
        $.ajax({
            url: 'transaksi/detail-transaksi/edit-pelanggan.php',
            method: 'post',
            data: {kodeTransaksi:kodeTransaksi,kodePelanggan:kodePelanggan},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Edit transaksi barang';
            }
        });
        $('#modal').modal('show');
    });

    $('.btn-hapus-transaksi').on('click',function(){
        konfirmasi=confirm("Yakin ingin menghapus data transaksi ini?")
        if (konfirmasi){
            return true;
        }else {
            return false;
        }
    });

        $('#lihat_detail_transaksi').on('click',function(){
        var kodePelanggan = $(this).attr("kodePelanggan");
        $.ajax({
            url: 'transaksi/detail-transaksi/data-barang.php',
            method: 'post',
            data: {kodePelanggan:kodePelanggan},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Daftar barang yang Sedang Dipinjam';
            }
        });
        $('#modal').modal('show');
    });


let map = null;
let routingControl = null;
let sudahDimuat = false; // Flag global untuk status peta

$(document).on('click', '.btn-lihat-peta', function () {
    const alamatTujuan = $(this).data('alamat');
    const id_detail_transaksi = $(this).data('id_detail_transaksi'); // ✅ Tangkap id_detail_transaksi
    const asal = [-7.2575, 112.7521];

    $('#modalPeta').modal('show');

    setTimeout(() => {
        if (sudahDimuat) return;

        $.getJSON(https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(alamatTujuan)}, function (data) {
            if (data && data.length > 0) {
                const tujuan = [parseFloat(data[0].lat), parseFloat(data[0].lon)];

                map = L.map('mapPeta').setView(asal, 10);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                routingControl = L.Routing.control({
                    waypoints: [L.latLng(asal), L.latLng(tujuan)],
                    createMarker: function (i, wp, nWps) {
                        return L.marker(wp.latLng).bindPopup(i === 0 ? "Gudang/Asal" : "Tujuan");
                    },
                    routeWhileDragging: false,
                    addWaypoints: false,
                    show: false
                }).addTo(map);

                routingControl.on('routesfound', function (e) {
                    const coords = e.routes[0].coordinates;
                    let i = 0;
                    let marker = L.marker(coords[0], {
                        icon: L.icon({
                            iconUrl: 'transaksi/motor.png',
                            iconSize: [40, 40],
                            iconAnchor: [20, 20]
                        })
                    }).addTo(map);

                    marker.on('click', function () {
                        Swal.fire({
                            icon: 'question',
                            title: 'Selesaikan Pengiriman?',
                            text: 'Klik "Selesai" jika barang sudah diterima pelanggan.',
                            showCancelButton: true,
                            confirmButtonText: 'Selesai',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.post('transaksi/detail-transaksi/update-status.php', {
                                    id_detail_transaksi: id_detail_transaksi, // ✅ Dikirim sekarang
                                    status: '3'
                                }, function (response) {
                                    console.log("Respon update-status:", response);
                                    status = '3'
                                    Swal.fire('Berhasil!', 'Status transaksi diubah menjadi Selesai.', 'success')
                                        .then(() => location.reload());
                                }).fail(function () {
                                    Swal.fire('Gagal!', 'Gagal mengubah status.', 'error');
                                });
                            }
                        });
                    });

                    const anim = setInterval(() => {
                        if (i < coords.length) {
                            marker.setLatLng(coords[i]);
                            i++;
                        } else {
                            clearInterval(anim);
                            Swal.fire({
                                icon: 'info',
                                title: 'Barang Telah Sampai',
                                text: 'Klik "Konfirmasi" jika barang telah diterima.',
                                confirmButtonText: 'Konfirmasi Diterima'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.post('transaksi/detail-transaksi/update-status.php', {
                                        id_detail_transaksi: id_detail_transaksi, 
                                        status: '3'
                                    }, function (response) {
                                        console.log("Respon update-status:", response);
                                        Swal.fire('Berhasil!', 'Transaksi telah diselesaikan.', 'success')
                                            .then(() => location.reload());
                                    }).fail(function (xhr) {
                                        console.error("Gagal:", xhr.responseText);
                                        Swal.fire('Gagal!', 'Terjadi kesalahan saat update status.', 'error');
                                    });
                                }
                            });
                        }
                    }, 5);
                });

                sudahDimuat = true;
            } else {
                alert("Gagal menemukan lokasi tujuan.");
            }
        });
    }, 500);
});
</script>