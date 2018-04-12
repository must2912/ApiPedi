<?php
require_once 'include/db_function.php';
$db = new db_function();
 
// json response array
$response = array("error" => FALSE);
$_POST = json_decode(file_get_contents('php://input'), true);
 
if (isset($_POST['email']) && isset($_POST['password'])) {
 
    // menerima parameter POST ( email dan password )
    $email = $_POST['email'];
    $password = $_POST['password'];
 
    // get the user by email and password
    // get user berdasarkan email dan password
    $user = $db->getUserByEmailAndPassword($email, $password);
 
    if ($user != false) {
        // user ditemukan
        $response["error"] = FALSE;
        $response["message"] = "Login Berhasil";
        $response["data"]["nama"] = $user["username"];
        $response["data"]["email"] = $user["email"];
        /*$response["data"]["notelp"] = $user["notelp"];*/
        echo json_encode($response);
    } else {
        // user tidak ditemukan password/email salah
        $response["error"] = TRUE;
        $response["error_msg"] = "Login gagal. Password/Email salah";
        echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Parameter (email atau password) ada yang kurang";
    echo json_encode($response);
}
?>