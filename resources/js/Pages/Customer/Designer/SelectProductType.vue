<template>
  <CustomerLayout>
    <div class="product-type-selection">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">
            <span class="title-icon">👕</span>
            {{ $t('designer.select_product_title') }}
          </h1>
          <p class="page-subtitle">{{ $t('designer.select_product_subtitle') }}</p>
        </div>
      </div>

      <!-- Product Types Grid -->
      <div class="product-types-grid">
        <div 
          v-for="productType in productTypes" 
          :key="productType.id"
          class="product-type-card"
          @click="selectProductType(productType)"
        >
          <!-- Product Type Image/Icon -->
          <div class="card-image">
            <img 
              v-if="productType.image_url || productType.thumbnail_url" 
              :src="productType.image_url || productType.thumbnail_url" 
              :alt="productType.name"
              class="product-image"
            />
            <div v-else class="product-placeholder">
              <span class="placeholder-icon">{{ getProductIcon(productType.slug) }}</span>
            </div>
          </div>
          
          <!-- Card Content -->
          <div class="card-content">
            <h3 class="product-name">{{ productType.name }}</h3>
            <p class="product-description">{{ productType.description }}</p>
            
            <div class="card-footer">
              <span class="starting-price">
                {{ $t('designer.starting_from') }} {{ formatPrice(productType.base_price) }}
              </span>
              <button class="btn-select">
                {{ $t('designer.select') }}
                <span class="btn-arrow">→</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Back Button -->
      <div class="back-section">
        <Link 
          :href="route('designer.my-designs')"
          class="btn-back"
        >
          <span>←</span> {{ $t('designer.back_to_designs') }}
        </Link>
      </div>
    </div>
  </CustomerLayout>
</template>

<script>
import CustomerLayout from '@/Layouts/Customer.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

export default {
  name: 'SelectProductType',
  
  components: {
    CustomerLayout,
    Link,
  },
  
  data() {
    return {
      productTypes: [],
      loading: true,
    };
  },
  
  mounted() {
    this.fetchProductTypes();
  },
  
  methods: {
    async fetchProductTypes() {
      try {
        const response = await axios.get('/api/v1/product-types');
        this.productTypes = response.data.data.filter(pt => pt.is_active);
        this.loading = false;
      } catch (error) {
        console.error('Failed to fetch product types:', error);
        this.loading = false;
      }
    },
    
    getProductIcon(slug) {
      const icons = {
        't-shirt': '👕',
        'hoodie': '🧥',
        'tank-top': '🎽',
        'polo': '👔',
        'sweatshirt': '🧶',
        'jacket': '🧥',
        'cap': '🧢',
        'mug': '☕',
        'poster': '🖼️',
        'canvas': '🎨',
        'phone-case': '📱',
        'pillow': '🛏️',
        'blanket': '🛌',
        'socks': '🧦',
        'shoes': '👟',
        'bag': '👜',
        'other': '✨'
      };
      
      return icons[slug] || icons['other'];
    },
    
    formatPrice(price) {
      const locale = this.$page?.props?.locale === 'ar' ? 'ar-EG' : 'en-US';
      const currency = this.$page?.props?.currency || 'USD';
      
      return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency,
      }).format(price);
    },
    
    selectProductType(productType) {
      window.location.href = route('designer.create', { 
        productType: productType.slug 
      });
    },
  },
};
</script>

<style scoped>
.product-type-selection {
  min-height: 100vh;
  background: linear-gradient(135deg, #e0e5ec 0%, #c9d6ff 100%);
  padding: 2rem;
}

.page-header {
  max-width: 1200px;
  margin: 0 auto 3rem;
  background: #e0e5ec;
  padding: 2rem;
  border-radius: 20px;
  box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff;
}

.header-content {
  text-align: center;
}

.page-title {
  font-size: 2.5rem;
  font-weight: bold;
  color: #4a5568;
  margin: 0 0 0.5rem 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.title-icon {
  font-size: 3rem;
}

.page-subtitle {
  font-size: 1.125rem;
  color: #6d7587;
  margin: 0;
}

.product-types-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto 3rem;
}

.product-type-card {
  background: #e0e5ec;
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.3s;
  cursor: pointer;
  box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff;
}

.product-type-card:hover {
  transform: translateY(-5px);
  box-shadow: inset 4px 4px 8px #a3b1c6, inset -4px -4px 8px #ffffff;
}

.card-image {
  position: relative;
  aspect-ratio: 4/3;
  background: #e0e5ec;
  overflow: hidden;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: all 0.3s;
}

.product-type-card:hover .product-image {
  transform: scale(1.05);
}

.product-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 5rem;
  background: linear-gradient(135deg, #c9d6ff 0%, #e0e5ec 100%);
}

.card-content {
  padding: 1.5rem;
}

.product-name {
  font-size: 1.5rem;
  font-weight: bold;
  color: #4a5568;
  margin: 0 0 0.5rem 0;
}

.product-description {
  font-size: 0.9375rem;
  color: #6d7587;
  margin: 0 0 1rem 0;
  line-height: 1.5;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.starting-price {
  font-size: 1rem;
  font-weight: 600;
  color: #4a7eff;
}

.btn-select {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: #e0e5ec;
  color: #4a7eff;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.9375rem;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 4px 4px 8px #a3b1c6, -4px -4px 8px #ffffff;
}

.btn-select:hover {
  box-shadow: inset 4px 4px 8px #a3b1c6, inset -4px -4px 8px #ffffff;
}

.btn-arrow {
  font-size: 1.25rem;
}

.back-section {
  text-align: center;
  max-width: 1200px;
  margin: 0 auto;
}

.btn-back {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem 2rem;
  background: #e0e5ec;
  color: #4a7eff;
  text-decoration: none;
  border-radius: 15px;
  font-weight: 600;
  font-size: 1rem;
  transition: all 0.3s;
  box-shadow: 5px 5px 10px #a3b1c6, -5px -5px 10px #ffffff;
}

.btn-back:hover {
  box-shadow: inset 5px 5px 10px #a3b1c6, inset -5px -5px 10px #ffffff;
}

@media (max-width: 768px) {
  .product-type-selection {
    padding: 1rem;
  }
  
  .page-header {
    padding: 1.5rem;
  }
  
  .page-title {
    font-size: 1.75rem;
    flex-direction: column;
  }
  
  .product-types-grid {
    grid-template-columns: 1fr;
  }
}
</style>
