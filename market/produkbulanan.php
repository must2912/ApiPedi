<?php
 
require_once '../include/db_function.php';
$db = new db_function();
 
// json response array
$response = array("error" => FALSE);

if ($_SERVER['REQUEST_METHOD']=='GET') {
 
    $produk = $db->getAllProductBulanan();

    if ($produk != FALSE) {
        $response["error"] = FALSE;
        $response["datalist"] = $produk;
        /*$response["data"]["id_paket"] = $produk["id_paket"];
        $response["data"]["nama_paket"] = $produk["nama_paket"];
        $response["data"]["komposisi"] = $produk["komposisi"];
        $response["data"]["harga"] = $produk["harga"];*/
        echo json_encode($response);

    } else {
        $response["error"] = TRUE;
        $response["message"] = "Gagal Mengambil produk";
        echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["message"] = "Salah Method";
    echo json_encode($response);
}
?>