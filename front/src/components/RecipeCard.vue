<template>
  <article 
    class="relative flex flex-col w-full h-48 border border-gray-300 rounded-lg overflow-hidden text-center transition-transform duration-300 ease-in-out cursor-pointer bg-white hover:scale-105 hover:shadow-lg"
    @click="handleClick"
  >
    <img :src="image" :alt="title" class="w-full h-1/2 object-cover" />
    <div class="p-3 flex-grow flex flex-col justify-between">
      <h2 class="text-sm font-bold text-gray-800 truncate">{{ title }}</h2>
      <p class="text-xs text-gray-600 line-clamp-2">{{ truncatedDescription }}</p>
    </div>
    
    <!-- Modal fuera del flujo normal con portal -->
    <teleport to="body">
      <transition 
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <div 
          v-if="showAuthModal && !authStore.isAuthenticated" 
          class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
          @click.self="closeModal"
        >
          <div class="bg-white p-6 rounded-lg shadow-xl w-11/12 max-w-sm text-center">
            <p class="text-gray-700 mb-6">Debes iniciar sesión para ver esta receta</p>
            <div class="flex justify-center space-x-4">
              <button 
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors"
                @click="closeModal"
              >
                Cancelar
              </button>
              <button 
                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors"
                @click="goToLogin"
              >
                Aceptar
              </button>
            </div>
          </div>
        </div>
      </transition>
    </teleport>
  </article>
</template>

<script>
import { useAuthStore, eventBus } from '@/stores/authStore';
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';

export default {
  name: 'RecipeCard',
  props: {
    recipeId: {
      type: [String, Number],
      required: true
    },
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      default: '', 
    },
    image: {
      type: String,
      required: true
    }
  },
  setup(props) {
    const authStore = useAuthStore();
    const router = useRouter();
    const showAuthModal = ref(false);

    // Cerrar modal cuando el usuario se autentique
    const closeAuthModalOnLogin = () => {
      authStore.checkAuth(); 
      if (authStore.isAuthenticated) {
        showAuthModal.value = false;
      }
    };

    onMounted(() => {
      eventBus.on('authUpdated', closeAuthModalOnLogin);
      authStore.checkAuth(); // 🔥 Asegurar que el token está actualizado al montar
    });

    onUnmounted(() => {
      eventBus.off('authUpdated', closeAuthModalOnLogin);
    });

    watch(() => authStore.token, (newToken) => {
      if (newToken) {
        showAuthModal.value = false;
      }
    });

    const truncatedDescription = computed(() => {
      const descriptionText = props.description || '';
      const words = descriptionText.split(' ');
      return words.length > 15 
        ? words.slice(0, 15).join(' ') + '...'
        : descriptionText;
    });

    const handleClick = () => {
      if (!authStore.isAuthenticated) {
        showAuthModal.value = true;
      } else {
        router.push({ 
          name: 'InfoReceta', 
          params: { recipeId: String(props.recipeId) } 
        });
      }
    };

    const goToLogin = () => {
      router.push({ 
        name: 'login',
        query: { redirect: `/info/${props.recipeId}` }
      });
    };

    const closeModal = (e) => {
      e?.stopPropagation();
      showAuthModal.value = false;
    };

    return {
      authStore,
      showAuthModal,
      truncatedDescription,
      handleClick,
      goToLogin,
      closeModal
    };
  }
};
</script>