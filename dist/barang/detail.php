<?php
session_start();
include '../../config/database.php';

class BarangDetailCarouselDenganThumbnail {
    protected $kon;

    public function __construct($kon) {
        $this->kon = $kon;
    }

    protected function getBarang($idBarang) {
        $idBarang = mysqli_real_escape_string($this->kon, $idBarang);
        $sql = "SELECT b.*, k.namaKategori 
                FROM barang b
                INNER JOIN kategoribarang k ON k.kodeKategori = b.kodeKategori
                WHERE b.idBarang = '$idBarang' LIMIT 1";
        $result = mysqli_query($this->kon, $sql);
        return mysqli_fetch_assoc($result);
    }

    protected function getGambarUtama($kodeBarang) {
        $kodeBarang = mysqli_real_escape_string($this->kon, $kodeBarang);
        $sql = "SELECT * FROM gambarutama WHERE kodeBarang = '$kodeBarang'";
        $result = mysqli_query($this->kon, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    protected function getVarianBarang($kodeBarang) {
        $kodeBarang = mysqli_real_escape_string($this->kon, $kodeBarang);
        $sql = "SELECT v.*, g.gambarvarian 
                FROM varianbarang v
                LEFT JOIN gambarvarian g ON v.idGambarVarian = g.idGambarVarian
                WHERE v.kodeBarang = '$kodeBarang'";
        $result = mysqli_query($this->kon, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function tampilkanDetail($idBarang) {
        $barang = $this->getBarang($idBarang);
        if (!$barang) {
            echo "<div class='alert alert-danger'>Barang tidak ditemukan.</div>";
            return;
        }

        $gambarUtamaList = $this->getGambarUtama($barang['kodeBarang']);
        $varians = $this->getVarianBarang($barang['kodeBarang']);

        echo "<h4>{$barang['namaBarang']} <small class='text-muted'>({$barang['namaKategori']})</small></h4>";

        $semuaGambar = [];

        $gambarVarianUnik = [];
        foreach ($varians as $varian) {
            if (!empty($varian['gambarvarian'])) {
                $gambarVarianUnik[$varian['gambarvarian']] = true;
            }
        }
        $jumlahGambarVarianUnik = count($gambarVarianUnik);

        if ($jumlahGambarVarianUnik > 1) {
            foreach ($gambarUtamaList as $gambar) {
                $semuaGambar[] = [
                    'tipe' => 'utama',
                    'gambar' => $gambar['gambarUtama'],
                    'info' => $barang['deskripsi']
                ];
            }
        }

        foreach ($varians as $varian) {
            if (!empty($varian['gambarvarian'])) {
                $semuaGambar[] = [
                    'tipe' => 'varian',
                    'gambar' => $varian['gambarvarian'],
                    'info' => $varian,
                    'idVarian' => $varian['idVarian']
                ];
            }
        }

        $varianSlideIndexMap = [];
        foreach ($semuaGambar as $index => $item) {
            if ($item['tipe'] === 'varian' && isset($item['idVarian'])) {
                $varianSlideIndexMap[$item['idVarian']] = $index;
            }
        }

        $typeVarianGroups = [];
        foreach ($varians as $v) {
            $type = $v['typeVarian'];
            if (!isset($typeVarianGroups[$type])) {
                $typeVarianGroups[$type] = [];
            }
            $typeVarianGroups[$type][] = $v;
        }

        $ukuranTetap = ['S', 'M', 'L', 'XL'];
        $buttonsPerTypeVarian = [];

        foreach ($typeVarianGroups as $type => $listVarian) {
            ob_start();

            echo "<div class='mb-3'>";
            echo "<strong>$type:</strong><br>";

            $varianMap = [];
            foreach ($listVarian as $v) {
                $varianMap[$v['size']] = $v;
            }

            foreach ($ukuranTetap as $size) {
                if (isset($varianMap[$size])) {
                    $v = $varianMap[$size];
                    $disabled = ($v['stok'] <= 0 || empty($v['gambarvarian']));
                    $btnClass = $disabled ? 'btn-outline-secondary disabled' : 'btn-outline-primary';
                    $label = $disabled ? "$size (Stok Habis)" : $size;

                    $slideIndex = isset($varianSlideIndexMap[$v['idVarian']]) ? $varianSlideIndexMap[$v['idVarian']] : -1;

                    echo "<button 
                            class='btn $btnClass m-1 varian-btn' 
                            data-harga='{$v['harga']}' 
                            data-stok='{$v['stok']}' 
                            data-value='{$size}' 
                            data-type='{$type}' 
                            data-slide='{$slideIndex}' 
                            " . ($disabled ? "disabled" : "") . ">$label</button>";
                } else {
                    echo "<button class='btn btn-outline-secondary m-1 disabled' disabled>$size (Stok Habis)</button>";
                }
            }

            echo "</div>";
            $buttonsPerTypeVarian[$type] = ob_get_clean();
        }

        echo '<div id="carouselDetailBarang" class="carousel slide position-relative" data-ride="carousel">';
        echo '<div class="carousel-inner">';

        foreach ($semuaGambar as $index => $item) {
            $active = $index === 0 ? 'active' : '';
            echo "<div class='carousel-item $active'>";
            echo "<div class='row'>";
            echo "<div class='col-md-6 text-center'>";
            echo "<img class='img-fluid' src='../dist/barang/gambar/{$item['gambar']}' alt='Gambar {$index}'>";
            echo '<a class="carousel-control-prev no-bg-arrow" href="#carouselDetailBarang" role="button" data-slide="prev"><span>&lt;</span></a>';
            echo '<a class="carousel-control-next no-bg-arrow" href="#carouselDetailBarang" role="button" data-slide="next"><span>&gt;</span></a>';
            echo "</div>";
//
            echo "<div class='col-md-6'>";

            if (count($varians) <= 1 && isset($varians[0])) {
                $varian = $varians[0];
                echo "<div class='mt-3'>";
                echo $buttonsPerTypeVarian[$varian['typeVarian']] ?? '';
                echo "<p><strong>Ukuran:</strong> <span id='info-value-{$index}'>{$varian['size']}</span></p>";
                echo "<p><strong>Type Varian:</strong> <span id='info-type-{$index}'>{$varian['typeVarian']}</span></p>";
                echo "<p><strong>Stok:</strong> <span id='info-stok-{$index}'>{$varian['stok']}</span></p>";
                echo "<p><strong>Harga:</strong> Rp <span id='info-harga-{$index}'>" . number_format($varian['harga'], 0, ',', '.') . "</span></p>";

                if (strtolower($_SESSION['level'] ?? '') === 'pelanggan' && $varian['stok'] > 0) {
                    echo "<a href='index.php?page=keranjang&kodeBarang={$barang['kodeBarang']}&idVarian={$varian['idVarian']}' class='btn btn-primary btn-sm'>";
                    echo "<i class='fas fa-cart-plus'></i> Tambah ke Keranjang</a>";
                } elseif ($varian['stok'] <= 0) {
                    echo "<div class='alert alert-warning p-1 text-center'>Stok Kosong</div>";
                }

                echo "</div>";

            } else if ($item['tipe'] === 'varian') {
                $varian = $item['info'];
                echo "<div class='mt-3'>";
                echo $buttonsPerTypeVarian[$varian['typeVarian']] ?? '';
                echo "<p><strong>Ukuran:</strong> <span id='info-value-{$index}'>{$varian['size']}</span></p>";
                echo "<p><strong>Stok:</strong> {$varian['stok']}</p>";
                echo "<p><strong>Harga:</strong> Rp" . number_format($varian['harga'], 0, ',', '.') . "</p>";

                if (strtolower($_SESSION['level'] ?? '') === 'pelanggan' && $varian['stok'] > 0) {
                    echo "<a href='index.php?page=keranjang&kodeBarang={$barang['kodeBarang']}&idVarian={$varian['idVarian']}' class='btn btn-primary btn-sm'>";
                    echo "<i class='fas fa-cart-plus'></i> Tambah ke Keranjang</a>";
                } elseif ($varian['stok'] <= 0) {
                    echo "<div class='alert alert-warning p-1 text-center'>Stok Kosong</div>";
                }

                echo "</div>";
            } else {
                if (count($varians) > 0 && $jumlahGambarVarianUnik > 1) {
                    $hargaArray = array_column($varians, 'harga');
                    $hargaMin = min($hargaArray);
                    $hargaMax = max($hargaArray);
                    echo "<p><strong>Harga:</strong> Rp" . number_format($hargaMin, 0, ',', '.') . " - Rp" . number_format($hargaMax, 0, ',', '.') . "</p>";
                } else {
                    echo "<p><strong>Harga:</strong> Tidak tersedia</p>";
                }
            }

            echo "</div></div></div>";
        }

        echo '</div></div>';

        if ($jumlahGambarVarianUnik > 1) {
            echo "<div class='mt-3 d-flex justify-content-center flex-wrap'>";
            $typeVarianShown = [];

            foreach ($semuaGambar as $index => $item) {
                if ($item['tipe'] === 'varian') {
                    $typeVarian = $item['info']['typeVarian'] ?? null;

                    if ($typeVarian && !in_array($typeVarian, $typeVarianShown)) {
                        echo "<img src='../dist/barang/gambar/{$item['gambar']}' class='img-thumbnail m-1' width='100' height='100' onclick='goToSlide($index)'>";
                        $typeVarianShown[] = $typeVarian;
                    }
                }
            }

            echo "</div>";
        }

        echo "<script>
            function goToSlide(index) {
                $('#carouselDetailBarang').carousel(index);
            }

            document.querySelectorAll('.varian-btn').forEach(function(btn) {
                btn.addEventListener('click', function () {
                    const harga = parseInt(this.dataset.harga).toLocaleString();
                    const stok = this.dataset.stok;
                    const value = this.dataset.value;
                    const slideIndex = parseInt(this.dataset.slide);

                    if (!isNaN(slideIndex) && slideIndex >= 0) {
                        $('#carouselDetailBarang').carousel(slideIndex);
                        setTimeout(() => {
                            const valEl = document.getElementById('info-value-' + slideIndex);
                            const hargaEl = document.getElementById('info-harga-' + slideIndex);
                            const stokEl = document.getElementById('info-stok-' + slideIndex);
                            if (valEl && hargaEl && stokEl) {
                                valEl.textContent = value;
                                hargaEl.textContent = harga;
                                stokEl.textContent = stok + ' pcs';
                            }
                        }, 500);
                    }
                });
            });
        </script>";

        echo "<div class='col-md-15'><p><strong>Deskripsi:</strong></p><p>{$barang['deskripsi']}</p></div>";
    }
}

$idBarang = $_GET['idBarang'] ?? $_POST['idBarang'] ?? null;
if (!$idBarang) {
    echo "<div class='alert alert-danger'>ID Barang tidak ditemukan.</div>";
    exit;
}

$handler = new BarangDetailCarouselDenganThumbnail($kon);
?>

<div class="container mt-4">
    <?php $handler->tampilkanDetail($idBarang); ?>
</div>
