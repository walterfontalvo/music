<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/Database.php';

class SongManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createSong($title, $artist, $genre, $difficulty, $lyrics, $imagePath = null) {
        $sql = "INSERT INTO songs (title, artist, genre, difficulty, lyrics, image_path) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$title, $artist, $genre, $difficulty, $lyrics, $imagePath]);
        
        return $this->db->lastInsertId();
    }

    public function getSong($id) {
        $sql = "SELECT * FROM songs WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllSongs($page = 1, $orderBy = 'created_at', $genre = null, $difficulty = null, $search = null) {
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        
        $sql = "SELECT * FROM songs WHERE 1=1";
        $params = [];

        if ($genre) {
            $sql .= " AND genre = ?";
            $params[] = $genre;
        }

        if ($difficulty) {
            $sql .= " AND difficulty = ?";
            $params[] = $difficulty;
        }

        if ($search) {
            $sql .= " AND (title LIKE ? OR artist LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Order by
        if ($orderBy === 'trending') {
            $sql .= " ORDER BY rating_sum / NULLIF(rating_count, 0) DESC, views DESC";
        } elseif ($orderBy === 'popular') {
            $sql .= " ORDER BY views DESC";
        } else {
            $sql .= " ORDER BY created_at DESC";
        }

        $sql .= " LIMIT ? OFFSET ?";
        $params[] = ITEMS_PER_PAGE;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount($genre = null, $difficulty = null, $search = null) {
        $sql = "SELECT COUNT(*) as count FROM songs WHERE 1=1";
        $params = [];

        if ($genre) {
            $sql .= " AND genre = ?";
            $params[] = $genre;
        }

        if ($difficulty) {
            $sql .= " AND difficulty = ?";
            $params[] = $difficulty;
        }

        if ($search) {
            $sql .= " AND (title LIKE ? OR artist LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function incrementViews($id) {
        $sql = "UPDATE songs SET views = views + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function updateSong($id, $title, $artist, $genre, $difficulty, $lyrics, $imagePath = null) {
        $sql = "UPDATE songs SET title = ?, artist = ?, genre = ?, difficulty = ?, lyrics = ?, 
                image_path = COALESCE(?, image_path), updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $artist, $genre, $difficulty, $lyrics, $imagePath, $id]);
    }

    public function deleteSong($id) {
        $sql = "DELETE FROM songs WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}

class RatingManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addRating($songId, $rating, $ipAddress) {
        $sql = "INSERT OR REPLACE INTO ratings (song_id, rating, ip_address) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$songId, $rating, $ipAddress]);

        // Update song rating cache
        $this->updateSongRating($songId);
        
        return true;
    }

    public function getRating($songId, $ipAddress) {
        $sql = "SELECT rating FROM ratings WHERE song_id = ? AND ip_address = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$songId, $ipAddress]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['rating'] : null;
    }

    public function getAverageRating($songId) {
        $sql = "SELECT AVG(rating) as average FROM ratings WHERE song_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$songId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($result['average'] ?? 0, 1);
    }

    private function updateSongRating($songId) {
        $sql = "UPDATE songs SET rating_count = (SELECT COUNT(*) FROM ratings WHERE song_id = ?),
                rating_sum = (SELECT SUM(rating) FROM ratings WHERE song_id = ?) WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$songId, $songId, $songId]);
    }
}

class CommentManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addComment($songId, $name, $email, $comment) {
        $sql = "INSERT INTO comments (song_id, name, email, comment) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$songId, $name, $email, $comment]);
    }

    public function getComments($songId) {
        $sql = "SELECT * FROM comments WHERE song_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$songId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteComment($id) {
        $sql = "DELETE FROM comments WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}

class ChordParser {
    // All valid chord variations
    private static $chordPatterns = [
        'C', 'Cm', 'C7', 'Cm7', 'Cmaj7', 'Csus2', 'Csus4', 'Cadd9',
        'D', 'Dm', 'D7', 'Dm7', 'Dmaj7', 'Dsus2', 'Dsus4', 'Dadd9',
        'E', 'Em', 'E7', 'Em7', 'Emaj7', 'Esus2', 'Esus4', 'Eadd9',
        'F', 'Fm', 'F7', 'Fm7', 'Fmaj7', 'Fsus2', 'Fsus4', 'Fadd9',
        'G', 'Gm', 'G7', 'Gm7', 'Gmaj7', 'Gsus2', 'Gsus4', 'Gadd9',
        'A', 'Am', 'A7', 'Am7', 'Amaj7', 'Asub2', 'Asub4', 'Aadd9',
        'B', 'Bm', 'B7', 'Bm7', 'Bmaj7', 'Bsus2', 'Bsus4', 'Badd9',
        'C#', 'C#m', 'C#7', 'C#m7', 'C#maj7',
        'D#', 'D#m', 'D#7', 'D#m7', 'D#maj7',
        'F#', 'F#m', 'F#7', 'F#m7', 'F#maj7',
        'G#', 'G#m', 'G#7', 'G#m7', 'G#maj7',
        'A#', 'A#m', 'A#7', 'A#m7', 'A#maj7',
        'Db', 'Dbm', 'Db7', 'Dbm7', 'Dbmaj7',
        'Eb', 'Ebm', 'Eb7', 'Ebm7', 'Ebmaj7',
        'Gb', 'Gbm', 'Gb7', 'Gbm7', 'Gbmaj7',
        'Ab', 'Abm', 'Ab7', 'Abm7', 'Abmaj7',
        'Bb', 'Bbm', 'Bb7', 'Bbm7', 'Bbmaj7'
    ];

    private static $noteSequence = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
    private static $noteSequenceFlat = ['C', 'Db', 'D', 'Eb', 'E', 'F', 'Gb', 'G', 'Ab', 'A', 'Bb', 'B'];

    public static function highlightChords($text) {
        $pattern = '/\b(' . implode('|', array_map('preg_quote', self::$chordPatterns)) . ')\b/';
        return preg_replace($pattern, '<span class="chord-highlight">$1</span>', $text);
    }

    public static function extractChords($text) {
        $chords = [];
        $pattern = '/\b(' . implode('|', array_map('preg_quote', self::$chordPatterns)) . ')\b/';
        
        if (preg_match_all($pattern, $text, $matches)) {
            $chords = array_unique($matches[1]);
        }
        
        return array_values($chords);
    }

    public static function transposeChord($chord, $semitones) {
        if (!in_array($chord, self::$chordPatterns)) {
            return $chord;
        }

        // Extract base note and type
        preg_match('/^([A-G]#?|[A-G]b?)(.*)$/', $chord, $matches);
        $baseNote = $matches[1];
        $chordType = $matches[2];

        // Determine sequence to use
        $sequence = strpos($baseNote, '#') !== false ? self::$noteSequence : self::$noteSequenceFlat;
        
        // Find current position
        $currentPos = array_search($baseNote, $sequence);
        if ($currentPos === false) {
            return $chord;
        }

        // Calculate new position
        $newPos = ($currentPos + $semitones + 12) % 12;
        $newBaseNote = $sequence[$newPos];

        return $newBaseNote . $chordType;
    }

    public static function transposeText($text, $semitones) {
        if ($semitones == 0) {
            return $text;
        }

        $pattern = '/\b(' . implode('|', array_map('preg_quote', self::$chordPatterns)) . ')\b/';
        
        return preg_replace_callback($pattern, function($matches) use ($semitones) {
            return self::transposeChord($matches[1], $semitones);
        }, $text);
    }
}
