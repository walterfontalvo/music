<?php

class CommentManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addComment($songId, $userId, $content) {
        $stmt = $this->pdo->prepare("
            INSERT INTO comments (song_id, user_id, content)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$songId, $userId, $content]);
    }

    public function getComments($songId) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.username FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.song_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$songId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteComment($id) {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
