<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import CustomerLayout from '@/Layouts/Customer.vue';
import ProductCard from '@/Components/Customer/ProductCard.vue';

const props = defineProps({
    products: Object,
});

const searchQuery = ref('');
const selectedCategory = ref('all');

// Filter products based on search and category
const filteredProducts = computed(() => {
    let filtered = props.products.data || [];
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(product => 
            product.name.toLowerCase().includes(query) ||
            product.description.toLowerCase().includes(query)
        );
    }
    
    if (selectedCategory.value !== 'all') {
        filtered = filtered.filter(product => 
            product.product_type_id == selectedCategory.value
        );
    }
    
    return filtered;
});

const categories = computed(() => {
    const types = {};
    (props.products.data || []).forEach(product => {
        if (product.productType) {
            types[product.productType.id] = product.productType.name;
        }
    });
    return types;
});

const getTemplateCategoryId = () => {
    // Find the category that has template-based products
    const templateProduct = (props.products.data || []).find(p => p.is_template_based);
    return templateProduct?.product_type_id || 'all';
};

const hasRealModelImage = ref(true);

const useCssMannequin = () => {
    hasRealModelImage.value = false;
};
</script>

<template>
    <CustomerLayout>
        <div class="products-page-container">
            <!-- Hero Section with 3D Model -->
            <div class="products-hero">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                        <!-- Text Content -->
                        <div class="text-center lg:text-right">
                            <h1 class="products-title mb-4">
                                {{ $t('products.title') }}
                            </h1>
                            <p class="products-subtitle">
                                {{ $t('products.subtitle') }}
                            </p>
                            <div class="mt-6 flex justify-center lg:justify-end gap-4">
                                <button
                                    @click="selectedCategory = 'all'"
                                    class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-semibold hover:shadow-lg transition-all transform hover:-translate-y-1"
                                >
                                    <i class="pi pi-th-large mr-2"></i>
                                    {{ $t('products.filter_all') }}
                                </button>
                                <button
                                    @click="selectedCategory = getTemplateCategoryId()"
                                    class="px-6 py-3 bg-gradient-to-r from-gold-500 to-rose-400 text-white rounded-lg font-semibold hover:shadow-lg transition-all transform hover:-translate-y-1"
                                >
                                    <i class="pi pi-star-fill mr-2"></i>
                                    Templates
                                </button>
                            </div>
                        </div>
                        
                        <!-- 3D Model Display -->
                        <div class="relative">
                            <!-- Use actual image if available, otherwise use CSS mannequin -->
                            <div class="model-showcase-container" v-if="hasRealModelImage">
                                <div class="model-background-glow"></div>
                                <img
                                    src="/images/3d-girl-model-professional.png"
                                    alt="Professional 3D Girl Model"
                                    class="model-3d-professional"
                                    @error="useCssMannequin"
                                />
                                <div class="model-base-platform"></div>
                            </div>
                            
                            <!-- CSS-Only Professional Mannequin (Fallback) -->
                            <div v-else class="css-mannequin-wrapper">
                                <div class="css-mannequin-container">
                                    <div class="mannequin-head"></div>
                                    <div class="mannequin-torso">
                                        <div class="torso-highlight"></div>
                                    </div>
                                    <div class="mannequin-arms">
                                        <div class="arm left-arm"></div>
                                        <div class="arm right-arm"></div>
                                    </div>
                                    <div class="mannequin-waist"></div>
                                </div>
                                <div class="css-model-glow"></div>
                                <div class="css-base-platform"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="neumorphic-card search-section">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-brand-primary mb-2">
                                {{ $t('products.search_label') }}
                            </label>
                            <div class="relative">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    :placeholder="$t('products.search_placeholder')"
                                    class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all neumorphic-input"
                                />
                                <i class="pi pi-search absolute right-3 top-3.5 text-brand-secondary"></i>
                            </div>
                        </div>
                        
                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-brand-primary mb-2">
                                {{ $t('products.filter_label') }}
                            </label>
                            <select
                                v-model="selectedCategory"
                                class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all neumorphic-select"
                            >
                                <option value="all">{{ $t('products.filter_all') }}</option>
                                <option 
                                    v-for="(name, id) in categories" 
                                    :key="id" 
                                    :value="id"
                                >
                                    {{ name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div v-if="filteredProducts.length > 0" class="products-grid">
                    <ProductCard
                        v-for="product in filteredProducts"
                        :key="product.id"
                        :product="product"
                    />
                </div>

                <!-- No Products Found -->
                <div v-else class="empty-state-container">
                    <div class="neumorphic-card empty-state">
                        <i class="pi pi-search text-5xl text-brand-secondary mb-4"></i>
                        <h3 class="empty-title">
                            {{ $t('products.no_results_title') }}
                        </h3>
                        <p class="empty-text">
                            {{ $t('products.no_results_text') }}
                        </p>
                        <button
                            @click="searchQuery = ''; selectedCategory = 'all'"
                            class="clear-btn"
                        >
                            {{ $t('products.clear_filters') }}
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="products.links && products.links.length > 3" class="pagination-container">
                    <nav class="inline-flex rounded-md shadow neumorphic-pagination">
                        <Link
                            v-for="(link, index) in products.links"
                            :key="index"
                            :href="link.url || '#'"
                            :class="{
                                'px-4 py-2 text-sm font-medium border border-brand-gold border-opacity-20': true,
                                'bg-brand-accent text-white': link.url && link.active,
                                'bg-neumorphic text-brand-primary hover:bg-brand-secondary hover:text-white': link.url && !link.active,
                                'bg-neumorphic text-brand-secondary cursor-default': !link.url
                            }"
                            v-html="link.label"
                            :disabled="!link.url"
                        />
                    </nav>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<style scoped>
/* Products Page Container */
.products-page-container {
  background: linear-gradient(135deg, #e0e5ec 0%, #c9d6ff 100%);
  min-height: 100vh;
  padding-bottom: 50px;
}

body.night-mode .products-page-container {
  background: linear-gradient(135deg, #1e1e1e 0%, #3a3a5a 100%);
}

/* Products Hero */
.products-hero {
  padding: 80px 20px 50px;
  background: linear-gradient(135deg, #e0e5ec 0%, #c9d6ff 100%);
  position: relative;
  overflow: hidden;
}

body.night-mode .products-hero {
  background: linear-gradient(135deg, #1e1e1e 0%, #3a3a5a 100%);
}

/* 3D Model Showcase Container */
.model-showcase-container {
  position: relative;
  width: 100%;
  max-width: 500px;
  height: 600px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Background Glow Effect */
.model-background-glow {
  position: absolute;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(102, 126, 234, 0.3) 0%, transparent 70%);
  border-radius: 50%;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  animation: glow-pulse 3s ease-in-out infinite;
}

@keyframes glow-pulse {
  0%, 100% {
    opacity: 0.5;
    transform: translate(-50%, -50%) scale(1);
  }
  50% {
    opacity: 0.8;
    transform: translate(-50%, -50%) scale(1.1);
  }
}

/* Professional 3D Model */
.model-3d-professional {
  position: relative;
  z-index: 2;
  max-height: 550px;
  max-width: 100%;
  object-fit: contain;
  filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.3));
  animation: float-model 4s ease-in-out infinite;
  transition: all 0.5s ease;
}

.model-3d-professional:hover {
  filter: drop-shadow(0 30px 60px rgba(102, 126, 234, 0.4));
  transform: scale(1.05);
}

@keyframes float-model {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-15px);
  }
}

/* Base Platform */
.model-base-platform {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  width: 300px;
  height: 60px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  opacity: 0.3;
  filter: blur(20px);
  z-index: 1;
  animation: platform-rotate 10s linear infinite;
}

@keyframes platform-rotate {
  from {
    transform: translateX(-50%) rotate(0deg);
  }
  to {
    transform: translateX(-50%) rotate(360deg);
  }
}

body.night-mode .model-background-glow {
  background: radial-gradient(circle, rgba(110, 174, 255, 0.2) 0%, transparent 70%);
}

body.night-mode .model-base-platform {
  background: linear-gradient(135deg, #6ea8ff 0%, #8b9fe3 100%);
}

/* CSS-Only Professional Mannequin */
.css-mannequin-wrapper {
  position: relative;
  width: 100%;
  max-width: 400px;
  height: 600px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
}

.css-mannequin-container {
  position: relative;
  z-index: 2;
  animation: float-model 4s ease-in-out infinite;
}

/* Head */
.mannequin-head {
  width: 90px;
  height: 110px;
  background: linear-gradient(135deg, #f5d0b5 0%, #e8b896 50%, #d4a574 100%);
  border-radius: 45px;
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  box-shadow:
    inset -5px -5px 15px rgba(0, 0, 0, 0.1),
    inset 5px 5px 15px rgba(255, 255, 255, 0.3),
    0 10px 30px rgba(0, 0, 0, 0.2);
}

.mannequin-head::before {
  content: '';
  position: absolute;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  width: 70px;
  height: 60px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.4) 0%, transparent 100%);
  border-radius: 35px;
}

/* Torso */
.mannequin-torso {
  width: 220px;
  height: 300px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #5568d3 100%);
  border-radius: 70px 70px 50px 50px;
  position: absolute;
  top: 100px;
  left: 50%;
  transform: translateX(-50%);
  box-shadow:
    inset -10px -10px 30px rgba(0, 0, 0, 0.2),
    inset 10px 10px 30px rgba(255, 255, 255, 0.2),
    0 20px 50px rgba(102, 126, 234, 0.4);
}

.torso-highlight {
  position: absolute;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  width: 180px;
  height: 200px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.15) 0%, transparent 100%);
  border-radius: 60px;
}

/* Arms */
.mannequin-arms {
  position: absolute;
  top: 120px;
  left: 50%;
  transform: translateX(-50%);
  width: 380px;
}

.arm {
  position: absolute;
  width: 70px;
  height: 280px;
  background: linear-gradient(135deg, #f5d0b5 0%, #e8b896 50%, #d4a574 100%);
  border-radius: 35px;
  box-shadow:
    inset -3px -3px 10px rgba(0, 0, 0, 0.1),
    inset 3px 3px 10px rgba(255, 255, 255, 0.3),
    0 5px 15px rgba(0, 0, 0, 0.15);
}

.left-arm {
  left: 0;
  transform: rotate(15deg);
  transform-origin: top center;
}

.right-arm {
  right: 0;
  transform: rotate(-15deg);
  transform-origin: top center;
}

/* Waist */
.mannequin-waist {
  width: 160px;
  height: 80px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 40px 40px 60px 60px;
  position: absolute;
  top: 380px;
  left: 50%;
  transform: translateX(-50%);
  box-shadow:
    inset -5px -5px 20px rgba(0, 0, 0, 0.2),
    0 15px 40px rgba(102, 126, 234, 0.3);
}

/* Glow Effect */
.css-model-glow {
  position: absolute;
  width: 350px;
  height: 350px;
  background: radial-gradient(circle, rgba(102, 126, 234, 0.4) 0%, transparent 70%);
  border-radius: 50%;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 1;
  animation: glow-pulse 3s ease-in-out infinite;
}

/* Base Platform */
.css-base-platform {
  position: absolute;
  bottom: 30px;
  left: 50%;
  transform: translateX(-50%);
  width: 280px;
  height: 50px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  opacity: 0.4;
  filter: blur(25px);
  z-index: 1;
  animation: platform-rotate 10s linear infinite;
}

/* Night Mode Adjustments */
body.night-mode .mannequin-head,
body.night-mode .arm {
  background: linear-gradient(135deg, #4a5568 0%, #2d3748 50%, #1a202c 100%);
  box-shadow:
    inset -3px -3px 10px rgba(0, 0, 0, 0.3),
    inset 3px 3px 10px rgba(255, 255, 255, 0.1),
    0 10px 30px rgba(110, 174, 255, 0.2);
}

body.night-mode .mannequin-torso {
  background: linear-gradient(135deg, #6ea8ff 0%, #5c8ae6 50%, #4a7eff 100%);
  box-shadow:
    inset -10px -10px 30px rgba(0, 0, 0, 0.3),
    inset 10px 10px 30px rgba(255, 255, 255, 0.15),
    0 20px 50px rgba(110, 174, 255, 0.3);
}

body.night-mode .mannequin-waist {
  background: linear-gradient(135deg, #6ea8ff 0%, #5c8ae6 100%);
}

body.night-mode .css-model-glow {
  background: radial-gradient(circle, rgba(110, 174, 255, 0.3) 0%, transparent 70%);
}

body.night-mode .css-base-platform {
  background: linear-gradient(135deg, #6ea8ff 0%, #5c8ae6 100%);
}

/* Products Title */
.products-title {
  font-family: 'Dancing Script', cursive;
  font-size: 56px;
  color: #4a7eff;
  margin-bottom: 15px;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

body.night-mode .products-title {
  color: #6ea8ff;
}

/* Products Subtitle */
.products-subtitle {
  font-size: 20px;
  color: #4a5568;
  max-width: 3xl;
  margin: 0 auto;
  line-height: 1.6;
}

body.night-mode .products-subtitle {
  color: #e2e8f0;
}

/* Neumorphic Card */
.neumorphic-card {
  background: #e0e5ec;
  border-radius: 20px;
  padding: 30px;
  box-shadow:
    8px 8px 15px #a3b1c6,
    -8px -8px 15px #ffffff;
}

body.night-mode .neumorphic-card {
  background: #2d3748;
  box-shadow:
    8px 8px 15px #1a202c,
    -8px -8px 15px #4a5568;
}

.search-section {
  margin-bottom: 30px;
}

/* Neumorphic Input */
.neumorphic-input {
  background: #e0e5ec;
  border: none;
  box-shadow:
    inset 3px 3px 6px #a3b1c6,
    inset -3px -3px 6px #ffffff;
}

.neumorphic-input:focus {
  outline: none;
  box-shadow:
    inset 5px 5px 10px #a3b1c6,
    inset -5px -5px 10px #ffffff;
}

body.night-mode .neumorphic-input {
  background: #34495e;
  box-shadow:
    inset 3px 3px 6px #1a202c,
    inset -3px -3px 6px #4a5568;
}

body.night-mode .neumorphic-input:focus {
  box-shadow:
    inset 5px 5px 10px #1a202c,
    inset -5px -5px 10px #4a5568;
}

/* Neumorphic Select */
.neumorphic-select {
  background: #e0e5ec;
  border: none;
  box-shadow:
    inset 3px 3px 6px #a3b1c6,
    inset -3px -3px 6px #ffffff;
}

.neumorphic-select:focus {
  outline: none;
  box-shadow:
    inset 5px 5px 10px #a3b1c6,
    inset -5px -5px 10px #ffffff;
}

body.night-mode .neumorphic-select {
  background: #34495e;
  box-shadow:
    inset 3px 3px 6px #1a202c,
    inset -3px -3px 6px #4a5568;
}

body.night-mode .neumorphic-select:focus {
  box-shadow:
    inset 5px 5px 10px #1a202c,
    inset -5px -5px 10px #4a5568;
}

/* Products Grid */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 25px;
  margin-bottom: 40px;
}

/* Empty State Container */
.empty-state-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 80px 20px;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 50px;
  max-width: 500px;
}

/* Empty Title */
.empty-title {
  font-family: 'Dancing Script', cursive;
  font-size: 32px;
  color: #4a7eff;
  margin-bottom: 15px;
}

body.night-mode .empty-title {
  color: #6ea8ff;
}

/* Empty Text */
.empty-text {
  font-size: 16px;
  color: #4a5568;
  margin-bottom: 25px;
  line-height: 1.6;
}

body.night-mode .empty-text {
  color: #cbd5e0;
}

/* Clear Button */
.clear-btn {
  padding: 12px 30px;
  border-radius: 15px;
  background: #e0e5ec;
  color: #4a7eff;
  font-weight: bold;
  font-size: 16px;
  cursor: pointer;
  border: none;
  box-shadow:
    5px 5px 10px #a3b1c6,
    -5px -5px 10px #ffffff;
  transition: all 0.3s ease;
}

.clear-btn:hover {
  box-shadow:
    inset 3px 3px 6px #a3b1c6,
    inset -3px -3px 6px #ffffff;
  transform: translateY(-2px);
}

body.night-mode .clear-btn {
  background: #2d3748;
  box-shadow:
    5px 5px 10px #1a202c,
    -5px -5px 10px #4a5568;
}

/* Pagination Container */
.pagination-container {
  display: flex;
  justify-content: center;
  margin-top: 40px;
}

/* Neumorphic Pagination */
.neumorphic-pagination {
  background: #e0e5ec;
  border-radius: 15px;
  padding: 5px;
  box-shadow:
    5px 5px 10px #a3b1c6,
    -5px -5px 10px #ffffff;
  gap: 5px;
}

body.night-mode .neumorphic-pagination {
  background: #2d3748;
  box-shadow:
    5px 5px 10px #1a202c,
    -5px -5px 10px #4a5568;
}

.neumorphic-pagination a {
  text-decoration: none;
  border-radius: 10px;
  transition: all 0.3s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
  .products-hero {
    padding: 50px 15px 30px;
  }
  
  .products-title {
    font-size: 42px;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
  }
  
  .empty-state {
    padding: 30px;
  }
  
  .model-showcase-container {
    height: 400px;
    max-width: 350px;
  }
  
  .model-3d-professional {
    max-height: 350px;
  }
  
  .model-background-glow {
    width: 280px;
    height: 280px;
  }
  
  .model-base-platform {
    width: 200px;
    height: 40px;
  }
  
  /* CSS Mannequin Responsive */
  .css-mannequin-wrapper {
    height: 400px;
    max-width: 280px;
  }
  
  .mannequin-head {
    width: 60px;
    height: 75px;
  }
  
  .mannequin-torso {
    width: 150px;
    height: 200px;
    top: 70px;
  }
  
  .torso-highlight {
    width: 120px;
    height: 140px;
  }
  
  .mannequin-arms {
    width: 260px;
    top: 85px;
  }
  
  .arm {
    width: 50px;
    height: 190px;
  }
  
  .mannequin-waist {
    width: 110px;
    height: 55px;
    top: 260px;
  }
  
  .css-model-glow {
    width: 240px;
    height: 240px;
  }
  
  .css-base-platform {
    width: 180px;
    height: 35px;
    bottom: 20px;
  }
}
</style>
