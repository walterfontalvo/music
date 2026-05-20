<?php

class SongManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addSong($title, $artist, $lyrics, $chords, $key, $genre, $difficulty, $userId) {
        $stmt = $this->pdo->prepare("
            INSERT INTO songs (title, artist, lyrics, chords, key_original, genre, difficulty, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$title, $artist, $lyrics, $chords, $key, $genre, $difficulty, $userId]);
    }

    public function getSongById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM songs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateViews($songId) {
        $stmt = $this->pdo->prepare("UPDATE songs SET views = views + 1 WHERE id = ?");
        return $stmt->execute([$songId]);
    }

    public function getSongs($search = '', $genre = '', $sort = 'recent', $page = 1, $perPage = 12) {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT s.*, u.username FROM songs s JOIN users u ON s.user_id = u.id WHERE 1=1";
        $params = [];

        if ($search) {
            $query .= " AND (s.title LIKE ? OR s.artist LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if ($genre) {
            $query .= " AND s.genre = ?";
            $params[] = $genre;
        }

        switch ($sort) {
            case 'trending':
                $query .= " ORDER BY s.rating DESC, s.views DESC";
                break;
            case 'popular':
                $query .= " ORDER BY s.views DESC";
                break;
            case 'recent':
            default:
                $query .= " ORDER BY s.created_at DESC";
        }

        $query .= " LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countSongs($search = '', $genre = '') {
        $query = "SELECT COUNT(*) FROM songs WHERE 1=1";
        $params = [];

        if ($search) {
            $query .= " AND (title LIKE ? OR artist LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if ($genre) {
            $query .= " AND genre = ?";
            $params[] = $genre;
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function transpose($chords, $steps) {
        $chordMap = [
            'C' => 0, 'C#' => 1, 'D' => 2, 'D#' => 3, 'E' => 4, 'F' => 5,
            'F#' => 6, 'G' => 7, 'G#' => 8, 'A' => 9, 'A#' => 10, 'B' => 11
        ];
        $reverseMap = array_flip($chordMap);

        $pattern = '/\b(' . implode('|', array_keys($chordMap)) . ')(?:m|maj7|min7|7)?\b/i';
        
        return preg_replace_callback($pattern, function($matches) use ($chordMap, $reverseMap, $steps) {
            $chord = $matches[1];
            $base = $chordMap[$chord];
            $newBase = ($base + $steps) % 12;
            return $reverseMap[$newBase];
        }, $chords);
    }

    public function editSong($id, $title, $artist, $lyrics, $chords, $key, $genre, $difficulty) {
        $stmt = $this->pdo->prepare("
            UPDATE songs SET title = ?, artist = ?, lyrics = ?, chords = ?, key_original = ?, genre = ?, difficulty = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([$title, $artist, $lyrics, $chords, $key, $genre, $difficulty, $id]);
    }

    public function deleteSong($id) {
        $stmt = $this->pdo->prepare("DELETE FROM songs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
