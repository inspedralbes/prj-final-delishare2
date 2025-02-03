<template>
  <div class="filter-buttons">
    <button @click="obtenerCategorias">Categoría</button>
    <div v-if="datos.length">
      <button v-for="dato in datos" :key="dato.id" class="button-secondary">
        {{ dato.name }}
      </button>
    </div>

    <button @click="obtenerCuisines">Cuisine</button>
    <div v-if="countries.length">
      <button v-for="country in countries" :key="country.id" class="button-secondary">
        {{ country.country }}
      </button>
    </div>

    <button @click="obtenerTiempos">Tiempo</button>
    <div v-if="tiempos.length">
      <button v-for="tiempo in tiempos" :key="tiempo" class="button-secondary">
        {{ tiempo }} <!-- Mostramos el tiempo -->
      </button>
    </div>

    <button @click="obtenerUsuarios">Usuario</button>
    <div v-if="usuarios.length">
      <!-- Muestra el nombre de cada usuario -->
      <button v-for="usuario in usuarios" :key="usuario.id" class="button-secondary">
        {{ usuario.name }}
      </button>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import communicationManager from '../services/communicationManager';

export default {
  name: 'Botones',
  setup() {
    const datos = ref([]);
    const countries = ref([]);
    const usuarios = ref([]);
    const tiempos = ref([]);

    // Obtener categorías
    const obtenerCategorias = async () => {
      try {
        datos.value = await communicationManager.fetchCategories();
        console.log(datos.value);
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    };

    // Obtener cocinas
    const obtenerCuisines = async () => {
      try {
        countries.value = await communicationManager.fetchCuisines();
        console.log(countries.value);
      } catch (error) {
        console.error('Error fetching cuisines:', error);
      }
    };

    // Obtener usuarios
    const obtenerUsuarios = async () => {
      try {
        usuarios.value = await communicationManager.fetchUsers(); // Asegúrate de que esta función existe
        console.log(usuarios.value);  // Opcional: Verifica que los usuarios se estén obteniendo correctamente
      } catch (error) {
        console.error('Error fetching users:', error);
      }
    };

    // Obtener tiempos
    const obtenerTiempos = async () => {
      try {
        tiempos.value = await communicationManager.fetchAllTimes(); // Actualizado a 'fetchAllTimes'
        console.log(tiempos.value);
      } catch (error) {
        console.error('Error fetching times:', error);
      }
    };

    return {
      datos,
      countries,
      usuarios,
      tiempos,
      obtenerCategorias,
      obtenerCuisines,
      obtenerUsuarios,
      obtenerTiempos,
    };
  },
};
</script>

<style>
.filter-buttons {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

button {
  padding: 12px;
  background-color: #f1f1f1;
  border: 1px solid #ccc;
  cursor: pointer;
  font-size: 1rem;
}

button:hover {
  background-color: #e0e0e0;
}

.button-secondary {
  font-size: 0.75rem;
  padding: 6px 10px;
  background-color: #dcdcdc;
  border: 1px solid #bbb;
  color: #333;
  cursor: pointer;
  border-radius: 100px;
}

.button-secondary:hover {
  background-color: #c4c4c4;
}

div {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

div button {
  margin: 5px 0;
  display: inline-block;
}
</style>
