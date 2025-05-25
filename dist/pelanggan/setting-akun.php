<?php
session_start();
    if (isset($_POST['simpan_profil'])) {
        include '../../config/database.php';
        //session_start();
        mysqli_query($kon,"START TRANSACTION");
        
        function input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        if ($_SESSION["level"]=='Pelanggan' or $_SESSION["level"]=='pelanggan'){
           
            $idPelanggan=$_POST["idPelanggan"];
            $namaPelanggan=input($_POST["namaPelanggan"]);
            $noTelp=input($_POST["noTelp"]);
            $email=input($_POST["email"]);
            $alamat=input($_POST["alamat"]);
            
          
            $foto_saat_ini=$_POST['foto_saat_ini'];
            $foto_baru = $_FILES['foto_baru']['name'];
            $ekstensi_diperbolehkan	= array('png','jpg','jpeg','gif');
            $x = explode('.', $foto_baru);
            $ekstensi = strtolower(end($x));
            $ukuran	= $_FILES['foto_baru']['size'];
            $file_tmp = $_FILES['foto_baru']['tmp_name'];

            if (!empty($foto_baru)){
                if(in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
                    move_uploaded_file($file_tmp,'../pelanggan/foto/'.$foto_baru);
                    if ($foto_saat_ini!='foto_default.png'){
                        unlink("../pelanggan/foto/".$foto_saat_ini);
                    }
                    
                    $sql="update pelanggan set
                    namaPelanggan='$nama  Pelanggana',
                    noTelp='$noTelp',
                    alamat='$alamat',
                    foto='$foto_baru'
                    where idPelanggan=$idPelanggan";
                }
            } else {
                $sql="update pelanggan set
                namaPelanggan='$namaPelanggana',
                noTelp='$noTelp',
                alamat='$alamat'
                where idPelanggan=$idPelanggan";    
}

            $update=mysqli_query($kon,$sql);

            $idPengguna=$_POST["idPengguna"];
            $username=input($_POST["username_baru"]);

            $ambil_password=mysqli_query($kon,"select password from pengguna where idPengguna=$idPengguna limit 1");
            $data = mysqli_fetch_array($ambil_password);

            if ($data['password']==$_POST["password"]){
                $password=input($_POST["password"]);
            }else {
                $password=md5(input($_POST["password"]));
            }

            $sql="update pengguna set
            username='$username',
            password='$password'
            where idPengguna=$idPengguna";
            $update_pengguna=mysqli_query($kon,$sql);

        }

        if ($_SESSION["level"]=='Penjual' or $_SESSION["level"]=='penjual'){
           
            $idPenjual=$_POST["idPenjual"];
            $namaPenjual = isset($_POST["nama"]) ? input($_POST["nama"]) : '';
            $noTelp=input($_POST["noTelp"]?? '');
            $alamat=input($_POST["alamat"]?? '');

            $foto_saat_ini=$_POST['foto_saat_ini'];
            $foto_baru = $_FILES['foto_baru']['name'];
            $ekstensi_diperbolehkan	= array('png','jpg','jpeg','gif');
            $x = explode('.', $foto_baru);
            $ekstensi = strtolower(end($x));
            $ukuran	= $_FILES['foto_baru']['size'];
            $file_tmp = $_FILES['foto_baru']['tmp_name'];

            
            if (!empty($foto_baru)){
                if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
                    move_uploaded_file($file_tmp,'../penjual/foto/'.$foto_baru);


                    if ($foto_saat_ini!='foto_default.png'){
                        unlink("../penjual/foto/".$foto_saat_ini);
                    }
                    
                    $sql="update penjual set
                    namaPenjual='$namaPenjual',
                    noTelp='$noTelp',
                    alamat='$alamat',
                    foto='$foto_baru'
                    where idPenjual=$idPenjual";
                }
            }else {
                $sql="update penjual set
                namaPenjual='$namaPenjual',
                noTelp='$noTelp',
                alamat='$alamat'
                where idPenjual=$idPenjual";;
            }

            
            $update=mysqli_query($kon,$sql);

       
            $idPengguna=$_POST["idPengguna"];
            $username=input($_POST["username_baru"]);

            $ambil_password=mysqli_query($kon,"select password from pengguna where idPengguna=$idPengguna limit 1");
            $data = mysqli_fetch_array($ambil_password);

            if ($data['password']==$_POST["password"]){
                $password=input($_POST["password"]);
            }else {
                $password=md5(input($_POST["password"]));
            }

            $sql="update pengguna set
            username='$username',
            password='$password'
            where idPengguna=$idPengguna";
            $update_pengguna=mysqli_query($kon,$sql);

        }

        if ($update and $update_pengguna) {
            mysqli_query($kon,"COMMIT");
            header("Location:../../dist/index.php?page=profil&edit=berhasil");
        }
        else {
            mysqli_query($kon,"ROLLBACK");
            header("Location:../../dist/index.php?page=profil&edit=gagal");
        }
    }    
?>

