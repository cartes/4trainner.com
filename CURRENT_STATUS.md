# Estado Actual del Proyecto: Antigravity

Este documento detalla la arquitectura, funcionalidades implementadas y estructura del proyecto al día de hoy.

## 1. Arquitectura Técnica

El proyecto utiliza una arquitectura híbrida monolith:

*   **Backend:** Laravel 12 (PHP 8.2+).
*   **Frontend:** Vue.js 3 (Composition API) montado sobre vistas Blade.
    *   No es una SPA completa (Single Page Application).
    *   Blade maneja el enrutamiento inicial y renderiza un contenedor `<div id="app" data-component="...">`.
    *   `resources/js/app.js` monta el componente Vue correspondiente basado en el atributo `data-component`.
*   **Base de Datos:** MySQL (con soporte configurado para Oracle en `composer.json` pero usando migraciones estándar de Laravel).
*   **Estilos:** Tailwind CSS + Flowbite.
*   **Autenticación:**
    *   **Web:** Sesiones estándar de Laravel (Breeze/Default).
    *   **API:** Laravel Sanctum.
*   **Permisos:** `spatie/laravel-permission`.

## 2. Modelos y Base de Datos

Las entidades principales implementadas son:

*   **User:** Modelo central con SoftDeletes y Roles.
    *   Relaciones: `students`, `professors` (pivot `professor_student`), `routines`, `progress`.
    *   Nota: Existe una relación legacy `trainers` (pivot `trainer_student`) que parece redundante con `professors`.
*   **Routine:** Rutinas de entrenamiento asignadas por entrenadores.
*   **Exercise:** Ejercicios que componen las rutinas.
*   **StudentProgress:** Seguimiento del progreso del alumno.
*   **Category:** Categorización (probablemente para ejercicios o videos futuros).
*   **UserMeta:** Información adicional del usuario.

### Migraciones Clave
*   `users`, `roles`, `permissions`.
*   `exercises`, `routines`, `user_routine`, `routine_exercise`.
*   `professor_student` (Relación Profesor-Alumno).
*   `trainer_student` (Relación Entrenador-Alumno - **Posible duplicidad**).

## 3. Roles y Permisos

El sistema define los siguientes roles (vía `DatabaseSeeder` o lógica de negocio):

1.  **Super-Admin:** Acceso total. (Email seed: `cristiancartesa@gmail.com` según memoria, o `admin@antigravity.com` según `AGENTS.md`).
2.  **Profesor (Teacher/Trainer):** Gestiona alumnos y rutinas.
3.  **Alumno (Student):** Visualiza rutinas y dashboard.
4.  **Moderador:** Rol administrativo limitado.

## 4. Funcionalidades Implementadas

### Backend (API & Web)
*   **Autenticación:** Registro, Login, Logout (Web y API v1).
*   **Gestión de Usuarios:** CRUD básico para administradores.
*   **Gestión de Rutinas (API):**
    *   Endpoints para crear y listar rutinas (`/api/v1/trainer/routines`).
    *   Asignación de rutinas a alumnos.
    *   Listado de ejercicios.
*   **Gestión de Alumnos (Teacher):**
    *   Vista de lista de alumnos (`/teacher/students`).
    *   Endpoint API para obtener detalles del alumno y su progreso (`/api/v1/teacher/students/{id}`).

### Frontend (Vue Components)
Los componentes principales se encuentran en `resources/js/Components/`:
*   `Teacher/Students/Index.vue` y `Show.vue`: Gestión de alumnos.
*   `StudentDashboard.vue`: Panel del alumno.
*   `TrainerDashboard.vue`: Panel del profesor/entrenador.
*   `SuperAdminDashboard.vue`: Panel de administración.
*   `Welcome.vue`, `Pricing.vue`: Páginas públicas.

## 5. Discrepancias y Áreas de Atención

1.  **Terminología Mixta:** El código mezcla "Trainer" y "Profesor".
    *   Rutas API: `/api/v1/trainer/...`
    *   Rutas Web: `/teacher/...` y roles `profesor`.
    *   Esto debe unificarse para evitar confusión.
2.  **Funcionalidad "Antigravity" Faltante:**
    *   Según `AGENTS.md`, el proyecto es una plataforma de **Streaming y VOD**.
    *   **NO existen** modelos ni lógica para: `Channel` (Canales), `Video`, `Stream`, integración con OBS.
    *   Actualmente el sistema funciona más como un gestor de entrenamientos (Gym Management) que como una plataforma de streaming.

## 6. Estructura de Directorios Relevante

```
app/
├── Http/Controllers/
│   ├── Api/V1/ (Lógica principal de la API)
│   ├── Teacher/ (Controladores web para profesores)
│   └── ...
├── Models/ (User, Routine, Exercise, etc.)
resources/
├── js/
│   ├── Components/ (Componentes Vue)
│   ├── app.js (Punto de entrada, montaje de Vue)
│   └── ...
├── views/ (Vistas Blade contenedoras)
routes/
├── api.php (Definición de endpoints V1)
├── web.php (Rutas de vistas y autenticación web)
```
