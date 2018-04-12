<?php
require_once 'include/db_function.php';
$db = new db_function();

// json response array
$response = array("error" => FALSE);
$_POST = json_decode(file_get_contents('php://input'), true);

if (isset($_POST['email']) && isset($_POST)) {

    // menerima parameter POST ( email dan password )
    $email = $_POST['email'];

    // get the user by email and password
    // get user berdasarkan email dan password
    $user = $db->checkEmailExist($email);

    if ($user != false) {

        $result = $db->passwordResetRequest($email);

        if ($result != false) {

            $mail_result = $db->sendEmail($result["email"],$result["encrypted_temp_password"]);

            if($mail_result == true){

                $response["error"] = "false";
                $response["message"] = "Check your mail for reset password code.";
                return json_encode($response);

            } else {

                $response["error"] = "false";
                $response["message"] = "Reset Password Failure";
                return json_encode($response);
            }

        } else {
            $response["error"] = TRUE;
            $response["message"] = "Gagal Reset password";
            echo json_encode($response);  
        }

        // user ditemukan
        /*$response["error"] = FALSE;
        $response["message"] = "Login Berhasil";
        $response["data"]["nama"] = $user["username"];
        $response["data"]["email"] = $user["email"];
        $response["data"]["notelp"] = $user["notelp"];
        echo json_encode($response);*/
    } else {
        // user tidak ditemukan password/email salah
        $response["error"] = TRUE;
        $response["error_msg"] = "Email Tidak ditemukan";
        echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Parameter (email atau password) ada yang kurang";
    echo json_encode($response);
}

?>

