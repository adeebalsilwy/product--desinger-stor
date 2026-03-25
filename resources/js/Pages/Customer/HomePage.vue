<script setup>
import CustomerLayout from '@/Layouts/Customer.vue';
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AhlamsHeroSection from '@/Components/Customer/AhlamsHeroSection.vue';
import ProductShowcase3D from '@/Components/ProductShowcase3D.vue';

const page = usePage();

// Get featured products from props (passed from controller)
const featuredProducts = computed(() => page.props.featuredProducts || []);
const defaultProductType = computed(() => page.props.defaultProductType || 't-shirt');
const loading = ref(false); // No longer needed since data comes from server
const activeCategory = ref('all');
const testimonials = ref([
  {
    quote: "The quality and elegance of Ahlam's Girls exceeded my expectations. Every piece feels like a work of art.",
    name: "Sarah Johnson",
    role: "Fashion Enthusiast"
  },
  {
    quote: "I've never felt more confident and beautiful than when wearing Ahlam's Girls creations. Simply exquisite!",
    name: "Emily Chen",
    role: "Customer"
  },
  {
    quote: "The attention to detail and craftsmanship is remarkable. This is luxury fashion at its finest.",
    name: "Maria Rodriguez",
    role: "Style Influencer"
  }
]);

const addToWishlist = (productId) => {
    console.log('Adding to wishlist:', productId);
};

// Neumorphism colors matching PHP design
const neumorphismColors = {
    bg: '#e0e5ec',
    shadowLight: '#ffffff',
    shadowDark: '#a3b1c6',
    primary: '#4a7eff',
    text: '#4a5568'
};
</script>

<template>
  <CustomerLayout>
    <!-- Hero Section with Neumorphic Design -->
    <section class="neumorphic-hero">
      <div class="stars-container" id="stars-container"></div>
      
      <div class="hero-content">
        <div class="welcome-section">
          <h1 class="hero-title">{{ $t('home.hero_title') }}</h1>
          <p class="hero-description">
            {{ $t('home.hero_subtitle') }}
          </p>
          
          <div class="ind">
            <h1 class="designs-title">{{ $t('home.designs_title') }}</h1>
          </div>
        </div>

        <div class="categories-grid">
          <Link :href="route('products.index')" class="neumorphic-box">
            <div class="icon"><i class="fas fa-tags"></i></div>
            <h3>{{ $t('home.categories.products') }}</h3>
          </Link>

          <Link :href="route('designer.create', { productType: defaultProductType })" class="neumorphic-box">
            <div class="icon"><i class="fas fa-pencil-ruler"></i></div>
            <h3>{{ $t('home.categories.designer') }}</h3>
          </Link>
          
          <Link :href="route('about')" class="neumorphic-box">
            <div class="icon"><i class="fas fa-info-circle"></i></div>
            <h3>{{ $t('home.categories.about') }}</h3>
          </Link>
          
          <Link :href="route('home')" class="neumorphic-box">
            <div class="icon"><i class="fas fa-home"></i></div>
            <h3>{{ $t('home.categories.home') }}</h3>
          </Link>
        </div>
      </div>
    </section>

    <section class="home-section">
      <div class="section-header">
        <h2 class="section-title">{{ $t('home.sections.featured_title') }}</h2>
        <p class="section-subtitle">{{ $t('home.sections.featured_subtitle') }}</p>
      </div>
      <div class="featured-grid">
        <Link
          v-for="product in featuredProducts"
          :key="product.id"
          :href="route('product.page', { slug: product.slug })"
          class="featured-card"
        >
          <div class="featured-image">
            <img :src="product.image_url" :alt="product.name" />
          </div>
          <div class="featured-body">
            <h3>{{ product.name }}</h3>
            <p>{{ product.description }}</p>
            <span class="featured-price">{{ product.price }}</span>
          </div>
        </Link>
      </div>
    </section>

    <section class="home-section alt-section">
      <div class="section-header">
        <h2 class="section-title">{{ $t('home.sections.templates_title') }}</h2>
        <p class="section-subtitle">{{ $t('home.sections.templates_subtitle') }}</p>
      </div>
      <div class="template-showcase">
        <div class="template-card" v-for="(item, index) in featuredProducts.slice(0, 4)" :key="`template-${index}`">
          <img :src="item.image_url" :alt="item.name" />
          <div class="template-overlay">
            <h3>{{ item.name }}</h3>
            <Link :href="route('designer.create', { productType: defaultProductType, product: item.slug })" class="template-action">
              {{ $t('home.sections.templates_cta') }}
            </Link>
          </div>
        </div>
      </div>
    </section>

    <section class="home-section">
      <div class="section-header">
        <h2 class="section-title">{{ $t('home.sections.testimonials_title') }}</h2>
        <p class="section-subtitle">{{ $t('home.sections.testimonials_subtitle') }}</p>
      </div>
      <div class="testimonials-grid">
        <div class="testimonial-card" v-for="(item, index) in testimonials" :key="`testimonial-${index}`">
          <p class="testimonial-quote">“{{ item.quote }}”</p>
          <div class="testimonial-author">
            <span class="author-name">{{ item.name }}</span>
            <span class="author-role">{{ item.role }}</span>
          </div>
        </div>
      </div>
    </section>

  </CustomerLayout>
</template>

<style scoped>
/* Neumorphic Hero Section */
.neumorphic-hero {
  background: linear-gradient(135deg, #e0e5ec 0%, #c9d6ff 100%);
  min-height: 100vh;
  padding: 50px;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

body.night-mode .neumorphic-hero {
  background: linear-gradient(135deg, #1e1e1e 0%, #3a3a5a 100%);
}

.stars-container {
  position: absolute;
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
  0%, 100% { opacity: 0; }
  50% { opacity: var(--opacity); }
}

body.night-mode .star {
  background-color: #03dac6;
}

.hero-content {
  display: flex;
  gap: 50px;
  position: relative;
  z-index: 2;
  width: 100%;
  max-width: 1400px;
}

.welcome-section {
  flex: 1;
  padding-top: 50px;
}

.hero-title {
  font-family: 'Dancing Script', cursive;
  font-size: 48px;
  color: #4a5568;
  margin-bottom: 20px;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

body.night-mode .hero-title {
  color: #ffffff;
}

.hero-description {
  font-size: 18px;
  color: #4a5568;
  line-height: 1.6;
  margin-bottom: 30px;
}

body.night-mode .hero-description {
  color: #ffffff;
}

.ind h1.designs-title {
  font-size: 30px;
  color: #c9d6ff;
  text-transform: uppercase;
  text-shadow: 1px 1px 1px #919191,
               1px 2px 1px #919191,
               1px 3px 1px #919191,
               1px 4px 1px #919191,
               1px 5px 1px #919191,
               1px 6px 1px #919191,
               1px 7px 1px #919191,
               1px 8px 1px #919191,
               1px 9px 1px #919191,
               1px 10px 1px #919191,
               1px 18px 6px rgba(16,16,16,0.4),
               1px 22px 10px rgba(16,16,16,0.2),
               1px 25px 35px rgba(16,16,16,0.2),
               1px 30px 60px rgba(16,16,16,0.4);
  transition: all 0.4s ease;
  cursor: default;
}

.ind h1.designs-title:hover {
  color: #c9d6ff;
  transform: scale(1.2);
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  width: 400px;
}

.neumorphic-box {
  background: #e0e5ec;
  border: none;
  padding: 15px 30px;
  border-radius: 15px;
  box-shadow:
    8px 8px 15px #a3b1c6,
    -8px -8px 15px #ffffff;
  color: #6d7587;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100px;
  text-decoration: none;
}

.neumorphic-box:hover {
  box-shadow:
    inset 4px 4px 8px #a3b1c6,
    inset -4px -4px 8px #ffffff;
  transform: translateY(-2px);
}

body.night-mode .neumorphic-box {
  background: #2d3748;
  color: #ffffff !important;
  box-shadow:
    8px 8px 15px #1a202c,
    -8px -8px 15px #4a5568;
}

body.night-mode .neumorphic-box:hover {
  box-shadow:
    inset 4px 4px 8px #1a202c,
    inset -4px -4px 8px #4a5568;
}

.icon {
  font-size: 24px;
  margin-bottom: 10px;
  color: #6d7587;
  transition: all 0.3s ease;
}

body.night-mode .icon {
  color: #ffffff;
}

.neumorphic-box:hover .icon {
  transform: scale(1.1);
  color: #4a7eff;
}

.home-section {
  padding: 80px 24px;
  background: #e0e5ec;
}

.alt-section {
  background: linear-gradient(135deg, #e0e5ec 0%, #c9d6ff 100%);
}

.section-header {
  text-align: center;
  margin-bottom: 40px;
}

.section-title {
  font-family: 'Dancing Script', cursive;
  font-size: 42px;
  color: #4a7eff;
  margin-bottom: 10px;
}

.section-subtitle {
  color: #4a5568;
  font-size: 18px;
}

.featured-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

.featured-card {
  background: #e0e5ec;
  border-radius: 20px;
  box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff;
  overflow: hidden;
  text-decoration: none;
  color: inherit;
  transition: all 0.3s ease;
}

.featured-card:hover {
  box-shadow: inset 4px 4px 8px #a3b1c6, inset -4px -4px 8px #ffffff;
  transform: translateY(-4px);
}

.featured-image img {
  width: 100%;
  height: 220px;
  object-fit: cover;
}

.featured-body {
  padding: 20px;
}

.featured-body h3 {
  font-size: 20px;
  color: #4a5568;
  margin-bottom: 8px;
}

.featured-body p {
  font-size: 14px;
  color: #6d7587;
  margin-bottom: 12px;
  min-height: 40px;
}

.featured-price {
  font-weight: bold;
  color: #4a7eff;
}

.template-showcase {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

.template-card {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff;
}

.template-card img {
  width: 100%;
  height: 260px;
  object-fit: cover;
}

.template-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.65), rgba(0,0,0,0));
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 20px;
  color: #ffffff;
}

.template-action {
  margin-top: 10px;
  align-self: flex-start;
  background: #e0e5ec;
  color: #4a7eff;
  padding: 8px 16px;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
}

.testimonials-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 24px;
  max-width: 1100px;
  margin: 0 auto;
}

.testimonial-card {
  background: #e0e5ec;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff;
}

.testimonial-quote {
  color: #4a5568;
  font-size: 15px;
  line-height: 1.6;
}

.testimonial-author {
  margin-top: 16px;
  display: flex;
  flex-direction: column;
}

.author-name {
  font-weight: 600;
  color: #4a7eff;
}

.author-role {
  color: #6d7587;
  font-size: 13px;
}

body.night-mode .home-section {
  background: #2d3748;
}

body.night-mode .section-title,
body.night-mode .featured-price,
body.night-mode .author-name {
  color: #6ea8ff;
}

body.night-mode .section-subtitle,
body.night-mode .featured-body h3,
body.night-mode .featured-body p,
body.night-mode .testimonial-quote,
body.night-mode .author-role {
  color: #e2e8f0;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .hero-content {
    flex-direction: column;
    align-items: center;
  }
  
  .welcome-section {
    text-align: center;
  }
  
  .categories-grid {
    width: 100%;
    max-width: 400px;
  }
}

@media (max-width: 768px) {
  .neumorphic-hero {
    padding: 30px 20px;
  }
  
  .hero-title {
    font-size: 36px;
  }
  
  .categories-grid {
    grid-template-columns: 1fr;
  }
}
</style>
