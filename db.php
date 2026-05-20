<?php
if (!class_exists('Database')) {
    class Database {
        private $host = "localhost";
        private $user = "root";       
        private $pass = "";
        private $db   = "toko_sparepart";
        public $conn;

        public function __construct() {
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

            if ($this->conn->connect_error) {
                die("Mesin mati! Koneksi gagal: " . $this->conn->connect_error);
            }
        }
    }
}

if (!isset($conn)) {
    $database = new Database();
    $conn = $database->conn;
}
?>