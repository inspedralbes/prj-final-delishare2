<template>
  <div>
    <button 
      @click="drawerOpen = true" 
      class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-lime-500 to-green-500 text-white shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
    <transition name="drawer">
      <div v-if="drawerOpen" class="fixed inset-0 bg-black/18 z-[1000] flex justify-end" @click.self="drawerOpen = false">
        <div class="bg-white w-[90vw] max-w-[400px] h-screen shadow-[-2px_0_16px_rgba(34,197,94,0.10)] rounded-l-[18px] p-8 px-6 relative flex flex-col animate-[slideInDrawer_0.3s_cubic-bezier(.4,0,.2,1)]">
          <button class="absolute top-5 right-5 bg-transparent border-none text-3xl text-[#22c55e] cursor-pointer z-10" @click="drawerOpen = false" aria-label="Tancar menú">&times;</button>
          <h2 class="text-2xl font-bold text-[#166534] mb-5 text-center">Panel d'Administració</h2>
          <div class="flex flex-col gap-2">
            <router-link to="/recetas" class="px-3 py-1.5 text-xs rounded font-semibold bg-blue-500 text-white hover:bg-blue-600 transition">Recetes</router-link>
            <router-link to="/categories" class="px-3 py-1.5 text-xs rounded font-semibold bg-green-500 text-white hover:bg-green-600 transition">Categories</router-link>
            <router-link to="/cuisines" class="px-3 py-1.5 text-xs rounded font-semibold bg-cyan-500 text-white hover:bg-cyan-600 transition">Cuines</router-link>
            <router-link to="/comments" class="px-3 py-1.5 text-xs rounded font-semibold bg-yellow-400 text-lime-900 hover:bg-yellow-500 transition">Comentaris</router-link>
            <router-link to="/users" class="px-3 py-1.5 text-xs rounded font-semibold bg-red-500 text-white hover:bg-red-600 transition">Usuaris</router-link>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'BotonesCrud',
  data() {
    return {
      drawerOpen: false
    };
  }
}
</script>

<style scoped>
.drawer-enter-active,
.drawer-leave-active {
  transition: opacity 0.3s ease;
}

.drawer-enter-from,
.drawer-leave-to {
  opacity: 0;
}

@keyframes slideInDrawer {
  from {
    transform: translateX(100%);
  }
  to {
    transform: translateX(0);
  }
}
</style>