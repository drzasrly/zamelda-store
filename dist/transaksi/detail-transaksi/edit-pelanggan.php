<?php
session_start();
if (isset($_POST['edit_pelanggan'])) {

    include '../../../config/database.php';

    mysqli_query($kon,"START TRANSACTION");

    function input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $kodeTransaksi=input($_POST["kodeTransaksi"]);
    echo $kodePelanggan=input($_POST["kodePelanggan"]);

    
    $sql="update transaksi set
    kodePelanggan='$kodePelanggan'
    where kodeTransaksi='$kodeTransaksi'";


    //Mengeksekusi atau menjalankan query diatas
    $edit_transaksi_pelanggan=mysqli_query($kon,$sql);

    $kodePengguna=$_SESSION["kodePengguna"];
    $waktu=date("Y-m-d h:i:s");
    $log_aktivitas="Edit transaksi pelanggan #$kodePelanggan ";
    $simpan_aktivitas=mysqli_query($kon,"insert into log_aktivitas (waktu,aktivitas,kodePengguna) values ('$waktu','$log_aktivitas',$kodePengguna)");


    //Kondisi apakah berhasil atau tidak dalam mengeksekusi query diatas
    if ($edit_transaksi_pelanggan) {
        mysqli_query($kon,"COMMIT");
        header("Location:../../index.php?page=detail-transaksi&kodeTransaksi=$kodeTransaksi&edit-pelanggan=berhasil");
    }
    else {
        mysqli_query($kon,"ROLLBACK");
        header("Location:../../index.php?page=detail-transaksi&kodeTransaksi=$kodeTransaksi&edit-pelanggan=gagal");

    }

}
//----------------------------------------------------------------------------
?>



<?php
  $kodePelanggan=$_POST['kodePelanggan'];
?>
<form action="transaksi/detail-transaksi/edit-pelanggan.php" method="post">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <input type="hidden" class="form-control" name="kodeTransaksi" value="<?php echo $_POST['kodeTransaksi'];?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>pelanggan:</label>
                <select class="form-control" name="kodePelanggan">
                    <?php
                        include '../../../config/database.php';
                        if ($kodePelanggan=='') echo "<option value='0'>-</option>";
                        $hasil=mysqli_query($kon,"select * from pelanggan order by kodePelanggan asc");
                        while ($data = mysqli_fetch_array($hasil)):
                    ?>
                        <option <?php if ($kodePelanggan==$data['kodePelanggan']) echo "selected"; ?>  value="<?php echo $data['kodePelanggan']; ?>"><?php echo $data['namaPelanggan']; ?></option>
                        <?php endwhile; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-5">
            <div class="form-group">
                <button class="btn btn-warning btn-circle" name="edit_pelanggan" ><i class="fas fa-cart-plus"></i> Update</button>
            </div>
        </div>
    </div>
</form>