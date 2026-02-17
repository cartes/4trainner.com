<x-admin-layout>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="container block w-full">
        <h1 class="text-2xl font-bold mb-6">Control de Usuarios</h1>

        <!-- Sistema de pestañas -->
        <div class="flex space-x-4 border-b mb-6">
            <a href="javascript:void(0)" class="tab-link text-md py-2 px-4 text-gray-600 hover:text-gray-800 border-b-2 border-transparent {{ session('active_tab') === 'users' ? 'active' : null }}" id="tab-users">Lista de Usuarios</a>
            <a href="javascript:void(0)" class="tab-link text-md py-2 px-4 text-gray-600 hover:text-gray-800 border-b-2 border-transparent {{ session('active_tab') === 'create' ? 'active' : null }}" id="tab-create">Crear Usuario</a>
        </div>

        <!-- Contenido de las pestañas -->
        <div id="tab-content-users" class="tab-content">

            <!-- Modal de Edición de Usuario -->
            <div id="editUserModal" class="fixed inset-0 z-50 hidden bg-gray-800 bg-opacity-50 flex items-center justify-center">
                <div class="bg-white rounded-lg p-8 shadow-lg w-1/3">
                    <h2 class="text-xl font-bold mb-4">Editar Usuario</h2>
                    <form id="editUserForm" method="POST" action="{{ route('admin.users.update', ':id') }}">
                        @csrf
                        @method('PUT') <!-- Método PUT para actualizar -->
                        <input class="hover:bg-blue-700" type="hidden" name="user_id" id="user_id">

                        <div class="mb-4">
                            <label for="edit_name" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="name" id="edit_name" class="mt-1 block w-full" required>
                        </div>

                        <div class="mb-4">
                            <label for="edit_email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input type="email" name="email" id="edit_email" class="mt-1 block w-full" required>
                        </div>

                        <div class="mb-4">
                            <label for="edit_phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input type="text" name="phone" id="edit_phone" class="mt-1 block w-full">
                        </div>

                        <div class="mb-4">
                            <label for="edit_role" class="block text-sm font-medium text-gray-700">Rol</label>
                            <select name="role" id="edit_role" class="mt-1 block w-full" required>
                                <option value="alumno">Alumno</option>
                                <option value="profesor">Profesor</option>
                            </select>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded">Actualizar Usuario</button>
                        </div>
                    </form>
                    <button id="closeModal" class="mt-4 text-red-500">Cerrar</button>
                </div>
            </div>

            <div class="bg-white rounded-xl p-8 shadow-lg">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Lista de Usuarios</h2>

                <!-- Tabla con DataTables -->
                <table id="usersTable" class="text-sm dataTable min-w-full divide-y divide-gray-200 no-footer">
                    <thead class="bg-gray-50 text-sm">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-2">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-2">Correo Electrónico</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-2">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-2">Creado el</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-4 py-2">Vence en</th>
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
                        <input type="text" name="name" id="name" class="rounded-md mt-1 block w-full" value="{{ old('name') }}">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="rounded-md mt-1 block w-full" value="{{ old('email') }}">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                        <input type="password" name="password" id="password" class="rounded-md mt-1 block w-full">
                        <button type="button" onclick="generatePassword()" class="mt-2 text-sm text-blue-500">Generar Contraseña Segura</button>
                    </div>
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" name="phone" id="phone" class="rounded-md mt-1 block w-full">
                    </div>
                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                        <select name="role" id="role" class="rounded-md mt-1 block w-full">
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
                    $(cell).addClass('px-4 py-2 text-sm');
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
                { data: 'created_at_formatted', name: 'created_at_formatted', orderable: false, serchable: false },
                { data: 'remaining_days', name: 'remaining_days', orderable: false, serchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
        });

        $(window).on('resize', function() {
            usersTable.columns.adjust().draw();
        });

        $(document).ready(function() {
            if ('{{ session('active_tab') }}' === 'create') {
                document.getElementById('tab-create').click();
            } else {
                document.getElementById('tab-users').click();
            }
        });

        // Controlador del modal de edicion
        //
        $(document).on('click', '.user-edit-btn', function() {
            var user_id = $(this).data('id');

            $.ajax({
                url: '/admin/users/' + user_id + '/edit',
                type: 'GET',
                success: function(data) {
                    $('#user_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_email').val(data.email);
                    $('#edit_phone').val(data.phone);
                    $('#edit_role').val(data.role);

                    $('#editUserForm').attr('action', '{{ route("admin.users.update", ":id") }}'.replace(':id', user_id));

                    $('#editUserModal').removeClass('hidden');
                }
            })
        })

        // Cerrar el modal
        $('#closeModal').on('click', function() {
            $('#editUserModal').addClass('hidden');
        });

    </script>
</x-admin-layout>
