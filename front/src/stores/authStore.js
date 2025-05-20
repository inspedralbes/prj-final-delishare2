import { defineStore } from 'pinia';
import mitt from 'mitt';

export const eventBus = mitt();

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: null
  }),

  actions: {
    setAuth(token, user) {
      this.token = token;
      this.user = user;
      localStorage.setItem('token', token);
      
      // Forzar reactividad en tiempo real
      this.$patch({ token });

      eventBus.emit('authUpdated');
    },

    clearAuth() {
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
      this.$patch({ token: null });

      eventBus.emit('authUpdated');
    },

    checkAuth() {
      const storedToken = localStorage.getItem('token');
      if (storedToken) {
        this.token = storedToken;
      }
    }
  },

  getters: {
    isAuthenticated: (state) => !!state.token,
  }
});
