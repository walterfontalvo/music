<?php

class UserManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($username, $email, $password) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, email, password)
                VALUES (?, ?, ?)
            ");
            return $stmt->execute([$username, $email, $hashedPassword]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT id, username, email, reputation, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateReputation($userId, $points) {
        $stmt = $this->pdo->prepare("UPDATE users SET reputation = reputation + ? WHERE id = ?");
        return $stmt->execute([$points, $userId]);
    }
}
