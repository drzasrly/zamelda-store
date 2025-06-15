<?php
include '../../config/database.php';

// Ambil kode barang yang sudah dipilih di cart
$kodeBarang = "";
if (!empty($_SESSION["cart_barang"])) {
    foreach ($_SESSION["cart_barang"] as $item) {
        $kode = $item["kodeBarang"];
        $kodeBarang .= "'$kode',";
    }
    $kodeBarang = rtrim($kodeBarang, ",");
}

// SQL dengan join yang benar + kondisi
if (!empty($_SESSION["cart_barang"])) {
    $sql = "
        SELECT vb.*, gv.gambarVarian 
        FROM varianbarang vb 
        LEFT JOIN gambarvarian gv ON vb.kodeBarang = gv.kodeBarang 
        WHERE vb.kodeBarang NOT IN ($kodeBarang) AND vb.stok >= 1
    ";
} else {
    $sql = "
        SELECT vb.*, gv.gambarVarian 
        FROM varianbarang vb 
        LEFT JOIN gambarvarian gv ON vb.kodeBarang = gv.kodeBarang 
        WHERE vb.stok >= 1
    ";
}

$hasil = mysqli_query($kon, $sql);
$no = 0;
?>

<div class="row">
<?php while ($data = mysqli_fetch_array($hasil)): $no++; ?>
    <div class="col-sm-2">
        <div class="card">
            <div class="card bg-basic">
                <?php
                $gambar = !empty($data['gambarvarian']) ? $data['gambarvarian'] : 'default.png';
                $gambar_path = "barang/gambar/" . $gambar;
                ?>
                <img class="card-img-top" src="barang/gambar/<?php echo $gambar; ?>" alt="Card image cap">
                <div class="card-body text-center">
                    <button type="button" data-dismiss="modal"
                            class="btn-pilih-barang btn btn-dark btn-block"
                            aksi="pilih_barang"
                            kodeBarang="<?php echo $data['kodeBarang']; ?>">
                        <span class="text"><i class="fas fa-mouse-pointer"></i></span> Pilih
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>


<script>
$('.btn-pilih-barang').on('click',function(){
    var aksi = $(this).attr("aksi");
    var kodeBarang= $(this).attr("kodeBarang");

    $.ajax({
        url: 'transaksi/cart.php',
        method: 'POST',
        data:{kodeBarang:kodeBarang,aksi:aksi},
        success:function(data){
            $('#tampil_cart').html(data);
        }
    }); 

});
</script>