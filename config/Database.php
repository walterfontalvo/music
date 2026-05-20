<?php

class Database {
    private $pdo;
    private $dbPath = __DIR__ . '/../data/music.db';

    public function __construct() {
        $this->connect();
        $this->createTables();
    }

    private function connect() {
        try {
            $this->pdo = new PDO('sqlite:' . $this->dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    private function createTables() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                reputation INTEGER DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS songs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                artist TEXT NOT NULL,
                lyrics TEXT NOT NULL,
                chords TEXT NOT NULL,
                key_original TEXT,
                genre TEXT,
                user_id INTEGER NOT NULL,
                views INTEGER DEFAULT 0,
                difficulty TEXT DEFAULT 'medium',
                rating REAL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS comments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                song_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                content TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (song_id) REFERENCES songs(id),
                FOREIGN KEY (user_id) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS ratings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                song_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                rating INTEGER,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(song_id, user_id),
                FOREIGN KEY (song_id) REFERENCES songs(id),
                FOREIGN KEY (user_id) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS chord_diagrams (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                chord_name TEXT UNIQUE NOT NULL,
                frets TEXT NOT NULL,
                fingers TEXT NOT NULL,
                svg TEXT
            );
        ");
    }

    public function getConnection() {
        return $this->pdo;
    }
}
