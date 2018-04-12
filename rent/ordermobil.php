<?php
require_once '../include/db_function.php';
$db = new db_function();
 
// json response array
$response = array("error" => FALSE);
$_POST = json_decode(file_get_contents('php://input'), true);
 
if (isset($_POST['id_user']) && isset($_POST['id_mobil']) && isset($_POST['tanggal_pesan']) && isset($_POST['tanggal_pakai']) && isset($_POST['durasi']) && isset($_POST['supir']) && isset($_POST['alamat']) && isset($_POST['jaminan']) && isset($_POST['biaya'])) {
 
    // menerima parameter POST ( email dan password )
    $user = $_POST['id_user'];
    $id_mobil = $_POST['id_mobil'];
    $tanggalpesan = $_POST['tanggal_pesan'];
    $tanggalpakai =$_POST['tanggal_pakai'];
    $durasi = $_POST['durasi'];
    $supir = $_POST['supir'];
    $alamat = $_POST['alamat'];
    $jaminan = $_POST['jaminan'];
    $biaya = $_POST['biaya'];
 
    // get the user by email and password
    // get user berdasarkan email dan password
    $pesan = $db->orderMobil($user, $id_mobil, $tanggalpesan, $tanggalpakai, $durasi, $supir, $alamat, $jaminan, $biaya);
 
    if ($pesan != false) {
        // user ditemukan
        $response["error"] = FALSE;
        $response["message"] = "Order Berhasil";
        echo json_encode($response);
    } else {
        // user tidak ditemukan password/email salah
        $response["error"] = TRUE;
        $response["message"] = "Gagal Order";
        echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["message"] = "Ada Yang Belum Diisi";
    echo json_encode($response);
}
?>