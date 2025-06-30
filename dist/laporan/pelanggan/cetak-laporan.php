<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <!-- Custom styles for this template -->
  <link href="../../../src/templates/css/styles.css" rel="stylesheet">
  <link href="../../../src/plugin/bootstrap/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <title>LAPORAN PUSTAKA</title>
</head>
    <body onload="window.print();">
        <?php
        include '../../../config/database.php';
   
        $query = mysqli_query($kon, "select * from profil_aplikasi order by nama_aplikasi desc limit 1");    
        $row = mysqli_fetch_array($query);
        ?>
        <div class="container-fluid">
            <div class="card">
            <div class="card-header py-3">
                <div class="row">
                    <div class="col-sm-2 float-left">
                    <img src="../../aplikasi/logo/<?php echo $row['logo']; ?>" width="95px" alt="brand"/>
                    </div>
                    <div class="col-sm-10 float-left">
                        <h3><?php echo strtoupper($row['nama_aplikasi']);?></h3>
                        <h6><?php echo $row['alamat'].', Telp '.$row['no_telp'];?></h6>
                        <h6><?php echo $row['website'];?></h6>
                    </div>
                </div>
            </div>
                <div class="card-body">
                    <!--rows -->
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>No Telp</th>
                                        <th>Alamat</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    // include database
                                    include '../../../config/database.php';
                                    $kondisi="";
                                    $status="";
                              
                                    if ($_GET['kata_kunci']=='aktif' or $_GET['kata_kunci']=='AKTIF'){
                                        $status='1';
                                    }else {
                                        $status='0';
                                    }
                                    $kata_kunci=$_GET['kata_kunci'];
                                    $sql="select *
                                    from pelanggan a
                                    inner join pengguna p on p.kodePengguna=a.kodePelanggan
                                    where kodePelanggan like'%".$kata_kunci."%' or namaPelanggan like'%".$kata_kunci."%' or email like'%".$kata_kunci."%' or status='".$status."'
                                    ";
                                    $hasil=mysqli_query($kon,$sql);
                                    $no=0;
                                    $status='';
                                    $tanggal_kembali="-";
                                    //Menampilkan data dengan perulangan while
                                    while ($data = mysqli_fetch_array($hasil)):
                                    $no++;
                
                                ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $data['kodePelanggan']; ?> </td>
                                    <td><?php echo $data['namaPelanggan']; ?> </td>
                                    <td><?php echo $data['email']; ?> </td>
                                    <td><?php echo $data['noTelp']; ?> </td>
                                    <td><?php echo $data['alamat']; ?> </td>
                                    <td>
                                        <?php
                                            if ($data['status']=='1'){
                                                echo "Aktif";
                                            }else {
                                                echo "Tidak Aktif";
                                            }
                                        ?> 
                                    </td>
                                
                                </tr>
                                <!-- bagian akhir (penutup) while -->
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>