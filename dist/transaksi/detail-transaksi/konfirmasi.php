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
            $query = "UPDATE barang SET stok = stok + ($jumlah) WHERE kodeBarang='" . mysqli_real_escape_string($kon, $this->kodeBarang) . "'";
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
                        status = '" . mysqli_real_escape_string($kon, $this->status) . "', 
                        tglTransaksi = '" . mysqli_real_escape_string($kon, $this->tanggal) . "'
                    WHERE id_detail_transaksi = '" . mysqli_real_escape_string($kon, $this->id_detail_transaksi) . "'";
            return mysqli_query($kon, $sql);
        }
    }

    function input($data) {
        return isset($data) ? htmlspecialchars(trim($data)) : '';
    }

    // Mapping angka ke enum string
    $status_map = [
        '0' => 'belum dibayar',
        '1' => 'dikemas',
        '2' => 'dikirim',
        '3' => 'selesai'
        // Tambahkan di database enum kalau mau 'dibatalkan' atau 'selesai'
    ];

    $status_input = input($_POST["status"]);
    if (!array_key_exists($status_input, $status_map)) {
        mysqli_query($kon, "ROLLBACK");
        die("Status tidak valid");
    }

    $status_enum = $status_map[$status_input];

    $kodeBarang = input($_POST["kodeBarang"]);
    $barang = new Barang($kodeBarang);

    $transaksi = new Transaksi(
        input($_POST["id_detail_transaksi"]),
        input($_POST["kodeTransaksi"]),
        $status_enum,
        input($_POST["kodePelanggan"]),
        date('Y-m-d H:i:s'),
        $barang
    );

    $updateTransaksi = $transaksi->updateTransaksi($kon);

    // Update stok jika dibatalkan (hanya jika enum mendukung)
    $updateStok = true;
    if ($status_enum === 'dibatalkan') {
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
    <input type="hidden" name="id_detail_transaksi" value="<?= htmlspecialchars($_POST['id_detail_transaksi']) ?>"/>
    <input type="hidden" name="kodeTransaksi" value="<?= htmlspecialchars($_POST['kodeTransaksi']) ?>"/>
    <input type="hidden" name="kodeBarang" value="<?= htmlspecialchars($_POST['kodeBarang']) ?>"/>
    <input type="hidden" name="kodePelanggan" value="<?= htmlspecialchars($_POST['kodePelanggan']) ?>"/>

    <div class="form-group">
        <label for="status">Status:</label>
        <select class="form-control" name="status" id="status" required>
            <option value="0" <?= $_POST['status'] == '0' ? 'selected' : '' ?>>Belum Dibayar</option>
            <option value="1" <?= $_POST['status'] == '1' ? 'selected' : '' ?>>Dikemas</option>
            <option value="2" <?= $_POST['status'] == '2' ? 'selected' : '' ?>>Dikirim</option>
            <option value="3" <?= $_POST['status'] == '3' ? 'selected' : '' ?>>Selesai</option>
        </select>
    </div>

    <button type="submit" name="konfirmasi" class="btn btn-success">Update Status</button>
</form>
