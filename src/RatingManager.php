<?php

class RatingManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function rateSong($songId, $userId, $rating) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT OR REPLACE INTO ratings (song_id, user_id, rating)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$songId, $userId, $rating]);
            return $this->updateAverageRating($songId);
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateAverageRating($songId) {
        $stmt = $this->pdo->prepare("SELECT AVG(rating) FROM ratings WHERE song_id = ?");
        $stmt->execute([$songId]);
        $avg = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("UPDATE songs SET rating = ? WHERE id = ?");
        return $stmt->execute([$avg, $songId]);
    }

    public function getUserRating($songId, $userId) {
        $stmt = $this->pdo->prepare("SELECT rating FROM ratings WHERE song_id = ? AND user_id = ?");
        $stmt->execute([$songId, $userId]);
        return $stmt->fetchColumn();
    }
}
