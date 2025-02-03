<template>
    <article class="recipe-card" @click="handleClick" style="cursor: pointer;">
      <img :src="image" :alt="title" class="recipe-image" />
      <div class="recipe-content">
        <h2>{{ title }}</h2>
        <p>{{ description }}</p>
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
    width: 140px; /* Ancho más reducido */
    border: 1px solid #343330;
    border-radius: 8px;
    overflow: hidden;
    text-align: center;
    margin: 10px;
    transition: transform 0.3s ease-in-out;
  }
  
  .recipe-card:hover {
    transform: scale(1.05);
  }
  
  .recipe-image {
    width: 100%;
    height: 60%;
    object-fit: cover;
  }
  
  .recipe-content {
    padding: 10px;
  }
  
  .recipe-content h2 {
    font-size: 14px; /* Fuente más pequeña */
    color: #343330;
    font-weight: bold;
  }
  
  .recipe-content p {
    font-size: 10px; /* Fuente más pequeña */
    color: #666;
  }
  </style>
  