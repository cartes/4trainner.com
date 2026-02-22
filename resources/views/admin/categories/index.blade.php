<x-admin-layout>
    @if ($errors->any())
        <div class="alert alert-danger mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="container block w-full">
        <h1 class="text-2xl font-bold mb-6">Gestión de Categorías</h1>

        <!-- Pestañas -->
        <div class="flex space-x-4 border-b mb-6">
            <a href="javascript:void(0)"
                class="tab-link text-md py-2 px-4 text-gray-600 hover:text-gray-800 border-b-2 border-transparent active border-blue-500"
                id="tab-list">Lista de Categorías</a>
            <a href="javascript:void(0)"
                class="tab-link text-md py-2 px-4 text-gray-600 hover:text-gray-800 border-b-2 border-transparent"
                id="tab-create">Crear Categoría</a>
        </div>

        <!-- Lista -->
        <div id="tab-content-list" class="tab-content">
            <div class="bg-white rounded-xl p-8 shadow-lg">
                <table id="categoriesTable" class="text-sm dataTable min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orden</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>

        <!-- Crear -->
        <div id="tab-content-create" class="tab-content hidden">
            <div class="bg-white rounded-xl p-8 shadow-lg">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Nueva Categoría</h2>
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" name="name" class="rounded-md mt-1 block w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Slug (Opcional, se genera auto)</label>
                        <input type="text" name="slug" class="rounded-md mt-1 block w-full">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Orden</label>
                        <input type="number" name="order" class="rounded-md mt-1 block w-full" value="0">
                    </div>
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Guardar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div id="editCategoryModal" class="fixed inset-0 z-50 hidden bg-gray-800 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-4">Editar Categoría</h2>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" name="name" id="edit_name" class="rounded-md mt-1 block w-full" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Slug</label>
                    <input type="text" name="slug" id="edit_slug" class="rounded-md mt-1 block w-full" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Orden</label>
                    <input type="number" name="order" id="edit_order" class="rounded-md mt-1 block w-full">
                </div>
                <div class="mt-6 flex justify-between">
                    <button type="button" id="closeEditModal" class="text-red-500">Cancelar</button>
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // DataTables
            var table = $('#categoriesTable').DataTable({
                ajax: '{{ route('admin.categories.index') }}',
                columns: [
                    { data: 'name' },
                    { data: 'slug' },
                    { data: 'order' },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <button class="btn-edit bg-blue-500 text-white px-3 py-1 rounded text-xs mr-2" data-id="${row.id}" data-name="${row.name}" data-slug="${row.slug}" data-order="${row.order}">Editar</button>
                                <button class="btn-delete bg-red-500 text-white px-3 py-1 rounded text-xs" data-id="${row.id}">Eliminar</button>
                            `;
                        }
                    }
                ]
            });

            // Tabs
            $('#tab-list').click(function() {
                $('.tab-content').addClass('hidden');
                $('#tab-content-list').removeClass('hidden');
                $('.tab-link').removeClass('active border-blue-500').addClass('border-transparent');
                $(this).addClass('active border-blue-500').removeClass('border-transparent');
            });
            $('#tab-create').click(function() {
                $('.tab-content').addClass('hidden');
                $('#tab-content-create').removeClass('hidden');
                $('.tab-link').removeClass('active border-blue-500').addClass('border-transparent');
                $(this).addClass('active border-blue-500').removeClass('border-transparent');
            });

            // Edit
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $('#edit_name').val($(this).data('name'));
                $('#edit_slug').val($(this).data('slug'));
                $('#edit_order').val($(this).data('order'));
                $('#editCategoryForm').attr('action', '/admin/categories/' + id);
                $('#editCategoryModal').removeClass('hidden');
            });

            $('#closeEditModal').click(function() {
                $('#editCategoryModal').addClass('hidden');
            });

            // Delete
            $(document).on('click', '.btn-delete', function() {
                if(confirm('¿Seguro que deseas eliminar esta categoría?')) {
                    var id = $(this).data('id');
                    $.ajax({
                        url: '/admin/categories/' + id,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            alert('Categoría eliminada');
                            table.ajax.reload();
                        },
                        error: function() {
                            alert('Error al eliminar');
                        }
                    });
                }
            });
        });
    </script>
</x-admin-layout>
