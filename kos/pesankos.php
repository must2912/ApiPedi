<?php
require_once '../include/db_function.php';
$db = new db_function();
 
// json response array
$response = array("error" => FALSE);
$_POST = json_decode(file_get_contents('php://input'), true);
 
if (isset($_POST['id_user']) && isset($_POST['id_kos']) && isset($_POST['notelp'])) {
 
    // menerima parameter POST ( email dan password )
    $user = $_POST['id_user'];
    $kos = $_POST['id_kos'];
    $notelp = $_POST['notelp'];
 
    // get the user by email and password
    // get user berdasarkan email dan password
    $pesan = $db->pesanKos($user, $kos, $notelp);
 
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