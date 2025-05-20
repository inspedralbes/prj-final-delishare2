<template>
  <div class="min-h-screen bg-lime-50 flex flex-col">
    <section class="relative overflow-hidden">
      <div class="bg-gradient-to-br from-lime-100 via-lime-200 to-green-200 py-16 relative">
        <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
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
                Solicitar Verificación
              </span>
              <span class="block text-2xl mt-3 text-lime-700 font-medium">
                Conviértete en un chef verificado
              </span>
            </h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Formulario Section -->
    <div class="flex-1 flex items-center justify-center px-4 -mt-40 pb-0">
      <div class="w-full max-w-2xl">
        <div class="bg-white rounded-2xl shadow-xl p-1">
          <div class="flex items-center mb-6">
            <button @click="$router.push('/perfil')"
              class="flex items-center text-lime-600 hover:text-lime-700 transition-colors">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
              </svg>
              Volver al perfil
            </button>
          </div>

          <form @submit.prevent="sendRequest" class="space-y-6">
            <div>
              <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                Mensaje para el administrador
              </label>
              <textarea id="message" v-model="message" rows="6" placeholder="Escribe aquí tu motivo de verificación..."
                required
                class="w-full px-4 py-3 text-gray-700 bg-white border-2 border-lime-200 rounded-xl focus:outline-none focus:border-lime-400 focus:ring-2 focus:ring-lime-200 transition-all duration-300"></textarea>
            </div>

            <button type="submit"
              class="w-full px-6 py-3 bg-gradient-to-r from-lime-400 to-green-500 text-white font-medium rounded-xl shadow-md hover:from-lime-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-lime-300 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105">
              Enviar solicitud
            </button>
          </form>

          <div v-if="success" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
            {{ success }}
          </div>
          <div v-if="error" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
            {{ error }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import communicationManager from '@/services/communicationManager';

export default {
  name: 'Verification',
  data() {
    return {
      message: '',
      success: '',
      error: '',
    };
  },
  methods: {
    async sendRequest() {
      this.success = '';
      this.error = '';

      try {
        await communicationManager.sendVerificationRequest(this.message);
        this.success = 'Solicitud enviada correctamente.';
        this.message = '';
      } catch (err) {
        this.error = 'Error al enviar la solicitud.';
        console.error(err);
      }
    },
  },
};
</script>

<style scoped>
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
</style>