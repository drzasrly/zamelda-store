<?php
    $host="localhost:3306";
    $user="root";
    $password="";

    $db="zamalda";
    $password="";
    $kon = mysqli_connect($host,$user,$password,$db);
    if (!$kon){
          die("Koneksi gagal:".mysqli_connect_error());
    }
?>