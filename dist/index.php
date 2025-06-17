
<?php 

  session_start();
 
  if (!$_SESSION["idPengguna"]){
        header("Location:tampilanAwal.php");
        header("Location:login.php");
        
  }else {

    include '../config/database.php';
    $idPengguna=$_SESSION["idPengguna"];
    $username=$_SESSION["username"];

    $hasil=mysqli_query($kon,"select username from pengguna where idPengguna=$idPengguna");
    $data = mysqli_fetch_array($hasil); 
    $username_db=$data['username'];

    if ($username!=$username_db){
        session_unset();
        session_destroy();
        header("Location:login.php");
    }
  }

?>

<?php
  include '../config/database.php';
  $hasil=mysqli_query($kon,"select * from profil_aplikasi order by nama_aplikasi desc limit 1");
  $data = mysqli_fetch_array($hasil); 
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        
        <title><?php echo $data['nama_aplikasi'];?></title>
        <link href="../src/templates/css/styles.css" rel="stylesheet" />
        <link href="../src/plugin/bootstrap/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />

        <link href="../src/plugin/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
        <script src="../src/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
        <script src="../src/js/jquery/jquery-3.5.1.min.js"></script>
        <script src="../src/plugin/chart/Chart.js"></script>
        <script src="../src/plugin/datatables/jquery.dataTables.min.js"></script>
        <script src="../src/plugin/datatables/dataTables.bootstrap4.min.js"></script>

    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand" href="#"><?php  echo $data['nama_aplikasi'];?></a>
            <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
            <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
             
            </form>
            <ul class="navbar-nav ml-auto ml-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="index.php?page=profil">Profil</a>
                        <a class="dropdown-item" href="#"  data-toggle="modal" data-target="#logoutModal" >Logout</a>
                    </div>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                    <?php 
                        if ($_SESSION['level']=='Admin' or $_SESSION['level']=='admin'):
                    ?>
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Menu</div>
                            <a class="nav-link" href="index.php?page=dashboard">
                                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                Dashboard
                            </a>
                            <a class="nav-link" href="index.php?page=daftar-transaksi">
                                <div class="sb-nav-link-icon"><i class="fas fa-list-ol"></i></div>
                                Transaksi
                            </a>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                                Laporan
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLaporan" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=laporan-transaksi"><i class="fas fa-book"></i>  &nbsp; Transaksi</a>
                                    <a class="nav-link" href="index.php?page=laporan-barang"><i class="fas fa-grip-horizontal"></i> &nbsp; Barang</a>
                                    <a class="nav-link" href="index.php?page=laporan-pelanggan"><i class="fas fa-user-tag"></i> &nbsp; Pelanggan</a>
                                </nav>
                            </div>

                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePustaka" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                                Barang
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePustaka" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=barang"><i class="fas fa-book"></i>  &nbsp; Barang</a>
                                    <a class="nav-link" href="index.php?page=kategori"><i class="fas fa-grip-horizontal"></i> &nbsp; Kategori</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePengguna" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Pengguna
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePengguna" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=pelanggan"><i class="fas fa-user"></i>  &nbsp; Pelanggan</a>
                                    <a class="nav-link" href="index.php?page=penjual"><i class="fas fa-user-tie"></i> &nbsp; Penjual</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePengaturan" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                                Pengaturan
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePengaturan" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=aplikasi"><i class="fas fa-desktop"></i>  &nbsp; Aplikasi</a>
                                </nav>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php 
                        if ($_SESSION['level']=='Penjual' or $_SESSION['level']=='penjual'):?>
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Menu</div>
                            <a class="nav-link" href="index.php?page=dashboard">
                                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                Dashboard
                            </a>
                            <a class="nav-link" href="index.php?page=daftar-transaksi">
                                <div class="sb-nav-link-icon"><i class="fas fa-list-ol"></i></div>
                                Transaksi
                            </a>
                            <!-- <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                                Laporan
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLaporan" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=laporan-transaksi"><i class="fas fa-book"></i>  &nbsp; Transaksi</a>
                                    <a class="nav-link" href="index.php?page=laporan-barang"><i class="fas fa-grip-horizontal"></i> &nbsp; Barang</a>
                                    <a class="nav-link" href="index.php?page=laporan-pelanggan"><i class="fas fa-user-tag"></i> &nbsp; Pelanggan</a>
                                </nav>
                            </div> -->

                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePustaka" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                                Barang
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePustaka" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=barang"><i class="fas fa-book"></i>  &nbsp; Barang</a>
                                    <a class="nav-link" href="index.php?page=kategori"><i class="fas fa-grip-horizontal"></i> &nbsp; Kategori</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePengguna" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Pengguna
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePengguna" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=pelanggan"><i class="fas fa-user"></i>  &nbsp; Pelanggan</a>
                                    <!-- <a class="nav-link" href="index.php?page=penjual"><i class="fas fa-user-tie"></i> &nbsp; Penjual</a> -->
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePengaturan" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                                Pengaturan
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePengaturan" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=aplikasi"><i class="fas fa-desktop"></i>  &nbsp; Aplikasi</a>
                                </nav>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php 
                        if ($_SESSION['level']=='Pelanggan' or $_SESSION['level']=='pelanggan'):
                        ?>
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Menu</div>
                            <a class="nav-link" href="index.php?page=dashboard">
                                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                Dashboard
                            </a>
                            <a class="nav-link" href="index.php?page=barang">
                                <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                                Barang
                            </a>
                            <a class="nav-link" href="index.php?page=keranjang">
                                <div class="sb-nav-link-icon"><i class="fas fa-cart-arrow-down"></i></div>
                                Keranjang
                            </a>
                            <a class="nav-link" href="index.php?page=transaksi-saya">
                                <div class="sb-nav-link-icon"><i class="fas fa-list-alt"></i></div>
                                Transaksi
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Login Sebagai:</div>
                        <?php 
                            if ($_SESSION['level']=='admin' or $_SESSION['level']=='Admin'):
                                echo $_SESSION["username"];
                            endif;
                            if ($_SESSION['level']=='Penjual' or $_SESSION['level']=='penjual'):
                                echo $_SESSION["namaPenjual"];
                            endif; 
                            if ($_SESSION['level']=='Pelanggan' or $_SESSION['level']=='pelanggan'):
                                echo $_SESSION["namaPelanggan"];
                            endif; 
                        ?>
                       
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <?php 
                    if(isset($_GET['page'])){
                        $page = $_GET['page'];
                    
                        switch ($page) {
                            case 'dashboard':
                                include "dashboard/index.php";
                                break;
                            case 'pelanggan':
                                include "pelanggan/index.php";
                                break;
                            case 'penjual':
                                include "penjual/index.php";
                                break;
                            case 'barang':
                                include "barang/index.php";
                                break;
                            case 'kategori':
                                include "barang/kategori/index.php";
                                break;
                            case 'input-transaksi':
                                include "transaksi/input-transaksi.php";
                                break;
                            case 'transaksi':
                                include "transaksi/index.php";
                                break;
                            case 'daftar-transaksi':
                                include "transaksi/index.php";
                                break;
                            case 'detail-transaksi':
                                include "transaksi/detail-transaksi.php";
                                break;
                            case 'laporan-transaksi':
                                include "laporan/transaksi/laporan-transaksi.php";
                                break;
                            case 'laporan-barang':
                                include "laporan/barang/laporan-barang.php";
                                break;
                            case 'laporan-pelanggan':
                                include "laporan/pelanggan/laporan-pelanggan.php";
                                break;
                            case 'keranjang':
                                include "keranjang/index.php";
                                break;
                            case 'booking':
                                include "keranjang/booking.php";
                                break;
                            case 'transaksi-saya':
                                include "transaksi/index.php";
                                break;
                            case 'profil':
                                include "profil/index.php";
                            break;
                            case 'aplikasi':
                                include "aplikasi/index.php";
                            break;
                        default:
                            echo "<center><h3>Maaf. Halaman tidak di temukan !</h3></center>";
                            break;
                        }
                    }
                ?>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                        <?php 
                            include '../config/database.php';
                            $hasil=mysqli_query($kon,"select nama_aplikasi from profil_aplikasi order by nama_aplikasi desc limit 1");
                            $data = mysqli_fetch_array($hasil); 
                        ?>
                        <div class="text-muted">Copyright &copy; <?php echo $data['nama_aplikasi'];?> <?php echo date('Y');?></div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="../src/js/scripts.js"></script>
        <script src="../src/plugin/select2/select2.min.js"></script>
        <link href="../src/plugin/select2/select2.min.css" rel="stylesheet" crossorigin="anonymous" />
        <script src="../src/js/jquery-ui/jquery-ui.js"></script>
        <link href="../src/js/jquery-ui/jquery-ui.css" rel="stylesheet" crossorigin="anonymous" />
        <script src="../src/plugin/bootstrap/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
          <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Keluar Aplikasi</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
                </div>
                <div class="modal-body">Apakah anda yakin ingin keluar?</div>
                <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <a class="btn" href="logout.php" style="background-color: #706D54; color: white;">Logout</a>
                </div>
            </div>
            </div>
        </div>
    </body>
</html>
