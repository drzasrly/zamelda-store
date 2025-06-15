<?php
session_start();
if (isset($_POST['konfirmasi'])) {

    include '../../config/database.php';
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
        public $kodeBarang;

        public function __construct($id_detail_transaksi, $kodeTransaksi, $status, $kodeBarang) {
            $this->id_detail_transaksi = $id_detail_transaksi;
            $this->kodeTransaksi = $kodeTransaksi;
            $this->status = $status;
            $this->kodeBarang = $kodeBarang;
        }

        public function updateTransaksi($kon) {
            $sql = "UPDATE detail_transaksi SET
                        status='$this->status'
                    WHERE id_detail_transaksi='$this->id_detail_transaksi'";
            return mysqli_query($kon, $sql);
        }
    }

    function input($data) {
        return isset($data) ? htmlspecialchars(trim(stripslashes($data))) : '';
    }

    $barang = new Barang(input($_POST["kodeBarang"]));
    $transaksi = new Transaksi(
        input($_POST["id_detail_transaksi"]),
        input($_POST["kodeTransaksi"]),
        input($_POST["status"]),
        input($_POST["kodeBarang"])
    );

    $updateTransaksi = $transaksi->updateTransaksi($kon);
    $updateStok = false;

    if ($transaksi->status == 1) {
        // Sedang dipinjam -> stok berkurang
        $updateStok = $barang->updateStok($kon, -1);
    } elseif ($transaksi->status == 2) {
        // Telah selesai -> stok kembali
        $updateStok = $barang->updateStok($kon, 1);
    } else {
        // Status lainnya, tidak update stok
        $updateStok = true;
    }

    if ($updateTransaksi && $updateStok) {
        mysqli_query($kon, "COMMIT");
        header("Location: ../../index.php?page=detail-transaksi&kodeTransaksi=$transaksi->kodeTransaksi&konfirmasi=berhasil#bagian_detail_transaksi");
    } else {
        mysqli_query($kon, "ROLLBACK");
        header("Location: ../../index.php?page=detail-transaksi&kodeTransaksi=$transaksi->kodeTransaksi&konfirmasi=gagal#bagian_detail_transaksi");
    }
}
?>
