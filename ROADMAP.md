# Roadmap del Proyecto 4Trainner.com

Este documento describe la hoja de ruta para completar el desarrollo de la plataforma de streaming "4Trainer".

## Fase 1: Consolidación y Refactorización (Inmediato)

El objetivo es limpiar la base de código actual y preparar el terreno para las funcionalidades de streaming.

1.  **Unificación de Terminología:**
    - [x] Renombrar todo el código relacionado con "Trainer" a "Profesor" (o viceversa, pero ser consistente) - _Completado: UI usa "Personal Trainer", código usa "Trainer" (internamente) y DB usa "profesor"._
    - [x] Eliminar duplicidad en `routes/api.php` y `routes/web.php`.
    - [x] Consolidar relaciones `professor_student` y `trainer_student` en una sola tabla (`professor_student` preferiblemente).

2.  **Limpieza de Frontend:**
    - [x] Verificar que todos los componentes Vue (`resources/js/Components/`) estén correctamente importados y utilizados.
    - [x] Eliminar componentes no utilizados o redundantes (`Teacher/*` borrados).

## Fase 2: Experiencia de Usuario y Gestión (Dashboards)

Consolidar la experiencia de usuario para los diferentes roles y completar las vistas de gestión.

1.  **Dashboard de Alumno (Refinamiento):**
    - [ ] Asegurar que el dashboard muestre rutinas asignadas, progreso y entrenadores asignados.
    - [ ] Validar la integración completa con el backend (`StudentController`) y los datos reales.
    - [ ] Mejorar la experiencia visual y de navegación.

2.  **Configuración de Usuario (Settings):**
    - [ ] Implementar la ruta `/settings`, controlador (`SettingsController` o similar) y vistas.
    - [ ] Permitir cambio de contraseña, preferencias de notificación, y detalles de perfil extendidos.
    - [ ] Asegurar que el enlace en el menú de usuario funcione correctamente.

3.  **Dashboard Super-Admin (Completo):**
    - [ ] Implementar gestión completa de usuarios (CRUD) con asignación de roles y permisos.
    - [ ] Gestión de categorías y configuraciones globales del sistema.
    - [ ] Panel de métricas globales y auditoría de actividad.

## Fase 3: Arquitectura de Streaming (Núcleo)

Implementar los modelos y lógica necesarios para soportar transmisiones en vivo y videos bajo demanda.

1.  **Modelo `Channel` (Canales):**
    - Crear migración y modelo `Channel`.
    - Relación: Un `User` (Profesor) tiene uno o más `Channels`.
    - Propiedades: `name`, `description`, `slug`, `cover_image`, `stream_key`.

2.  **Modelo `Video` / `Stream`:**
    - Crear migración y modelo `Video`.
    - Relación: Un `Channel` tiene muchos `Videos`.
    - Propiedades: `title`, `description`, `status` (live, vod, processing), `file_path`, `thumbnail_path`, `duration`.

3.  **Integración con OBS (Backend):**
    - Generación de `stream_key` única por canal.
    - Endpoint para validar `stream_key` (si se usa un servidor RTMP propio o servicio externo).
    - Webhooks para detectar inicio/fin de transmisión desde OBS.

## Fase 4: Frontend de Streaming (Experiencia de Usuario)

Desarrollar la interfaz para profesores y alumnos.

1.  **Panel de Transmisión (Profesor):**
    - Vista donde el profesor puede ver su `stream_key` y URL de servidor RTMP.
    - Monitor de estado de la transmisión (En vivo / Offline).
    - Chat en vivo (opcional en esta fase).

2.  **Vista de Canal (Alumno):**
    - Página pública/privada del canal mostrando:
        - Reproductor de video principal (Live o último video).
        - Lista de videos anteriores (VOD).
        - Información del profesor.

3.  **Reproductor de Video:**
    - Implementar reproductor compatible con HLS (`.m3u8`) para streaming.
    - Soporte para reproducción de archivos VOD (MP4/WebM).

## Fase 5: Monetización y Acceso (Futuro)

Asegurar el modelo de negocio.

1.  **Suscripciones:**
    - Integrar pasarela de pago (Stripe/PayPal).
    - Modelo `Subscription` relacionado con `User` y `Channel`.
    - Middleware para restringir acceso a canales premium.

2.  **Notificaciones:**
    - Email/Push notifications cuando un canal comienza a transmitir.

## Estado de Prioridad

| Tarea                             | Prioridad | Estado    |
| :-------------------------------- | :-------- | :-------- |
| Refactorización Trainer/Profesor  | Alta      | Pendiente |
| Dashboard Alumno (Refinamiento)   | Alta      | Pendiente |
| Configuración de Usuario          | Alta      | Pendiente |
| Dashboard Super-Admin Completo    | Alta      | Pendiente |
| Creación de Modelos Channel/Video | Alta      | Pendiente |
| Integración OBS Básica            | Media     | Pendiente |
| Dashboard Profesor                | Media     | Pendiente |
| Reproductor de Video              | Alta      | Pendiente |
