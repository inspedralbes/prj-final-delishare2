<template>
    <div class="bootnes-crud">
      <h2>Panel de Administración</h2>
      
      <div class="button-container">
        <router-link to="/recetas" class="btn btn-primary">
          Recetas
        </router-link>
        <router-link to="/categories" class="btn btn-success">
          Categorías
        </router-link>
        <router-link to="/cuisines" class="btn btn-info">
          Cocinas
        </router-link>
        <router-link to="/comments" class="btn btn-warning">
          Comentarios
        </router-link>
        <router-link to="/users" class="btn btn-danger">
          Usuarios
        </router-link>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'BootnesCrud'
  }
  </script>
  
  <style scoped>
  .bootnes-crud {
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  h2 {
    margin-bottom: 20px;
    color: #343a40;
  }
  
  .button-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }
  
  .btn {
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    text-align: center;
    display: inline-block;
  }
  
  .btn:hover {
    opacity: 0.9;
    transform: translateY(-2px);
  }
  
  .btn-primary {
    background-color: #007bff;
    color: white;
  }
  
  .btn-success {
    background-color: #28a745;
    color: white;
  }
  
  .btn-info {
    background-color: #17a2b8;
    color: white;
  }
  
  .btn-warning {
    background-color: #ffc107;
    color: #212529;
  }
  
  .btn-danger {
    background-color: #dc3545;
    color: white;
  }
  
  /* Enlaces específicos */
  a.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  
  /* Responsive adjustments */
  @media (max-width: 768px) {
    .button-container {
      flex-direction: column;
    }
    
    .btn {
      width: 100%;
    }
  }
  </style>