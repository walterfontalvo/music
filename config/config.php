<?php

// Configuración de la aplicación
define('APP_NAME', 'LaMusica - Letras y Acordes');
define('APP_URL', 'http://localhost:8000');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Generos musicales
define('GENRES', [
    'rock' => 'Rock',
    'pop' => 'Pop',
    'reggaeton' => 'Reggaeton',
    'latin' => 'Latino',
    'folk' => 'Folk',
    'blues' => 'Blues',
    'jazz' => 'Jazz',
    'country' => 'Country',
    'metal' => 'Metal',
    'indie' => 'Indie'
]);

// Dificultad
define('DIFFICULTY_LEVELS', [
    'easy' => 'Fácil',
    'medium' => 'Medio',
    'hard' => 'Difícil',
    'expert' => 'Experto'
]);

// Acordes comunes
define('COMMON_CHORDS', [
    'C', 'Cm', 'C7', 'Cmaj7', 'Cmin7',
    'D', 'Dm', 'D7', 'Dmaj7', 'Dmin7',
    'E', 'Em', 'E7', 'Emaj7', 'Emin7',
    'F', 'Fm', 'F7', 'Fmaj7', 'Fmin7',
    'G', 'Gm', 'G7', 'Gmaj7', 'Gmin7',
    'A', 'Am', 'A7', 'Amaj7', 'Amin7',
    'B', 'Bm', 'B7', 'Bmaj7', 'Bmin7',
    'F#', 'F#m', 'Bb', 'Bbm', 'Eb', 'Ebm'
]);

require_once __DIR__ . '/Database.php';
$db = new Database();
$pdo = $db->getConnection();
