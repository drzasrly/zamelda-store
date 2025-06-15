<?php
session_start();
     

    include '../../../config/database.php';
    //Memulai transaksi
    mysqli_query($kon,"START TRANSACTION");

    $id_detail_transaksi=$_GET["id_detail_transaksi"];
    $kodeTransaksi=$_GET["kodeTransaksi"];

    //Mengeksekusi atau menjalankan query 
    $hapus_detail_transaksi=mysqli_query($kon,"delete from detail_transaksi where id_detail_transaksi=$id_detail_transaksi");

    $hasil=mysqli_query($kon,"select * from detail_transaksi where kodeTransaksi='$kodeTransaksi'");

    $cek = mysqli_num_rows($hasil);

    if ($cek==0){
        $hapus_transaksi=mysqli_query($kon,"delete from transaksi where kodeTransaksi='$kodeTransaksi'");

        if ($hapus_detail_transaksi and $hapus_transaksi) {
            mysqli_query($kon,"COMMIT");
            header("Location:../../../dist/index.php?page=daftar-transaksi&hapus-transaksi=berhasil");
        }
        else {
            mysqli_query($kon,"ROLLBACK");
            header("Location:../../../dist/index.php?page=daftar-transaksi&hapus-transaksi=gagal");
        }
    } else {

        if ($hapus_detail_transaksi) {
            mysqli_query($kon,"COMMIT");
            header("Location:../../../dist/index.php?page=detail-transaksi&kodeTransaksi=$kodeTransaksi&hapus-transaksi=berhasil&#bagian_transaksi");
        }
        else {
            mysqli_query($kon,"ROLLBACK");
            header("Location:../../../dist/index.php?page=detail-transaksi&id_transaksi=$id_transaksi&hapus-transaksi=gagal&#bagian_transaksi");
        }

    }

    
?>
<form action="transaksi/detail-transaksi/hapus-transaksi.php" method="post">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                    <h5>Yakin ingin menghapus transaksi ini?</h5>
            </div>
        </div>
    </div>
    <input type="hidden" name="id_detail_transaksi" value="<?php echo $_POST["id_detail_transaksi"]; ?>" />
    <input type="hidden" name="kodeTransaksi" value="<?php echo $_POST["kodeTransaksi"]; ?>" />
    <button type="submit" name="hapus_transaksi" class="btn btn-primary">Hapus</button>
</form>

