<x-admin-layout>
    <div class="container block w-full">
        <h1 class="text-2xl font-bold mb-6">Control de Usuarios</h1>

        <!-- Sistema de pestañas -->
        <div class="flex space-x-4 border-b mb-6">
            <a href="javascript:void(0)" class="tab-link text-md py-2 px-4 text-gray-600 hover:text-gray-800 border-b-2 border-transparent active" id="tab-users">Lista de Usuarios</a>
            <a href="javascript:void(0)" class="tab-link text-md py-2 px-4 text-gray-600 hover:text-gray-800 border-b-2 border-transparent" id="tab-create">Crear Usuario</a>
        </div>

        <!-- Contenido de las pestañas -->
        <div id="tab-content-users" class="tab-content">
            <div class="bg-white rounded-xl p-8 shadow-lg">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Lista de Usuarios</h2>

                <!-- Tabla con DataTables -->
                <table id="usersTable" class="dataTable min-w-full divide-y divide-gray-200 no-footer">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-2">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-2">Correo Electrónico</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-2">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>

        <div id="tab-content-create" class="tab-content hidden">
            <div class="bg-white rounded-xl p-8 shadow-lg">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Crear Usuario</h2>

                <!-- Formulario para crear nuevos usuarios -->
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="first_name" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="mt-1 block w-full">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full">
                        <button type="button" onclick="generatePassword()" class="mt-2 text-sm text-blue-500">Generar Contraseña Segura</button>
                    </div>
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" name="phone" id="phone" class="mt-1 block w-full">
                    </div>
                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                        <select name="role" id="role" class="mt-1 block w-full">
                            <option value="alumno">Alumno</option>
                            <option value="profesor">Profesor</option>
                        </select>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Generar contraseña segura -->
    <script>
        function generatePassword() {
            const password = Math.random().toString(36).slice(-8) + Math.random().toString(36).toUpperCase().slice(-4);
            document.getElementById('password').value = password;
            alert('Contraseña Generada: ' + password);
        }

        // Control de pestañas
        document.getElementById('tab-users').addEventListener('click', function() {
            document.getElementById('tab-content-users').classList.remove('hidden');
            document.getElementById('tab-content-create').classList.add('hidden');
            this.classList.add('active');
            document.getElementById('tab-create').classList.remove('active');
        });

        document.getElementById('tab-create').addEventListener('click', function() {
            document.getElementById('tab-content-users').classList.add('hidden');
            document.getElementById('tab-content-create').classList.remove('hidden');
            this.classList.add('active');
            document.getElementById('tab-users').classList.remove('active');
        });

        // Inicializar DataTables
        var usersTable = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: 'http://127.0.0.1:8000/admin/users/data',
            columnDefs: [{
                width: '25%',
                targets: '_all',
                createdCell: function(cell, cellData, rowData, rowIndex, colIndex) {
                    console.log(cellData);
                    $(cell).addClass('px-4 py-2');
                    if (cellData == 'profesor' || cellData == 'alumno') {
                        $(cell).addClass('uppercase');
                    }
                    if (cellData == 'profesor') {
                        $(cell).addClass('text-blue-500');
                    } else if (cellData == 'alumno') {
                        $(cell).addClass('text-green-500');
                    } else {
                        $(cell).addClass('text-gray-500');
                    }
                },
            }],
            createdRow: function(row, data, dataIndex) {
                $(row).addClass('px-6 py-4 whitespace-nowrap');
            },
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
        });

        $(window).on('resize', function() {
            usersTable.columns.adjust().draw();
        });
    </script>
</x-admin-layout>
