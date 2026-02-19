<template>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div v-if="loading" class="text-center py-8 text-gray-500">
                Cargando información del alumno...
            </div>

            <div v-else-if="student">
                <!-- Header / Profile -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200 flex flex-col md:flex-row items-center gap-6">
                        <img
                            :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(student.name) + '&background=random'"
                            alt="Profile"
                            class="w-24 h-24 rounded-full object-cover shadow-md"
                        >
                        <div class="text-center md:text-left flex-1">
                            <h2 class="text-3xl font-bold text-gray-800">{{ student.name }}</h2>
                            <p class="text-gray-500">{{ student.email }}</p>
                            <p class="mt-2 text-sm text-gray-600" v-if="getMeta(student, 'phone')">
                                <i class="fab fa-whatsapp text-green-500 mr-1"></i> {{ getMeta(student, 'phone') }}
                            </p>
                        </div>
                        <div class="flex gap-2 mt-4 md:mt-0">
                            <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow transition">
                                Asignar Rutina
                            </button>
                            <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition">
                                Evaluar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Metrics Column -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4 border-b border-gray-200 bg-gray-50 font-semibold">
                                Métricas Corporales
                            </div>
                            <div class="p-4 space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Peso Actual</span>
                                    <span class="font-bold text-lg">{{ getMetricValue(getLatestMetric('weight')) || '--' }} kg</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Altura</span>
                                    <span class="font-bold text-lg">{{ getMetricValue(getLatestMetric('height')) || '--' }} cm</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">% Grasa</span>
                                    <span class="font-bold text-lg">{{ getMetricValue(getLatestMetric('body_fat')) || '--' }} %</span>
                                </div>
                            </div>
                        </div>

                         <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4 border-b border-gray-200 bg-gray-50 font-semibold">
                                Notas Internas
                            </div>
                            <div class="p-4">
                                <textarea
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    rows="4"
                                    placeholder="Añadir nota..."
                                ></textarea>
                                <button class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                    Guardar Nota
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4 border-b border-gray-200 bg-gray-50 font-semibold">
                                Avance / Progreso
                            </div>
                            <div class="p-6 space-y-6">
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-blue-700">Asistencia a Clases (Mes Actual)</span>
                                        <span class="text-sm font-medium text-blue-700">80%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: 80%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-green-700">Rutinas Completadas</span>
                                        <span class="text-sm font-medium text-green-700">60%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-green-600 h-2.5 rounded-full" style="width: 60%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4 border-b border-gray-200 bg-gray-50 font-semibold">
                                Historial Reciente
                            </div>
                            <div class="p-0">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actividad</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalle</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="record in recentHistory" :key="record.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ new Date(record.date).toLocaleDateString() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ formatType(record.type) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ formatValue(record) }}
                                            </td>
                                        </tr>
                                        <tr v-if="recentHistory.length === 0">
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No hay actividad reciente.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-8 text-red-500">
                Error al cargar el alumno.
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, defineProps } from 'vue';
import axios from 'axios';

const props = defineProps({
    studentId: {
        type: [String, Number],
        required: true
    }
});

const student = ref(null);
const loading = ref(true);

const fetchStudent = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/v1/teacher/students/${props.studentId}`);
        student.value = response.data;
    } catch (error) {
        console.error('Error fetching student:', error);
    } finally {
        loading.value = false;
    }
};

const getMeta = (student, key) => {
    if (!student.meta || !Array.isArray(student.meta)) return null;
    const metaItem = student.meta.find(m => m.meta_key === key);
    return metaItem ? metaItem.meta_value : null;
};

const getLatestMetric = (type) => {
    if (!student.value || !student.value.progress) return null;
    const metrics = student.value.progress.filter(p => p.type === type);
    if (metrics.length === 0) return null;
    metrics.sort((a, b) => new Date(b.date) - new Date(a.date));
    return metrics[0].value;
};

const getMetricValue = (value) => {
    if (value === null || value === undefined) return null;
    if (typeof value === 'object') return value.value || JSON.stringify(value);
    return value;
}

const recentHistory = computed(() => {
    if (!student.value || !student.value.progress) return [];
    return [...student.value.progress].sort((a, b) => new Date(b.date) - new Date(a.date)).slice(0, 5);
});

const formatType = (type) => {
    const types = {
        'attendance': 'Asistencia',
        'routine_completion': 'Rutina',
        'weight': 'Peso',
        'body_fat': '% Grasa',
        'height': 'Altura'
    };
    return types[type] || type;
};

const formatValue = (record) => {
    if (typeof record.value === 'object' && record.value !== null) {
        if (record.value.present) return 'Presente';
        if (record.value.value) return record.value.value; // generic wrapper
        return JSON.stringify(record.value);
    }
    return record.value;
};

onMounted(() => {
    fetchStudent();
});
</script>
