# LaMusica - Plataforma de Letras y Acordes 🎸

Una plataforma colaborativa estilo **LaCuerda.net** para compartir letras y acordes de canciones en español.

## ✨ Características

### 🎵 Gestión de Canciones
- ✅ Subir letras y acordes de canciones
- ✅ Búsqueda por título, artista o género
- ✅ Filtrado por dificultad y género
- ✅ Ordenamiento: reciente, tendencia, popular
- ✅ Contador de vistas

### 🎼 Herramientas Musicales
- ✅ **Transpositor (Transpose)**: Cambiar tonalidad automáticamente
- ✅ **AutoScroll**: Desplazamiento automático de la pantalla
- ✅ **Diagramas de acordes**: Visualización de acordes
- ✅ **Resaltado de acordes**: Acordes destacados en color

### 👥 Funcionalidades Sociales
- ✅ Sistema de calificación (1-5 estrellas)
- ✅ Comentarios en canciones
- ✅ Reputación de usuarios
- ✅ Perfil de usuarios

### 📱 Características Técnicas
- ✅ 100% Responsive (móvil, tablet, desktop)
- ✅ Diseño moderno con Tailwind CSS
- ✅ Base de datos SQLite
- ✅ Sin dependencias externas
- ✅ Tema oscuro profesional

## 🚀 Instalación

### Requisitos
- PHP 7.4+
- SQLite3 (incluido en PHP)
- Navegador moderno

### Pasos

1. **Clonar el repositorio**
```bash
git clone https://github.com/walterfontalvo/music.git
cd music
```

2. **Crear carpeta de datos**
```bash
mkdir -p data
chmod 777 data
```

3. **Crear carpeta de uploads**
```bash
mkdir -p public/uploads
chmod 777 public/uploads
```

4. **Iniciar servidor local**
```bash
cd public
php -S localhost:8000
```

5. **Acceder a la aplicación**
Abre tu navegador en: **http://localhost:8000**

## 📁 Estructura del Proyecto

```
music/
├── config/
│   ├── config.php              # Configuración general
│   └── Database.php            # Conexión SQLite
├── src/
│   ├── SongManager.php         # Gestión de canciones
│   ├── CommentManager.php      # Sistema de comentarios
│   ├── RatingManager.php       # Sistema de calificaciones
│   └── UserManager.php         # Gestión de usuarios
├── public/
│   ├── index.php               # Galería principal
│   ├── song.php                # Detalle de canción
│   ├── upload.php              # Subir canción
│   └── uploads/                # Almacén de imágenes
├── data/
│   └── music.db                # Base de datos SQLite
└── README.md                   # Este archivo
```

## 💾 Base de Datos

### Tablas

**users** - Información de usuarios
```sql
id, username, email, password, reputation, created_at
```

**songs** - Catálogo de canciones
```sql
id, title, artist, lyrics, chords, key_original, genre, user_id, 
views, difficulty, rating, created_at, updated_at
```

**comments** - Comentarios en canciones
```sql
id, song_id, user_id, content, created_at
```

**ratings** - Calificaciones
```sql
id, song_id, user_id, rating, created_at
```

**chord_diagrams** - Diagramas de acordes
```sql
id, chord_name, frets, fingers, svg
```

## 🎯 Funcionalidades Principales

### 1. Buscar Canciones
```
Búsqueda por:
- Título de canción
- Nombre del artista
- Género musical
- Dificultad
```

### 2. Subir Canción
```
Datos requeridos:
- Título *
- Artista *
- Acordes con formato [C] [G] [D] *
- Tonalidad (opcional)
- Género (opcional)
- Dificultad (opcional)
- Letras (opcional)
```

### 3. Transposer (Cambiar Tonalidad)
```
Botones de -6 a +6 semitonos
Automático basado en tonalidad original
```

### 4. Calificar y Comentar
```
Sistema de 5 estrellas
Comentarios con timestamp
Asociados al usuario
```

## 🎨 Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Frontend**: HTML5 + Tailwind CSS
- **Base de datos**: SQLite
- **Iconos**: Font Awesome 6
- **JavaScript**: Vanilla JS

## 📝 Formato de Acordes

### Ejemplo 1: Simple
```
[C]Esto es una canción
[G]Con acordes [D]simples
[Am]Y fácil de seguir
```

### Ejemplo 2: Con cambios
```
[Verse]
[C]Primera estrofa aquí
[G]Segunda línea

[Chorus]
[Am]Coro [F]con acordes
[G]Diferentes [C]acordes
```

## 🔧 Configuración

Edita `config/config.php` para personalizar:

```php
define('APP_NAME', 'LaMusica');
define('APP_URL', 'http://localhost:8000');
define('GENRES', [...]); // Géneros
define('DIFFICULTY_LEVELS', [...]); // Niveles de dificultad
```

## 🌐 Despliegue en Producción

### Requisitos adicionales
- Dominio propio
- Hosting con PHP
- SSL/TLS

### Pasos

1. Subir archivos a servidor
2. Cambiar `APP_URL` en config.php
3. Configurar permisos de carpetas
4. Configurar base de datos
5. Activar HTTPS

## 📱 Características Móviles

- ✅ Interfaz 100% responsive
- ✅ Optimizada para pantallas pequeñas
- ✅ AutoScroll para leer mientras tocas
- ✅ Botones grandes y fáciles de tocar
- ✅ Tema oscuro para bajo consumo de batería

## 🚦 Próximas Características

- 🔄 Autenticación con redes sociales
- 🎙️ Grabación de versiones
- 🎸 Diagramas animados de acordes
- 📊 Estadísticas de usuario
- 🎵 Integración con Spotify
- 🌙 Modo karaoke

## 📄 Licencia

MIT License - Libre para usar y modificar

## 👨‍💻 Autor

**Walter Fontalvo** - [GitHub](https://github.com/walterfontalvo)

## 📞 Soporte

¿Problemas? Abre un issue en el repositorio o contacta al autor.

## 🤝 Contribuciones

¡Las contribuciones son bienvenidas! Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

---

⭐ Si te gusta este proyecto, ¡dale una estrella!
