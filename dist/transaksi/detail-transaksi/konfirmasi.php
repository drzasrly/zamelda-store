<?php
session_start();
if (isset($_POST['konfirmasi'])) {

    include '../../../config/database.php';
    mysqli_query($kon, "START TRANSACTION");

    class Pustaka {
        public $kode_pustaka;
        public $stok;

        public function __construct($kode_pustaka)
        {
            $this->kode_pustaka = $kode_pustaka;
        }

        public function updateStok($kon, $jumlah)
        {
            $query = "UPDATE pustaka SET stok = stok + ($jumlah) WHERE kode_pustaka='$this->kode_pustaka'";
            return mysqli_query($kon, $query);
        }
    }

    class Peminjaman {
        public $id_detail_peminjaman;
        public $kode_peminjaman;
        public $status_peminjaman;
        public $kode_anggota;
        public $jenis_denda;
        public $denda;
        public $tanggal_pinjam;
        public $tanggal_kembali;
        public $pustaka;

        public function __construct($id_detail_peminjaman, $kode_peminjaman, $status_peminjaman, $kode_anggota, $jenis_denda, $denda, $tanggal_pinjam, $tanggal_kembali, Pustaka $pustaka)
        {
            $this->id_detail_peminjaman = $id_detail_peminjaman;
            $this->kode_peminjaman = $kode_peminjaman;
            $this->status_peminjaman = $status_peminjaman;
            $this->kode_anggota = $kode_anggota;
            $this->jenis_denda = $jenis_denda;
            $this->denda = $denda;
            $this->tanggal_pinjam = $tanggal_pinjam;
            $this->tanggal_kembali = $tanggal_kembali;
            $this->pustaka = $pustaka;
        }

        public function updatePeminjaman($kon)
        {
            $sql = "UPDATE detail_peminjaman SET
                    status='$this->status_peminjaman',
                    jenis_denda='$this->jenis_denda',
                    denda='$this->denda',
                    tanggal_pinjam='$this->tanggal_pinjam',
                    tanggal_kembali='$this->tanggal_kembali'
                    WHERE id_detail_peminjaman='$this->id_detail_peminjaman'";
            return mysqli_query($kon, $sql);
        }
    }

    function input($data)
    {
        return isset($data) ? htmlspecialchars(trim(stripslashes($data))) : '';
    }

    $pustaka = new Pustaka(input($_POST["kode_pustaka"]));
    $peminjaman = new Peminjaman(
        input($_POST["id_detail_peminjaman"]),
        input($_POST["kode_peminjaman"]),
        input($_POST["status_peminjaman"]),
        input($_POST["kode_anggota"]),
        input($_POST["jenis_denda"]),
        isset($_POST["biaya_keterlambatan"]) ? (int) $_POST["biaya_keterlambatan"] : 0,
        date('Y-m-d'),
        date('Y-m-d'),
        $pustaka
    );

    $updatePeminjaman = $peminjaman->updatePeminjaman($kon);
    $updateStok = false;

    if ($peminjaman->status_peminjaman == 1) {
        $updateStok = $pustaka->updateStok($kon, -1);
    } elseif ($peminjaman->status_peminjaman == 2) {
        $updateStok = $pustaka->updateStok($kon, 1);
    }

    if ($updatePeminjaman && $updateStok) {
        mysqli_query($kon, "COMMIT");
        header("Location:../../index.php?page=detail-peminjaman&kode_peminjaman=$peminjaman->kode_peminjaman&konfirmasi=berhasil#bagian_detail_peminjaman");
    } else {
        mysqli_query($kon, "ROLLBACK");
        header("Location:../../index.php?page=detail-peminjaman&kode_peminjaman=$peminjaman->kode_peminjaman&konfirmasi=gagal#bagian_detail_peminjaman");
    }
}

?>


<form action="peminjaman/detail-peminjaman/konfirmasi.php" method="post">
    <input type="hidden" name="tanggal_pinjam" id="tanggal_pinjam" value="<?php echo $_POST['tanggal_pinjam'];?>"/>
    <input type="hidden" name="id_detail_peminjaman" id="id_detail_peminjaman" value="<?php echo $_POST['id_detail_peminjaman'];?>"/>
    <input type="hidden" name="status" id="status" value="<?php echo $_POST['status'];?>"/>
    <input type="hidden" name="kode_peminjaman" id="kode_peminjaman" value="<?php echo $_POST['kode_peminjaman'];?>"/>
    <input type="hidden" name="kode_pustaka" id="kode_pustaka" value="<?php echo $_POST['kode_pustaka'];?>"/>
    <input type="hidden" name="kode_anggota" id="kode_anggota" value="<?php echo $_POST['kode_anggota'];?>"/>

    <div class="form-group">
    <label for="status_peminjaman">Status:</label>
    <select class="form-control" name="status_peminjaman" id="status_peminjaman">
        <option value="0"<?php echo $_POST['status'] == 0 ? 'selected' : ''; ?> >Belum diambil</option>
        <option value="1"<?php echo $_POST['status'] == 1 ? 'selected' : ''; ?> >Sedang Dipinjam</option>
        <?php if ($_POST['status'] == 1 || $_POST['status'] == 2 ):?>
        <option value="2"<?php echo $_POST['status'] == 2 ? 'selected' : ''; ?> >Telah Selesai</option>
        <?php endif; ?>
        <option value="3"<?php echo $_POST['status'] == 3 ? 'selected' : ''; ?> >Batal</option>
    </select>
    </div>

    <?php if ($_POST['status'] != 2): ?>
    <div id="tabel_denda">           
        <?php
            include '../../../config/database.php';
            // ... kode untuk menghitung denda ...
        ?>
        <table class="table table-bordered">
            <tbody>
            <!-- Jenis denda dan formulir input -->
            <tr>
                <td>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="tidak_ada" name="jenis_denda" value="0"  <?php if ($_POST['jenis_denda']=='0') echo "checked"; ?> />
                        <label class="custom-control-label" for="tidak_ada">Tidak ada</label>
                    </div>
                </td> 
                <td>Rp.0</td>
            </tr>
            <?php 
             $tanggal_kembali = strtotime($_POST["tanggal_kembali"]);
             $tanggal_kembali = date('Y-m-d', $tanggal_kembali);
             $tanggal_sekarang = date('Y-m-d');
            if ($_POST['tanggal_pinjam']!='0000-00-00'):
                if ($tanggal_sekarang > $tanggal_kembali):
            ?>
            <tr>
                <td>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="keterlambatan" name="jenis_denda" value="1"  <?php if ($_POST['jenis_denda']=='1') echo "checked"; ?> />
                        <label class="custom-control-label" for="keterlambatan">Keterlambatan</label>
                    </div>
                </td>
                <td>
                    Rp. <span id="tampil_denda_keterlambatan"><?php echo number_format($biaya_keterlambatan,0,',','.'); ?></span>
                    <input type="hidden" name="biaya_keterlambatan" id="biaya_keterlambatan" value="<?php echo $biaya_keterlambatan;?>"/>
                </td>
            </tr>
            <?php 
                endif;
            endif;
            ?>
            <tr>
                <td>
                <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="hilang_rusak" name="jenis_denda" value="2"  <?php if ($_POST['jenis_denda']=='2') echo "checked"; ?>  />
                        <label class="custom-control-label" for="hilang_rusak">Hilang/rusak</label>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input type="number" class="form-control" id="biaya_hilang_kerusakan" name="biaya_hilang_kerusakan" value="<?php if ($_POST['jenis_denda']=='2') echo $_POST['denda']; ?> " placeholder="Masukan biaya" disabled>  
                    </div>
                    <div class="form-group">
                        <span id="info_denda"></span>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <input type="submit" class="btn btn-primary" id="tombol_submit" name="konfirmasi" value="Submit">
</form>

<script>

    function format_rupiah(nominal){
        var  reverse = nominal.toString().split('').reverse().join(''),
                ribuan = reverse.match(/\d{1,3}/g);
         return ribuan	= ribuan.join('.').split('').reverse().join('');
    }
    //Tabel denda pada awalnya disembunyikan
    $('#tabel_denda').hide();


    $('#status_peminjaman').bind('change', function () {
        var status_peminjaman = $("#status_peminjaman").val();
            if (status_peminjaman==2){
            $('#tabel_denda').show(200);
        }else {
            $('#tabel_denda').hide();
        }
   });



   $('#keterlambatan').on('click',function(){
        $( "#biaya_hilang_kerusakan" ).prop( "disabled", true );
    });

    $('#hilang_rusak').on('click',function(){
        $( "#biaya_hilang_kerusakan" ).prop( "disabled", false );
    });

    $('#biaya_hilang_kerusakan').bind('keyup', function () {
        var denda=$("#biaya_hilang_kerusakan").val();
        $("#info_denda").text('Rp.'+format_rupiah(denda));     
    });



    var status = $("#status").val();

    if (status==2){
        $('#tabel_denda').show();
    }else {
        $('#tabel_denda').hide();
    }
    
</script>