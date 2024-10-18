<x-admin-layout>
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-6">Dashboard del Super-Admin</h1>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl p-8 shadow-lg flex items-start align-start">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Usuarios</h2>
                    <p class="text-lg font-medium text-gray-800">Total de Usuarios Registrados
                        <span class="text-4xl font-extrabold text-blue-500 mt-2 ml-3">{{ $totalUsers }}</span>
                    </p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-8 shadow-lg">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Tendencia de Nuevas Solicitudes</h2>
                <div class="h-40 bg-gradient-to-r from-green-300 via-yellow-300 to-red-400 rounded-lg"></div>
            </div>
        </div>
    </div>
</x-admin-layout>
