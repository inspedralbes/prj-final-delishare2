<template>
  <div class="min-h-screen bg-lime-50 flex flex-col">
    <!-- Hero Section with animated background -->
    <section class="relative overflow-hidden">
      <div class="bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 py-16 relative">
        <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
        <!-- Animated circles decoration -->
        <div class="absolute inset-0 overflow-hidden">
          <div
            class="absolute -left-10 -top-10 w-40 h-40 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob">
          </div>
          <div
            class="absolute -right-10 -top-10 w-40 h-40 bg-lime-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000">
          </div>
          <div
            class="absolute -bottom-10 left-20 w-40 h-40 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000">
          </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
          <div class="text-center">

            <h1 class="text-4xl tracking-tight font-extrabold text-lime-900 sm:text-5xl md:text-6xl">
              <span
                class="block bg-gradient-to-r from-lime-900 via-lime-700 to-green-800 bg-clip-text text-transparent">
                Lives en Directe
              </span>
              <span class="block text-2xl mt-3 text-lime-700 font-medium">
                Aprèn i cuina en temps real amb xefs experts
              </span>
            </h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Search Section with floating elements -->
    <div class="w-full px-6 -mt-8 relative z-20 flex justify-center">
      <div
        class="w-full sm:w-5/6 md:w-4/5 lg:w-3/4 xl:w-2/3 2xl:w-1/2 transform hover:scale-105 transition-transform duration-300">
        <div class="relative">
          <input type="text" v-model="searchQuery" @input="handleSearch" placeholder="Cerca lives per títol o xef..."
            class="w-full px-8 py-5 text-lg text-lime-900 border-2 border-lime-300 rounded-full focus:outline-none focus:ring-4 focus:ring-lime-300/50 focus:border-lime-400 bg-white/80 backdrop-blur-sm shadow-lg" />
          <div class="absolute inset-y-0 right-0 pr-8 flex items-center pointer-events-none">
            <svg class="w-6 h-6 text-lime-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>
      </div>
    </div>


    <!-- Loading State with animated chef hat -->
    <div v-if="loading" class="max-w-7xl mx-auto px-6 py-12">
      <div class="flex flex-col items-center justify-center">
        <div class="relative">
          <div class="w-16 h-16 border-4 border-lime-300 border-dashed rounded-full animate-spin"></div>
          <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-3xl">👨‍🍳</span>
        </div>
        <p class="mt-4 text-lime-700 font-medium animate-pulse">Preparant els lives...</p>
      </div>
    </div>

    <!-- Error State with shake animation -->
    <div v-else-if="error" class="max-w-7xl mx-auto px-6 py-12">
      <div
        class="bg-red-50 rounded-xl p-8 text-center border border-red-200 shadow-lg hover:shadow-xl transition-all duration-300 animate-shake">
        <div class="text-4xl mb-4">😕</div>
        <p class="text-red-600 mb-4 font-medium">⚠️ {{ error }}</p>
        <button @click="fetchLives"
          class="bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 px-8 py-3 rounded-full hover:from-green-600 hover:via-lime-500 hover:to-lime-400 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 font-medium">
          Tornar a intentar
        </button>
      </div>
    </div>

    <!-- No Results State -->
    <div v-else-if="filteredLives.length === 0 && searchQuery" class="max-w-7xl mx-auto px-6 py-12">
      <div class="text-center">
        <div class="text-6xl mb-4">🔍</div>
        <p class="text-lime-700 text-xl">No hem trobat resultats per "<span class="font-semibold">{{ searchQuery
            }}</span>"</p>
      </div>
    </div>

    <!-- No Lives State -->
    <div v-else-if="displayedLives.length === 0" class="max-w-7xl mx-auto px-6 py-12">
      <div class="text-center">
        <div class="text-6xl mb-4">📅</div>
        <p class="text-lime-700 text-xl">No hi ha lives programats actualment</p>
      </div>
    </div>

    <!-- Lives Grid with hover effects -->
    <div v-else class="max-w-7xl mx-auto px-6 py-12 pb-32 md:pb-24">
      <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
        <div v-for="live in displayedLives" :key="live.id"
          class="group bg-white rounded-2xl shadow-lg overflow-hidden transform transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl">
          <!-- Chef Info with hover effect -->
          <div
            class="p-6 bg-gradient-to-r from-lime-50 to-green-50 group-hover:from-lime-100 group-hover:to-green-100 transition-colors duration-300">
            <div class="flex items-center space-x-4">
              <div class="relative">
                <img :src="live.chef.img || defaultProfile" alt="Chef"
                  class="h-16 w-16 rounded-full object-cover border-2 border-white shadow-md group-hover:scale-110 transition-transform duration-300">
                <div class="absolute -bottom-1 -right-1 bg-lime-400 rounded-full p-1.5 border-2 border-white">
                  <svg class="w-5 h-5 text-lime-800" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M89.5817 120C58.6934 91.5024 74.0344 44.757 110.302 41.133C127.029 39.4629 149.113 54.0714 159.999 54.0714C194.568 54.0714 228.221 27.2561 260.493 54.0714C279.192 69.6052 269.699 99.5044 257.006 107.036" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M90 117C142.667 111.566 197.696 95.755 250 112.449" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M93.0559 139C92.8995 145.073 93.028 151.088 94 157" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M97 155.173C145.65 132.936 205.18 134.027 254 156" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M253 131C253.464 138.692 254 146.28 254 154" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M93.7931 186.199C85.6744 187.218 87.5015 211.695 103.941 211.695C103.941 305 263.001 295 247.277 202.221C256 202.221 265.677 181.04 253.5 179" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M160 206C160.457 203.005 160.774 199.981 161 197" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M196 206C196 203.545 196 200.005 196 197" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M186 245C179.911 247.785 172.5 249.957 168 245.225" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M38 360C47.0818 317.424 79.2813 287.286 125.151 287.286C125.88 287.286 154.503 315.535 180.414 313.586C203.33 311.863 216.097 294.682 235.003 287.286C263.137 276.278 307 338.995 307 358.456" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M350 159C338.607 183.953 286.988 272.605 292.396 306" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M353.983 156C361.109 192.062 371.983 282.642 333 308" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M328 304C318.437 300.114 309.042 295.03 299 293" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M294.975 308C285.934 328.2 276.314 343.303 299 320.79" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M311 320C308.736 331.403 297.744 346.679 290 331.889" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M211 336.863C210.815 349.971 203 348.348 203 336" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M146 351.449C135.34 353.573 138.871 349.231 143.412 344" stroke="currentColor" stroke-opacity="0.9" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </div>
              </div>
              <div>
                <h3 class="text-lg font-bold text-lime-900">{{ live.chef.name }}</h3>
                <p class="text-sm text-lime-600 flex items-center">
                   Xef professional
                </p>
              </div>
            </div>
          </div>

          <!-- Recipe Info with interactive elements -->
          <div class="p-6 space-y-6">
            <div>
              <h2 class="text-xl font-bold text-lime-900 group-hover:text-lime-700 transition-colors duration-300">
                {{ live.recipe.title }}
              </h2>
              <p class="text-lime-600 mt-2 line-clamp-2 group-hover:line-clamp-none transition-all duration-300">
                {{ live.recipe.description }}
              </p>
            </div>

            <!-- Date and Time with animated icons -->
            <div class="flex space-x-6">
              <div
                class="flex items-center text-lime-700 bg-lime-50 px-4 py-2 rounded-full group-hover:bg-lime-100 transition-colors duration-300">
                <svg class="w-5 h-5 mr-2 text-lime-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ formatDate(live.dia) }}
              </div>
              <div
                class="flex items-center text-lime-700 bg-lime-50 px-4 py-2 rounded-full group-hover:bg-lime-100 transition-colors duration-300">
                <svg class="w-5 h-5 mr-2 text-lime-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ live.hora }}
              </div>
            </div>

            <!-- Actions with dynamic effects -->
            <div class="flex space-x-4">
              <button @click="setReminder(live)"
                class="flex-1 bg-gradient-to-r from-yellow-400 to-yellow-300 text-yellow-900 px-4 py-3 rounded-xl transition-all duration-300 hover:shadow-lg hover:brightness-110 hover:scale-105 flex items-center justify-center font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Recorda'm
              </button>
              <button @click="joinLive(live)"
                class="flex-1 bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 px-4 py-3 rounded-xl transition-all duration-300 hover:shadow-lg hover:brightness-110 hover:scale-105 flex items-center justify-center font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                Uneix-te!
              </button>
            </div>
          </div>
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
    };

    const fetchLives = async () => {
      try {
        loading.value = true;
        error.value = null;

        const response = await communicationManager.getLives();

        const responseData = response.data || response;

        if (!responseData || !Array.isArray(responseData)) {
          throw new Error('Formato de resposta inesperado');
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
      const title = encodeURIComponent(`Live de cuina: ${live.recipe.title}`);
      const description = encodeURIComponent(live.recipe.description);
      const location = encodeURIComponent('Online');

      // Convertir fecha y hora a formato de Google Calendar (UTC)
      const startDate = new Date(`${live.dia}T${live.hora}`);
      const endDate = new Date(startDate.getTime() + 60 * 60 * 1000);

      const formatDate = (date) =>
        date.toISOString().replace(/-|:|\.\d\d\d/g, '');

      const start = formatDate(startDate);
      const end = formatDate(endDate);

      const calendarUrl = `https://www.google.com/calendar/render?action=TEMPLATE&text=${title}&dates=${start}/${end}&details=${description}&location=${location}&sf=true&output=xml`;

      window.open(calendarUrl, '_blank');
    };

    const joinLive = (live) => {
      router.push({
        name: 'ChatRoom', 
        params: {
          liveId: live.id 
        }
      });
    };

    const formatDate = (dateString) => {
      try {
        if (!dateString) return 'Data no definida';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'Data invàlida';

        const options = { 
          weekday: 'long', 
          year: 'numeric', 
          month: 'long', 
          day: 'numeric' 
        };

        const catalanDate = date.toLocaleDateString('ca-ES', options);
        
        // Personalizar nombres de meses y días
        const months = {
          'gener': 'gener',
          'febrer': 'febrer',
          'març': 'març',
          'abril': 'abril',
          'maig': 'maig',
          'juny': 'juny',
          'juliol': 'juliol',
          'agost': 'agost',
          'setembre': 'setembre',
          'octubre': 'octubre',
          'novembre': 'novembre',
          'desembre': 'desembre'
        };

        const days = {
          'dilluns': 'dilluns',
          'dimarts': 'dimarts',
          'dimecres': 'dimecres',
          'dijous': 'dijous',
          'divendres': 'divendres',
          'dissabte': 'dissabte',
          'diumenge': 'diumenge'
        };

        // Reemplazar los nombres en español por catalán
        let formattedDate = catalanDate;
        Object.entries(months).forEach(([cat, _]) => {
          formattedDate = formattedDate.replace(cat, cat);
        });
        Object.entries(days).forEach(([cat, _]) => {
          formattedDate = formattedDate.replace(cat, cat);
        });

        return formattedDate;
      } catch (e) {
        console.error('Error formateando fecha:', e);
        return 'Data invàlida';
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

<style>
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }

  33% {
    transform: translate(30px, -50px) scale(1.1);
  }

  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }

  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

@keyframes shake {

  0%,
  100% {
    transform: translateX(0);
  }

  25% {
    transform: translateX(5px);
  }

  75% {
    transform: translateX(-5px);
  }
}

.animate-shake {
  animation: shake 0.5s ease-in-out;
}
</style>