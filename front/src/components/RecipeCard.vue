<template>
  <article 
    class="relative flex flex-col w-full border-2 border-lime-300 rounded-2xl overflow-hidden text-center transition-transform duration-300 ease-in-out cursor-pointer bg-white hover:scale-105 hover:shadow-xl hover:border-lime-400"
    @click="handleClick"
  >
    <img :src="image" :alt="title" class="h-52 w-full object-cover rounded-2xl mb-3 md:h-32" />
    <div class="p-4 flex-grow flex flex-col justify-between">
      <h2 class="text-sm md:text-lg font-bold text-lime-700 md:truncate mb-1">{{ title }}</h2>
      <p class="text-base text-gray-700 line-clamp-2">{{ truncatedDescription }}</p>
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
          class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
          @click.self="closeModal"
        >
          <div class="bg-white rounded-2xl shadow-lg p-8 text-center w-full max-w-xs md:max-w-md">
            <p class="text-lime-900 text-lg font-semibold mb-6">Debes iniciar sesión para ver esta receta</p>
            <div class="flex justify-center gap-4">
              <button 
                class="flex-1 py-2 bg-gradient-to-r from-red-400 to-red-600 text-white rounded-xl font-semibold shadow hover:from-red-500 hover:to-red-700 transition-all duration-200"
                @click="closeModal"
              >
                Cancelar
              </button>
              <button 
                class="flex-1 py-2 bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 rounded-xl font-semibold shadow hover:from-green-600 hover:via-lime-500 hover:to-lime-400 hover:brightness-110 transition-all duration-200"
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