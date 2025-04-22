<template>
  <div class="page-container">
    <header class="header">
      <img src="@/assets/images/delishare.png" alt="Logotip de DeliShare" class="header-logo" />
      <router-link to="/notifications" class="notification-bell">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
          <path d="M13.73 21a2 2 0 0 1-3.46 0" />
        </svg>
      </router-link>
    </header>

    <transition name="fade">
      <div v-if="popupMessage" class="popup-notification">
        {{ popupMessage }}
        <button @click="popupMessage = ''" class="close-popup">&times;</button>
      </div>
    </transition>

    <!-- Carrusel con transición -->
    <div class="carousel-container">
      <transition :name="carouselDirection" mode="out-in">
        <div :key="currentImage" class="carousel-slide">
          <img :src="carouselImages[currentImage]" alt="Imatge del carrusel" class="carousel-image" />
          <div class="carousel-caption">
            <h3>{{ carouselCaptions[currentImage] }}</h3>
          </div>
        </div>
      </transition>
      <div class="carousel-dots">
        <span v-for="(image, index) in carouselImages" 
              :key="index" 
              :class="{ active: currentImage === index }"
              @click="goToSlide(index)"></span>
      </div>
    </div>

    <div class="toggle-buttons">
      <button :class="{ active: activeTab === 'popular' }" @click="showPopularRecipes">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
        </svg>
        Més populars
      </button>
      <button :class="{ active: activeTab === 'recent' }" @click="showRecentRecipes">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
        Més recents
      </button>
      <button :class="{ active: activeTab === 'recommended' }" @click="showRecommendedRecipes">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.42 4.58a5.4 5.4 0 0 0-7.65 0l-.77.78-.77-.78a5.4 5.4 0 0 0-7.65 0C1.46 6.7 1.33 10.28 4 13l8 8 8-8c2.67-2.72 2.54-6.3.42-8.42z"></path>
        </svg>
        Per a tu
      </button>
    </div>

    <transition name="fade" mode="out-in">
      <div v-if="activeTab === 'popular'" class="tab-content">
        <section class="recipes-section">
          <h2 class="section-title">Més populars</h2>
          <div class="recipe-carousel-wrapper">
            <button class="carousel-nav prev" @click="scrollCarousel('popular', -1)" :disabled="popularScrollPos <= 0">
              &lt;
            </button>
            <div class="recipe-carousel-container" ref="popularCarousel">
              <div class="recipe-carousel" :style="{ transform: `translateX(-${popularScrollPos}px)` }">
                <RecipeCard v-for="(recipe, index) in sortedPopularRecipes" :key="'pop-'+index" 
                  :recipe-id="recipe.id"
                  :title="recipe.title" 
                  :description="recipe.description || 'Sense descripció disponible'"
                  :image="recipe.image"
                  :likes="recipe.likes_count"
                  class="recipe-card" />
              </div>
            </div>
            <button class="carousel-nav next" @click="scrollCarousel('popular', 1)" 
                    :disabled="popularScrollPos >= popularMaxScroll">
              &gt;
            </button>
          </div>
        </section>
      </div>

      <div v-else-if="activeTab === 'recent'" class="tab-content">
        <section class="recipes-section">
          <h2 class="section-title">Més recents</h2>
          <div class="recipe-carousel-wrapper">
            <button class="carousel-nav prev" @click="scrollCarousel('recent', -1)" :disabled="recentScrollPos <= 0">
              &lt;
            </button>
            <div class="recipe-carousel-container" ref="recentCarousel">
              <div class="recipe-carousel" :style="{ transform: `translateX(-${recentScrollPos}px)` }">
                <RecipeCard v-for="(recipe, index) in sortedRecentRecipes" :key="'rec-'+index" 
                  :recipe-id="recipe.id"
                  :title="recipe.title" 
                  :description="recipe.description || 'Sense descripció disponible'"
                  :image="recipe.image"
                  :date="recipe.created_at"
                  class="recipe-card" />
              </div>
            </div>
            <button class="carousel-nav next" @click="scrollCarousel('recent', 1)" 
                    :disabled="recentScrollPos >= recentMaxScroll">
              &gt;
            </button>
          </div>
        </section>
      </div>

      <div v-else class="tab-content">
        <section class="recipes-section">
          <h2 class="section-title">Recomanades per a tu</h2>
          <div v-if="recommendedRecipes.length > 0" class="recipe-carousel-wrapper">
            <button class="carousel-nav prev" @click="scrollCarousel('recommended', -1)" 
                    :disabled="recommendedScrollPos <= 0">
              &lt;
            </button>
            <div class="recipe-carousel-container" ref="recommendedCarousel">
              <div class="recipe-carousel" :style="{ transform: `translateX(-${recommendedScrollPos}px)` }">
                <RecipeCard v-for="(recipe, index) in recommendedRecipes" :key="'rec-'+index" 
                  :recipe-id="recipe.id"
                  :title="recipe.title" 
                  :description="recipe.description || 'Sense descripció disponible'"
                  :image="recipe.image"
                  class="recipe-card" />
              </div>
            </div>
            <button class="carousel-nav next" @click="scrollCarousel('recommended', 1)" 
                    :disabled="recommendedScrollPos >= recommendedMaxScroll">
              &gt;
            </button>
          </div>
          <div v-else class="no-recommendations">
            <p>No tens recomanacions personalitzades encara.</p>
            <router-link to="/preferences" class="preferences-link">
              Configura les teves preferències
            </router-link>
          </div>
        </section>
      </div>
    </transition>
  </div>
</template>

<script>
import communicationManager from '@/services/communicationManager';
import RecipeCard from '@/components/RecipeCard.vue';

export default {
  name: 'LandingPage',
  components: {
    RecipeCard
  },
  data() {
    return {
      recipes: [],
      recommendedRecipes: [],
      sortedPopularRecipes: [],
      sortedRecentRecipes: [],
      activeTab: 'popular',
      carouselImages: [
        new URL('@/assets/images/carrusel/image1.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image2.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image3.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image4.jpg', import.meta.url).href,
        new URL('@/assets/images/carrusel/image5.jpg', import.meta.url).href
      ],
      carouselCaptions: [
        "Descobreix noves receptes",
        "Comparteix les teves creacions",
        "Troba inspiració culinària",
        "Connecta amb altres foodies",
        "Millora les teves habilitats"
      ],
      currentImage: 0,
      carouselDirection: 'slide-next',
      popupMessage: '',
      popularScrollPos: 0,
      recentScrollPos: 0,
      recommendedScrollPos: 0,
      popularMaxScroll: 0,
      recentMaxScroll: 0,
      recommendedMaxScroll: 0,
      cardWidth: 300,
      visibleCards: 4
    };
  },
  created() {
    this.startCarousel();
    this.fetchAllRecipes();
    window.addEventListener('resize', this.calculateMaxScroll);
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.calculateMaxScroll);
  },
  methods: {
    startCarousel() {
      this.carouselInterval = setInterval(() => {
        this.carouselDirection = 'slide-next';
        this.currentImage = (this.currentImage + 1) % this.carouselImages.length;
      }, 5000);
    },
    goToSlide(index) {
      this.carouselDirection = index > this.currentImage ? 'slide-next' : 'slide-prev';
      this.currentImage = index;
      clearInterval(this.carouselInterval);
      this.startCarousel();
    },
    async fetchAllRecipes() {
      try {
        const data = await communicationManager.fetchRecipes();
        this.recipes = data;
        this.sortedPopularRecipes = [...this.recipes].sort((a, b) => b.likes_count - a.likes_count);
        this.sortedRecentRecipes = [...this.recipes].sort(
          (a, b) => new Date(b.created_at) - new Date(a.created_at)
        );
        this.$nextTick(() => {
          this.calculateMaxScroll();
        });
      } catch (error) {
        console.error('Error fetching recipes:', error);
        this.popupMessage = 'Error al cargar las recetas';
      }
    },
    async fetchRecommendedRecipes() {
      try {
        const response = await communicationManager.getRecommendedRecipes();
        this.recommendedRecipes = response.recipes || [];
        this.$nextTick(() => {
          this.calculateMaxScroll();
        });
      } catch (error) {
        console.error('Error fetching recommended recipes:', error);
        this.popupMessage = 'Error al cargar recomendaciones';
        this.recommendedRecipes = [];
      }
    },
    showPopularRecipes() {
      this.activeTab = 'popular';
    },
    showRecentRecipes() {
      this.activeTab = 'recent';
    },
    async showRecommendedRecipes() {
      this.activeTab = 'recommended';
      if (this.recommendedRecipes.length === 0) {
        await this.fetchRecommendedRecipes();
      }
    },
    scrollCarousel(type, direction) {
      const scrollAmount = this.cardWidth * this.visibleCards * direction;
      
      if (type === 'popular') {
        this.popularScrollPos = Math.max(0, Math.min(this.popularScrollPos + scrollAmount, this.popularMaxScroll));
      } else if (type === 'recent') {
        this.recentScrollPos = Math.max(0, Math.min(this.recentScrollPos + scrollAmount, this.recentMaxScroll));
      } else {
        this.recommendedScrollPos = Math.max(0, Math.min(this.recommendedScrollPos + scrollAmount, this.recommendedMaxScroll));
      }
    },
    calculateMaxScroll() {
      if (this.$refs.popularCarousel) {
        const containerWidth = this.$refs.popularCarousel.offsetWidth;
        const contentWidth = this.cardWidth * this.sortedPopularRecipes.length;
        this.popularMaxScroll = Math.max(0, contentWidth - containerWidth);
      }
      
      if (this.$refs.recentCarousel) {
        const containerWidth = this.$refs.recentCarousel.offsetWidth;
        const contentWidth = this.cardWidth * this.sortedRecentRecipes.length;
        this.recentMaxScroll = Math.max(0, contentWidth - containerWidth);
      }
      
      if (this.$refs.recommendedCarousel && this.recommendedRecipes.length > 0) {
        const containerWidth = this.$refs.recommendedCarousel.offsetWidth;
        const contentWidth = this.cardWidth * this.recommendedRecipes.length;
        this.recommendedMaxScroll = Math.max(0, contentWidth - containerWidth);
      }
    }
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

:root {
  --primary-color: #6C5CE7;
  --secondary-color: #A29BFE;
  --dark-color: #2D3436;
  --light-color: #F5F6FA;
  --accent-color: #FD79A8;
  --success-color: #00B894;
  --warning-color: #FDCB6E;
  --danger-color: #D63031;
  --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  --transition: all 0.3s ease;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: 'Poppins', sans-serif;
}

.page-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
  background-color: var(--light-color);
  min-height: 100vh;
}

.header {
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  padding: 20px 0;
}

.header-logo {
  width: 220px;
  height: auto;
  transition: var(--transition);
}

.notification-bell {
  position: absolute;
  right: 20px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--dark-color);
  cursor: pointer;
  transition: var(--transition);
}

.notification-bell:hover {
  color: var(--primary-color);
  transform: translateY(-50%) scale(1.1);
}

/* Carrusel principal */
.carousel-container {
  position: relative;
  width: 100%;
  height: 400px;
  overflow: hidden;
  border-radius: 16px;
  margin: 30px 0;
  box-shadow: var(--shadow);
}

.carousel-slide {
  position: absolute;
  width: 100%;
  height: 100%;
}

.carousel-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.carousel-caption {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0,0,0,0.7));
  padding: 30px;
  color: white;
  text-align: left;
}

.carousel-caption h3 {
  font-size: 2rem;
  margin-bottom: 10px;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.carousel-dots {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 10px;
}

.carousel-dots span {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background-color: rgba(255,255,255,0.5);
  cursor: pointer;
  transition: var(--transition);
}

.carousel-dots span.active {
  background-color: white;
  transform: scale(1.2);
}

/* Transiciones del carrusel */
.slide-next-enter-active,
.slide-next-leave-active,
.slide-prev-enter-active,
.slide-prev-leave-active {
  transition: all 0.5s ease;
}

.slide-next-enter-from {
  transform: translateX(100%);
}
.slide-next-leave-to {
  transform: translateX(-100%);
}

.slide-prev-enter-from {
  transform: translateX(-100%);
}
.slide-prev-leave-to {
  transform: translateX(100%);
}

/* Botones de toggle */
.toggle-buttons {
  display: flex;
  justify-content: center;
  margin: 40px 0;
  gap: 15px;
  flex-wrap: wrap;
}

.toggle-buttons button {
  display: flex;
  align-items: center;
  gap: 8px;
  background: white;
  color: var(--dark-color);
  border: 2px solid var(--secondary-color);
  padding: 12px 25px;
  cursor: pointer;
  border-radius: 50px;
  font-size: 16px;
  font-weight: 600;
  transition: var(--transition);
  box-shadow: var(--shadow);
}

.toggle-buttons button svg {
  transition: var(--transition);
}

.toggle-buttons button.active {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(108, 92, 231, 0.3);
}

.toggle-buttons button.active svg {
  stroke: white;
}

.toggle-buttons button:hover {
  background: var(--secondary-color);
  color: white;
  border-color: var(--secondary-color);
}

.toggle-buttons button:hover svg {
  stroke: white;
}

/* Sección de recetas */
.tab-content {
  margin: 30px 0;
}

.recipes-section {
  margin-bottom: 50px;
}

.section-title {
  color: var(--dark-color);
  margin-bottom: 25px;
  font-size: 1.8rem;
  position: relative;
  display: inline-block;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 4px;
  background: var(--primary-color);
  border-radius: 2px;
}

.recipe-carousel-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.carousel-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 40px;
  height: 40px;
  background: white;
  border: none;
  border-radius: 50%;
  font-size: 20px;
  font-weight: bold;
  color: var(--primary-color);
  cursor: pointer;
  box-shadow: var(--shadow);
  z-index: 10;
  transition: var(--transition);
  display: flex;
  align-items: center;
  justify-content: center;
}

.carousel-nav:hover {
  background: var(--primary-color);
  color: white;
}

.carousel-nav:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background: #ddd;
  color: #777;
}

.carousel-nav.prev {
  left: -20px;
}

.carousel-nav.next {
  right: -20px;
}

.recipe-carousel-container {
  width: 100%;
  overflow: hidden;
  padding: 10px 0;
}

.recipe-carousel {
  display: flex;
  gap: 20px;
  transition: transform 0.5s ease;
}

.recipe-card {
  flex: 0 0 280px;
}

/* Sin recomendaciones */
.no-recommendations {
  text-align: center;
  padding: 40px;
  background: white;
  border-radius: 16px;
  box-shadow: var(--shadow);
}

.empty-state-img {
  width: 200px;
  height: auto;
  margin-bottom: 20px;
}

.no-recommendations p {
  color: var(--dark-color);
  font-size: 1.1rem;
  margin-bottom: 20px;
}

.preferences-link {
  display: inline-block;
  padding: 12px 25px;
  background: var(--primary-color);
  color: white;
  text-decoration: none;
  border-radius: 50px;
  font-weight: 500;
  transition: var(--transition);
  box-shadow: var(--shadow);
}

.preferences-link:hover {
  background: var(--secondary-color);
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(162, 155, 254, 0.3);
}

/* Popup notification */
.popup-notification {
  position: fixed;
  top: 30px;
  right: 30px;
  padding: 15px 25px;
  background-color: var(--success-color);
  color: white;
  border-radius: 8px;
  z-index: 1000;
  box-shadow: var(--shadow);
  display: flex;
  align-items: center;
  gap: 10px;
}

.close-popup {
  background: none;
  border: none;
  color: white;
  font-size: 1.2rem;
  cursor: pointer;
  margin-left: 10px;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}

/* Responsive */
@media (max-width: 1024px) {
  .carousel-container {
    height: 350px;
  }
  
  .carousel-caption h3 {
    font-size: 1.5rem;
  }
  
  .recipe-card {
    flex: 0 0 240px;
  }
}

@media (max-width: 768px) {
  .header-logo {
    width: 180px;
  }
  
  .carousel-container {
    height: 300px;
  }
  
  .carousel-caption {
    padding: 20px;
  }
  
  .carousel-caption h3 {
    font-size: 1.3rem;
  }
  
  .toggle-buttons {
    gap: 10px;
  }
  
  .toggle-buttons button {
    padding: 10px 15px;
    font-size: 14px;
  }
  
  .recipe-card {
    flex: 0 0 200px;
  }
  
  .carousel-nav {
    width: 30px;
    height: 30px;
    font-size: 16px;
  }
}

@media (max-width: 576px) {
  .header-logo {
    width: 150px;
  }
  
  .carousel-container {
    height: 250px;
    margin: 20px 0;
  }
  
  .carousel-caption {
    padding: 15px;
  }
  
  .carousel-caption h3 {
    font-size: 1.1rem;
  }
  
  .toggle-buttons {
    flex-direction: column;
    align-items: center;
  }
  
  .toggle-buttons button {
    width: 100%;
    max-width: 250px;
    justify-content: center;
  }
  
  .recipe-card {
    flex: 0 0 85vw;
  }
  
  .carousel-nav {
    display: none;
  }
  
  .no-recommendations {
    padding: 30px 20px;
  }
  
  .empty-state-img {
    width: 150px;
  }
}
</style>