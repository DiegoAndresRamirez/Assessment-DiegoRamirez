<template>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <h2 class="sidebar-title">{{ props.auth.user.name }}</h2>
        <span class="sidebar-subtitle">Gestión de Citas</span>
      </div>
      <button @click="logout" class="sidebar-logout">Cerrar Sesión</button>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
      <!-- Header Section -->
      <header class="main-header">
        <h1 class="main-title">{{ title }}</h1>
        <p class="main-description">{{ description }}</p>
      </header>

      <!-- Info Section -->
      <section class="info-section">
        <h2 class="info-title">Panel de Control</h2>
        <p class="info-text">Gestiona tu horario y citas con pacientes de manera eficiente.</p>
      </section>

      <!-- Schedule Section -->
      <section class="schedule-section">
        <h2 class="section-title">Mi Horario</h2>
        <div v-if="currentSchedule" class="schedule-container">
          <p><strong>Temporada:</strong> {{ currentSchedule.season }}</p>
          <button @click="showBlockModal = true" class="add-block-button">Agregar Bloque</button>
          <div class="blocks-grid">
            <div
              v-for="block in currentSchedule.blocks"
              :key="block.id"
              class="block-card"
            >
              <p><strong>Inicio:</strong> {{ block.hour_start }}</p>
              <p><strong>Fin:</strong> {{ block.hour_end }}</p>
              <p><strong>Día:</strong> {{ block.day }}</p>
              <p><strong>Disponibilidad:</strong> {{ block.disponibility }}</p>
            </div>
          </div>
        </div>
        <div v-else class="no-schedule">
          <button @click="showScheduleModal = true" class="create-schedule-button">Crear Horario</button>
        </div>
      </section>

      <!-- Appointments Section -->
      <section class="appointments-section">
        <h2 class="section-title">Citas Programadas</h2>
        <div v-if="appointments.length === 0" class="no-appointments">
          No tienes citas programadas.
        </div>
        <div v-else class="appointment-list">
          <div
            v-for="appointment in appointments"
            :key="appointment.id"
            class="appointment-card"
          >
            <p><strong>Paciente:</strong> {{ appointment.patient.name }} - {{ appointment.patient.speciality }}</p>
            <p><strong>Hora:</strong> {{ appointment.block.hour_start }} - {{ appointment.block.hour_end }}</p>
            <p><strong>Motivo:</strong> {{ appointment.reason }}</p>
            <p><strong>Estado:</strong>
              <span :class="getStatusClass(appointment.status)">
                {{ appointment.status }}
              </span>
            </p>
            <div v-if="appointment.status === 'pending'" class="action-buttons">
              <button @click="changeStatus(appointment.id, 'confirmed')" class="confirm-button">Confirmar</button>
              <button @click="changeStatus(appointment.id, 'cancelled')" class="cancel-button">Cancelar</button>
            </div>
          </div>
        </div>
      </section>

      <!-- Modals -->
      <modal v-if="showScheduleModal" @close="showScheduleModal = false">
        <h3 class="modal-title">Crear Horario</h3>
        <form @submit.prevent="createSchedule">
          <input v-model="season" type="text" placeholder="Temporada" class="input-field">
          <button type="submit" class="submit-button">Guardar</button>
        </form>
      </modal>

      <modal v-if="showBlockModal" @close="showBlockModal = false">
        <h3 class="modal-title">Agregar Bloque</h3>
        <form @submit.prevent="createBlock">
          <input v-model="hourStart" type="time" class="input-field">
          <input v-model="hourEnd" type="time" class="input-field">
          <input v-model="day" type="date" class="input-field">
          <select v-model="disponibility" class="input-field">
            <option value="available">Disponible</option>
            <option value="unavailable">No Disponible</option>
          </select>
          <button button type="submit" class="submit-button">Guardar</button>
        </form>
      </modal>
    </main>
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

    currentSchedule.value = response.data.schedule;
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

const getStatusClass = (status) => {
  return {
    'status-pending': status === 'pending',
    'status-confirmed': status === 'confirmed',
    'status-cancelled': status === 'cancelled'
  };
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
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.dashboard-container {
  display: flex;
  font-family: 'Arial', sans-serif;
  height: 100vh;
  color: #333;
}

/* Sidebar */
.sidebar {
  width: 250px;
  background-color: #2e3b4e;
  color: #fff;
  display: flex;
  flex-direction: column;
  padding: 1.5rem;
  justify-content: space-between;
}

.sidebar-header {
  margin-bottom: 2rem;
}

.sidebar-title {
  font-size: 1.5rem;
  margin-bottom: 0.25rem;
}

.sidebar-subtitle {
  font-size: 0.9rem;
  color: #ccc;
}

.sidebar-logout {
  background: #bf3f3f;
  border: none;
  padding: 0.7rem 1rem;
  width: 100%;
  text-align: center;
  border-radius: 0.25rem;
  color: #fff;
  cursor: pointer;
}

/* Main Content */
.main-content {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
  background: #f5f5f5;
}

.main-header {
  margin-bottom: 1.5rem;
}

.main-title {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.main-description {
  color: #666;
  margin-bottom: 1rem;
}

.info-section {
  background: #fff;
  padding: 1.5rem;
  border-radius: 0.5rem;
  margin-bottom: 2rem;
  border: 1px solid #ddd;
}

.info-title {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

 .info-text {
  font-size: 1rem;
  color: #444;
}

/* Schedule Section */
.schedule-section {
  margin-bottom: 2rem;
  background: #fff;
  padding: 1.5rem;
  border-radius: 0.5rem;
  border: 1px solid #ddd;
}

.section-title {
  font-size: 1.5rem;
  margin-bottom: 1rem;
}

.schedule-container {
  margin-top: 1rem;
}

.add-block-button {
  background: #4285f4;
  color: #fff;
  border: none;
  padding: 0.5rem 1rem;
  margin-top: 1rem;
  cursor: pointer;
  border-radius: 0.25rem;
}

.blocks-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.block-card {
  background: #fafafa;
  border: 1px solid #ccc;
  padding: 1rem;
  border-radius: 0.25rem;
}

.no-schedule {
  text-align: center;
}

.create-schedule-button {
  background: #4285f4;
  color: #fff;
  border: none;
  padding: 0.5rem 1rem;
  cursor: pointer;
  border-radius: 0.25rem;
}

/* Appointments Section */
.appointments-section {
  background: #fff;
  padding: 1.5rem;
  border-radius: 0.5rem;
  border: 1px solid #ddd;
}

.no-appointments {
  color: #999;
  font-style: italic;
}

.appointment-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1rem;
}

.appointment-card {
  background: #fafafa;
  border: 1px solid #ddd;
  padding: 1rem;
  border-radius: 0.25rem;
}

.status-pending {
  color: #c90;
}
.status-confirmed {
  color: #5cb85c;
}
.status-cancelled {
  color: #d9534f;
}

.action-buttons {
  margin-top: 1rem;
}

.confirm-button {
  background: #5cb85c;
  color: #fff;
  border: none;
  padding: 0.5rem 1rem;
  margin-right: 0.5rem;
  cursor: pointer;
  border-radius: 0.25rem;
}

.cancel-button {
  background: #d9534f;
  color: #fff;
  border: none;
  padding: 0.5rem 1rem;
  cursor: pointer;
  border-radius: 0.25rem;
}

/* Modal */
.modal-title {
  font-size: 1.2rem;
  margin-bottom: 1rem;
}

.input-field {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  margin-bottom: 1rem;
  border-radius: 0.25rem;
}

.submit-button {
  background: #5cb85c;
  color: #fff;
  border: none;
  padding: 0.5rem 1rem;
  cursor: pointer;
  border-radius: 0.25rem;
}
</style>