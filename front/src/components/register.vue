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
      <img :src="logo" alt="Logo" class="register-logo"> 
      <h3 class="register-title">Registra't !</h3>
      <form @submit.prevent="handleRegister" class="register-form">
        <div class="form-group">
          <input
            type="text"
            v-model="form.name"
            placeholder="Nom d'usuari"
            required
            class="form-control"
          />
        </div>
        <div class="form-group">
          <input
            type="email"
            v-model="form.email"
            placeholder="Correu electrònic"
            required
            class="form-control"
          />
        </div>
        <div class="form-group">
          <input
            type="password"
            v-model="form.password"
            placeholder="Contrasenya"
            required
            class="form-control"
          />
        </div>
        <button type="submit" class="btn-submit">Registrar-me</button>
        <p class="login-link">
          Ja tens un compte? <router-link to="/">Inicia sessió</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<style scoped>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.register-container {
  background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem;
}

.register-card {
  background-color: #fff;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  width: 100%;
  text-align: center;
}

.register-logo {
  max-width: 250px;
  margin-bottom: 1rem;
}

.register-title {
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  color: #333;
  font-weight: 600;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  width: 100%;
}

input.form-control {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input.form-control:focus {
  border-color: #007BFF;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
  outline: none;
}

.btn-submit {
  padding: 0.8rem;
  background-color: #0c0636;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-submit:hover {
  background-color: #322b5f;
  transform: translateY(-2px);
}

.login-link {
  margin-top: 1rem;
  font-size: 0.9rem;
}

.login-link a {
  color: #2708ee;
  text-decoration: none;
  transition: color 0.3s ease;
}

.login-link a:hover {
  color: #0056b3;
}

@media (min-width: 768px) {
  .register-card {
    padding: 2.5rem;
  }
}
</style>
