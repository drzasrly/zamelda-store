<?php
session_start();
include '../../config/database.php';

function input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_POST['tambah_barang']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    mysqli_query($kon, "START TRANSACTION");

    $kodeBarang = input($_POST["kodeBarang"]);
    $namaBarang = input($_POST["namaBarang"]);
    $kodeKategori = input($_POST["kodeKategori"]);
    $deskripsi = input($_POST["deskripsi"]);

    $simpan_barang = mysqli_query($kon, "INSERT INTO barang (kodeBarang, namaBarang, kodeKategori, deskripsi) VALUES ('$kodeBarang', '$namaBarang', '$kodeKategori', '$deskripsi')");

    // Proses gambar utama
    $jumlah_gambar_utama = count($_FILES['gambarUtama']['name']);
    if ($jumlah_gambar_utama > 5) $jumlah_gambar_utama = 5;

    for ($i = 0; $i < $jumlah_gambar_utama; $i++) {
        $namaFile = $_FILES['gambarUtama']['name'][$i];
        $tmpName = $_FILES['gambarUtama']['tmp_name'][$i];
        $ukuran = $_FILES['gambarUtama']['size'][$i];
        $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
        $allowed_ext = ['png', 'jpg', 'jpeg'];

        if (in_array($ekstensi, $allowed_ext) && $ukuran < 2048000) {
            $namaBaru = uniqid() . '.' . $ekstensi;
            move_uploaded_file($tmpName, "gambar/" . $namaBaru);
            mysqli_query($kon, "INSERT INTO gambarutama (kodeBarang, gambarUtama) VALUES ('$kodeBarang', '$namaBaru')");
        }
    }

    // Proses gambar varian dan varian terkait
    $gambar_varian = $_FILES['gambarBarang'];
    $jumlah_gambar_varian = count($gambar_varian['name']);

    for ($i = 0; $i < $jumlah_gambar_varian; $i++) {
        $namaFile = $gambar_varian['name'][$i];
        $tmpName = $gambar_varian['tmp_name'][$i];
        $ukuran = $gambar_varian['size'][$i];
        $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
        $allowed_ext = ['png', 'jpg', 'jpeg'];

        if (in_array($ekstensi, $allowed_ext) && $ukuran < 2048000) {
            $namaBaru = uniqid() . '.' . $ekstensi;
            move_uploaded_file($tmpName, 'gambar/' . $namaBaru);

            // Simpan gambar ke tabel gambarvarian
            mysqli_query($kon, "INSERT INTO gambarvarian (kodeBarang, gambarvarian) VALUES ('$kodeBarang', '$namaBaru')");
            $idGambarVarian = mysqli_insert_id($kon);

            // Simpan varian terkait gambar ini
            if (isset($_POST['ukuran'][$i])) {
                $ukuranArr = $_POST['ukuran'][$i];
                $varianArr = $_POST['varian'][$i];
                $hargaArr = $_POST['harga'][$i];
                $stokArr = $_POST['stok'][$i];

                for ($j = 0; $j < count($ukuranArr); $j++) {
                    $ukuranVar = input($ukuranArr[$j]);
                    $typeVarian = input($varianArr[$j]);
                    $harga = input($hargaArr[$j]);
                    $stok = input($stokArr[$j]);

                    $query_varian = "INSERT INTO varianbarang (kodeBarang, idGambarVarian, typeVarian, size, harga, stok) 
                                    VALUES ('$kodeBarang', $idGambarVarian, '$typeVarian', '$ukuranVar', '$harga', '$stok')";
                    mysqli_query($kon, $query_varian);
                }
            }

        }
    }

    if ($simpan_barang) {
        mysqli_query($kon, "COMMIT");
        header("Location:../../dist/index.php?page=barang&add=berhasil");
    } else {
        mysqli_query($kon, "ROLLBACK");
        header("Location:../../dist/index.php?page=barang&add=gagal");
    }
}

$query = mysqli_query($kon, "SELECT MAX(RIGHT(kodeBarang, 3)) as kodeTerbesar FROM barang");
$data = mysqli_fetch_array($query);
$kode_terakhir = (int)$data['kodeTerbesar'];
$kode_terbaru = $kode_terakhir + 1;
$kodeBarang = "br" . sprintf("%03s", $kode_terbaru);
?>

<form action="barang/tambah.php" method="post" enctype="multipart/form-data">
  <div class="row">
    <div class="col-sm-10">
      <div class="form-group">
        <label>Nama Barang:</label>
        <input name="namaBarang" type="text" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Deskripsi:</label>
        <input name="deskripsi" type="text" class="form-control" required>
      </div>
    </div>

    <div class="col-sm-2">
      <div class="form-group">
        <label>Kode:</label>
        <h4><?php echo $kodeBarang; ?></h4>
        <input name="kodeBarang" value="<?php echo $kodeBarang; ?>" type="hidden">
      </div>
    </div>
  </div>
  <div class="form-group">
      <label>Kategori</label>
        <select name="kodeKategori" class="form-control" required>
          <?php
            $kategori = mysqli_query($kon, "SELECT * FROM kategoribarang");
              while ($row = mysqli_fetch_assoc($kategori)) {
                echo "<option value='{$row['kodeKategori']}'>{$row['namaKategori']}</option>";
              }
          ?>
        </select>
  </div>

<div class="form-group">
  <label>Gambar Utama (maksimal 5 gambar):</label>
  <input type="file" name="gambarUtama[]" class="form-control-file" multiple accept="image/*" required>
</div>


  <hr>
  <h5>Data Varian Barang</h5>
<div id="form-varian">
  <div class="varian-item border p-3 mb-3" data-index="0">
    <label>Gambar Varian:</label>
    <input type="file" name="gambarBarang[]" required class="form-control-file">

    <div class="sub-varian mt-2">
      <div class="form-row mb-2">
        <div class="col"><input type="text" name="ukuran[0][]" placeholder="Ukuran" class="form-control" required></div>
        <div class="col"><input type="text" name="varian[0][]" placeholder="Varian" class="form-control" required></div>
        <div class="col"><input type="number" name="harga[0][]" placeholder="Harga" class="form-control" required></div>
        <div class="col"><input type="number" name="stok[0][]" placeholder="Stok" class="form-control" required></div>
      </div>
    </div>

    <button type="button" class="btn btn-sm btn-primary add-sub-varian">+ Tambah Ukuran</button>
    <button type="button" class="btn btn-sm btn-danger remove-varian">Hapus Gambar Varian</button>
  </div>
</div>

<button type="button" id="add-varian" class="btn btn-secondary mt-3 mb-3">+ Tambah Gambar Varian</button>


  <div class="form-group">
    <button type="submit" name="tambah_barang" class="btn btn-success">Tambah Barang</button>
  </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $('#add-varian').click(function () {
    let item = $('.form-varian-item').first().clone();
    item.find('input').val('');
    $('#form-varian').append(item);
  });

  $('#form-varian').on('click', '.remove-varian', function () {
    if ($('.form-varian-item').length > 1) {
      $(this).closest('.form-varian-item').remove();
    }
  });
</script>
<script>
$(document).ready(function () {
let varianIndex = 1;

$('#add-varian').click(function () {
  let item = $('.varian-item').first().clone();
  item.find('input').val('');
  item.find('.sub-varian').html(getSubVarianRow(varianIndex));
  item.attr('data-index', varianIndex);

  item.find('input').each(function () {
    let name = $(this).attr('name');
    if (name) {
      name = name.replace(/\[\d+\]/, `[${varianIndex}]`);
      $(this).attr('name', name);
    }
  });

  $('#form-varian').append(item);
  varianIndex++;
});


$('#form-varian').on('click', '.add-sub-varian', function () {
  const container = $(this).siblings('.sub-varian');
  const nameIndex = $(this).closest('.varian-item').data('index'); 
  container.append(getSubVarianRow(nameIndex));
});


  $('#form-varian').on('click', '.remove-varian', function () {
    if ($('.varian-item').length > 1) {
      $(this).closest('.varian-item').remove();
    }
  });

  function getSubVarianRow(index) {
    return `
      <div class="form-row mb-2">
        <div class="col"><input type="text" name="ukuran[${index}][]" placeholder="Ukuran" class="form-control" required></div>
        <div class="col"><input type="text" name="varian[${index}][]" placeholder="Varian" class="form-control" required></div>
        <div class="col"><input type="number" name="harga[${index}][]" placeholder="Harga" class="form-control" required></div>
        <div class="col"><input type="number" name="stok[${index}][]" placeholder="Stok" class="form-control" required></div>
      </div>`;
  }
});
</script>
