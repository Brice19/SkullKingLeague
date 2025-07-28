<?php
class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            // Use SQLite for testing
            $this->conn = new PDO("sqlite:/tmp/skull_king_test.db");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create tables if they don't exist
            $this->initTables();
            
        } catch(PDOException $exception) {
            echo "Erreur de connexion: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    
    private function initTables() {
        // Create users table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            pseudo VARCHAR(50) NOT NULL UNIQUE,
            elo INTEGER DEFAULT 1000,
            parties_jouees INTEGER DEFAULT 0,
            victoires INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create games table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS games (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            date_partie TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            gagnant_id INTEGER,
            status TEXT DEFAULT 'en_cours',
            FOREIGN KEY (gagnant_id) REFERENCES users(id)
        )");
        
        // Create game_players table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS game_players (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            game_id INTEGER,
            user_id INTEGER,
            score_total INTEGER DEFAULT 0,
            player_order INTEGER DEFAULT 1,
            FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )");
        
        // Create rounds table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS rounds (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            game_id INTEGER,
            numero_manche INTEGER,
            player_id INTEGER,
            score INTEGER,
            starting_player_id INTEGER NULL,
            FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
            FOREIGN KEY (player_id) REFERENCES users(id),
            FOREIGN KEY (starting_player_id) REFERENCES users(id)
        )");
        
        // Create elo_history table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS elo_history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            game_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            old_elo INTEGER NOT NULL,
            new_elo INTEGER NOT NULL,
            elo_change INTEGER NOT NULL,
            rank INTEGER NOT NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (game_id) REFERENCES games(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        )");
        
        // Insert test users if none exist
        $count = $this->conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($count == 0) {
            $this->conn->exec("INSERT INTO users (pseudo) VALUES 
                ('Alice'), 
                ('Bob'), 
                ('Charlie'), 
                ('Diana'),
                ('Eve'),
                ('Frank'),
                ('Grace'),
                ('Henry')");
        }
    }
}
?>