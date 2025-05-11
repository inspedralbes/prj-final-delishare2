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
  <div class="register-container">
    <div class="register-card">
      <div class="logo-container">
        <img :src="logo" alt="Logo" class="register-logo"> 
      </div>
      <h3 class="register-title">Registra't a DeliShare!</h3>
      <p class="register-subtitle">Uneix-te a la nostra comunitat de cuina</p>
      
      <form @submit.prevent="handleRegister" class="register-form">
        <div class="form-group">
          <label class="form-label">Nom d'usuari</label>
          <div class="input-container">
            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <input
              type="text"
              v-model="form.name"
              placeholder="Introdueix el teu nom"
              required
              class="form-control"
            />
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Correu electrònic</label>
          <div class="input-container">
            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <input
              type="email"
              v-model="form.email"
              placeholder="Introdueix el teu correu"
              required
              class="form-control"
            />
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Contrasenya</label>
          <div class="input-container">
            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <input
              type="password"
              v-model="form.password"
              placeholder="Introdueix la teva contrasenya"
              required
              class="form-control"
            />
          </div>
        </div>
        
        <button type="submit" class="btn-submit">
          <span>Registrar-me</span>
          <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>
        </button>
        
        <p class="login-link">
          Ja tens un compte? <router-link to="/" class="link-highlight">Inicia sessió</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<style scoped>
.register-container {
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem;
}

.register-card {
  background-color: #fff;
  padding: 2.5rem;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
  max-width: 450px;
  width: 100%;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.register-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 6px;
  background: linear-gradient(90deg, #22c55e, #84cc16);
}

.logo-container {
  margin-bottom: 1.5rem;
}

.register-logo {
  max-width: 200px;
  transition: transform 0.3s ease;
}

.register-logo:hover {
  transform: scale(1.05);
}

.register-title {
  font-size: 2rem;
  margin-bottom: 0.5rem;
  color: #166534;
  font-weight: 700;
}

.register-subtitle {
  color: #4b5563;
  margin-bottom: 2rem;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  width: 100%;
  text-align: left;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  color: #374151;
  font-weight: 500;
}

.input-container {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 1.25rem;
  height: 1.25rem;
  color: #9ca3af;
}

.form-control {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 2.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background-color: #f9fafb;
}

.form-control:focus {
  border-color: #22c55e;
  background-color: #fff;
  box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
  outline: none;
}

.btn-submit {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 1rem;
  background: linear-gradient(135deg, #22c55e, #84cc16);
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 1rem;
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
}

.btn-submit:active {
  transform: translateY(0);
}

.btn-icon {
  width: 1.25rem;
  height: 1.25rem;
  transition: transform 0.3s ease;
}

.btn-submit:hover .btn-icon {
  transform: translateX(4px);
}

.login-link {
  margin-top: 1.5rem;
  color: #6b7280;
  font-size: 0.95rem;
}

.link-highlight {
  color: #22c55e;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.link-highlight:hover {
  color: #16a34a;
}

@media (max-width: 640px) {
  .register-card {
    padding: 2rem;
  }
  
  .register-title {
    font-size: 1.75rem;
  }
}
</style>
