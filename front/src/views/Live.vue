<template>
  <div class="lives-container">
    <div class="header">
      <h1>🎥 Lives Programados</h1>
      <p>Únete a nuestras transmisiones en vivo de cocina</p>
    </div>

    <!-- Añadido el buscador aquí -->
    <div class="search-container">
      <div class="search-box">
        <input
          type="text"
          v-model="searchQuery"
          @input="handleSearch"
          placeholder="Buscar lives por título o chef..."
          class="search-input"
        />
        <span class="search-icon">🔍</span>
      </div>
    </div>

    <div v-if="loading" class="loading-spinner">
      <div class="spinner"></div>
      <p>Cargando lives...</p>
    </div>

    <div v-else-if="error" class="error-message">
      <p>⚠️ {{ error }}</p>
      <button @click="fetchLives" class="retry-btn">Reintentar</button>
    </div>

    <div v-else-if="filteredLives.length === 0 && searchQuery" class="no-results">
      <p>No se encontraron resultados para "{{ searchQuery }}"</p>
    </div>

    <div v-else-if="displayedLives.length === 0" class="no-lives">
      <p>No hay lives programados actualmente.</p>
    </div>

    <div v-else class="lives-grid">
      <div v-for="live in displayedLives" :key="live.id" class="live-card">
        <div class="live-header">
          <img :src="live.chef.img || defaultProfile" alt="Chef" class="chef-avatar">
          <div class="chef-info">
            <h3>{{ live.chef.name }}</h3>
            <p class="chef-role">Chef profesional</p>
          </div>
        </div>

        <div class="live-content">
          <h2>{{ live.recipe.title }}</h2>
          <p class="recipe-description">{{ live.recipe.description }}</p>

          <div class="live-datetime">
            <div class="date">
              <span class="icon">📅</span>
              {{ formatDate(live.dia) }}
            </div>
            <div class="time">
              <span class="icon">⏰</span>
              {{ live.hora }}
            </div>
          </div>
        </div>

        <div class="live-actions">
          <button class="reminder-btn" @click="setReminder(live)">
            ⏰ Recordarme
          </button>
          <button class="join-btn" @click="joinLive(live)">
            🎥 Unirse al Live
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useRouter } from 'vue-router';
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import communicationManager from '@/services/communicationManager';
import defaultProfile from '@/assets/images/profile.svg';

export default {
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();
    const lives = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const searchQuery = ref('');

    const filteredLives = computed(() => {
      if (!searchQuery.value.trim()) {
        return lives.value;
      }
      
      const query = searchQuery.value.toLowerCase();
      return lives.value.filter(live => {
        return (
          live.recipe.title.toLowerCase().includes(query) ||
          live.chef.name.toLowerCase().includes(query)
        );
      });
    });

    const displayedLives = computed(() => {
      return searchQuery.value ? filteredLives.value : lives.value;
    });

    const handleSearch = () => {
      // La lógica de búsqueda ahora se maneja en las propiedades computadas
    };

    const fetchLives = async () => {
      try {
        loading.value = true;
        error.value = null;

        const response = await communicationManager.getLives();

        // Acepta tanto response.data como response directo
        const responseData = response.data || response;

        if (!responseData || !Array.isArray(responseData)) {
          throw new Error('Formato de respuesta inesperado');
        }

        lives.value = responseData;
      } catch (err) {
        console.error('Error fetching lives:', err);
        error.value = err.message || 'Error al cargar los lives programados';
      } finally {
        loading.value = false;
      }
    };

    const setReminder = (live) => {
      const title = encodeURIComponent(`Live de cocina: ${live.recipe.title}`);
      const description = encodeURIComponent(live.recipe.description);
      const location = encodeURIComponent('Online');

      // Convertir fecha y hora a formato de Google Calendar (UTC)
      const startDate = new Date(`${live.dia}T${live.hora}`);
      const endDate = new Date(startDate.getTime() + 60 * 60 * 1000); // duración 1 hora

      const formatDate = (date) =>
        date.toISOString().replace(/-|:|\.\d\d\d/g, '');

      const start = formatDate(startDate);
      const end = formatDate(endDate);

      const calendarUrl = `https://www.google.com/calendar/render?action=TEMPLATE&text=${title}&dates=${start}/${end}&details=${description}&location=${location}&sf=true&output=xml`;

      window.open(calendarUrl, '_blank');
    };

    const joinLive = (live) => {
      router.push({
        name: 'ChatRoom', // Usa el nombre de la ruta
        params: { 
          liveId: live.id // Asegúrate que live.id existe
        }
      });
    };

    const formatDate = (dateString) => {
      try {
        if (!dateString) return 'Fecha no definida';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'Fecha inválida';

        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('es-ES', options);
      } catch (e) {
        console.error('Error formateando fecha:', e);
        return 'Fecha inválida';
      }
    };

    onMounted(() => {
      try {
        fetchLives();
      } catch (e) {
        console.error('Error en onMounted:', e);
        error.value = 'Error inicializando el componente';
        loading.value = false;
      }
    });

    return {
      lives,
      loading,
      error,
      defaultProfile,
      searchQuery,
      filteredLives,
      displayedLives,
      fetchLives,
      formatDate,
      handleSearch,
      authStore,
      joinLive,
      setReminder
    };
  }
};
</script>

<style scoped>
.lives-container {
  max-width: 1200px;
  margin:  auto;
  padding: 2rem;
}

.header {
  text-align: center;
  margin-bottom: 2rem;
}

.header h1 {
  font-size: 2.5rem;
  color: #2c3e50;
}

.header p {
  font-size: 1.2rem;
  color: #7f8c8d;
}

/* Estilos para el buscador */
.search-container {
  margin-bottom: 2rem;
}

.search-box {
  position: relative;
  max-width: 600px;
  margin: 0 auto;
}

.search-input {
  width: 100%;
  padding: 12px 20px;
  padding-left: 45px;
  border: 2px solid #e0e0e0;
  border-radius: 30px;
  font-size: 16px;
  outline: none;
  transition: all 0.3s;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.search-input:focus {
  border-color: #e74c3c;
}

.search-icon {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.2rem;
  color: #7f8c8d;
}

.no-results {
  text-align: center;
  padding: 2rem;
  background-color: #f8f9fa;
  border-radius: 8px;
  color: #e74c3c;
}

.loading-spinner {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 200px;
}

.spinner {
  border: 5px solid #f3f3f3;
  border-top: 5px solid #3498db;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}

.error-message {
  text-align: center;
  padding: 2rem;
  background-color: #ffecec;
  border-radius: 8px;
  color: #e74c3c;
}

.retry-btn {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  background-color: #e74c3c;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.no-lives {
  text-align: center;
  padding: 2rem;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.lives-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.live-card {
  background-color: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.live-card:hover {
  transform: translateY(-5px);
}

.live-header {
  display: flex;
  align-items: center;
  padding: 1.5rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #eee;
}

.chef-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  margin-right: 1rem;
}

.chef-info h3 {
  margin: 0;
  font-size: 1.2rem;
}

.chef-role {
  margin: 0;
  color: #7f8c8d;
  font-size: 0.9rem;
}

.live-content {
  padding: 1.5rem;
}

.live-content h2 {
  margin-top: 0;
  color: #2c3e50;
}

.recipe-description {
  color: #7f8c8d;
  margin-bottom: 1.5rem;
}

.live-datetime {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 1rem;
}

.live-datetime .date,
.live-datetime .time {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.live-actions {
  display: flex;
  padding: 0 1.5rem 1.5rem;
  gap: 1rem;
}

.reminder-btn,
.join-btn {
  flex: 1;
  padding: 0.75rem;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s;
}

.reminder-btn {
  background-color: #f1c40f;
  color: #34495e;
}

.join-btn {
  background-color: #e74c3c;
  color: white;
}

.reminder-btn:hover {
  background-color: #f39c12;
}

.join-btn:hover {
  background-color: #c0392b;
}
</style>