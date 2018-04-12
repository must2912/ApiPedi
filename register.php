<?php
 
require_once 'include/db_function.php';
$db = new db_function();
 
// json response array
$response = array("error" => FALSE);
$_POST = json_decode(file_get_contents('php://input'), true);

if (isset($_POST["nama"]) && isset($_POST["email"]) && isset($_POST["notelp"]) && isset($_POST["password"])) {
 
    // menerima parameter POST ( nama, email, password )
    $nama = $_POST["nama"];
    $email = $_POST["email"];
    $notelp = $_POST["notelp"];
    $password = $_POST["password"];
 
    // Cek jika user ada dengan email yang sama
    if ($db->isUserExisted($email)) {
        // user telah ada
        $response["error"] = TRUE;
        $response["message"] = "User telah ada dengan email " . $email;
        echo json_encode($response);
    } else {
        // buat user baru
        $user = $db->simpanUser($nama, $email, $notelp, $password);
        if ($user) {
            // simpan user berhasil
            $response["error"] = FALSE;
            $response["message"] = "berhasil registrasi";
            $response["data"] = $user;
            echo json_encode($response);
        } else {
            // gagal menyimpan user
            $response["error"] = TRUE;
            $response["message"] = "Terjadi kesalahan saat melakukan registrasi";
            echo json_encode($response);
        }
    }
} else {
    $response["error"] = TRUE;
    $response["message"] = "Parameter (nama, email, atau password) ada yang kurang";
    echo json_encode($response);
}

function isTheseParametersAvailable($params){
 
 foreach($params as $param){
    if(!isset($_POST[$param])){
        return false; 
    }
 }
 return true; 
 }
?>