<?php
class db_connect {
    private $conn;
 
    // koneksi ke database
    public function connect() {
        require_once 'config.php';
         
        // koneksi ke mysql database
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
         
        // return database handler
        return $this->conn;
    }
}
 
?>