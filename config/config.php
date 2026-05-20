<?php
// Configuration
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');
define('DB_PATH', ROOT_PATH . '/db/lacuerda.db');

// Create directories if they don't exist
if (!is_dir(UPLOADS_PATH)) {
    mkdir(UPLOADS_PATH, 0755, true);
}
if (!is_dir(dirname(DB_PATH))) {
    mkdir(dirname(DB_PATH), 0755, true);
}

// Database
define('DB_HOST', 'sqlite:' . DB_PATH);

// Upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_EXT', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('ITEMS_PER_PAGE', 12);

// Genres
define('GENRES', [
    'rock' => 'Rock',
    'pop' => 'Pop',
    'reggaeton' => 'Reggaeton',
    'latin' => 'Latina',
    'folk' => 'Folklórico',
    'metal' => 'Metal',
    'jazz' => 'Jazz',
    'blues' => 'Blues',
    'country' => 'Country',
    'otros' => 'Otros'
]);

// Difficulty levels
define('DIFFICULTY_LEVELS', [
    'principiante' => 'Principiante',
    'intermedio' => 'Intermedio',
    'avanzado' => 'Avanzado'
]);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
