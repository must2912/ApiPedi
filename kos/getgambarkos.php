<?php
require_once '../include/db_function.php';
$db = new db_function();
 
// json response array
$response = array("error" => FALSE);
$_POST = json_decode(file_get_contents('php://input'), true);
 
if (isset($_POST['id_kos'])) {
 
    // menerima parameter POST ( email dan password )
    $id = $_POST['id_kos'];
 
    // get the user by email and password
    // get user berdasarkan email dan password
    $kos = $db->getGambarKos($id);
 
    if ($kos != false) {
        // user ditemukan
        $response["error"] = FALSE;
        $response["datalist"] = $kos;
        echo json_encode($response);
    } else {
        // user tidak ditemukan password/email salah
        $response["error"] = TRUE;
        $response["error_msg"] = "Gagal Mengambil Data";
        echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Kesalahan Sistem";
    echo json_encode($response);
}
?>