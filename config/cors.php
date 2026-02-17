<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Allowed Paths
    |--------------------------------------------------------------------------
    |
    | Especifica qué rutas deben tener habilitado CORS.
    | - 'api/*' permite CORS en todas las rutas API
    | - 'sanctum/csrf-cookie' es necesario para autenticación con cookies
    |
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | Métodos HTTP permitidos en las solicitudes CORS.
    | '*' permite todos los métodos (GET, POST, PUT, PATCH, DELETE, OPTIONS)
    |
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Orígenes permitidos para hacer solicitudes a tu API.
    |
    | DESARROLLO:
    | - http://localhost:5173 (Vite dev server por defecto)
    | - http://localhost:3000 (alternativa común)
    | - http://127.0.0.1:5173
    |
    | PRODUCCIÓN:
    | - Reemplazar con tu dominio real: https://tudominio.com
    |
    | IMPORTANTE: Nunca uses '*' en producción por seguridad
    |
    */
    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:5173'),
        'http://localhost:3000',
        'http://127.0.0.1:5173',
        'http://localhost:8000', // Para testing local
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | Patrones de orígenes permitidos usando expresiones regulares.
    | Útil para permitir subdominios dinámicos.
    |
    | Ejemplo: '#^https?://.*\.tudominio\.com$#'
    |
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Headers permitidos en las solicitudes CORS.
    | '*' permite todos los headers
    |
    | Headers comunes:
    | - 'Content-Type' (para JSON)
    | - 'Authorization' (para tokens Bearer)
    | - 'X-Requested-With' (para AJAX)
    | - 'Accept' (para negociación de contenido)
    |
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Headers que el navegador puede exponer al frontend.
    | Por defecto, solo headers simples están disponibles.
    |
    | Útil para exponer headers personalizados como:
    | - 'X-Total-Count' (para paginación)
    | - 'X-RateLimit-Remaining' (para rate limiting)
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | Tiempo en segundos que el navegador puede cachear la respuesta
    | de la solicitud preflight (OPTIONS).
    |
    | 0 = sin cache
    | 3600 = 1 hora
    | 86400 = 24 horas
    |
    */
    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Indica si las solicitudes pueden incluir credenciales (cookies, headers de autorización).
    |
    | true = Permite cookies y autenticación
    | false = Solo solicitudes sin credenciales
    |
    | IMPORTANTE: Si es true, 'allowed_origins' NO puede ser '*'
    |
    | Para Sanctum con tokens Bearer, puede ser false.
    | Para Sanctum con cookies (SPA), debe ser true.
    |
    */
    'supports_credentials' => false,

];
