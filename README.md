# 4Trainner - Plataforma de Streaming de Fitness

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.52-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.5">
  <img src="https://img.shields.io/badge/Tailwind-3.1-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.4-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js">
</p>

## 📋 Descripción

**4Trainner** es una plataforma moderna de streaming diseñada específicamente para entrenadores personales y profesionales del fitness que desean compartir sus conocimientos a través de clases en vivo y contenido grabado.

La plataforma permite a los trainers:

- 🎥 **Transmitir clases en vivo** con interacción en tiempo real
- 📹 **Subir rutinas de ejercicios grabadas** con categorización avanzada
- 👥 **Gestionar sus alumnos** con perfiles personalizados
- 📊 **Crear programas de entrenamiento** estructurados
- 💪 **Compartir rutinas especializadas** (fuerza, cardio, yoga, HIIT, etc.)
- 📅 **Programar sesiones** y mantener un calendario de clases
- 💬 **Interactuar con la comunidad** de fitness

## ✨ Características Principales

### Para Trainers

- **Panel de Control Profesional**: Dashboard completo con estadísticas y métricas
- **Gestión de Contenido**: Sube, edita y organiza tus clases y rutinas
- **Streaming en Vivo**: Transmite clases en tiempo real con chat integrado
- **Biblioteca de Rutinas**: Organiza tus ejercicios por categorías y niveles
- **Gestión de Alumnos**: Administra perfiles, progreso y suscripciones
- **Calendario de Clases**: Programa y gestiona tus sesiones

### Para Alumnos

- **Acceso a Clases en Vivo**: Participa en sesiones en tiempo real
- **Biblioteca On-Demand**: Accede a rutinas grabadas 24/7
- **Perfil Personalizado**: Seguimiento de progreso y favoritos
- **Múltiples Categorías**: Encuentra el entrenamiento perfecto para ti
- **Interacción Social**: Comenta y comparte con la comunidad

### Para Administradores

- **Panel de Administración**: Control total de la plataforma
- **Gestión de Usuarios**: Administra trainers y alumnos
- **Sistema de Roles**: Control granular de permisos
- **Moderación de Contenido**: Revisa y aprueba publicaciones
- **Analíticas**: Métricas de uso y engagement

## 🚀 Tecnologías Utilizadas

### Backend

- **Laravel 12.52** - Framework PHP moderno y robusto
- **PHP 8.5** - Última versión de PHP con mejoras de rendimiento
- **SQLite/MySQL** - Base de datos flexible (SQLite para desarrollo, MySQL para producción)
- **Spatie Permission** - Sistema avanzado de roles y permisos
- **Laravel Breeze** - Autenticación completa y segura

### Frontend

- **Tailwind CSS 3.1** - Framework CSS utility-first
- **Alpine.js 3.4** - Framework JavaScript reactivo y ligero
- **Vite 5.0** - Build tool moderno y rápido
- **Blade Components** - Sistema de componentes reutilizables
- **Yajra DataTables** - Tablas dinámicas con AJAX

### Herramientas de Desarrollo

- **Composer** - Gestor de dependencias PHP
- **NPM** - Gestor de paquetes JavaScript
- **PHPUnit** - Testing automatizado
- **Laravel Pint** - Code styling

## 📦 Instalación

### Requisitos Previos

- PHP >= 8.2
- Composer
- Node.js >= 18.x
- NPM o Yarn
- SQLite o MySQL

### Pasos de Instalación

1. **Clonar el repositorio**

```bash
git clone https://github.com/tu-usuario/4trainner.git
cd 4trainner
```

2. **Instalar dependencias de PHP**

```bash
composer install
```

3. **Instalar dependencias de JavaScript**

```bash
npm install
```

4. **Configurar el entorno**

```bash
cp .env.example .env
php artisan key:generate
```

5. **Configurar la base de datos**

Para **desarrollo local** (SQLite):

```bash
touch database/database.sqlite
```

Edita `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/completa/a/tu/proyecto/database/database.sqlite
```

Para **producción** (MySQL):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=4trainner
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

6. **Ejecutar migraciones y seeders**

```bash
php artisan migrate --seed
```

7. **Crear enlace simbólico para almacenamiento**

```bash
php artisan storage:link
```

8. **Compilar assets**

Para desarrollo:

```bash
npm run dev
```

Para producción:

```bash
npm run build
```

9. **Iniciar el servidor de desarrollo**

```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`

## 👥 Sistema de Roles

La plataforma cuenta con un sistema de roles robusto:

### Roles Principales

| Rol             | Descripción             | Permisos                                      |
| --------------- | ----------------------- | --------------------------------------------- |
| **Super Admin** | Administrador principal | Acceso total a la plataforma                  |
| **Trainer**     | Entrenador personal     | Crear contenido, gestionar alumnos, streaming |
| **Student**     | Alumno/Usuario          | Ver contenido, participar en clases           |
| **Moderator**   | Moderador de contenido  | Revisar y aprobar publicaciones               |

### Permisos por Rol

```php
// Super Admin
- Gestionar todos los usuarios
- Gestionar roles y permisos
- Acceso a panel de administración
- Ver analíticas globales
- Configuración del sistema

// Trainer
- Crear y editar clases
- Subir videos y rutinas
- Gestionar sus alumnos
- Iniciar streaming en vivo
- Ver sus estadísticas

// Student
- Ver clases en vivo
- Acceder a biblioteca de rutinas
- Comentar y valorar
- Gestionar su perfil
- Seguir trainers

// Moderator
- Revisar contenido reportado
- Aprobar/rechazar publicaciones
- Gestionar comentarios
- Aplicar políticas de comunidad
```

## 🗂️ Estructura del Proyecto

```
4trainner/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Controladores de administración
│   │   │   ├── Auth/           # Autenticación (Breeze)
│   │   │   ├── Trainer/        # Controladores de trainers
│   │   │   └── Student/        # Controladores de alumnos
│   │   ├── Middleware/         # Middleware personalizado
│   │   └── Requests/           # Form Requests para validación
│   ├── Models/                 # Modelos Eloquent
│   ├── Services/               # Lógica de negocio
│   └── Providers/              # Service Providers
├── database/
│   ├── migrations/             # Migraciones de base de datos
│   ├── seeders/                # Seeders para datos iniciales
│   └── factories/              # Factories para testing
├── resources/
│   ├── views/
│   │   ├── admin/              # Vistas de administración
│   │   ├── trainer/            # Vistas de trainers
│   │   ├── student/            # Vistas de alumnos
│   │   ├── components/         # Componentes Blade reutilizables
│   │   └── layouts/            # Layouts principales
│   ├── css/                    # Estilos Tailwind
│   └── js/                     # JavaScript/Alpine.js
├── routes/
│   ├── web.php                 # Rutas web
│   ├── api.php                 # API endpoints
│   └── auth.php                # Rutas de autenticación
└── tests/                      # Tests automatizados
```

## 🔒 Seguridad

La plataforma implementa múltiples capas de seguridad:

- ✅ **Autenticación robusta** con Laravel Breeze
- ✅ **Verificación de email** obligatoria
- ✅ **Protección CSRF** en todos los formularios
- ✅ **Hashing de contraseñas** con Bcrypt
- ✅ **Middleware de roles** para protección de rutas
- ✅ **Validación de formularios** en servidor
- ✅ **Soft Deletes** para recuperación de datos
- ✅ **Rate Limiting** en endpoints críticos
- ✅ **Logging completo** de errores y acciones

## 🧪 Testing

Ejecutar todos los tests:

```bash
php artisan test
```

Ejecutar tests con cobertura:

```bash
php artisan test --coverage
```

Tests específicos:

```bash
php artisan test --filter=UserTest
```

## 📊 Base de Datos

### Tablas Principales

- **users** - Usuarios de la plataforma
- **user_meta** - Metadata de usuarios (teléfono, bio, etc.)
- **roles** - Roles del sistema
- **permissions** - Permisos granulares
- **categories** - Categorías de rutinas
- **workouts** - Rutinas de ejercicios
- **live_sessions** - Sesiones en vivo
- **subscriptions** - Suscripciones de alumnos
- **comments** - Comentarios en clases
- **favorites** - Favoritos de usuarios

## 🚀 Despliegue

### Preparación para Producción

1. **Optimizar configuración**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. **Compilar assets**

```bash
npm run build
```

3. **Configurar variables de entorno**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
```

4. **Configurar base de datos de producción**

```bash
php artisan migrate --force
```

### Servidores Recomendados

- **Laravel Forge** - Despliegue automático
- **DigitalOcean** - VPS económico
- **AWS** - Escalabilidad empresarial
- **Heroku** - Despliegue rápido

## 🤝 Contribución

¡Las contribuciones son bienvenidas! Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Roadmap

### Fase 1 - MVP (Actual)

- [x] Sistema de autenticación
- [x] Gestión de usuarios y roles
- [x] Panel de administración básico
- [ ] Subida de videos
- [ ] Categorización de rutinas

### Fase 2 - Streaming

- [ ] Integración de streaming en vivo
- [ ] Chat en tiempo real
- [ ] Sistema de notificaciones
- [ ] Calendario de clases

### Fase 3 - Comunidad

- [ ] Sistema de comentarios
- [ ] Valoraciones y reviews
- [ ] Perfiles públicos de trainers
- [ ] Feed social

### Fase 4 - Monetización

- [ ] Sistema de suscripciones
- [ ] Pagos integrados
- [ ] Planes premium
- [ ] Comisiones para trainers

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👨‍💻 Autor

**Cristian Cartes**

- GitHub: [@cristiancartes](https://github.com/cristiancartes)
- Email: contacto@4trainner.com

## 🙏 Agradecimientos

- Laravel Team por el increíble framework
- Spatie por sus excelentes paquetes
- La comunidad de código abierto

---

<p align="center">Hecho con ❤️ para la comunidad fitness</p>
