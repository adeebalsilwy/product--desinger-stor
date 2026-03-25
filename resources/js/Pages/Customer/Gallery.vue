<script setup>
import CustomerLayout from '@/Layouts/Customer.vue';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';

const templates = ref([]);
const filterMode = ref('all');
const isLoading = ref(false);
const starsRef = ref(null);
const progress = ref(50);
const startX = ref(0);
const isDown = ref(false);
const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth);
const defaultProductType = computed(() => page.props.defaultProductType || 't-shirt');

const displayItems = computed(() => {
  const items = templates.value.length ? templates.value : [];
  const activeIndex = Math.floor((progress.value / 100) * (items.length - 1));
  const total = items.length || 1;
  return items.map((item, index) => {
    const zIndex = total - Math.abs(activeIndex - index);
    const active = (index - activeIndex) / total;
    return {
      ...item,
      style: {
        '--zIndex': zIndex,
        '--active': active
      }
    };
  });
});

const createStars = () => {
  if (!starsRef.value) return;
  starsRef.value.innerHTML = '';
  const starCount = 150;
  for (let i = 0; i < starCount; i += 1) {
    const star = document.createElement('div');
    star.classList.add('star');
    const size = Math.random() * 2 + 1;
    star.style.width = `${size}px`;
    star.style.height = `${size}px`;
    star.style.left = `${Math.random() * 100}%`;
    star.style.top = `${Math.random() * 100}%`;
    star.style.setProperty('--duration', `${Math.random() * 3 + 2}s`);
    star.style.setProperty('--opacity', Math.random() * 0.8 + 0.2);
    star.style.animationDelay = `${Math.random() * 5}s`;
    starsRef.value.appendChild(star);
  }
};

const animate = () => {
  if (!templates.value.length) return;
  progress.value = Math.max(0, Math.min(progress.value, 100));
};

const handleWheel = (e) => {
  if (!templates.value.length || templates.value.length <= 1) return; // Only allow navigation if more than 1 template
  const speedWheel = 0.02;
  progress.value += e.deltaY * speedWheel;
  animate();
};

const handleMouseMove = (e) => {
  if (e.type === 'mousemove') {
    const cursors = document.querySelectorAll('.cursor');
    cursors.forEach((cursor) => {
      cursor.style.transform = `translate(${e.clientX}px, ${e.clientY}px)`;
    });
  }
  if (!isDown.value) return;
  const x = e.clientX || (e.touches && e.touches[0].clientX) || 0;
  const speedDrag = -0.1;
  progress.value += (x - startX.value) * speedDrag;
  startX.value = x;
  animate();
};

const handleMouseDown = (e) => {
  isDown.value = true;
  startX.value = e.clientX || (e.touches && e.touches[0].clientX) || 0;
};

const handleMouseUp = () => {
  isDown.value = false;
};

const selectTemplate = (template) => {
  if (!template?.id) return;
  const productType = template.product_type?.slug || template.productType?.slug || defaultProductType.value;
  router.visit(route('designer.create', { productType }) + `?template=${template.id}`);
};

const navigateCarousel = (direction) => {
  if (!templates.value.length || templates.value.length <= 1) return;
  const step = 100 / (templates.value.length - 1);
  progress.value += direction * step;
  progress.value = Math.max(0, Math.min(progress.value, 100));
  animate();
};

const fetchTemplates = async () => {
  try {
    isLoading.value = true;
    const response = await axios.get('/gallery/templates', {
      params: {
        per_page: 5, // Only fetch first 5 templates
        page: 1,
        my_templates: filterMode.value === 'mine' ? 1 : 0
      }
    });
    const data = response.data?.data || [];
    templates.value = data.sort((a, b) => {
      const aName = (a.name || '').toLowerCase();
      const bName = (b.name || '').toLowerCase();
      return aName.localeCompare(bName);
    });
    progress.value = 50;
  } catch (error) {
    templates.value = [];
  } finally {
    isLoading.value = false;
  }
};

const setFilter = (mode) => {
  if (mode === 'mine' && !isAuthenticated.value) return;
  if (filterMode.value === mode) return;
  filterMode.value = mode;
  fetchTemplates();
};

onMounted(() => {
  createStars();
  fetchTemplates();
  animate();
  document.addEventListener('mousewheel', handleWheel);
  document.addEventListener('mousedown', handleMouseDown);
  document.addEventListener('mousemove', handleMouseMove);
  document.addEventListener('mouseup', handleMouseUp);
  document.addEventListener('touchstart', handleMouseDown);
  document.addEventListener('touchmove', handleMouseMove);
  document.addEventListener('touchend', handleMouseUp);
});

onUnmounted(() => {
  document.removeEventListener('mousewheel', handleWheel);
  document.removeEventListener('mousedown', handleMouseDown);
  document.removeEventListener('mousemove', handleMouseMove);
  document.removeEventListener('mouseup', handleMouseUp);
  document.removeEventListener('touchstart', handleMouseDown);
  document.removeEventListener('touchmove', handleMouseMove);
  document.removeEventListener('touchend', handleMouseUp);
});
</script>

<template>
  <CustomerLayout>
    <div class="gallery-page">
      <div class="stars-container" ref="starsRef"></div>
      <div class="gallery-header">
        <h1>{{ $t('gallery.title') }}</h1>
        <p>{{ $t('gallery.subtitle') }}</p>
        <div class="gallery-filters">
          <button
            class="filter-btn"
            :class="{ active: filterMode === 'all' }"
            @click="setFilter('all')"
          >
            {{ $t('gallery.all') }}
          </button>
          <button
            class="filter-btn"
            :class="{ active: filterMode === 'mine', disabled: !isAuthenticated }"
            :disabled="!isAuthenticated"
            @click="setFilter('mine')"
          >
            {{ $t('gallery.mine') }}
          </button>
        </div>
      </div>
      <div class="carousel" v-if="templates.length && !isLoading">
        <div
          v-for="(item, index) in displayItems"
          :key="item.id || index"
          class="carousel-item"
          :style="item.style"
          @click="selectTemplate(item)"
        >
          <div class="carousel-box">
            <div class="title">{{ item.name || $t('gallery.template') }}</div>
            <div class="num">{{ String(index + 1).padStart(2, '0') }}</div>
            <img :src="item.preview_url || '/images/placeholder-product.jpg'" :alt="item.name || 'template'" />
          </div>
        </div>
        <!-- Navigation Arrows -->
        <button 
          v-if="templates.length > 1"
          class="carousel-nav prev"
          @click="navigateCarousel(-1)"
          aria-label="Previous template"
        >
          ‹
        </button>
        <button 
          v-if="templates.length > 1"
          class="carousel-nav next"
          @click="navigateCarousel(1)"
          aria-label="Next template"
        >
          ›
        </button>
      </div>
      <div class="empty-state" v-else-if="isLoading">
        <h3>{{ $t('gallery.loading_title') }}</h3>
        <p>{{ $t('gallery.loading_text') }}</p>
      </div>
      <div class="empty-state" v-else>
        <h3>{{ $t('gallery.empty_title') }}</h3>
        <p>{{ $t('gallery.empty_text') }}</p>
      </div>
      <div class="gallery-grid" v-if="templates.length">
        <button
          v-for="template in templates"
          :key="`grid-${template.id}`"
          class="grid-card"
          @click="selectTemplate(template)"
        >
          <img :src="template.preview_url || '/images/placeholder-product.jpg'" :alt="template.name || 'template'" />
          <div class="grid-info">
            <h4>{{ template.name || $t('gallery.template') }}</h4>
          </div>
        </button>
      </div>
      <div class="cursor"></div>
      <div class="cursor cursor2"></div>
    </div>
  </CustomerLayout>
</template>

<style scoped>
.gallery-page {
  font-family: 'Arial', sans-serif;
  background: linear-gradient(135deg, #1e1f26, #3a3a5a);
  color: white;
  min-height: 100vh;
  overflow: hidden;
  position: relative;
}

.gallery-header {
  position: relative;
  z-index: 3;
  text-align: center;
  padding: 40px 20px 0;
}

.gallery-header h1 {
  font-size: 40px;
  margin-bottom: 8px;
}

.gallery-header p {
  color: rgba(255,255,255,0.7);
}

.gallery-filters {
  display: flex;
  justify-content: center;
  gap: 12px;
  margin-top: 16px;
}

.filter-btn {
  padding: 8px 18px;
  border-radius: 20px;
  border: 1px solid rgba(255,255,255,0.2);
  background: rgba(255,255,255,0.1);
  color: #ffffff;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-btn.active,
.filter-btn:hover {
  background: rgba(255,255,255,0.25);
}

.filter-btn.disabled,
.filter-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.stars-container {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
  pointer-events: none;
}

.star {
  position: absolute;
  background-color: #fff;
  border-radius: 50%;
  animation: twinkle var(--duration) infinite ease-in-out;
  opacity: 0;
}

@keyframes twinkle {
  0%, 100% {
    opacity: 0;
  }
  50% {
    opacity: var(--opacity);
  }
}

.carousel {
  position: relative;
  z-index: 2;
  height: 80vh;
  overflow: hidden;
}

/* Carousel Navigation */
.carousel-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  border: 2px solid rgba(255, 255, 255, 0.4);
  color: white;
  font-size: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(4px);
}

.carousel-nav:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.6);
  transform: translateY(-50%) scale(1.1);
}

.carousel-nav.prev {
  left: 20px;
}

.carousel-nav.next {
  right: 20px;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: rgba(255,255,255,0.8);
  z-index: 2;
  position: relative;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  padding: 0 20px 40px;
  z-index: 2;
  position: relative;
}

.grid-card {
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.15);
  border-radius: 14px;
  overflow: hidden;
  cursor: pointer;
  text-align: left;
  transition: all 0.2s ease;
}

.grid-card img {
  width: 100%;
  height: 160px;
  object-fit: cover;
}

.grid-info {
  padding: 10px 12px;
}

.grid-info h4 {
  font-size: 14px;
  color: rgba(255,255,255,0.9);
}

.carousel-item {
  --items: 10;
  --width: clamp(150px, 30vw, 300px);
  --height: clamp(200px, 40vw, 400px);
  --x: calc(var(--active) * 800%);
  --y: calc(var(--active) * 200%);
  --rot: calc(var(--active) * 120deg);
  --opacity: calc(var(--zIndex) / var(--items) * 3 - 2);
  overflow: hidden;
  position: absolute;
  z-index: var(--zIndex);
  width: var(--width);
  height: var(--height);
  margin: calc(var(--height) * -0.5) 0 0 calc(var(--width) * -0.5);
  border-radius: 10px;
  top: 55%;
  left: 50%;
  user-select: none;
  transform-origin: 0% 100%;
  box-shadow: 0 10px 50px 10px rgba(0, 0, 0, 0.5);
  background: black;
  pointer-events: all;
  transform: translate(var(--x), var(--y)) rotate(var(--rot));
  transition: transform 0.8s cubic-bezier(0, 0.02, 0, 1);
}

.carousel-box {
  position: absolute;
  z-index: 1;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transition: opacity 0.8s cubic-bezier(0, 0.02, 0, 1);
  opacity: var(--opacity);
}

.carousel-box::before {
  content: '';
  position: absolute;
  z-index: 1;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0) 30%, rgba(0, 0, 0, 0) 50%, rgba(0, 0, 0, 0.5));
}

.carousel-box .title {
  position: absolute;
  z-index: 1;
  color: #fff;
  bottom: 20px;
  left: 20px;
  transition: opacity 0.8s cubic-bezier(0, 0.02, 0, 1);
  font-size: clamp(20px, 3vw, 30px);
  text-shadow: 0 4px 4px rgba(0, 0, 0, 0.1);
}

.carousel-box .num {
  position: absolute;
  z-index: 1;
  color: #fff;
  top: 10px;
  left: 20px;
  transition: opacity 0.8s cubic-bezier(0, 0.02, 0, 1);
  font-size: clamp(20px, 10vw, 80px);
}

.carousel-box img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  pointer-events: none;
}

.cursor {
  position: fixed;
  z-index: 10;
  top: 0;
  left: 0;
  --size: 40px;
  width: var(--size);
  height: var(--size);
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.2);
  margin: calc(var(--size) * -0.5) 0 0 calc(var(--size) * -0.5);
  transition: transform 0.85s cubic-bezier(0, 0.02, 0, 1);
  display: none;
  pointer-events: none;
}

.cursor2 {
  --size: 2px;
  transition-duration: 0.7s;
}

@media (pointer: fine) {
  .cursor {
    display: block;
  }
}

@media (max-width: 768px) {
  .gallery-header h1 {
    font-size: 30px;
  }
}
</style>
