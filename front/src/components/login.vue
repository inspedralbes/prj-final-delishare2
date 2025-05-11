<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import communicationManager from '@/services/communicationManager';
import logo from '@/assets/images/delishare.png'; 

const router = useRouter();

const form = ref({
  name: '',
  password: ''
});

const errorMessage = ref('');

const handleLogin = async () => {
  try {
    const response = await communicationManager.login(form.value);
    // Emmagatzemar el token al localStorage
    const token = response.token;
    localStorage.setItem('token', token);
    // Redirigir a la pàgina principal després d'iniciar sessió
    router.push('/');
  } catch (error) {
    console.error("Error en iniciar sessió:", error.response?.data);
    errorMessage.value = error.response?.data?.message || "Credencials incorrectes o token invàlid.";
  }
};
</script>

<template>
  <div class="login-container">
    <div class="login-card">
      <div class="logo-container">
        <img :src="logo" alt="Logo" class="login-logo"> 
      </div>
      <h2 class="login-title">Benvingut de nou!</h2>
      <p class="login-subtitle">Inicia sessió a la teva compte DeliShare</p>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label class="form-label">Nom d'usuari</label>
          <div class="input-container">
            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <input
              type="text"
              v-model="form.name"
              placeholder="Introdueix el teu nom d'usuari"
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

        <div v-if="errorMessage" class="error-message">
          <svg class="error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ errorMessage }}
        </div>

        <button type="submit" class="btn-submit">
          <span>Inicia sessió</span>
          <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>
        </button>

        <p class="register-link">
          Encara no tens compte? <router-link to="/register" class="link-highlight">Registra't aquí</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<style scoped>
.login-container {
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem;
}

.login-card {
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

.login-card::before {
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

.login-logo {
  max-width: 200px;
  transition: transform 0.3s ease;
}

.login-logo:hover {
  transform: scale(1.05);
}

.login-title {
  font-size: 2rem;
  margin-bottom: 0.5rem;
  color: #166534;
  font-weight: 700;
}

.login-subtitle {
  color: #4b5563;
  margin-bottom: 2rem;
}

.login-form {
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

.error-message {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background-color: #fee2e2;
  border: 1px solid #fecaca;
  border-radius: 12px;
  color: #dc2626;
  font-size: 0.95rem;
  margin-top: 0.5rem;
}

.error-icon {
  width: 1.25rem;
  height: 1.25rem;
  flex-shrink: 0;
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

.register-link {
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
  .login-card {
    padding: 2rem;
  }
  
  .login-title {
    font-size: 1.75rem;
  }
}
</style>