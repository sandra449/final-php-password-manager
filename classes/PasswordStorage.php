<?php
require_once __DIR__ . '/../config/database.php';

class PasswordStorage {
    private $conn;
    private $table_name = "passwords";
    private $user_id;
    private $encryption_key;
    
    public function __construct($db, $user_id, $encryption_key) {
        $this->conn = $db;
        $this->user_id = $user_id;
        if (!$encryption_key) {
            throw new Exception("Encryption key not available - please log in again");
        }
        $this->encryption_key = $encryption_key;
    }
    
    public function store($website_name, $password) {
        $encrypted_password = $this->encrypt($password);
        
        $query = "INSERT INTO " . $this->table_name . " 
                 (user_id, website_name, password) 
                 VALUES (:user_id, :website_name, :password)";
                 
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":website_name", $website_name);
        $stmt->bindParam(":password", $encrypted_password);
        
        return $stmt->execute();
    }
    
    public function getAll() {
        $query = "SELECT id, website_name, password FROM " . $this->table_name . " 
                 WHERE user_id = :user_id ORDER BY created_at DESC";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        
        $passwords = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['password'] = $this->decrypt($row['password']);
            $passwords[] = $row;
        }
        
        return $passwords;
    }
    
    private function encrypt($password) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($password, 'aes-256-cbc', $this->encryption_key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    private function decrypt($encrypted_data) {
        $data = base64_decode($encrypted_data);
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($data, 0, $iv_length);
        $encrypted = substr($data, $iv_length);
        return openssl_decrypt($encrypted, 'aes-256-cbc', $this->encryption_key, 0, $iv);
    }
}
?> 