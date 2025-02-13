// src/stores/authStore.js
import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: null,
  }),
  
  actions: {
    setAuth(token, user) {
      this.token = token;
      this.user = user;
      localStorage.setItem('token', token);
    },

    clearAuth() {
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
    },
  },

  getters: {
    isAuthenticated: (state) => !!state.token,
  }
});
