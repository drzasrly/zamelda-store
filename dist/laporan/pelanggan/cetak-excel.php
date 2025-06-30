<?php
session_start();
    //Koneksi database
    include '../../../config/database.php';
    //Mengambil nama aplikasi
    $query = mysqli_query($kon, "select nama_aplikasi from profil_aplikasi order by nama_aplikasi desc limit 1");    
    $row = mysqli_fetch_array($query);

    //Membuat file format excel
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=LAPORAN PELANGGAN ".strtoupper($row['nama_aplikasi']).".xls");
?>  
<h2><center>LAPORAN PELANGGAN <?php echo strtoupper($row['nama_aplikasi']);?></center></h2>
<table border="1">
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