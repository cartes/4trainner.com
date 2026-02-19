<template>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Mis Alumnos</h2>
                        <div class="flex gap-2 w-full md:w-auto">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Buscar por nombre o email..."
                                class="w-full md:w-64 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                        </div>
                    </div>

                    <div v-if="loading" class="text-center py-8 text-gray-500">
                        Cargando alumnos...
                    </div>

                    <div v-else-if="filteredStudents.length === 0" class="text-center py-8 text-gray-500">
                        No se encontraron alumnos.
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="student in filteredStudents" :key="student.id" class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow duration-200 bg-white">
                            <div class="flex items-center gap-4 mb-4">
                                <img
                                    :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(student.name) + '&background=random'"
                                    alt="Profile"
                                    class="w-16 h-16 rounded-full object-cover"
                                >
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800">{{ student.name }}</h3>
                                    <p class="text-sm text-gray-500">{{ student.email }}</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm text-gray-600">
                                <p class="flex items-center gap-2">
                                    <span class="font-semibold w-16">Teléfono:</span>
                                    <span v-if="getMeta(student, 'phone')">
                                        {{ getMeta(student, 'phone') }}
                                        <a :href="'https://wa.me/' + getMeta(student, 'phone').replace(/\D/g,'')" target="_blank" class="text-green-500 hover:text-green-600 ml-1 inline-flex items-center" title="Enviar WhatsApp">
                                            (WhatsApp)
                                        </a>
                                    </span>
                                    <span v-else class="text-gray-400 italic">No registrado</span>
                                </p>
                                <p class="flex items-center gap-2">
                                    <span class="font-semibold w-16">Estado:</span>
                                    <span :class="getStatusClass(student)">
                                        {{ getStatusLabel(student) }}
                                    </span>
                                </p>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                                <a :href="'/teacher/students/' + student.id" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Ver Detalle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const students = ref([]);
const loading = ref(true);
const search = ref('');

const fetchStudents = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/v1/teacher/students');
        students.value = response.data;
    } catch (error) {
        console.error('Error fetching students:', error);
    } finally {
        loading.value = false;
    }
};

const getMeta = (student, key) => {
    if (!student.meta || !Array.isArray(student.meta)) return null;
    const metaItem = student.meta.find(m => m.meta_key === key);
    return metaItem ? metaItem.meta_value : null;
};

const getStatusLabel = (student) => {
    const status = getMeta(student, 'status');
    return status ? status : 'Activo';
};

const getStatusClass = (student) => {
    const status = getStatusLabel(student).toLowerCase();
    if (status === 'activo') return 'text-green-600 font-medium bg-green-50 px-2 py-0.5 rounded';
    if (status === 'inactivo') return 'text-red-600 font-medium bg-red-50 px-2 py-0.5 rounded';
    return 'text-gray-600 font-medium bg-gray-50 px-2 py-0.5 rounded';
};

const filteredStudents = computed(() => {
    if (!search.value) return students.value;
    const term = search.value.toLowerCase();
    return students.value.filter(s =>
        s.name.toLowerCase().includes(term) ||
        s.email.toLowerCase().includes(term)
    );
});

onMounted(() => {
    fetchStudents();
});
</script>
