<template>
    <div class="container mx-auto">

        <nav class="bg-blue-600 text-white p-4 flex justify-between items-center">
            <div class="text-xl font-semibold">
                {{ props.auth.user.name }} - Gestion de Citas
            </div>
            <button @click="logout" class="bg-red-600 text-white px-4 py-2 rounded">
                Logout
            </button>
        </nav>

        <h1 class="text-3xl font-bold mb-6">{{ title }}</h1>
        <p class="mb-6">{{ description }}</p>
        <!-- Hero Section -->
        <section class="hero bg-blue-600 text-white py-12 text-center">
            <h1 class="text-4xl font-bold">Agenda tu cita médica</h1>
            <p class="mt-4">Escoge tu doctor y agenda una cita con facilidad.</p>
            <button @click="showCreateModal = true" class="bg-white text-blue-600 px-6 py-2 mt-6 rounded-full">Crear
                Cita</button>
        </section>

        <!-- Mostrar las citas -->
        <section class="my-12">
            <h2 class="text-2xl font-semibold mb-4">Mis Citas</h2>

            <!-- Filtro por estado -->
            <div class="mb-4">
                <label for="statusFilter" class="mr-2">Filtrar por estado:</label>
                <select v-model="statusFilter" @change="filterAppointments" class="p-2 border rounded">
                    <option value="all">Todos</option>
                    <option value="pending">Pendiente</option>
                    <option value="confirmed">Confirmada</option>
                    <option value="cancelled">Cancelada</option>
                </select>
            </div>

            <!-- Lista de citas -->
            <div v-if="filteredAppointments.length === 0" class="text-gray-500">
                No tienes citas programadas.
            </div>
            <div v-else>
                <div v-for="appointment in filteredAppointments" :key="appointment.id"
                    class="mb-4 p-4 bg-gray-100 rounded shadow">
                    <p><strong>Doctor:</strong> {{ appointment.doctor.name }} - {{ appointment.doctor.speciality }}</p>
                    <p><strong>Hora:</strong> {{ appointment.block.hour_start }} - {{ appointment.block.hour_end }}</p>
                    <p><strong>Motivo:</strong> {{ appointment.reason }}</p>
                    <p><strong>Estado:</strong>
                        <span
                            :class="appointment.status === 'pending' ? 'text-yellow-500' : appointment.status === 'confirmed' ? 'text-green-500' : 'text-red-500'">
                            {{ appointment.status }}
                        </span>
                    </p>
                </div>
            </div>
        </section>

        <!-- Modal para Crear Cita -->
        <modal v-if="showCreateModal" @close="showCreateModal = false">
            <h3 class="text-lg font-bold mb-4">Crear Cita</h3>
            <form @submit.prevent="createAppointment">
                <!-- Selección del doctor -->
                <div class="mb-4">
                    <label for="doctor" class="block">Selecciona el Doctor</label>
                    <select v-model="doctorId" @change="loadBlocks" class="w-full p-2 border rounded">
                        <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                            {{ doctor.name }} - {{ doctor.speciality }}
                        </option>
                    </select>
                </div>

                <!-- Selección del bloque (solo si hay bloques disponibles) -->
                <div v-if="blocks.length > 0" class="mb-4">
                    <label for="block" class="block">Selecciona el Horario</label>
                    <select v-model="blockId" class="w-full p-2 border rounded">
                        <option v-for="block in blocks" :key="block.id" :value="block.id">
                            {{ block.hour_start }} - {{ block.hour_end }} | {{ block.day }}
                        </option>
                    </select>
                </div>
                <div v-else class="text-gray-500 mb-4">No hay horarios disponibles para este doctor.</div>

                <!-- Motivo y especialidad -->
                <div class="mb-4">
                    <label for="reason" class="block">Motivo de la cita</label>
                    <input v-model="reason" type="text" placeholder="Motivo" class="w-full p-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label for="speciality" class="block">Especialidad</label>
                    <input v-model="speciality" type="text" placeholder="Especialidad" class="w-full p-2 border rounded"
                        required>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Agendar Cita</button>
            </form>
        </modal>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Modal from '@/Components/ModalDoctor.vue';

const showCreateModal = ref(false);
const doctors = ref([]);
const blocks = ref([]);
const appointments = ref([]);
const statusFilter = ref('all');
const doctorId = ref(null);
const blockId = ref(null);
const reason = ref('');
const speciality = ref('');

const props = defineProps({
    auth: Object,
    title: String,
    description: String,
});

// Cargar los doctores desde la API
const loadDoctors = async () => {
    try {
        const response = await axios.get('/doctors');
        doctors.value = response.data;
    } catch (error) {
        console.error('Error al cargar los doctores:', error);
    }
};

// Cargar los bloques de un doctor seleccionado
const loadBlocks = async () => {
    if (doctorId.value) {
        try {
            const response = await axios.get(`/appointments/blocks/${doctorId.value}`);
            blocks.value = response.data;
        } catch (error) {
            console.error('Error al cargar los bloques:', error);
        }
    } else {
        blocks.value = [];
    }
};

// Filtrar las citas por estado
const filterAppointments = () => {
    // Esto es solo necesario si se quiere controlar el filtrado manualmente, pero con una propiedad computada puede ser más simple.
};

// Propiedad computada para las citas filtradas
const filteredAppointments = computed(() => {
    if (statusFilter.value === 'all') {
        return appointments.value;
    }
    return appointments.value.filter(appointment => appointment.status === statusFilter.value);
});

// Cargar las citas del paciente
const loadAppointments = async () => {
    try {
        const response = await axios.get('/appointments');
        appointments.value = response.data;
    } catch (error) {
        console.error('Error al cargar las citas:', error);
    }
};

// Crear la cita
const createAppointment = async () => {
    try {
        await axios.post('/appointments', {
            doctor_id: doctorId.value,
            block_id: blockId.value,
            reason: reason.value,
            speciality: speciality.value,
        });

        // Limpiar el formulario y cerrar el modal
        reason.value = '';
        speciality.value = '';
        doctorId.value = null;
        blockId.value = null;
        blocks.value = [];

        showCreateModal.value = false;
        loadAppointments(); // Recargar citas después de crear una
    } catch (error) {
        console.error('Error al crear la cita:', error);
    }
};

// Cargar los doctores y citas cuando se monta el componente
onMounted(() => {
    loadDoctors();
    loadAppointments();
});

const logout = async () => {
    try {
        await axios.post('/logout');
        window.location.href = '/login';
    } catch (error) {
        console.error('Error al hacer logout:', error);
    }
};
</script>

<style scoped>
.container {
    padding: 20px;
}

.hero {
    background-color: #1E40AF;
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.hero button {
    background-color: #3B82F6;
    color: white;
    padding: 1rem 2rem;
    border-radius: 9999px;
    font-size: 1rem;
}

nav {
    background-color: #1E40AF;
    color: white;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

nav button {
    background-color: #EF4444;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    cursor: pointer;
}
</style>