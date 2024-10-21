<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>
    @vite(['resources/css/app.css']) <!-- Incluye los estilos y scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body class="bg-gray-200 font-sans leading-relaxed tracking-normal">
    <div class="flex flex-col md:flex-row min-h-screen">
        <div class="w-full md:w-2/12 h-full md:h-screen">
            <div class="p-6 text-gray-800 text-3xl font-bold">
                4trainner Admin
            </div>
            @include('partials.super-admin-menu')
        </div>
        <div class="w-full md:w-4/5 p-8">
            <div class="bg-white rounded-lg py-6 px-4">
                {{ $slot }}
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])
</body>
</html>
