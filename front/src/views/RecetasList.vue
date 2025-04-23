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
              <span class="cuisine">{{ receta.cuisine?.country }}</span>
            </div>
            <p class="receta-description">{{ truncateDescription(receta.description) }}</p>
            <div class="receta-footer">
              <span class="author">Por: {{ receta.user?.name }}</span>
              <button @click="showDeleteModal(receta)" class="view-btn">Eliminar receta</button>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Modal de confirmación -->
      <div v-if="showModal" class="modal-overlay" @click="cancelDelete">
        <div class="modal-content" @click.stop>
          <h3>Confirmar eliminación</h3>
          <p>¿Estás seguro de que deseas eliminar la receta "{{ recetaToDelete?.title }}"?</p>
          <div class="modal-buttons">
            <button @click="cancelDelete" class="cancel-btn">Cancelar</button>
            <button @click="confirmDelete" class="confirm-btn">Eliminar</button>
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
        error: null,
        showModal: false,
        recetaToDelete: null
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
      showDeleteModal(receta) {
        this.recetaToDelete = receta;
        this.showModal = true;
      },
      cancelDelete() {
        this.showModal = false;
        this.recetaToDelete = null;
      },
      async confirmDelete() {
        try {
          await communicationManager.deleteRecipe(this.recetaToDelete.id);
          this.recetas = this.recetas.filter(receta => receta.id !== this.recetaToDelete.id);
          this.showModal = false;
          this.recetaToDelete = null;
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
/* Variables y reset básico */
:root {
  --primary-color: #3498db;
  --primary-dark: #2980b9;
  --accent-color: #e74c3c;
  --accent-dark: #c0392b;
  --text-dark: #2c3e50;
  --text-medium: #34495e;
  --text-light: #7f8c8d;
  --background-light: #f8f9fa;
  --shadow-subtle: 0 4px 6px rgba(0, 0, 0, 0.05);
  --shadow-medium: 0 6px 12px rgba(0, 0, 0, 0.1);
  --shadow-strong: 0 10px 25px rgba(0, 0, 0, 0.15);
  --border-radius: 10px;
  --transition-quick: 0.2s ease;
  --transition-smooth: 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.recetas-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  animation: fadeIn 0.5s ease-in-out;
}

/* Animación de entrada */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Título principal con línea decorativa */
.title {
  text-align: center;
  margin-bottom: 3rem;
  color: var(--text-dark);
  font-size: 2.5rem;
  font-weight: 700;
  position: relative;
  padding-bottom: 1rem;
}

.title::after {
  content: "";
  position: absolute;
  left: 50%;
  bottom: 0;
  width: 80px;
  height: 4px;
  background: linear-gradient(to right, var(--primary-color), var(--accent-color));
  transform: translateX(-50%);
  border-radius: 2px;
  animation: expandLine 0.6s ease-in-out forwards;
}

@keyframes expandLine {
  from { width: 0; opacity: 0; }
  to { width: 80px; opacity: 1; }
}

/* Estado de carga mejorado */
.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 300px;
}

.spinner {
  border: 3px solid rgba(52, 152, 219, 0.2);
  border-top: 3px solid var(--primary-color);
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s cubic-bezier(0.76, 0.21, 0.24, 0.93) infinite;
  margin-bottom: 1.5rem;
  box-shadow: 0 0 15px rgba(52, 152, 219, 0.3);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading p {
  color: var(--text-medium);
  font-size: 1.1rem;
  animation: pulse 1.5s infinite alternate;
}

@keyframes pulse {
  from { opacity: 0.6; }
  to { opacity: 1; }
}

/* Estado de error mejorado */
.error {
  text-align: center;
  color: var(--accent-color);
  padding: 2.5rem;
  background: rgba(231, 76, 60, 0.08);
  border-radius: var(--border-radius);
  border-left: 4px solid var(--accent-color);
  box-shadow: var(--shadow-subtle);
  transform: translateZ(0);
  transition: transform var(--transition-smooth);
}

.retry-btn {
  margin-top: 1.5rem;
  padding: 0.7rem 1.5rem;
  background: var(--accent-color);
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: all var(--transition-smooth);
  font-weight: 600;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 5px rgba(231, 76, 60, 0.3);
}

.retry-btn:hover {
  background: var(--accent-dark);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(231, 76, 60, 0.4);
}

.retry-btn:active {
  transform: translateY(0);
  box-shadow: 0 1px 3px rgba(231, 76, 60, 0.4);
}

/* Estado sin recetas */
.no-recetas {
  text-align: center;
  padding: 3rem 2rem;
  background: var(--background-light);
  border-radius: var(--border-radius);
  color: var(--text-light);
  font-size: 1.1rem;
  box-shadow: var(--shadow-subtle);
  border: 1px dashed #e0e0e0;
}

/* Grid de recetas mejorado */
.recetas-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2.5rem;
  animation: gridFadeIn 0.6s ease-out;
}

@keyframes gridFadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Tarjetas de recetas con animación y efectos */
.receta-card {
  background: white;
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: var(--shadow-medium);
  transition: all var(--transition-smooth);
  position: relative;
  border: 1px solid rgba(0, 0, 0, 0.05);
  animation: cardAppear 0.4s ease-out forwards;
  opacity: 0;
  transform: scale(0.96);
}

.receta-card:nth-child(odd) {
  animation-delay: 0.1s;
}

.receta-card:nth-child(even) {
  animation-delay: 0.2s;
}

@keyframes cardAppear {
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.receta-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-strong);
  border-color: rgba(52, 152, 219, 0.2);
}

.receta-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(to right, var(--primary-color), var(--accent-color));
  opacity: 0;
  transition: opacity var(--transition-smooth);
}

.receta-card:hover::before {
  opacity: 1;
}

/* Imagen de receta con efecto */
.receta-image {
  overflow: hidden;
  position: relative;
}

.receta-image::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom, transparent 70%, rgba(0, 0, 0, 0.2));
  z-index: 1;
}

.receta-image img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.receta-card:hover .receta-image img {
  transform: scale(1.05);
}

/* Contenido de la tarjeta */
.receta-content {
  padding: 1.8rem;
  position: relative;
}

.receta-title {
  margin: 0 0 0.8rem;
  color: var(--text-dark);
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1.3;
  transition: color var(--transition-quick);
}

.receta-card:hover .receta-title {
  color: var(--primary-color);
}

/* Metadatos con estilo de badge */
.receta-meta {
  display: flex;
  gap: 0.8rem;
  margin-bottom: 1.2rem;
  flex-wrap: wrap;
}

.category,
.cuisine {
  display: inline-block;
  padding: 0.3rem 0.8rem;
  font-size: 0.8rem;
  border-radius: 50px;
  font-weight: 500;
  transition: all var(--transition-quick);
}

.category {
  background-color: rgba(52, 152, 219, 0.1);
  color: var(--primary-color);
  border: 1px solid rgba(52, 152, 219, 0.3);
}

.cuisine {
  background-color: rgba(231, 76, 60, 0.1);
  color: var(--accent-color);
  border: 1px solid rgba(231, 76, 60, 0.3);
}

.receta-card:hover .category,
.receta-card:hover .cuisine {
  transform: translateY(-2px);
}

/* Descripción de receta */
.receta-description {
  color: var(--text-medium);
  margin-bottom: 1.8rem;
  line-height: 1.6;
  position: relative;
  font-size: 0.95rem;
}

/* Footer de la tarjeta */
.receta-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid rgba(0, 0, 0, 0.05);
  padding-top: 1.2rem;
}

.author {
  color: var(--text-light);
  font-style: italic;
  font-size: 0.9rem;
  transition: color var(--transition-quick);
}

.receta-card:hover .author {
  color: var(--text-medium);
}

.view-btn {
  padding: 0.5rem 1rem;
  background: #db0202;
  color: white;
  text-decoration: none;
  border-radius: 4px;
  transition: all var(--transition-smooth);
  border: none;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.9rem;
  box-shadow: 0 2px 5px rgba(219, 2, 2, 0.3);
}

.view-btn:hover {
  background: #b30000;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(219, 2, 2, 0.4);
}

.view-btn:active {
  transform: translateY(0);
  box-shadow: 0 1px 3px rgba(219, 2, 2, 0.4);
}

/* Estilos del modal mejorados */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(3px);
  animation: fadeInOverlay 0.3s ease;
}

@keyframes fadeInOverlay {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal-content {
  background-color: white;
  padding: 2.5rem;
  border-radius: var(--border-radius);
  max-width: 500px;
  width: 90%;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
  animation: dropInModal 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  transform-origin: center;
  border-top: 4px solid #db0202;
}

@keyframes dropInModal {
  from { 
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to { 
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.modal-content h3 {
  margin-top: 0;
  color: var(--text-dark);
  font-size: 1.6rem;
  margin-bottom: 1rem;
}

.modal-content p {
  color: var(--text-medium);
  font-size: 1.1rem;
  line-height: 1.5;
}

.modal-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.cancel-btn {
  padding: 0.7rem 1.2rem;
  background: #e0e0e0;
  color: #333;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: all var(--transition-smooth);
  font-weight: 600;
}

.cancel-btn:hover {
  background: #c0c0c0;
  transform: translateY(-2px);
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.cancel-btn:active {
  transform: translateY(0);
}

.confirm-btn {
  padding: 0.7rem 1.2rem;
  background: #db0202;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: all var(--transition-smooth);
  font-weight: 600;
  box-shadow: 0 2px 5px rgba(219, 2, 2, 0.3);
}

.confirm-btn:hover {
  background: #b30000;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(219, 2, 2, 0.4);
}

.confirm-btn:active {
  transform: translateY(0);
  box-shadow: 0 1px 3px rgba(219, 2, 2, 0.4);
}

/* Responsividad mejorada */
@media (max-width: 992px) {
  .recetas-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
  }
}

@media (max-width: 768px) {
  .recetas-grid {
    grid-template-columns: 1fr;
    gap: 1.8rem;
  }
  
  .title {
    font-size: 2rem;
    margin-bottom: 2rem;
  }
  
  .modal-content {
    width: 95%;
    padding: 1.8rem;
  }
  
  .receta-card {
    max-width: 500px;
    margin: 0 auto;
  }
}

@media (max-width: 480px) {
  .recetas-container {
    padding: 1.5rem 1rem;
  }
  
  .receta-content {
    padding: 1.5rem;
  }
  
  .modal-buttons {
    flex-direction: column-reverse;
    gap: 0.8rem;
  }
  
  .confirm-btn, .cancel-btn {
    width: 100%;
    padding: 0.8rem;
  }
}

/* Estilo para pantalla impresa */
@media print {
  .recetas-container {
    padding: 0;
  }
  
  .receta-card {
    box-shadow: none;
    border: 1px solid #ddd;
    page-break-inside: avoid;
  }
  
  .view-btn, .modal-overlay {
    display: none;
  }
}
</style>