<?php
session_start();

class Auth {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function login($email, $password, $remember = false) {

        $email = $this->db->real_escape_string($email);
        
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $this->db->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
 
            if (password_verify($password, $user['password'])) {

                $_SESSION['login'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['username'] = $user['username']; 

                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $this->db->query("UPDATE users SET remember_token = '$token' WHERE id = " . $user['id']);
                    setcookie('remember_me', $token, time() + (86400 * 30), "/"); 
                }
                return true;
            }
        }
        return false; 
    }

    public function register($username, $email, $password) {
        $username = $this->db->real_escape_string($username);
        $email = $this->db->real_escape_string($email);
        
        $check_user = $this->db->query("SELECT id FROM users WHERE username = '$username'");
        if ($check_user->num_rows > 0) {
            return "Username sudah terpakai mekanik lain!";
        }

        $check_email = $this->db->query("SELECT id FROM users WHERE email = '$email'");
        if ($check_email->num_rows > 0) {
            return "Email sudah terdaftar! Gunakan email lain.";
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
        
        if ($this->db->query($sql)) {
            return true;
        } else {
            return "Gagal daftar: " . $this->db->error;
        }
    }

    public function checkLogin() {
        if (isset($_SESSION['login'])) {
            return true;
        }

        if (isset($_COOKIE['remember_me'])) {
            $token = $this->db->real_escape_string($_COOKIE['remember_me']);
            $sql = "SELECT * FROM users WHERE remember_token = '$token'";
            $result = $this->db->query($sql);
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $_SESSION['login'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                return true;
            }
        }
        return false;
    }

    public function logout() {
        if(isset($_SESSION['admin_id'])) {
            $this->db->query("UPDATE users SET remember_token = NULL WHERE id = " . $_SESSION['admin_id']);
        }
        
        session_destroy();
        setcookie('remember_me', '', time() - 3600, "/");
        
        header("Location: login.php");
        exit;
    }
}
?>