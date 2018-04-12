<?php
require_once '../include/db_function.php';
$db = new db_function();
 
// json response array
$response = array("error" => FALSE);
$_POST = json_decode(file_get_contents('php://input'), true);
 
/*if (isset($_POST['pesanan']))  {*/
if (isset($_POST['id_user']) && isset($_POST['pesanan']) && isset($_POST['tanggal']) && isset($_POST['total']) && isset($_POST['alamat'])) {
    

    // menerima parameter POST ( email dan password )
    $user = $_POST['id_user'];
    $total_harga = $_POST['total'];
    $pesanan = $_POST['pesanan'];
    $alamat = $_POST['alamat'];
    $tanggal = $_POST['tanggal'];
    
    /*foreach($_POST['pesanan'] as $key => $val)
    {
        $id_paket = $val['id_paket'];
        $jumlah = $val['jumlah'];

        //insert into mysql table
        $sql = "insert into pesan_produk (id_user,id_paket,jumlah, total_harga,tanggal) values ($user,$id_paket,$jumlah,$total_harga,$tanggal)";

        echo $sql;
        //$sql1 = mysql_query($sql);
        $conn = mysqli_connect($host, $username, $password, $dbname);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        if(!mysqli_query($conn,$sql))
        {
            die('Error : ' . mysql_error());
        }
    }*/
    echo var_dump($pesanan);
    // get the user by email and password
    // get user berdasarkan email dan password
    // $order = $db->orderpesan($pesanan);
    $order = $db->orderpesan($user, $total_harga, $pesanan, $alamat, $tanggal);
    
    if ($order != false) {
        // user ditemukan
        $response["error"] = FALSE;
        $response["message"] = "Order Berhasil";
        echo json_encode($response);
    } else {
        // user tidak ditemukan password/email salah
        $response["error"] = TRUE;
        $response["error_msg"] = "Order gagal";
        echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Kurang parameter";
    echo json_encode($response);
}
?>