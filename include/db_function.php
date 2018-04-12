<?php

class db_function {

    private $conn;

    // constructor
    function __construct() {
        require_once 'db_connect.php';
        require 'PHPMailer/PHPMailerAutoload.php';
        // koneksi ke database
        $db = new db_connect();
        $this-> mail = new PHPMailer();
        $this->conn = $db->connect();
    }

    // destructor
    function __destruct() {

    }

    public function resetPassword($email, $kode, $password){
        $stmt = $this->conn->prepare("SELECT * from password_reset_request where email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result["encrypted_temp_password"] == $kode) {
            $stmt->close();
            $hash = $this->hashSSHA($password);
            $encrypted_password = $hash["encrypted"]; // encrypted password
            $salt = $hash["salt"];
            $stmt = $this->conn->prepare("UPDATE user set encrypted_password = ?, salt = ? where email = ?");
            $stmt->bind_param("sss", $encrypted_password, $salt, $email);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function sendEmail($email,$temp_password){

      $mail = $this -> mail;
      $mail->isSMTP();
      $mail->SMTPDebug = 2;
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'shuichi.tofa@gmail.com';
      $mail->Password = 'musthofaammar2912';
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;
      $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

      /*$mail->From = 'shuichi.tofa@gmail.com';
      $mail->FromName = 'Pedi';*/
      $mail->setFrom('shuichi.tofa@gmail.com', 'Pedi');
      $mail->addAddress($email, 'Pedi');

      $mail->addReplyTo('shuichi.tofa@gmail.com', 'Pedi');

      $mail->WordWrap = 50;
      $mail->isHTML(true);

      $mail->Subject = 'Password Reset Request';
      $mail->Body    = 'Hi,<br><br> Your password reset code is <b>'.$temp_password.'</b> . This code expires in 120 seconds. Enter this code within 120 seconds to reset your password.<br><br>Thanks,<br>Learn2Crack.';

      if(!$mail->send()) {

       return $mail->ErrorInfo;

   } else {

        return false;

    }
}

public function checkEmailExist($email){
    $stmt = $this->conn->prepare("SELECT * from user where email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        return true;
    } else {
        return false;
    }
}

public function passwordResetRequest($email){
    $random_string = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 6)), 0, 6);

    $stmt = $this->conn->prepare("SELECT * from password_reset_request WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0){
        $stmt->close();

        $stmt = $this->conn->prepare("INSERT into password_reset_request set email = ?, encrypted_temp_password = ?");
        $stmt->bind_param("ss", $email, $random_string);
        $executed = $stmt->execute();

        if($executed){

            $stmt->close();

            $stmt = $this->conn->prepare("SELECT * from password_reset_request WHERE email = ?");
            $stmt->bind_param("s", $email);

            if($stmt->execute()){
                $reset = $stmt->get_result()->fetch_assoc();
                
                return $reset;
            } else {
                return false;
            }

        } else {
            return false;
        }
    } else {
        $stmt->close();
        $stmt = $this->conn->prepare("UPDATE password_reset_request set encrypted_temp_password = ? WHERE email = ?");
        $stmt->bind_param("ss", $random_string, $email);

        if($stmt->execute()){
            $stmt->close();

            $stmt = $this->conn->prepare("SELECT * from password_reset_request WHERE email = ?");
            $stmt->bind_param("s", $email);

            if($stmt->execute()){
                $reset = $stmt->get_result()->fetch_assoc();

                return $reset;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

public function getAllMartKategori($id_layanan){
    $stmt = $this->conn->prepare("SELECT * from kategorimobil where id_layanan = ?");
    $stmt->bind_param("i", $id_layanan);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $mart = array();
        while ($fetch = $result->fetch_assoc()) {

            $mart[] = $fetch;

        }

        return $mart;

    } else {
        return false;
    }

}

public function getBarangKategori($id_kategori){
    $stmt = $this->conn->prepare("SELECT * from produk where id_kategori = ?");
    $stmt->bind_param("i", $id_kategori);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $barang = array();

        while($fetch = $result->fetch_assoc()){
            $barang[] = $fetch;
        }

        return $barang;
    } else {
        return false;
    }
}

public function getProvider($id_layanan){
    $stmt = $this->conn->prepare("Select * from provider where id_layanan = ?");
    $stmt->bind_param("i", $id_layanan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0){
        $provider= array();

        while($fetch = $result->fetch_assoc()){
            $provider[] = $fetch;
        }

        return $provider;

    } else {
        return false;
    }
}

public function editProfile($id_user, $username, $email, $notelp){
    $stmt = $this->conn->prepare("SELECT * from user where id_user = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $stmt->close();

        $stmt = $this->conn->prepare("UPDATE user set username = ?, email = ?, notelp = ? where id = ?");

        $stmt->bind_param("sssi", $username, $email, $notelp, $id_user);
        $stmt->execute();

        if ($stmt->execute()){
            $stmt->close();
            $stmt = $this->conn->prepare("SELECT * from user where id = ?");
            $stmt->bind_param("i", $id_user);

            $user = $stmt->get_result()->fetch_assoc();

            return $user;
        } else {
            return null;
        }

    } else {
        return null;
    }

}

public function pesanKos($id_user, $id_kos, $notelp){
    $stmt = $this->conn->prepare("INSERT into pesan_kos (id_user, id_kos, notelp) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $id_user, $id_kos, $notelp);
    $result = $stmt->execute();

    if ($result) {
        return true;
    } else {
        return null;
    }
}

public function getAllKos() {

    $stmt = $this->conn->prepare("SELECT * from kos");

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $kos = array();

        while ($fetch = $result->fetch_assoc()) {

            $kos[] = $fetch;

        }

        return $kos;

    } else {
        return NULL;
    }

}

public function getGambarKos($id){
    $stmt = $this->conn->prepare("SELECT gambar from gambarkos WHERE id_kos = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $gambar = array();
        while ($fetch = $result->fetch_assoc()) {

            $gambar[] = $fetch;
        }

        return $gambar;
    } else {
        return NULL;
    }

}

public function getAllMobil($id_tipe){
    $stmt = $this->conn->prepare("SELECT a.id, a.tahun, a.bensin, a.jumlah_kursi, a.keterangan, a.harga, a.gambar, b.tipe, c.nama, d.nama_provider,d.alamat, e.nama from mobil a INNER JOIN tipemobil b on a.id_tipe = b.id INNER JOIN kategorimobil c on a.id_category = c.id INNER JOIN provider d on a.id_provider = d.id INNER JOIN pabrik e on a.id_pabrik = e.id WHERE a.id_tipe = ?");
    $stmt->bind_param("s", $id_tipe);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $mobil = array();
        while ($fetch = $result->fetch_assoc()) {

            $mobil[] = $fetch;

        }

        return $mobil;

    } else {
        return NULL;
    }
}

public function getTipeMobil($id_category){
    $stmt = $this->conn->prepare("SELECT b.id, b.tipe, b.gambar from kategorimobil a INNER JOIN tipemobil b on a.id = b.id_kategori WHERE a.id = ?");
    $stmt->bind_param("s", $id_category);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $mobil = array();
        while ($fetch = $result->fetch_assoc()) {

            $mobil[] = $fetch;

        }

        return $mobil;

    } else {
        return NULL;
    }
}


public function simpanUser($nama, $email, $notelp, $password) {
    $uuid = uniqid('', true);
    $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt

        $stmt = $this->conn->prepare("INSERT INTO user (unique_id, username, email, encrypted_password, salt,  notelp) VALUES(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param ("ssssss", $uuid, $nama, $email, $encrypted_password, $salt, $notelp);
        $result = $stmt->execute();
        $stmt->close();

        // cek jika sudah sukses
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user;
        } else {
            return false;
        }
    }

    /**
     * Get user berdasarkan email dan password
     */
    public function getUserByEmailAndPassword($email, $password) {

        $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = ?");

        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // verifikasi password user
            $salt = $user['salt'];
            $encrypted_password = $user['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // cek password jika sesuai
            if ($encrypted_password == $hash) {
                // autentikasi user berhasil
                return $user;
            }
        } else {
            return NULL;
        }
    }

    public function viewHistoryOrder($id_user){

        $stmt = $this->conn->prepare("SELECT a.id, a.tanggal, a.status, b.nama_paket from pesan_produk a INNER JOIN produk b on a.id_paket = b.id_paket WHERE a.id_user = ?");
        $stmt->bind_param("i", $id_user);

        $stmt->execute();

        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $history = array();
            while ($fetch = $result->fetch_assoc()) {

                $history[] = $fetch;

            }

            return $history;

        } else {
            return NULL;
        }

    }
    
    public function historiRent($id_user){

        $stmt = $this->conn->prepare("SELECT a.id, b.tanggalpesan, b.status, c.tipe from mobil a INNER JOIN pesan_mobil b on a.id = b.id_mobil INNER JOIN tipemobil c on a.id_tipe = c.id WHERE b.id_user = ?");
        $stmt->bind_param("i", $id_user);

        $stmt->execute();

        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $history = array();
            while ($fetch = $result->fetch_assoc()) {

                $history[] = $fetch;

            }

            return $history;

        } else {
            return NULL;
        }

    }
    
    // public function orderpesan($pesanan){
    public function orderpesan($user, $total_harga, $pesanan, $alamat, $tanggal){
        if (is_array($pesanan)) {

            foreach ($pesanan as $value) {

                $produk = $value['produk'];
                $jumlah = $value['unit'];
                
                foreach ($produk as $value) {

                    $id_paket = $value['idPaket'];
                    
                    $stmt = $this->conn->prepare("INSERT INTO pesan_produk (id_user, id_paket, jumlah, total_harga, alamat, tanggal) VALUES (?, ?, ?, ?, ?, ?)");

                    $stmt->bind_param("iiisss", $user, $id_paket, $jumlah, $total_harga, $alamat, $tanggal);

                    $order = $stmt->execute();
                    
                }
            }

            $stmt->close();

            if ($order) {
                return TRUE;
            } else {
                return NULL;
            }
            

            // if ($order) {
            //     return $order;
            // } else {
            //     return NULL;
            // }

            /*$order = $stmt->execute();

            

            if ($order) {
                    return $order;  
            } else {
                    return NULL;
                }*/

            } else {
                return NULL;
            }
            
        }

        public function orderMobil($user, $id_mobil, $tanggalpesan, $tanggalpakai, $durasi, $supir, $alamat, $jaminan, $biaya){

            $stmt = $this->conn->prepare("INSERT INTO pesan_mobil (id_user, id_mobil, tanggalpesan, tanggalpakai, durasi, alamat, supir, jaminan, biaya) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("iissssssi", $user, $id_mobil, $tanggalpesan, $tanggalpakai, $durasi, $alamat, $supir, $jaminan, $biaya);

            $pesan = $stmt->execute();

            $stmt->close();

            if ($pesan) {

                return $pesan;   
            } else {
                return NULL;
            }

        }

        public function getAllProduct() {

            $kategori = "Sayuran";

            $stmt = $this->conn->prepare("SELECT id_paket, nama_paket, komposisi, harga, gambar from produk where kategori = ? ");

            $stmt->bind_param("s", $kategori);

            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $produk = array();
                while ($fetch = $result->fetch_assoc()) {

                    $produk[] = $fetch;
                /*$produk = array(
                    'id_paket' => $fetch['id_paket'],
                    'nama_paket' => $fetch['nama_paket'],
                    'komposisi' => $fetch['komposisi'],
                    'harga' => $fetch['harga']
                );*/

            }

            return $produk;

        } else {
            return NULL;
        }

    }

    public function getAllProductBulanan() {

        $kategori = "Bulanan";
        
        $stmt = $this->conn->prepare("SELECT id_paket, nama_paket, komposisi, harga, gambar from produk where kategori = ? ");
        $stmt->bind_param("s", $kategori);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $produk = array();
            while ($fetch = $result->fetch_assoc()) {

                $produk[] = $fetch;
                /*$produk = array(
                    'id_paket' => $fetch['id_paket'],
                    'nama_paket' => $fetch['nama_paket'],
                    'komposisi' => $fetch['komposisi'],
                    'harga' => $fetch['harga']
                );*/

            }

            return $produk;

        } else {
            return NULL;
        }

    }

    public function isUserExisted($email) {
        $stmt = $this->conn->prepare("SELECT email from user WHERE email = ?");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user telah ada 
            $stmt->close();
            return true;
        } else {
            // user belum ada 
            $stmt->close();
            return false;
        }
    }

    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }

}

?>