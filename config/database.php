<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        // Use environment variables if available, otherwise fall back to defaults
        $this->host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'skull_king_league';
        $this->username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'skullking_user';
        $this->password = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: 'SkullKing_2025!';
    }

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Erreur de connexion: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>
