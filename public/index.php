<?php
require_once '../config/config.php';
require_once '../src/SongManager.php';

$songManager = new SongManager($pdo);

$search = $_GET['search'] ?? '';
$genre = $_GET['genre'] ?? '';
$sort = $_GET['sort'] ?? 'recent';
$page = (int)($_GET['page'] ?? 1);

$songs = $songManager->getSongs($search, $genre, $sort, $page, 12);
$total = $songManager->countSongs($search, $genre);
$pages = ceil($total / 12);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Letras y Acordes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .chord { background-color: #fbbf24; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 0.85em; }
        .lyrics-display { font-family: monospace; white-space: pre-wrap; line-height: 1.8; }
    </style>
</head>
<body class="bg-gray-900 text-gray-100">
    <!-- Navbar -->
    <nav class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-amber-400">
                <i class="fas fa-guitar mr-2"></i>LaMusica
            </h1>
            <div class="flex gap-4">
                <a href="#" class="hover:text-amber-400 transition"><i class="fas fa-home mr-1"></i>Inicio</a>
                <a href="upload.php" class="hover:text-amber-400 transition"><i class="fas fa-plus mr-1"></i>Subir</a>
                <a href="#" class="hover:text-amber-400 transition"><i class="fas fa-user mr-1"></i>Perfil</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <div class="bg-gradient-to-r from-amber-600 to-orange-600 py-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-2">La Comunidad de Letras y Acordes</h2>
            <p class="text-lg opacity-90">Encuentra miles de canciones con acordes para guitarra y otros instrumentos</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-gray-800 border-b border-gray-700 py-6 sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-4">
            <form method="GET" class="flex gap-4 flex-wrap">
                <!-- Búsqueda -->
                <div class="flex-1 min-w-64">
                    <input type="text" name="search" placeholder="Buscar canción, artista..." value="<?= htmlspecialchars($search) ?>"
                        class="w-full px-4 py-2 rounded bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-400">
                </div>

                <!-- Género -->
                <select name="genre" class="px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <option value="">Todos los géneros</option>
                    <?php foreach (GENRES as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $genre === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>

                <!-- Ordenar -->
                <select name="sort" class="px-4 py-2 rounded bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Recientes</option>
                    <option value="trending" <?= $sort === 'trending' ? 'selected' : '' ?>>Tendencia</option>
                    <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Popular</option>
                </select>

                <button type="submit" class="px-6 py-2 bg-amber-500 hover:bg-amber-600 rounded font-semibold transition">
                    <i class="fas fa-search mr-1"></i>Buscar
                </button>
            </form>
        </div>
    </div>

    <!-- Grid de canciones -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($songs as $song): ?>
                <a href="song.php?id=<?= $song['id'] ?>" class="bg-gray-800 rounded-lg overflow-hidden hover:shadow-xl hover:shadow-amber-500/20 transition transform hover:scale-105">
                    <div class="bg-gradient-to-br from-amber-500 to-orange-600 h-32 flex items-center justify-center">
                        <i class="fas fa-music text-6xl opacity-20"></i>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg text-amber-400 line-clamp-2"><?= htmlspecialchars($song['title']) ?></h3>
                        <p class="text-gray-400 text-sm"><?= htmlspecialchars($song['artist']) ?></p>
                        <div class="mt-3 flex justify-between text-xs text-gray-500">
                            <span><i class="fas fa-eye mr-1"></i><?= $song['views'] ?> vistas</span>
                            <span><i class="fas fa-star mr-1"></i><?= round($song['rating'], 1) ?>/5</span>
                        </div>
                        <div class="mt-3 flex gap-2 text-xs">
                            <span class="bg-gray-700 px-2 py-1 rounded"><?= GENRES[$song['genre']] ?? $song['genre'] ?></span>
                            <span class="bg-amber-900 px-2 py-1 rounded"><?= DIFFICULTY_LEVELS[$song['difficulty']] ?? $song['difficulty'] ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($songs)): ?>
            <div class="text-center py-12">
                <i class="fas fa-music text-6xl text-gray-600 mb-4"></i>
                <p class="text-gray-400 text-xl">No se encontraron canciones</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Paginación -->
    <?php if ($pages > 1): ?>
        <div class="max-w-7xl mx-auto px-4 py-8 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?search=<?= urlencode($search) ?>&genre=<?= urlencode($genre) ?>&sort=<?= urlencode($sort) ?>&page=<?= $i ?>"
                    class="px-4 py-2 rounded <?= $i === $page ? 'bg-amber-500' : 'bg-gray-700 hover:bg-gray-600' ?> transition">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="bg-gray-800 border-t border-gray-700 mt-12 py-6 text-center text-gray-400">
        <p>&copy; 2024 LaMusica. Plataforma colaborativa de letras y acordes.</p>
    </footer>
</body>
</html>
