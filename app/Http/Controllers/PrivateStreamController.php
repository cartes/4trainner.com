<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrivateStreamController extends Controller
{
    /**
     * Sirve el video mediante Range requests, previniendo descarga directa
     * y requiriendo que el alumno tenga sesión activa.
     */
    public function stream(Video $video)
    {
        // 1. Verificación básica (podrías expandir esto a Validar que el alumno pague suscripción)
        if (!auth()->check()) {
            abort(403, 'Acceso denegado a FoxFit TV.');
        }

        // Si es 'live', el flujo HLS o RTMP va por otro lado. Aquí manejaremos VODs locales por ahora.
        // Si el video usa una URL externa o S3 cloud no necesita esto.

        $path = $video->file_path;

        // Verifica si existe en el disco privado storage/app/videos (asumiendo disco 'local')
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Video no encontrado en la bóveda segura.');
        }

        $absolutePath = Storage::disk('local')->path($path);

        // Usamos una feature de Laravel para leer y enviar streams de archivos locales
        return response()->file($absolutePath);
    }
}

