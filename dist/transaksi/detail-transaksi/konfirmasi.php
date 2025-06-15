<?php
session_start();
if (isset($_POST['konfirmasi'])) {

    include '../../../config/database.php';
    mysqli_query($kon, "START TRANSACTION");

    class Barang {
        public $kodeBarang;

        public function __construct($kodeBarang) {
            $this->kodeBarang = $kodeBarang;
        }

        public function updateStok($kon, $jumlah) {
            $query = "UPDATE barang SET stok = stok + ($jumlah) WHERE kodeBarang='$this->kodeBarang'";
            return mysqli_query($kon, $query);
        }
    }

    class Transaksi {
        public $id_detail_transaksi;
        public $kodeTransaksi;
        public $status;
        public $kodePelanggan;
        public $tanggal;
        public $barang;

        public function __construct($id_detail_transaksi, $kodeTransaksi, $status, $kodePelanggan, $tanggal, Barang $barang) {
            $this->id_detail_transaksi = $id_detail_transaksi;
            $this->kodeTransaksi = $kodeTransaksi;
            $this->status = $status;
            $this->kodePelanggan = $kodePelanggan;
            $this->tanggal = $tanggal;
            $this->barang = $barang;
        }

        public function updateTransaksi($kon) {
            $sql = "UPDATE detail_transaksi SET 
                        status='$this->status', 
                        tglTransaksi='$this->tanggal' 
                    WHERE id_detail_transaksi='$this->id_detail_transaksi'";
            return mysqli_query($kon, $sql);
        }
    }

    function input($data) {
        return isset($data) ? htmlspecialchars(trim($data)) : '';
    }

    $kodeBarang = input($_POST["kodeBarang"]);
    $barang = new Barang($kodeBarang);

    $transaksi = new Transaksi(
        input($_POST["id_detail_transaksi"]),
        input($_POST["kodeTransaksi"]),
        input($_POST["status"]),
        input($_POST["kodePelanggan"]),
        date('Y-m-d H:i:s'),
        $barang
    );

    $updateTransaksi = $transaksi->updateTransaksi($kon);

    // Update stok hanya jika dibatalkan (status = 3), atau selesai (4) bila perlu
    $updateStok = true;
    if ($transaksi->status == '3') { // Jika dibatalkan
        $updateStok = $barang->updateStok($kon, 1);
    }

    if ($updateTransaksi && $updateStok) {
        mysqli_query($kon, "COMMIT");
        header("Location:../../index.php?page=detail-transaksi&kodeTransaksi={$transaksi->kodeTransaksi}&konfirmasi=berhasil#bagian_detail_transaksi");
    } else {
        mysqli_query($kon, "ROLLBACK");
        header("Location:../../index.php?page=detail-transaksi&kodeTransaksi={$transaksi->kodeTransaksi}&konfirmasi=gagal#bagian_detail_transaksi");
    }
}
?>
<form action="transaksi/detail-transaksi/konfirmasi.php" method="post">
    <input type="hidden" name="tanggal" value="<?php echo date('Y-m-d'); ?>"/>
    <input type="hidden" name="id_detail_transaksi" value="<?php echo $_POST['id_detail_transaksi']; ?>"/>
    <input type="hidden" name="kodeTransaksi" value="<?php echo $_POST['kodeTransaksi']; ?>"/>
    <input type="hidden" name="kodeBarang" value="<?php echo $_POST['kodeBarang']; ?>"/>
    <input type="hidden" name="kodePelanggan" value="<?php echo $_POST['kodePelanggan']; ?>"/>

    <div class="form-group">
        <label for="status">Status:</label>
        <select class="form-control" name="status" id="status" required>
            <option value="0" <?= $_POST['status'] == '0' ? 'selected' : '' ?>>Belum Dibayar</option>
            <option value="1" <?= $_POST['status'] == '1' ? 'selected' : '' ?>>Dikemas</option>
            <?php if ($_POST['status'] >= 1): ?>
                <option value="2" <?= $_POST['status'] == '2' ? 'selected' : '' ?>>Dikirim</option>
                <option value="4" <?= $_POST['status'] == '4' ? 'selected' : '' ?>>Selesai</option>
            <?php endif; ?>
            <option value="3" <?= $_POST['status'] == '3' ? 'selected' : '' ?>>Dibatalkan</option>
        </select>
    </div>

    <button type="submit" name="konfirmasi" class="btn btn-success">Update Status</button>
</form>
