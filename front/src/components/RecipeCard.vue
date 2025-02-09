<template>
  <article class="recipe-card" @click="handleClick">
    <img :src="image" :alt="title" class="recipe-image" />
    <div class="recipe-info">
      <p class="recipe-title">{{ title }}</p>
    </div>
  </article>
</template>

<script>
export default {
  name: 'RecipeCard',
  props: {
    recipeId: {
      type: [String, Number],
      required: true
    },
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      default: ''
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
  width: 100%;
  max-width: 150px;
  height: 160px;
  display: flex;
  flex-direction: column;
  align-items: center;
  border: 2px solid #2f8394;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease-in-out;
  background: white;
  cursor: pointer;
}

.recipe-card:hover {
  transform: scale(1.05);
}

.recipe-image {
  width: 100%;
  height: 130px;
  object-fit: cover;
}

.recipe-info {
  text-align: center;
  padding: 2px;
  width: 100%;
  background-color: #f4f4f4;
}

.recipe-title {
  font-weight: bold;
  font-size: 14px;
  margin: 0;
  color: #343330;
}

/* Tablets */
@media (min-width: 600px) {
  .recipe-card {
    max-width: 180px;
    height: 180px;
  }
  .recipe-image {
    height: 140px;
  }
  .recipe-title {
    font-size: 15px;
  }
}

/* Escritorio */
@media (min-width: 1024px) {
  .recipe-card {
    max-width: 200px;
    height: 220px;
  }
  .recipe-image {
    height: 170px;
  }
  .recipe-title {
    font-size: 16px;
  }
}
</style>
