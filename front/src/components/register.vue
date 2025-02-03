<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore'; 
import communicationManager from '@/services/communicationManager';

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
    console.log("Registro exitoso:", response);
    authStore.setToken(response.token);
    router.push('/');
  } catch (error) {
    console.error("Error en registro:", error.response?.data);
  }
};
</script>

<template>
  <div class="register-container">
    <div class="register-card">
      <img src="../assets/images/delishare-logo.jpg" alt="Logo" class="register-logo">
      <h3 class="register-title">Registra't i comença a compartir!</h3>
      <form @submit.prevent="handleRegister" class="register-form">
        <div class="form-group">
          <input type="text" v-model="form.name" placeholder="Nom d'usuari" required class="form-control"/>
        </div>
        <div class="form-group">
          <input type="email" v-model="form.email" placeholder="Correu electrònic" required class="form-control"/>
        </div>
        <div class="form-group">
          <input type="password" v-model="form.password" placeholder="Contrasenya" required class="form-control"/>
        </div>
        <button type="submit" class="btn-submit">Registrar-me</button>
        <p class="forgot-password">
          <a href="/">¿Ja tens un compte?</a>
        </p>
      </form>
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

.register-container {
  background-image: url('/img/image.png');
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.register-card {
  background: #ffffff;
  padding: 2rem 1.5rem;
  border-radius: 8px;
  box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 350px;
  text-align: center;
  box-sizing: border-box;
}

.register-logo {
  max-width: 120px;
  margin-bottom: 1rem;
}

.register-title {
  margin-bottom: 2rem;
  font-size: 1.6rem;
  font-weight: 600;
  color: #343330;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  justify-content: center;
  align-items: center;
}

.form-group {
  width: 100%;
  display: flex;
  justify-content: center;
}

input.form-control {
  width: 100%;
  max-width: 300px; /* Limita el ancho máximo */
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.3s, box-shadow 0.3s;
  text-align: left;
}

input.form-control:focus {
  border-color: #343330;
  box-shadow: 0 0 4px rgba(0, 123, 255, 0.3);
  outline: none;
}

.btn-submit {
  width: 100%;
  max-width: 300px;
  padding: 0.8rem;
  background: #358600;
  color: #ffffff;
  font-size: 1rem;
  font-weight: 600;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s, box-shadow 0.3s;
}

.btn-submit:hover {
  background: #235800;
  box-shadow: 0 2px 10px rgba(0, 91, 179, 0.2);
}

.forgot-password {
  margin-top: 1rem;
  font-size: 0.9rem;
}

.forgot-password a {
  color: #358600;
  text-decoration: none;
  transition: color 0.3s;
}

.forgot-password a:hover {
  color: #235800;
}

/* Media queries */
@media (min-width: 768px) {
  .register-card {
    padding: 2.5rem 2rem;
  }

  .register-title {
    font-size: 1.8rem;
  }

  input.form-control {
    padding: 1rem;
  }

  .btn-submit {
    font-size: 1.1rem;
    padding: 1rem;
  }
}

@media (min-width: 992px) {
  .register-card {
    max-width: 450px;
  }
}
</style>
