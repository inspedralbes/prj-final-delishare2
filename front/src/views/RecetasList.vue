<template>
    <div class="recetas-container">
      <h1 class="title">Todas las Recetas</h1>
      
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Cargando recetas...</p>
      </div>
      
      <div v-else-if="error" class="error">
        <p>Error al cargar las recetas: {{ error }}</p>
        <button @click="fetchRecetas" class="retry-btn">Intentar nuevamente</button>
      </div>
      
      <div v-else-if="recetas.length === 0" class="no-recetas">
        <p>No hay recetas disponibles.</p>
      </div>
      
      <div v-else class="recetas-grid">
        <div v-for="receta in recetas" :key="receta.id" class="receta-card">
          <div class="receta-image">
            <img :src="receta.image_url || 'https://via.placeholder.com/300x200?text=No+Image'" :alt="receta.title">
          </div>
          <div class="receta-content">
            <h2 class="receta-title">{{ receta.title }}</h2>
            <div class="receta-meta">
              <span class="category">{{ receta.category?.name }}</span>
              <span class="cuisine">{{ receta.cuisine?.name }}</span>
            </div>
            <p class="receta-description">{{ truncateDescription(receta.description) }}</p>
            <div class="receta-footer">
              <span class="author">Por: {{ receta.user?.name }}</span>
              <button @click="eliminarReceta(receta.id)" class="view-btn">Eliminar receta</button>

            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import communicationManager from '@/services/communicationManager';



  export default {
    name: 'RecetasList',
    data() {
      return {
        recetas: [],
        loading: true,
        error: null
      }
    },
    mounted() {
      this.fetchRecetas();
    },
    methods: {
      async fetchRecetas() {
        this.loading = true;
        this.error = null;
        try {
          const response = await fetch('http://localhost:8000/api/recipes');
          if (!response.ok) {
            throw new Error('Error al obtener las recetas');
          }
          this.recetas = await response.json();
        } catch (err) {
          this.error = err.message;
          console.error('Error fetching recetas:', err);
        } finally {
          this.loading = false;
        }
      },
      async eliminarReceta(id) {
  if (!confirm('¿Estás seguro de que deseas eliminar esta receta?')) return;

  try {
    await communicationManager.deleteRecipe(id);
    this.recetas = this.recetas.filter(receta => receta.id !== id);
  } catch (error) {
    alert('Ocurrió un error al eliminar la receta.');
  }
},
      truncateDescription(desc) {
        if (!desc) return '';
        return desc.length > 150 ? desc.substring(0, 150) + '...' : desc;
      }
    }
  }
  </script>
  
  <style scoped>
  .recetas-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
  }
  
  .title {
    text-align: center;
    margin-bottom: 2rem;
    color: #2c3e50;
    font-size: 2.5rem;
  }
  
  .loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 200px;
  }
  
  .spinner {
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
  }
  
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  .error {
    text-align: center;
    color: #e74c3c;
    padding: 2rem;
    background: #fde8e8;
    border-radius: 8px;
  }
  
  .retry-btn {
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s;
  }
  
  .retry-btn:hover {
    background: #c0392b;
  }
  
  .no-recetas {
    text-align: center;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 8px;
    color: #6c757d;
  }
  
  .recetas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
  }
  
  .receta-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
  }
  
  .receta-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }
  
  .receta-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
  }
  
  .receta-content {
    padding: 1.5rem;
  }
  
  .receta-title {
    margin: 0 0 0.5rem;
    color: #2c3e50;
    font-size: 1.5rem;
  }
  
  .receta-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    color: #7f8c8d;
    font-size: 0.9rem;
  }
  
  .receta-description {
    color: #34495e;
    margin-bottom: 1.5rem;
    line-height: 1.5;
  }
  
  .receta-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .author {
    color: #7f8c8d;
    font-style: italic;
  }
  
  .view-btn {
    padding: 0.5rem 1rem;
    background: #db0202;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.3s;
  }
  
  .view-btn:hover {
    background: #2980b9;
  }
  
  @media (max-width: 768px) {
    .recetas-grid {
      grid-template-columns: 1fr;
    }
    
    .title {
      font-size: 2rem;
    }
  }
  </style>