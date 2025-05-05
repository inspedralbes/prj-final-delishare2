<template>
    <div class="verification-form">
      <button class="back-button" @click="$router.push('/perfil')">← Volver</button>
  
      <h2>Solicitar verificación</h2>
  
      <form @submit.prevent="sendRequest">
        <label for="message">Mensaje para el administrador:</label>
        <textarea
          id="message"
          v-model="message"
          placeholder="Escribe aquí tu motivo de verificación..."
          required
        ></textarea>
  
        <button type="submit">Enviar solicitud</button>
      </form>
  
      <p v-if="success" class="success">{{ success }}</p>
      <p v-if="error" class="error">{{ error }}</p>
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
  .verification-form {
    max-width: 500px;
    margin: 40px auto;
    background: #fff;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    position: relative;
  }
  
  .back-button {
    position: absolute;
    top: 10px;
    left: 10px;
    background: none;
    border: none;
    color: #0ea5e9;
    font-size: 16px;
    cursor: pointer;
    padding: 5px 10px;
  }
  
  .back-button:hover {
    text-decoration: underline;
  }
  
  h2 {
    margin-bottom: 20px;
    text-align: center;
  }
  
  textarea {
    width: 100%;
    height: 120px;
    padding: 10px;
    resize: none;
    font-size: 16px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-bottom: 15px;
  }
  
  button[type="submit"] {
    padding: 10px 20px;
    background-color: #38bdf8;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
  }
  
  button[type="submit"]:hover {
    background-color: #0ea5e9;
  }
  
  .success {
    color: green;
    margin-top: 15px;
  }
  
  .error {
    color: red;
    margin-top: 15px;
  }
  </style>
  