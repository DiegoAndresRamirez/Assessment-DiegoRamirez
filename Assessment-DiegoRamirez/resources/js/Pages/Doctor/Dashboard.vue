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
    <section class="hero bg-blue-600 text-white py-12 text-center">
      <h1 class="text-4xl font-bold">Gestiona tus citas médicas</h1>
      <p class="mt-4">Aquí puedes ver y gestionar las citas programadas con tus pacientes.</p>
    </section>

    <!-- Sección de Citas -->
    <section class="my-12">
      <h2 class="text-2xl font-semibold mb-4">Citas Programadas</h2>

      <!-- Lista de citas -->
      <div v-if="appointments.length === 0" class="text-gray-500">
        No tienes citas programadas.
      </div>
      <div v-else>
        <div v-for="appointment in appointments" :key="appointment.id" class="mb-4 p-4 bg-gray-100 rounded shadow">
          <p><strong>Paciente:</strong> {{ appointment.patient.name }} - {{ appointment.patient.speciality }}</p>
          <p><strong>Hora:</strong> {{ appointment.block.hour_start }} - {{ appointment.block.hour_end }}</p>
          <p><strong>Motivo:</strong> {{ appointment.reason }}</p>
          <p><strong>Estado:</strong>
            <span :class="appointment.status === 'pending' ? 'text-yellow-500' : appointment.status === 'confirmed' ? 'text-green-500' : 'text-red-500'">
              {{ appointment.status }}
            </span>
          </p>
          
          <!-- Botones de Confirmación/Cancelación -->
          <div v-if="appointment.status === 'pending'" class="mt-2">
            <button @click="changeStatus(appointment.id, 'confirmed')" class="bg-green-600 text-white px-4 py-2 rounded mr-2">Confirmar</button>
            <button @click="changeStatus(appointment.id, 'cancelled')" class="bg-red-600 text-white px-4 py-2 rounded">Cancelar</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Sección del Horario -->
    <div class="mb-6">
      <h2 class="text-xl font-semibold mb-2">Mi Horario</h2>

      <div v-if="currentSchedule">
        <!-- Si ya existe el horario, mostrarlo -->
        <div class="p-4 bg-blue-100 rounded shadow">
          <p><strong>Temporada:</strong> {{ currentSchedule.season }}</p>
          <button @click="showBlockModal = true" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
            Agregar Bloque
          </button>

          <!-- Lista de Bloques -->
          <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="block in currentSchedule.blocks" :key="block.id" class="p-4 bg-white shadow rounded">
              <p><strong>Inicio:</strong> {{ block.hour_start }}</p>
              <p><strong>Fin:</strong> {{ block.hour_end }}</p>
              <p><strong>Día:</strong> {{ block.day }}</p>
              <p><strong>Disponibilidad:</strong> {{ block.disponibility }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Si no existe el horario, mostrar botón para crearlo -->
      <div v-else>
        <button @click="showScheduleModal = true" class="bg-blue-600 text-white px-4 py-2 rounded">
          Crear Horario
        </button>
      </div>
    </div>

    <!-- Modal para Crear Schedule -->
    <modal v-if="showScheduleModal" @close="showScheduleModal = false">
      <h3 class="text-lg font-bold mb-4">Crear Horario</h3>
      <form @submit.prevent="createSchedule">
        <input v-model="season" type="text" placeholder="Temporada" class="w-full mb-4 p-2 border rounded">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
      </form>
    </modal>

    <!-- Modal para Crear Block -->
    <modal v-if="showBlockModal" @close="showBlockModal = false">
      <h3 class="text-lg font-bold mb-4">Agregar Bloque</h3>
      <form @submit.prevent="createBlock">
        <input v-model="hourStart" type="time" class="w-full mb-4 p-2 border rounded">
        <input v-model="hourEnd" type="time" class="w-full mb-4 p-2 border rounded">
        <input v-model="day" type="date" class="w-full mb-4 p-2 border rounded">
        <select v-model="disponibility" class="w-full mb-4 p-2 border rounded">
          <option value="available">Disponible</option>
          <option value="unavailable">No Disponible</option>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
      </form>
    </modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Modal from '@/Components/ModalDoctor.vue';

const props = defineProps({
  title: String,
  description: String,
  schedule: Object,
  auth: Object, 
});

const appointments = ref([]);
const currentSchedule = ref(props.schedule);
const showScheduleModal = ref(false);
const showBlockModal = ref(false);
const season = ref('');
const hourStart = ref('');
const hourEnd = ref('');
const day = ref('');
const disponibility = ref('available');

const createSchedule = () => {
  axios.post('/schedules', { season: season.value })
    .then(response => {
      currentSchedule.value = response.data.schedule;
      showScheduleModal.value = false;
      season.value = '';
    })
    .catch(error => {
      console.error('Error al crear el horario:', error);
    });
};

const createBlock = async () => {
  if (!currentSchedule.value) {
    console.error('No se encontró el horario (schedule).');
    return;
  }

  try {
    const response = await axios.post('/blocks', {
      schedule_id: currentSchedule.value.id,
      hour_start: hourStart.value,
      hour_end: hourEnd.value,
      day: day.value,
      disponibility: disponibility.value,
    });

    // Actualizar el schedule con los nuevos datos
    currentSchedule.value = response.data.schedule;

    // Limpiar el formulario y cerrar el modal
    showBlockModal.value = false;
    hourStart.value = '';
    hourEnd.value = '';
    day.value = '';
    disponibility.value = 'available';
  } catch (error) {
    console.error('Error al crear el bloque:', error);
  }
};

const loadAppointments = async () => {
  try {
    const response = await axios.get(`/appointments/doctor/${props.auth.user.id}`);
    appointments.value = response.data;
  } catch (error) {
    console.error('Error al cargar las citas:', error);
  }
};

const changeStatus = async (appointmentId, status) => {
  try {
    await axios.patch(`/appointments/${appointmentId}/status`, { status });
    loadAppointments();
  } catch (error) {
    console.error('Error al cambiar el estado de la cita:', error);
  }
};

onMounted(() => {
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