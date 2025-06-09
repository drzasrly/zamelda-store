<script>
    $('title').text('Profil');
</script>
<main>
    <div class="container-fluid">
        <h1 class="mt-4">Profil</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Profil</li>
        </ol>
        <?php
            if (isset($_GET['edit'])) {
                if ($_GET['edit']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Profil telah diupdate</div>";
                }else if ($_GET['edit']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Profil gagal diupdate</div>";
                }    
            }
        ?>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-12 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Profil Pengguna</h6>
                            </div>

                            <?php 
                                include '../config/database.php';
                                $idPengguna=$_SESSION["idPengguna"];
                                
                                if ($_SESSION["level"]=='Penjual' or $_SESSION["level"]=='penjual'){
                                    $sql="select * from pengguna p
                                    inner join penjual k on k.kodePenjual=p.kodePengguna
                                    where p.idPengguna=$idPengguna limit 1";
                                }
                                
                                if ($_SESSION["level"]=='Pelanggan' or $_SESSION["level"]=='pelanggan'){
                                    $sql="select * from pengguna p
                                    inner join pelanggan a on a.kodePelanggan=p.kodePengguna
                                    where p.idPengguna=$idPengguna limit 1";
                                }
                                
                                if ($_SESSION["level"]=='Admin' or $_SESSION["level"]=='admin'){
                                    $sql="select * from pengguna p
                                    inner join admin a on a.kodeAdmin=p.kodePengguna
                                    where p.idPengguna=$idPengguna limit 1";
                                }
                       
                                $hasil=mysqli_query($kon,$sql);
                                $data = mysqli_fetch_array($hasil); 
                            ?>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-3">
                                    <?php 
                                        if ($_SESSION["level"]=='Penjual' or $_SESSION["level"]=='penjual'):
                                    ?>
                                    <img class="card-img-top img-fluid" src="penjual/foto/<?= htmlspecialchars($data['foto']) ?>" alt="<?= htmlspecialchars($data['namaPenjual']) ?>">
                                    <!-- <img class="card-img-top" src="penjual/foto/<?php echo $data['foto'];?>" width="54px" alt="Card image"> -->
                                    <?php endif; ?>
                                    <?php 
                                        if ($_SESSION["level"]=='Admin' or $_SESSION["level"]=='admin'):
                                    ?>
                                    <img class="card-img-top img-fluid" src="penjual/foto/<?= htmlspecialchars($data['foto']) ?>" alt="<?= htmlspecialchars($data['namaAdmin']) ?>">
                                    <?php endif; ?>
                                    <?php 
                                        if ($_SESSION["level"]=='Pelanggan' or $_SESSION["level"]=='pelanggan'):
                                    ?>
                                    <img class="card-img-top img-fluid" src="pelanggan/foto/<?= htmlspecialchars($data['foto']) ?>" alt="<?= htmlspecialchars($data['namaPelanggan']) ?>">
                                    <?php endif; ?>
                                    </div>
                                </div>
                                <BR>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td>Kode</td>
                                                    <td width="80%">: <?php echo $data['kodePengguna'];?></td>
                                                </tr>
                                                <tr>
                                                    <!-- <?php echo 'Level Anda: ' . $_SESSION["level"]; ?> -->
                                                    <td>Nama</td>
                                                    <?php if ($_SESSION["level"]=='Penjual' or $_SESSION["level"]=='penjual'):?>
                                                    <td width="80%">: <?php echo $data['namaPenjual'];?></td>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($_SESSION["level"]=='Admin' or $_SESSION["level"]=='admin'):?>
                                                    <td width="80%">: <?php echo $data['namaAdmin'];?></td>
                                                    <?php endif; ?>    

                                                    <?php if ($_SESSION["level"]=='Pelanggan' or $_SESSION["level"]=='pelanggan'):?>
                                                    <td width="80%">: <?php echo $data['namaPelanggan'];?></td>
                                                    <?php endif; ?>
                                                    
                                                </tr>
                                                <tr>
                                                    <td>Username</td>
                                                    <td width="80%">: <?php echo $data['username'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>No Telp</td>
                                                    <td width="80%">: <?php echo $data['noTelp'];?></td>
                                                </tr>
                                                <?php if ($_SESSION["level"]=='Pelanggan' or $_SESSION["level"]=='pelanggan'):?>
                                                <tr>
                                                    <td>Alamat</td>
                                                    <td width="80%">: <?php echo $data['alamat'];?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <td>Sebagai</td>
                                                    <td width="80%">: <?php echo $data['level'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>Status</td>
                                                    <td width="80%">: <?php echo $data['status'] == 1 ? 'Aktif' : 'Tidak Aktif';?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#ubah_profil">Edit Profil</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="ubah_profil">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ubah Profil</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                    <form action="profil/edit-profil.php" method="post" enctype="multipart/form-data">
                        <div class="card">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Data Diri</h6>
                            </div>
                            <div class="card-body">
 
                            <?php 
                                if ($_SESSION["level"]=='Penjual' or $_SESSION["level"]=='penjual'):
                            ?>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Kode:</label>
                                            <input name="kode" value="<?php echo $data['kodePengguna']?>" type="text" class="form-control" placeholder="Masukan kode" disabled>
                                            <input name="idPengguna" value="<?php echo $data['idPengguna'];?>" type="hidden" class="form-control">
                                            <input name="idPenjual" value="<?php echo $data['idPenjual'];?>" type="hidden" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Nama:</label>
                                             <input name="namaPenjual" value="<?php echo $data['namaPenjual']?>" type="text" class="form-control" placeholder="Masukan nama" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>No Telp:</label>
                                            <input name="noTelp" value="<?php echo $data['noTelp']?>" type="text" class="form-control" placeholder="Masukan no telp" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Alamat:</label>
                                            <textarea class="form-control" name="alamat" rows="5" ><?php echo $data['alamat'];?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div id="msg"></div>
                                        <label>Foto:</label>
                                        <input type="hidden" name="foto_saat_ini" value="<?php echo $data['foto'];?>" class="form-control" />
                                        <input type="file" name="foto_baru" class="file" >
                                            <div class="input-group my-3">
                                                <input type="text" class="form-control" disabled placeholder="Upload File" id="file">
                                                <div class="input-group-append">
                                                    <button type="button" id="pilih_foto" class="browse btn btn-dark">Pilih Foto</button>
                                                </div>
                                            </div>
                                        <img src="penjual/foto/<?php echo $data['foto'];?>" width="50%" id="preview" class="img-thumbnail">
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php 
                                if ($_SESSION["level"]=='Admin' or $_SESSION["level"]=='admin'):
                            ?>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Kode:</label>
                                            <input name="kode" value="<?php echo $data['kodePengguna']?>" type="text" class="form-control" placeholder="Masukan kode" disabled>
                                            <input name="idPengguna" value="<?php echo $data['idPengguna'];?>" type="hidden" class="form-control">
                                            <input name="idAdmin" value="<?php echo $data['idAdmin'];?>" type="hidden" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Nama:</label>
                                             <input name="namaAdmin" value="<?php echo $data['namaAdmin']?>" type="text" class="form-control" placeholder="Masukan nama" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>No Telp:</label>
                                            <input name="noTelp" value="<?php echo $data['noTelp']?>" type="text" class="form-control" placeholder="Masukan no telp" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Alamat:</label>
                                            <textarea class="form-control" name="alamat" rows="5" ><?php echo $data['alamat'];?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div id="msg"></div>
                                        <label>Foto:</label>
                                        <input type="hidden" name="foto_saat_ini" value="<?php echo $data['foto'];?>" class="form-control" />
                                        <input type="file" name="foto_baru" class="file" >
                                            <div class="input-group my-3">
                                                <input type="text" class="form-control" disabled placeholder="Upload File" id="file">
                                                <div class="input-group-append">
                                                    <button type="button" id="pilih_foto" class="browse btn btn-dark">Pilih Foto</button>
                                                </div>
                                            </div>
                                        <img src="penjual/foto/<?php echo $data['foto'];?>" width="50%" id="preview" class="img-thumbnail">
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php 
                                if ($_SESSION["level"]=='Pelanggan' or $_SESSION["level"]=='pelanggan'):
                            ?>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Kode:</label>
                                            <input name="kodePengguna" value="<?php echo $data['kodePengguna'];?>" type="text" class="form-control" disabled>
                                            <input name="idPengguna" value="<?php echo $data['idPengguna'];?>" type="hidden" class="form-control">
                                            <input name="idPelanggan" value="<?php echo $data['idPelanggan'];?>" type="hidden" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Nama:</label>
                                             <input name="namaPelanggan" value="<?php echo $data['namaPelanggan'];?>" type="text" class="form-control" placeholder="Masukan nama" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>No Telp:</label>
                                            <input name="noTelp" value="<?php echo $data['noTelp'];?>" type="text" class="form-control" placeholder="Masukan no telp" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Alamat:</label>
                                            <textarea class="form-control" name="alamat" rows="5" ><?php echo $data['alamat'];?></textarea>
                                        </div>
                                    </div>
                                </div>               
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div id="msg"></div>
                                        <label>Foto:</label>
                                        <input type="hidden" name="foto_saat_ini" value="<?php echo $data['foto'];?>" class="form-control" />
                                        <input type="file" name="foto_baru" class="file" >
                                            <div class="input-group my-3">
                                                <input type="text" class="form-control" disabled placeholder="Upload File" id="file">
                                                <div class="input-group-append">
                                                        <button type="button" id="pilih_foto" class="browse btn btn-dark">Pilih Foto</button>
                                                </div>
                                            </div>
                                        <img src="pelanggan/foto/<?php echo $data['foto'];?>" width="50%" id="preview" class="img-thumbnail">
                                    </div>
                                </div>
                                <?php endif; ?>

                            </div>
                        </div>
                        <br>
                        <div class="card">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Setting Akun</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Username:</label>
                                            <input name="username_baru" id="username_baru" value="<?php echo $data['username']?>" type="text" class="form-control" placeholder="Masukan username" required>
                                            <input name="username_lama" id="username_lama" value="<?php echo $data['username']?>" type="hidden" class="form-control">
                                         
                                            <div id="info_username"> </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Password:</label>
                                            <input name="password" value="<?php echo $data['password']?>" type="password" class="form-control" placeholder="Masukan password" required>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="idPengguna" value="<?php echo $data['idPengguna']?>"/>
                                <button type="submit" name="simpan_profil"  id="simpan_profil" class="btn btn-success" >Simpan</button>

                            </div>
                        </div>   
                    </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .file {
    visibility: hidden;
    position: absolute;
    }
</style>

<script>

    $(document).on("click", "#pilih_foto", function() {
    var file = $(this).parents().find(".file");
    file.trigger("click");
    });
    $('input[type="file"]').change(function(e) {
    var fileName = e.target.files[0].name;
    $("#file").val(fileName);

    var reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById("preview").src = e.target.result;
    };
    reader.readAsDataURL(this.files[0]);
    });

    $("#username_baru").bind('keyup', function () {
        var username_baru = $('#username_baru').val();
        var username_lama = $('#username_lama').val();

        if (username_baru==username_lama){
            $.ajax({
                    url: 'profil/cek-username.php',
                    method: 'POST',
                    data:{username_baru:username_baru},
                    success:function(data){
                        $('#info_username').show();
                        $('#info_username').html(data);
                    }
                }); 
        } else {
            document.getElementById("username_baru").value=username_baru;
            $('#info_username').hide();
        }
                
    });
</script>

