<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import communicationManager from '@/services/communicationManager';
import logo from '@/assets/images/delishare.png'; 

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  name: '',
  email: '',
  password: ''
});

const handleRegister = async () => {
  try {
    const response = await communicationManager.register(form.value);
    console.log("Registre realitzat correctament:", response);
    authStore.setAuth(response.token, response.user); 
    router.push('/Formulario');
  } catch (error) {
    if (error.response) {
      console.error("Error en el registre:", error.response.data.message || 'Error desconocido');
    } else {
      console.error("Error de red o cliente:", error.message || 'Error desconocido');
    }
  }
};

</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-lime-50 to-green-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header con gradiente -->
        <div class="bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 p-6">
          <div class="flex justify-center mb-4">
            <img :src="logo" alt="Logo" class="h-20 w-auto transform hover:scale-105 transition-transform duration-300">
          </div>
          <h3 class="text-2xl font-bold text-center text-lime-900">Registra't a DeliShare!</h3>
          <p class="text-center text-lime-800 mt-2">Uneix-te a la nostra comunitat de cuina</p>
        </div>

        <!-- Formulario -->
        <form @submit.prevent="handleRegister" class="p-6 space-y-6">
          <div class="space-y-4">
            <!-- Campo de nombre de usuario -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nom d'usuari</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <input
                  type="text"
                  v-model="form.name"
                  placeholder="Introdueix el teu nom"
                  required
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 bg-gray-50 transition-colors duration-200"
                />
              </div>
            </div>

            <!-- Campo de email -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Correu electrònic</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <input
                  type="email"
                  v-model="form.email"
                  placeholder="Introdueix el teu correu"
                  required
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 bg-gray-50 transition-colors duration-200"
                />
              </div>
            </div>

            <!-- Campo de contraseña -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Contrasenya</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <input
                  type="password"
                  v-model="form.password"
                  placeholder="Introdueix la teva contrasenya"
                  required
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 bg-gray-50 transition-colors duration-200"
                />
              </div>
            </div>
          </div>

          <!-- Botón de registro -->
          <button
            type="submit"
            class="w-full flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 via-lime-400 to-lime-300 text-lime-900 rounded-lg font-medium hover:from-green-600 hover:via-lime-500 hover:to-lime-400 transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl"
          >
            <span>Registrar-me</span>
            <svg class="h-5 w-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </button>

          <!-- Enlace de login -->
          <p class="text-center text-gray-600 text-sm">
            Ja tens un compte? 
            <router-link to="/" class="text-lime-600 hover:text-lime-700 font-medium transition-colors duration-200">
              Inicia sessió
            </router-link>
          </p>
        </form>
      </div>
    </div>
  </div>
</template>
