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
      default: '', 
    },
    image: {
      type: String,
      required: true
    }
  },
  computed: {
    truncatedDescription() {
      const descriptionText = this.description || '';
      const words = descriptionText.split(' ');
      if (words.length > 15) {
        return words.slice(0, 15).join(' ') + '...';
      }
      return descriptionText;
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
  height: 55%; 
}

.recipe-content {
  padding: 5px 10px; 
}

.recipe-content h2 {
  font-size: 14px; 
  color: #343330;
  font-weight: bold;
  margin: 5px 0;
}

.recipe-content p {
  font-size: 10px; 
  color: #666;
  overflow: hidden; 
  white-space: nowrap; 
  text-overflow: ellipsis;
  margin: 0; 
  padding: 0; 
}
</style>
