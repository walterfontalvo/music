# 🎸 LaCuerda - Plataforma de Acordes y Letras

Una plataforma colaborativa para compartir letras y acordes de canciones, inspirada en lacuerda.net.

## ✨ Características

### 🎵 Funcionalidades Principales
- **Catálogo Colaborativo** - Sube, busca y visualiza letras y acordes
- **🎚️ Transpositor Automático** - Cambia la tonalidad (+6 a -6 semitonos)
- **⏸️ AutoScroll** - Desplazamiento automático para tocar sin ver la pantalla
- **⭐ Sistema de Calificación** - Votación 1-5 estrellas
- **💬 Comentarios** - Comunidad colaborativa
- **🔍 Búsqueda Avanzada** - Filtra por título, artista, género, dificultad
- **📊 Ordenamiento** - Reciente, Tendencia, Popular

### 🎨 Diseño
- Tema oscuro profesional
- Interfaz 100% responsive
- Acordes resaltados en color
- Soporte para imágenes de portada

## 🚀 Requisitos

- PHP 7.4+
- SQLite3
- Navegador moderno

## 📦 Instalación

```bash
# 1. Clonar o descargar el repositorio
git clone https://github.com/walterfontalvo/music.git
cd music

# 2. Crear directorio de uploads
mkdir -p public/uploads
chmod 755 public/uploads

# 3. Iniciar servidor PHP
cd public
php -S localhost:8000

# 4. Acceder en navegador
# http://localhost:8000
```

## 📁 Estructura del Proyecto

```
music/
├── config/
│   ├── config.php          # Configuración general
│   └── Database.php        # Clase SQLite PDO
├── src/
│   ├── SongManager.php     # Gestión de canciones
│   ├── RatingManager.php   # Sistema de calificaciones
│   ├── CommentManager.php  # Gestión de comentarios
│   └── ChordParser.php     # Parser y transpositor de acordes
├── public/
│   ├── index.php           # Galería principal
│   ├── song.php            # Detalle de canción + herramientas
│   ├── upload.php          # Subir nueva canción
│   ├── rate.php            # AJAX para calificaciones
│   ├── add-comment.php     # Agregar comentarios
│   └── uploads/            # Almacén de imágenes
└── db/
    └── lacuerda.db         # Base de datos SQLite (creada automáticamente)
```

## 🛠️ Tecnologías

- **Backend**: PHP 7.4+
- **Frontend**: HTML5, Tailwind CSS
- **Base de Datos**: SQLite3
- **API**: AJAX/Fetch API

## 📝 Uso

### Subir una Canción

1. Haz clic en "Subir Canción"
2. Completa los campos:
   - Título
   - Artista
   - Género
   - Dificultad
   - Letras y acordes
   - Imagen (opcional)
3. Los acordes se resaltan automáticamente

### Formato de Acordes

Escribe los acordes en mayúsculas:
```
C, Cm, C7, Cmaj7, Csus4
D, Dm, D7, Dm7
E, Em, E7, Em7
...
```

Ejemplo de letra:
```
[C] Verso 1
This is [Am] a song
About [G] the night

[C] Chorus
La la [Dm] la la
```

### Usar Transpositor

1. Abre cualquier canción
2. Usa los botones ±  para cambiar la tonalidad
3. Los acordes se transponen automáticamente

### Usar AutoScroll

1. Abre cualquier canción
2. Haz clic en "Iniciar AutoScroll"
3. Ajusta la velocidad con el slider
4. Haz clic en "Pausar" para detener

## 🎯 Características Técnicas

### Transposición de Acordes
El sistema detecta todos los acordes válidos y los transpone automáticamente:
- Soporta tonos naturales, sostenidos y bemoles
- Preserva el tipo de acorde (mayor, menor, séptima, etc.)
- Maneja 17 notas base × 4 variaciones = cientos de combinaciones

### Base de Datos
- **Tablas**: songs, comments, ratings, chord_diagrams
- **Relaciones**: Foreign keys para integridad referencial
- **Índices**: Optimizados para búsquedas rápidas

### Seguridad
- Validación de entrada en todos los formularios
- Protección contra inyección SQL con prepared statements
- Limpieza de HTML/XSS con htmlspecialchars()
- Límites de tamaño de archivos

## 📊 Base de Datos

### Tabla: songs
```sql
CREATE TABLE songs (
    id INTEGER PRIMARY KEY,
    title TEXT NOT NULL,
    artist TEXT NOT NULL,
    genre TEXT NOT NULL,
    difficulty TEXT NOT NULL,
    lyrics TEXT NOT NULL,
    image_path TEXT,
    rating_count INTEGER,
    rating_sum INTEGER,
    views INTEGER,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
)
```

### Tabla: ratings
```sql
CREATE TABLE ratings (
    id INTEGER PRIMARY KEY,
    song_id INTEGER NOT NULL,
    ip_address TEXT NOT NULL,
    rating INTEGER NOT NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (song_id) REFERENCES songs(id)
)
```

### Tabla: comments
```sql
CREATE TABLE comments (
    id INTEGER PRIMARY KEY,
    song_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (song_id) REFERENCES songs(id)
)
```

## 🔧 Configuración Personalizada

Edita `config/config.php` para:

```php
// Cambiar máximo tamaño de archivo
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB

// Cambiar géneros disponibles
define('GENRES', [
    'rock' => 'Rock',
    'pop' => 'Pop',
    // agregar más...
]);

// Cambiar items por página
define('ITEMS_PER_PAGE', 20);
```

## 🐛 Troubleshooting

### Error: "Database connection failed"
- Verificar que `/db/` existe o es escribible
- Ejecutar: `mkdir -p db && chmod 755 db`

### Las imágenes no se suben
- Verificar que `/public/uploads/` existe y es escribible
- Ejecutar: `mkdir -p public/uploads && chmod 755 public/uploads`

### Error de sesión al comentar/calificar
- No se usa sesión, usa IP del usuario
- Los navegadores pueden tener IPs compartidas (proxies)

## 📱 Responsive Design

- ✅ Mobile (< 640px)
- ✅ Tablet (640px - 1024px)
- ✅ Desktop (> 1024px)

## 🎓 Conceptos Implementados

- OOP con clases separadas por responsabilidad
- Patrón Singleton para Database
- Prepared Statements para seguridad
- RESTful endpoints para AJAX
- Gestión de archivos
- Paginación
- Búsqueda y filtrado

## 📄 Licencia

Este proyecto es de código abierto. Siéntete libre de modificarlo y distribuirlo.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Puedes:
- Reportar bugs
- Sugerir nuevas características
- Hacer pull requests

---

**Desarrollado con ❤️ para músicos y guitarristas** 🎸
