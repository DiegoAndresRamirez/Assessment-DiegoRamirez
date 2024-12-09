<template>
    <div class="dashboard-wrapper">
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
  
        <!-- Info Section (información general) -->
        <section class="info-section">
          <h2 class="info-title">Agenda tus citas médicas</h2>
          <p class="info-text">Selecciona el doctor, el horario y agenda tu cita con facilidad.</p>
          <button @click="showCreateModal = true" class="create-appointment-button">Crear Cita</button>
        </section>
  
        <!-- Filtros de Citas -->
        <section class="filter-section">
          <h2 class="section-title">Mis Citas</h2>
          <div class="filter-container">
            <label for="statusFilter" class="filter-label">Filtrar por estado:</label>
            <select v-model="statusFilter" @change="filterAppointments" class="filter-select">
              <option value="all">Todos</option>
              <option value="pending">Pendiente</option>
              <option value="confirmed">Confirmada</option>
              <option value="cancelled">Cancelada</option>
            </select>
          </div>
        </section>
  
        <!-- Lista de Citas -->
        <section class="appointments-section">
          <div v-if="filteredAppointments.length === 0" class="no-appointments">
            No tienes citas programadas.
          </div>
          <div v-else class="appointment-list">
            <div v-for="appointment in filteredAppointments" :key="appointment.id" class="appointment-card">
              <p><strong>Doctor:</strong> {{ appointment.doctor.name }} - {{ appointment.doctor.speciality }}</p>
              <p><strong>Hora:</strong> {{ appointment.block.hour_start }} - {{ appointment.block.hour_end }}</p>
              <p><strong>Motivo:</strong> {{ appointment.reason }}</p>
              <p><strong>Estado:</strong>
                <span
                  :class="{
                    'status-pending': appointment.status === 'pending',
                    'status-confirmed': appointment.status === 'confirmed',
                    'status-cancelled': appointment.status === 'cancelled'
                  }"
                >
                  {{ appointment.status }}
                </span>
              </p>
            </div>
          </div>
        </section>
  
        <!-- Modal para Crear Cita -->
        <modal v-if="showCreateModal" @close="showCreateModal = false">
          <h3 class="modal-title">Crear Cita</h3>
          <form @submit.prevent="createAppointment">
            <div class="modal-field-group">
              <label for="doctor" class="modal-label">Selecciona el Doctor</label>
              <select v-model="doctorId" @change="loadBlocks" class="input-field">
                <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                  {{ doctor.name }} - {{ doctor.speciality }}
                </option>
              </select>
            </div>
  
            <div v-if="blocks.length > 0" class="modal-field-group">
              <label for="block" class="modal-label">Selecciona el Horario</label>
              <select v-model="blockId" class="input-field">
                <option v-for="block in blocks" :key="block.id" :value="block.id">
                  {{ block.hour_start }} - {{ block.hour_end }} | {{ block.day }}
                </option>
              </select>
            </div>
            <div v-else class="no-blocks-info">
              No hay horarios disponibles para este doctor.
            </div>
  
            <div class="modal-field-group">
              <label for="reason" class="modal-label">Motivo de la cita</label>
              <input v-model="reason" type="text" placeholder="Motivo" class="input-field" required>
            </div>
  
            <div class="modal-field-group">
              <label for="speciality" class="modal-label">Especialidad</label>
              <input v-model="speciality" type="text" placeholder="Especialidad" class="input-field" required>
            </div>
  
            <button type="submit" class="submit-button">Agendar Cita</button>
          </form>
        </modal>
      </main>
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
  
  // Cargar doctores y citas al montar
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
  
  // Este método está vacío, pero se mantiene la funcionalidad de filtro por si se quiere agregar lógica adicional
  const filterAppointments = () => {};
  </script>
  
  <style scoped>
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }
  
  .dashboard-wrapper {
    display: flex;
    font-family: sans-serif;
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
    font-size: 1.4rem;
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
    margin-bottom: 1rem;
  }
  
  .create-appointment-button {
    background: #4285f4;
    color: #fff;
    border: none;
    padding: 0.7rem 1.2rem;
    cursor: pointer;
    border-radius: 0.25rem;
    font-size: 1rem;
  }
  
  /* Filter Section */
  .filter-section {
    background: #fff;
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
    border: 1px solid #ddd;
  }
  
  .section-title {
    font-size: 1.5rem;
    margin-bottom: 1rem;
  }
  
  .filter-container {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .filter-label {
    font-weight: bold;
  }
  
  .filter-select {
    padding: 0.5rem;
    border-radius: 0.25rem;
    border: 1px solid #ccc;
  }
  
  /* Appointments Section */
  .appointments-section {
    background: #fff;
    padding: 1.5rem;
    border-radius: 0.5rem;
    border: 1px solid #ddd;
    margin-bottom: 2rem;
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
  
  /* Modal */
  .modal-title {
    font-size: 1.2rem;
    margin-bottom: 1rem;
  }
  
  .modal-field-group {
    margin-bottom: 1rem;
  }
  
  .modal-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
  }
  
  .input-field {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 0.25rem;
  }
  
  .no-blocks-info {
    color: #666;
    font-style: italic;
    margin-bottom: 1rem;
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
  