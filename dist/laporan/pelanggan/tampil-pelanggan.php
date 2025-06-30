<?php
session_start();
?>
<div class="card mb-4">
    <div class="card-body">
            <div class="table-responsive">
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
                        $kata_kunci=$_POST['kata_kunci'];

                        if ($_POST['kata_kunci']=='aktif' or $_POST['kata_kunci']=='AKTIF'){
                            $status='1';
                        }else {
                            $status='0';
                        }
                        
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
            <a href="laporan/pelanggan/cetak-laporan.php?kata_kunci=<?php if (!empty($_POST["kata_kunci"])) echo $_POST["kata_kunci"]; ?>" target='blank' class="btn btn-primary btn-icon-split"><span class="text"><i class="fas fa-print fa-sm"></i> Cetak Invoice</span></a>
            <a href="laporan/pelanggan/cetak-pdf.php?kata_kunci=<?php if (!empty($_POST["kata_kunci"])) echo $_POST["kata_kunci"]; ?>" target='blank' class="btn btn-danger btn-icon-pdf"><span class="text"><i class="fas fa-file-pdf fa-sm"></i> Export PDF</span></a>
	        <a href="laporan/pelanggan/cetak-excel.php?kata_kunci=<?php if (!empty($_POST["kata_kunci"])) echo $_POST["kata_kunci"]; ?>" target='blank' class="btn btn-success btn-icon-pdf"><span class="text"><i class="fas fa-file-excel fa-sm"></i> Export Excel</span></a>
        </div>
    </div>
</div>