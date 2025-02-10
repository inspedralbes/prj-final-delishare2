<template>
  <article class="recipe-card" @click="handleClick" style="cursor: pointer;">
    <img :src="image" :alt="title" class="recipe-image" />
    <div class="recipe-content">
      <h2>{{ title }}</h2>
      <p class="description">{{ truncatedDescription }}</p>
    </div>
  </article>
</template>

<script>
export default {
  name: 'RecipeCard',
  props: {
    recipeId: {
      type: String,
      required: true
    },
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      required: true
    },
    image: {
      type: String,
      required: true
    }
  },
  computed: {
    // Computed property to get the first 15 words of the description
    truncatedDescription() {
      const words = this.description.split(' ');
      if (words.length > 15) {
        return words.slice(0, 15).join(' ') + '...';
      }
      return this.description;
    }
  },
  methods: {
    handleClick() {
      this.$router.push({ name: 'InfoReceta', params: { recipeId: this.recipeId } });
    }
  }
};
</script>

<style scoped>
.recipe-card {
  display: flex;
  flex-direction: column;
  width: 150px; 
  height: 150px;
  border: 1px solid #343330;
  border-radius: 8px;
  overflow: hidden;
  text-align: center;
  margin: px;
  transition: transform 0.3s ease-in-out;

}

.recipe-card:hover {
  transform: scale(1.05);
}

.recipe-image {
  width: 100%;
  height: 55%; /* Reducir la altura de la imagen */
  object-fit: cover;
}

.recipe-content {
  padding: 5px 10px; /* Reducir el padding entre contenido */
}

.recipe-content h2 {
  font-size: 14px; /* Fuente más pequeña */
  color: #343330;
  font-weight: bold;
  margin: 5px 0; /* Reducir el margen entre el título y la descripción */
}

.recipe-content p {
  font-size: 10px; /* Fuente más pequeña */
  color: #666;
  overflow: hidden; /* Para ocultar el exceso de texto */
  white-space: nowrap; /* Evitar que el texto se divida en varias líneas */
  text-overflow: ellipsis; /* Añadir los tres puntos */
  margin: 0; /* Eliminar margen extra alrededor de la descripción */
  padding: 0; /* Eliminar el padding en la parte inferior */
}
</style>
