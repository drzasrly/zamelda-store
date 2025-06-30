<?php
session_start();
?>
<canvas id="transaksi_per_bulan" width="100%" height="60"></canvas>

<?php
include '../../config/database.php';
$tahun = date('Y');

// Label bulan
$label = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", 
          "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
$total = [];

// Ambil data transaksi per bulan
for ($bulan = 1; $bulan <= 12; $bulan++) {
    if ($_SESSION["level"] == 'Pelanggan' || $_SESSION["level"] == 'pelanggan') {
        $kodePelanggan = $_SESSION["kodePengguna"];
        $sql = "
            SELECT COUNT(*) as total
            FROM transaksi p
            INNER JOIN detail_transaksi d ON p.kodeTransaksi = d.kodeTransaksi
            WHERE MONTH(d.tglTransaksi) = '$bulan'
              AND YEAR(d.tglTransaksi) = '$tahun'
              AND p.kodePelanggan = '$kodePelanggan'
        ";
    } else {
        $sql = "
            SELECT COUNT(*) as total
            FROM transaksi p
            INNER JOIN detail_transaksi d ON p.kodeTransaksi = d.kodeTransaksi
            WHERE MONTH(d.tglTransaksi) = '$bulan'
              AND YEAR(d.tglTransaksi) = '$tahun'
        ";
    }

    $hasil = mysqli_query($kon, $sql);
    $data = mysqli_fetch_assoc($hasil);
    $total[] = isset($data['total']) ? (int)$data['total'] : 0;
}
?>

<script>
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

var ctx = document.getElementById("transaksi_per_bulan");
var myBarChart = new Chart(ctx, {
  type: 'bar', // Ganti ke 'line' kalau ingin grafik garis
  data: {
    labels: <?php echo json_encode($label); ?>,
    datasets: [{
      label: "Jumlah Transaksi",
      backgroundColor: "rgba(54, 162, 235, 0.7)",
      borderColor: "rgba(54, 162, 235, 1)",
      borderWidth: 1,
      data: <?php echo json_encode($total); ?>,
    }],
  },
  options: {
    responsive: true,
    title: {
      display: true,
      text: 'Transaksi per Bulan - Tahun <?php echo $tahun; ?>'
    },
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero: true,
          precision: 0 // agar tidak ada koma
        },
        gridLines: {
          display: true
        }
      }],
      xAxes: [{
        gridLines: {
          display: false
        }
      }]
    }
  }
});
</script>
