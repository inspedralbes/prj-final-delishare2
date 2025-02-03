<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import communicationManager from '@/services/communicationManager';

const router = useRouter();

const form = ref({
  name: '',  // Nombre de usuario (no es email)
  password: ''
});

const errorMessage = ref('');

const handleLogin = async () => {
  try {
    // Usamos el método login de communicationManager
    const response = await communicationManager.login(form.value);

    // Guardar token en localStorage
    const token = response.token;
    localStorage.setItem('token', token);

    // Redirigir a la landing page después de login
    router.push('/recetas');
  } catch (error) {
    console.error("Error en login:", error.response?.data);
    // Mensaje de error en caso de fallo
    errorMessage.value = error.response?.data?.message || "Credenciales incorrectas o token inválido.";
  }
};
</script>

<template>
  <div class="login-container">
    <div class="login-card">
      <h2 class="login-title">Iniciar Sesión</h2>
      <form @submit.prevent="handleLogin" class="login-form">
        <input type="text" v-model="form.name" placeholder="Nombre de usuario" required />
        <input type="password" v-model="form.password" placeholder="Contraseña" required />
        <button type="submit" class="btn-submit">Iniciar sesión</button>
      </form>
      <p v-if="errorMessage" style="color: red;">{{ errorMessage }}</p>
      <p class="register-link">
        ¿No tienes cuenta? <router-link to="/register">Regístrate aquí</router-link>
      </p>
    </div>
  </div>
</template>

<style scoped>
body {
  margin: 0;
  padding: 0;
  height: 100vh;
  width: 100vw;
  font-family: 'Roboto', sans-serif;
}

/* Estilos para la pantalla de login */
.login-container {
  background-image: url('/img/image.png');
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.login-card {
  background: #ffffff;
  padding: 2rem 1.5rem;
  border-radius: 8px;
  box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 350px;
  text-align: center;
}

.login-title {
  margin-bottom: 2rem;
  font-size: 1.6rem;
  font-weight: 600;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

input {
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
}

input:focus {
  border-color: #343330;
  outline: none;
}

.btn-submit {
  background: #358600;
  color: white;
  padding: 0.8rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.btn-submit:hover {
  background: #235800;
}

/* Estilos para el enlace de registro */
.register-link {
  margin-top: 1rem;
  font-size: 0.9rem;
}

.register-link a {
  color: #358600;
  text-decoration: none;
  transition: color 0.3s;
}

.register-link a:hover {
  color: #235800;
}
</style>
