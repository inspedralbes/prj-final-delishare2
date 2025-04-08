<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-4">Cuisines</h1>
    <ul class="mb-8 list-disc pl-5">
      <li v-for="cuisine in cuisines" :key="cuisine.id">
        {{ cuisine.country }}
      </li>
    </ul>

    <h1 class="text-3xl font-bold mb-4">Categories</h1>
    <ul class="list-disc pl-5">
      <li v-for="category in categories" :key="category.id">
        {{ category.name }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import communicationManager from '@/services/communicationManager'

const cuisines = ref([])
const categories = ref([])

const fetchData = async () => {
  try {
    const [cuisinesData, categoriesData] = await Promise.all([
      communicationManager.fetchCuisines(),
      communicationManager.getCategories()
    ])
    
    cuisines.value = cuisinesData
    categories.value = categoriesData
  } catch (error) {
    console.error('Error loading cuisines or categories:', error)
  }
}

onMounted(fetchData)
</script>

<style scoped>
.p-6 {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1.5rem;
}

.text-3xl {
  font-size: 1.875rem;
  line-height: 2.25rem;
  color: #2d3748;
  margin-bottom: 1rem;
}

.list-disc {
  list-style-type: disc;
  padding-left: 1.25rem;
  margin-top: 1rem;
}

.list-disc li {
  margin-bottom: 0.5rem;
  color: #4a5568;
  font-size: 1.125rem;
}
</style>
