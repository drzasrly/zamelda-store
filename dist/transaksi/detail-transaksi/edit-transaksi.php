<?php
session_start();
if (isset($_POST['edit_transaksi_barang'])) {

    include '../../../config/database.php';

    mysqli_query($kon,"START TRANSACTION");

    function input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $id_detail_transaksi=input($_POST["id_detail_transaksi"]);
    $kodeTransaksi=input($_POST["kodeTransaksi"]);
    $kodeBarang=input($_POST["kodeBarang"]);

    


    $sql="update detail_transaksi set
    kodeBarang='$kodeBarang'
    where id_detail_transaksi=$id_detail_transaksi";


    //Mengeksekusi atau menjalankan query diatas
    $edit_transaksi_barang=mysqli_query($kon,$sql);

    $kodePengguna=$_SESSION["kodePengguna"];
    $waktu=date("Y-m-d h:i:s");
    $log_aktivitas="Edit transaksi barang #$kodeBarang ";

    //Kondisi apakah berhasil atau tidak dalam mengeksekusi query diatas
    if ($edit_transaksi_barang) {
        mysqli_query($kon,"COMMIT");
        header("Location:../../index.php?page=detail-transaksi&kodeTransaksi=$kodeTransaksi&edit-transaksi=berhasil#bagian_detail_transaksi");
    }
    else {
        mysqli_query($kon,"ROLLBACK");
        header("Location:../../index.php?page=detail-transaksi&kodeTransaksi=$kodeTransaksi&edit-transaksi=gagal#bagian_detail_transaksi");

    }

}
//----------------------------------------------------------------------------
?>



<?php
  $kodeBarang=$_POST['kodeBarang'];
?>
<form action="transaksi/detail-transaksi/edit-transaksi.php" method="post">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <input type="hidden" class="form-control" name="id_detail_transaksi" value="<?php echo $_POST['id_detail_transaksi'];?>">   
                <input type="hidden" class="form-control" name="kodeTransaksi" value="<?php echo $_POST['kodeTransaksi'];?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>barang:</label>
                <select class="form-control" name="kodeBarang">
                    <?php
                        include '../../../config/database.php';
                        if ($kodeBarang=='') echo "<option value='0'>-</option>";
                        $hasil=mysqli_query($kon,"SELECT vb.*, b.namaBarang 
                            FROM varianbarang vb 
                            INNER JOIN barang b ON vb.kodeBarang = b.kodeBarang 
                            ORDER BY vb.idVarian ASC
                            ");
                        while ($data = mysqli_fetch_array($hasil)):
                    ?>
                        <option <?php if ($kodeBarang==$data['kodeBarang']) echo "selected"; ?>  value="<?php echo $data['kodeBarang']; ?>"><?php echo $data['namaBarang']; ?></option>
                        <?php endwhile; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-5">
            <div class="form-group">
                <button class="btn btn-warning btn-circle" name="edit_transaksi_barang" ><i class="fas fa-cart-plus"></i> Update</button>
            </div>
        </div>
    </div>
</form>