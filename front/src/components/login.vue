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
    router.push('/recetas');
  } catch (error) {
    console.error("Error en iniciar sessió:", error.response?.data);
    errorMessage.value = error.response?.data?.message || "Credencials incorrectes o token invàlid.";
  }
};
</script>

<template>
  <div class="login-container">
    <div class="login-card">
      <img :src="logo" alt="Logo" class="register-logo"> 

      <h2 class="login-title">Inicia Sessió</h2>
      <form @submit.prevent="handleLogin" class="login-form">
        <input
          type="text"
          v-model="form.name"
          placeholder="Nom d'usuari"
          required
        />
        <input
          type="password"
          v-model="form.password"
          placeholder="Contrasenya"
          required
        />
        <button type="submit" class="btn-submit">Inicia sessió</button>
      </form>
      <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
      <p class="register-link">
        Encara no tens compte? <router-link to="/register">Registra't aquí</router-link>
      </p>
    </div>
  </div>
</template>

<style scoped>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
.register-logo{
max-width: 250px;
  margin-bottom: 1rem;
}

.login-container {
  background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem;
}

.login-card {
  background-color: #fff;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  width: 100%;
  text-align: center;
}

.login-title {
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  color: #333;
  font-weight: 600;
}


.login-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.login-form input {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.login-form input:focus {
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

.error-message {
  color: red;
  margin-top: 0.5rem;
}

.register-link {
  margin-top: 1rem;
  font-size: 0.9rem;
}

.register-link a {
  color: #007BFF;
  text-decoration: none;
  transition: color 0.3s ease;
}

.register-link a:hover {
  color: #322b5f;
}

@media (min-width: 768px) {
  .login-card {
    padding: 2.5rem;
  }
}
</style>