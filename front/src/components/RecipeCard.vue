<template>
  <article class="recipe-card" @click="handleClick">
    <img :src="image" :alt="title" class="recipe-image" />
    <div class="recipe-content">
      <h2>{{ title }}</h2>
      <p class="description">{{ truncatedDescription }}</p>
    </div>
    
    <!-- Modal fuera del flujo normal con portal -->
    <teleport to="body">
      <transition name="modal-fade">
        <div 
          v-if="showAuthModal && !authStore.isAuthenticated" 
          class="auth-modal-overlay" 
          @click.self="closeModal"
        >
          <div class="auth-modal">
            <p>Debes iniciar sesión para ver esta receta</p>
            <div class="auth-modal-buttons">
              <button class="cancel-btn" @click="closeModal">Cancelar</button>
              <button class="confirm-btn" @click="goToLogin">Aceptar</button>
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

<style scoped>
.recipe-card {
  position: relative;
  display: flex;
  flex-direction: column;
  width: 150px;
  height: 150px;
  border: 1px solid #343330;
  border-radius: 8px;
  overflow: hidden;
  text-align: center;
  margin: 5px;
  transition: transform 0.3s ease-in-out;
  cursor: pointer;
  background: white;
}

.recipe-card:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.recipe-image {
  width: 100%;
  height: 55%;
  object-fit: cover;
}

.recipe-content {
  padding: 8px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.recipe-content h2 {
  font-size: 14px;
  color: #343330;
  font-weight: bold;
  margin: 0 0 5px 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.recipe-content .description {
  font-size: 10px;
  color: #666;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  margin: 0;
}
</style>

<style>
/* Estilos globales para el modal */
.auth-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.auth-modal {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  width: 90%;
  max-width: 300px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.auth-modal p {
  margin-bottom: 20px;
  color: #333;
}

.auth-modal-buttons {
  display: flex;
  justify-content: center;
  gap: 10px;
}

.confirm-btn, .cancel-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
}

.confirm-btn {
  background-color: #4CAF50;
  color: white;
}

.confirm-btn:hover {
  background-color: #45a049;
}

.cancel-btn {
  background-color: #f44336;
  color: white;
}

.cancel-btn:hover {
  background-color: #d32f2f;
}

/* Animaciones del modal */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-active .auth-modal,
.modal-fade-leave-active .auth-modal {
  transition: transform 0.3s ease;
}

.modal-fade-enter-from .auth-modal,
.modal-fade-leave-to .auth-modal {
  transform: translateY(-20px);
}
</style>