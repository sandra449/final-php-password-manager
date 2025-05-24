<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table_name = "users";
    
    public $id;
    public $username;
    private $password;
    private $encryption_key;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function login($username, $password) {
        $query = "SELECT id, username, password, encryption_key FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->encryption_key = $row['encryption_key'];
                return true;
            }
        }
        return false;
    }
    
    public function getEncryptionKey($password) {
        if (!$this->encryption_key) {
            throw new Exception("User not logged in or encryption key not available");
        }
        return $this->encryption_key;
    }
    
    public function create($username, $password) {
        $query = "INSERT INTO " . $this->table_name . " (username, password, encryption_key) VALUES (:username, :password, :encryption_key)";
        
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Generate encryption key
        $encryption_key = bin2hex(random_bytes(32));
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":encryption_key", $encryption_key);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?> 