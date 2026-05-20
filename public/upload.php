<?php
require_once '../config/config.php';
require_once '../src/SongManager.php';

// Verificar autenticación (simulada)
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    // En producción, redirigir a login
    $userId = 1; // Usuario de prueba
}

$songManager = new SongManager($pdo);

if ($_POST['action'] === 'upload') {
    $title = $_POST['title'] ?? '';
    $artist = $_POST['artist'] ?? '';
    $lyrics = $_POST['lyrics'] ?? '';
    $chords = $_POST['chords'] ?? '';
    $key = $_POST['key'] ?? 'C';
    $genre = $_POST['genre'] ?? 'rock';
    $difficulty = $_POST['difficulty'] ?? 'medium';

    if ($title && $artist && $chords) {
        $songManager->addSong($title, $artist, $lyrics, $chords, $key, $genre, $difficulty, $userId);
        $success = "¡Canción subida exitosamente!";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Canción - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-gray-100">
    <!-- Navbar -->
    <nav class="bg-gray-800 border-b border-gray-700 py-4">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-amber-400">
                <i class="fas fa-guitar mr-2"></i>LaMusica
            </a>
            <a href="index.php" class="hover:text-amber-400 transition"><i class="fas fa-arrow-left mr-1"></i>Volver</a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-amber-400 mb-8"><i class="fas fa-upload mr-2"></i>Subir Nueva Canción</h1>

        <?php if (isset($success)): ?>
            <div class="bg-green-900 border border-green-500 text-green-100 px-4 py-3 rounded mb-6">
                <i class="fas fa-check mr-2"></i><?= $success ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-gray-800 rounded-lg p-6 border border-gray-700 space-y-6">
            <input type="hidden" name="action" value="upload">

            <!-- Información básica -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-amber-400 mb-2">Título de la canción *</label>
                    <input type="text" name="title" required
                        class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-amber-400">
                </div>
                <div>
                    <label class="block font-bold text-amber-400 mb-2">Artista *</label>
                    <input type="text" name="artist" required
                        class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-amber-400">
                </div>
            </div>

            <!-- Tonalidad y género -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-amber-400 mb-2">Tonalidad original</label>
                    <select name="key" class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-amber-400">
                        <?php foreach (COMMON_CHORDS as $chord): ?>
                            <option value="<?= $chord ?>" <?= isset($_POST['key']) && $_POST['key'] === $chord ? 'selected' : '' ?>><?= $chord ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-amber-400 mb-2">Género</label>
                    <select name="genre" class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-amber-400">
                        <?php foreach (GENRES as $key => $label): ?>
                            <option value="<?= $key ?>" <?= isset($_POST['genre']) && $_POST['genre'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-amber-400 mb-2">Dificultad</label>
                    <select name="difficulty" class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-amber-400">
                        <?php foreach (DIFFICULTY_LEVELS as $key => $label): ?>
                            <option value="<?= $key ?>" <?= isset($_POST['difficulty']) && $_POST['difficulty'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Letras -->
            <div>
                <label class="block font-bold text-amber-400 mb-2">Letras (opcional)</label>
                <textarea name="lyrics" rows="6" placeholder="Escribe las letras de la canción..."
                    class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-amber-400 font-mono"></textarea>
            </div>

            <!-- Acordes -->
            <div>
                <label class="block font-bold text-amber-400 mb-2">Letras con Acordes *</label>
                <textarea name="chords" rows="10" placeholder="Escribe las letras con los acordes...\n\nEjemplo:\n[C]Esta es una canción\n[G]Con acordes [D]especiales"
                    required class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-amber-400 font-mono"></textarea>
                <small class="text-gray-400">Escribe los acordes entre corchetes: [C] [G] [D] etc.</small>
            </div>

            <!-- Botones -->
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 rounded font-bold transition">
                    <i class="fas fa-upload mr-2"></i>Subir Canción
                </button>
                <a href="index.php" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded font-bold transition">
                    Cancelar
                </a>
            </div>
        </form>

        <!-- Guía -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 mt-8">
            <h2 class="text-xl font-bold text-amber-400 mb-4"><i class="fas fa-question-circle mr-2"></i>Guía de Formato</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <strong class="text-amber-300">Acordes comunes:</strong>
                    <p class="text-gray-400">C, Cm, D, Dm, E, Em, F, G, Am, etc.</p>
                </div>
                <div>
                    <strong class="text-amber-300">Formato:</strong>
                    <p class="text-gray-400">[C]Texto de la [G]canción [D]aquí</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
