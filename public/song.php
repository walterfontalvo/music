<?php
require_once '../config/config.php';
require_once '../src/SongManager.php';
require_once '../src/CommentManager.php';
require_once '../src/RatingManager.php';

$songId = (int)($_GET['id'] ?? 0);
if (!$songId) header('Location: index.php');

$songManager = new SongManager($pdo);
$commentManager = new CommentManager($pdo);
$ratingManager = new RatingManager($pdo);

$song = $songManager->getSongById($songId);
if (!$song) header('Location: index.php');

$songManager->updateViews($songId);
$comments = $commentManager->getComments($songId);

$transpose = (int)($_GET['transpose'] ?? 0);
$chords = $song['chords'];
if ($transpose != 0) {
    $chords = $songManager->transpose($chords, $transpose);
}

$userId = $_SESSION['user_id'] ?? null;
$userRating = $userId ? $ratingManager->getUserRating($songId, $userId) : null;

// Procesar comentario
if ($_POST['action'] === 'comment' && $userId) {
    $content = $_POST['content'] ?? '';
    if (strlen($content) >= 3) {
        $commentManager->addComment($songId, $userId, $content);
        header('Location: song.php?id=' . $songId);
    }
}

// Procesar rating
if ($_POST['action'] === 'rate' && $userId) {
    $rating = (int)$_POST['rating'];
    if ($rating >= 1 && $rating <= 5) {
        $ratingManager->rateSong($songId, $userId, $rating);
        header('Location: song.php?id=' . $songId);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($song['title']) ?> - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .chord { background-color: #fbbf24; color: #000; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 0.85em; display: inline-block; }
        .lyrics-display { font-family: 'Courier New', monospace; white-space: pre-wrap; line-height: 2; }
        .lyric-line { margin-bottom: 1rem; }
    </style>
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

    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Encabezado -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6 border border-gray-700">
            <h1 class="text-4xl font-bold text-amber-400 mb-2"><?= htmlspecialchars($song['title']) ?></h1>
            <p class="text-xl text-gray-400 mb-4">por <span class="text-amber-300"><?= htmlspecialchars($song['artist']) ?></span></p>
            
            <div class="flex flex-wrap gap-4 mb-6">
                <div><i class="fas fa-eye text-amber-400 mr-2"></i><?= $song['views'] ?> vistas</div>
                <div><i class="fas fa-star text-amber-400 mr-2"></i><?= round($song['rating'], 1) ?>/5 (<?= count($comments) ?> comentarios)</div>
                <div><i class="fas fa-music text-amber-400 mr-2"></i><?= GENRES[$song['genre']] ?? $song['genre'] ?></div>
                <div><i class="fas fa-graduation-cap text-amber-400 mr-2"></i><?= DIFFICULTY_LEVELS[$song['difficulty']] ?? $song['difficulty'] ?></div>
            </div>

            <!-- Herramientas -->
            <div class="bg-gray-700 rounded-lg p-4">
                <h3 class="font-bold mb-3 text-amber-400"><i class="fas fa-tools mr-2"></i>Herramientas</h3>
                
                <!-- Transpositor -->
                <div class="mb-4">
                    <label class="block text-sm mb-2">Cambiar tono (Transpose):</label>
                    <div class="flex gap-2 flex-wrap">
                        <?php for ($i = -6; $i <= 6; $i++): ?>
                            <a href="?id=<?= $songId ?>&transpose=<?= $i ?>" 
                                class="px-3 py-2 rounded <?= $transpose === $i ? 'bg-amber-500' : 'bg-gray-600 hover:bg-gray-500' ?> transition text-sm">
                                <?= $i > 0 ? '+' : '' ?><?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- AutoScroll -->
                <button onclick="toggleAutoScroll()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded transition">
                    <i class="fas fa-scroll mr-2"></i>AutoScroll
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Contenido principal -->
            <div class="lg:col-span-2">
                <!-- Letras y Acordes -->
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 mb-6">
                    <h2 class="text-2xl font-bold text-amber-400 mb-4"><i class="fas fa-file-lines mr-2"></i>Letras y Acordes</h2>
                    <div class="bg-gray-900 rounded p-4 lyrics-display text-sm">
                        <?php 
                        // Renderizar letras con acordes
                        $lines = explode("\n", $chords);
                        foreach ($lines as $line):
                            if (trim($line)):
                                // Resaltar acordes
                                $line = preg_replace_callback(
                                    '/\b(' . implode('|', COMMON_CHORDS) . ')(?:m|maj7|min7|7)?\b/i',
                                    function($m) { return '<span class="chord">' . $m[0] . '</span>'; },
                                    htmlspecialchars($line)
                                );
                        ?>
                        <div class="lyric-line"><?= $line ?></div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>

                <!-- Rating -->
                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 mb-6">
                    <h3 class="font-bold text-amber-400 mb-3"><i class="fas fa-star mr-2"></i>Calificación</h3>
                    <form method="POST" class="flex gap-2">
                        <input type="hidden" name="action" value="rate">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button type="submit" name="rating" value="<?= $i ?>" 
                                class="text-3xl transition <?= ($userRating && $userRating >= $i) ? 'text-amber-400' : 'text-gray-600 hover:text-amber-300' ?>">
                                <i class="fas fa-star"></i>
                            </button>
                        <?php endfor; ?>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Información -->
                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 mb-6">
                    <h3 class="font-bold text-amber-400 mb-3"><i class="fas fa-info-circle mr-2"></i>Información</h3>
                    <div class="text-sm space-y-2">
                        <p><span class="text-gray-400">Tonalidad:</span> <span class="text-amber-300"><?= htmlspecialchars($song['key_original']) ?></span></p>
                        <p><span class="text-gray-400">Subido:</span> <span><?= date('d/m/Y', strtotime($song['created_at'])) ?></span></p>
                        <p><span class="text-gray-400">Por:</span> <span><?= htmlspecialchars($song['username']) ?></span></p>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 space-y-2">
                    <button class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded transition">
                        <i class="fas fa-download mr-2"></i>Descargar
                    </button>
                    <button class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 rounded transition">
                        <i class="fas fa-share mr-2"></i>Compartir
                    </button>
                    <button class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 rounded transition">
                        <i class="fas fa-flag mr-2"></i>Reportar
                    </button>
                </div>
            </div>
        </div>

        <!-- Comentarios -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 mt-8">
            <h2 class="text-2xl font-bold text-amber-400 mb-4"><i class="fas fa-comments mr-2"></i>Comentarios</h2>
            
            <?php if ($userId): ?>
                <form method="POST" class="mb-6">
                    <input type="hidden" name="action" value="comment">
                    <textarea name="content" placeholder="Escribe un comentario..." required
                        class="w-full px-4 py-2 rounded bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-400 mb-2"></textarea>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 rounded font-semibold transition">
                        Comentar
                    </button>
                </form>
            <?php else: ?>
                <p class="text-gray-400 mb-4"><a href="#" class="text-amber-400 hover:underline">Inicia sesión</a> para comentar</p>
            <?php endif; ?>

            <div class="space-y-4">
                <?php foreach ($comments as $comment): ?>
                    <div class="bg-gray-700 rounded p-4">
                        <div class="flex justify-between mb-2">
                            <strong class="text-amber-400"><?= htmlspecialchars($comment['username']) ?></strong>
                            <small class="text-gray-400"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></small>
                        </div>
                        <p><?= htmlspecialchars($comment['content']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleAutoScroll() {
            // Implementar autoscroll
            alert('AutoScroll activado');
        }
    </script>
</body>
</html>
