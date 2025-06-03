<?php
include '../../config/database.php';

function input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_POST['update_barang'])) {
    $kodeBarang = input($_POST['kodeBarang']);
    $namaBarang = input($_POST['namaBarang']);
    $deskripsi = input($_POST['deskripsi']);
    $kodeKategori = input($_POST['kodeKategori']);

    mysqli_query($kon, "UPDATE barang SET namaBarang='$namaBarang', deskripsi='$deskripsi', kodeKategori='$kodeKategori' WHERE kodeBarang='$kodeBarang'");

    if (isset($_FILES['gambarUtama']) && !empty($_FILES['gambarUtama']['name'][0])) {
        $files = $_FILES['gambarUtama'];
        for ($i=0; $i < count($files['name']); $i++) {
            $name = $files['name'][$i];
            $tmp_name = $files['tmp_name'][$i];
            $uploadPath = "barang/gambar/" . $name;

            if (move_uploaded_file($tmp_name, $uploadPath)) {
                mysqli_query($kon, "INSERT INTO gambarutama (kodeBarang, gambarUtama) VALUES ('$kodeBarang', '$name')");
            }
        }
    }

    $gambarBarangFiles = $_FILES['gambarBarang'] ?? ['name' => [], 'tmp_name' => []];
    $ukuran = $_POST['ukuran'] ?? [];
    $varian = $_POST['varian'] ?? [];
    $harga = $_POST['harga'] ?? [];
    $stok = $_POST['stok'] ?? [];

    $resGambarVarian = mysqli_query($kon, "SELECT * FROM gambarvarian WHERE kodeBarang='$kodeBarang'");
    $existingGambarVarian = [];
    while ($row = mysqli_fetch_assoc($resGambarVarian)) {
        $existingGambarVarian[$row['idGambarVarian']] = $row;
    }

    $idGambarVarianForm = $_POST['idGambarVarian'] ?? [];

    foreach ($ukuran as $index => $listUkuran) {
        $idGambarVarianVal = $idGambarVarianForm[$index] ?? 0; 

        if ($idGambarVarianVal > 0 && isset($existingGambarVarian[$idGambarVarianVal])) {
            if ($gambarBarangFiles && isset($gambarBarangFiles['name'][$index]) && $gambarBarangFiles['name'][$index] != '') {
                $oldFileName = $existingGambarVarian[$idGambarVarianVal]['gambarvarian'];
                $tmpName = $gambarBarangFiles['tmp_name'][$index];
                $uploadPath = "barang/gambar/" . $oldFileName;

                if (move_uploaded_file($tmpName, $uploadPath)) {
                }
            }
            mysqli_query($kon, "DELETE FROM varianbarang WHERE idGambarVarian='$idGambarVarianVal'");
            foreach ($listUkuran as $subIndex => $sizeVal) {
                $typeVal = $varian[$index][$subIndex];
                $hargaVal = $harga[$index][$subIndex];
                $stokVal = $stok[$index][$subIndex];
                mysqli_query($kon, "INSERT INTO varianbarang (kodeBarang, idGambarVarian, typeVarian, size, harga, stok) VALUES ('$kodeBarang', '$idGambarVarianVal', '$typeVal', '$sizeVal', '$hargaVal', '$stokVal')");
            }
        } else {
            if ($gambarBarangFiles && isset($gambarBarangFiles['name'][$index]) && $gambarBarangFiles['name'][$index] != '') {
                $fileName = basename($gambarBarangFiles['name'][$index]);
                $tmpName = $gambarBarangFiles['tmp_name'][$index];
                $uploadPath = "barang/gambar/" . $fileName;

                if (move_uploaded_file($tmpName, $uploadPath)) {
                    mysqli_query($kon, "INSERT INTO gambarvarian (kodeBarang, gambarvarian) VALUES ('$kodeBarang', '$fileName')");
                    $newIdGambarVarian = mysqli_insert_id($kon);

                    foreach ($listUkuran as $subIndex => $sizeVal) {
                        $typeVal = $varian[$index][$subIndex];
                        $hargaVal = $harga[$index][$subIndex];
                        $stokVal = $stok[$index][$subIndex];
                        mysqli_query($kon, "INSERT INTO varianbarang (kodeBarang, idGambarVarian, typeVarian, size, harga, stok) VALUES ('$kodeBarang', '$newIdGambarVarian', '$typeVal', '$sizeVal', '$hargaVal', '$stokVal')");
                    }
                }
            } else {
                error_log("Varian baru pada index $index diabaikan karena tidak ada gambar.");
            }
        }
    }

    header("Location:../../dist/index.php?page=barang&edit=berhasil");
    exit;
} else {
    echo "Akses tidak valid.";
}

if (!isset($_POST['idBarang'])) {
    echo "ID Barang tidak ditemukan.";
    exit;
}

$idBarang = input($_POST['idBarang']);
$barang = mysqli_fetch_assoc(mysqli_query($kon, "SELECT * FROM barang WHERE idBarang='$idBarang'"));
$kodeBarang = $barang['kodeBarang'];

$kategori = mysqli_query($kon, "SELECT * FROM kategoribarang");

$gambar_utama = mysqli_query($kon, "SELECT * FROM gambarutama WHERE kodeBarang='$kodeBarang'");

$gambar_varian = mysqli_query($kon, "SELECT * FROM gambarvarian WHERE kodeBarang='$kodeBarang'");

$all_varian = [];
while ($gv = mysqli_fetch_assoc($gambar_varian)) {
    $idGambarVarian = $gv['idGambarVarian'];
    $varianbarang = mysqli_query($kon, "SELECT * FROM varianbarang WHERE idGambarVarian='$idGambarVarian'");
    $varianList = [];
    while ($v = mysqli_fetch_assoc($varianbarang)) {
        $varianList[] = $v;
    }
    $all_varian[] = ['gambar' => $gv, 'varian' => $varianList];
}
?>

<form action="barang/edit.php" method="post" enctype="multipart/form-data">
  <input type="hidden" name="kodeBarang" value="<?= $kodeBarang; ?>">
  <input type="hidden" name="idBarang" value="<?= $barang['idBarang']; ?>">

  <!-- <input type="hidden" name="idGambarVarian[]" value="<?= $item['gambar']['idGambarVarian']; ?>"> -->

  <div class="form-group">
    <label>Nama Barang:</label>
    <input type="text" name="namaBarang" class="form-control" value="<?= $barang['namaBarang']; ?>" required>
  </div>

  <div class="form-group">
    <label>Deskripsi:</label>
    <input type="text" name="deskripsi" class="form-control" value="<?= $barang['deskripsi']; ?>" required>
  </div>

  <div class="form-group">
    <label>Kategori:</label>
    <select name="kodeKategori" class="form-control" required>
      <?php while ($row = mysqli_fetch_assoc($kategori)): ?>
        <option value="<?= $row['kodeKategori'] ?>" <?= $row['kodeKategori'] == $barang['kodeKategori'] ? 'selected' : '' ?>>
          <?= $row['namaKategori'] ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="form-group">
    <label>Ganti Gambar Utama (maks 5):</label>
    <input type="file" name="gambarUtama[]" multiple class="form-control-file">
    <small>Biarkan kosong jika tidak ingin mengganti gambar.</small>
    <br>
    <div>
      <?php while ($gu = mysqli_fetch_assoc($gambar_utama)): ?>
        <img src="barang/gambar/<?= $gu['gambarUtama'] ?>" alt="Gambar Utama" width="120" class="thumbnail">
      <?php endwhile; ?>
    </div>
  </div>

  <h5>Data Varian Barang</h5>
  <div id="form-varian">
    <?php foreach ($all_varian as $index => $item): ?>
      <input type="hidden" name="idGambarVarian[]" value="<?= $item['gambar']['idGambarVarian']; ?>">
      <div class="varian-item border p-3 mb-3" data-index="<?= $index ?>">
        <input type="hidden" name="kodeBarang" value="<?= $kodeBarang; ?>">

        <label>Gambar Varian:</label>
          <img src="barang/gambar/<?= $item['gambar']['gambarvarian']; ?>" alt="Gambar Varian" width="150" class="thumbnail" style="display:block; margin-bottom:10px;">
          <input type="file" name="gambarBarang[<?= $index ?>]" class="form-control-file">

        <div class="sub-varian mt-2">
          <?php foreach ($item['varian'] as $vitem): ?>
            <div class="form-row mb-2">
              <div class="col"><input type="text" name="ukuran[<?= $index ?>][]" placeholder="Ukuran" class="form-control" value="<?= htmlspecialchars($vitem['size']) ?>" required></div>
              <div class="col"><input type="text" name="varian[<?= $index ?>][]" placeholder="Varian" class="form-control" value="<?= htmlspecialchars($vitem['typeVarian']) ?>" required></div>
              <div class="col"><input type="number" name="harga[<?= $index ?>][]" placeholder="Harga" class="form-control" value="<?= $vitem['harga'] ?>" required></div>
              <div class="col"><input type="number" name="stok[<?= $index ?>][]" placeholder="Stok" class="form-control" value="<?= $vitem['stok'] ?>" required></div>
            </div>
          <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-sm btn-primary add-sub-varian">+ Tambah Ukuran</button>
        <button type="button" class="btn btn-sm btn-danger remove-varian">Hapus Gambar Varian</button>
      </div>
    <?php endforeach; ?>

    <?php if (count($all_varian) == 0): ?>
      <div class="varian-item border p-3 mb-3" data-index="0">
        <label>Gambar Varian:</label>
        <input type="file" name="gambarBarang[]" class="form-control-file">

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
    <?php endif; ?>
  </div>

  <button type="button" id="add-varian" class="btn btn-secondary mt-3 mb-3">+ Tambah Gambar Varian</button>

  <div class="form-group">
    <button type="submit" name="update_barang" class="btn btn-success">Update Barang</button>
  </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
  let varianIndex = <?= count($all_varian) ?>;

$('#add-varian').click(function () {
  let item = $('.varian-item').first().clone();

  item.find('input').not('[type=hidden]').val('');
  item.find('input[type=file]').val('');

  item.find('input[name="idGambarVarian[]"]').remove();
  item.prepend(`<input type="hidden" name="idGambarVarian[]" value="">`);

  item.find('input[type="file"]').attr('name', `gambarBarang[${varianIndex}]`);

  item.find('.sub-varian').html(getSubVarianRow(varianIndex));

  item.find('input[name^="ukuran"]').attr('name', `ukuran[${varianIndex}][]`);
  item.find('input[name^="varian"]').attr('name', `varian[${varianIndex}][]`);
  item.find('input[name^="harga"]').attr('name', `harga[${varianIndex}][]`);
  item.find('input[name^="stok"]').attr('name', `stok[${varianIndex}][]`);

  item.attr('data-index', varianIndex);

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
    } else {
      alert("Minimal harus ada satu gambar varian.");
    }
  });

  function getSubVarianRow(index) {
    return `
      <div class="form-row mb-2">
        <div class="col"><input type="text" name="ukuran[${index}][]" placeholder="Ukuran" class="form-control" required></div>
        <div class="col"><input type="text" name="varian[${index}][]" placeholder="Varian" class="form-control" required></div>
        <div class="col"><input type="number" name="harga[${index}][]" placeholder="Harga" class="form-control" required></div>
        <div class="col"><input type="number" name="stok[${index}][]" placeholder="Stok" class="form-control" required></div>
      </div>
    `;
  }

  
});
</script>
