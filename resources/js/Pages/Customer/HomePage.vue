<script setup>
import CustomerLayout from '@/Layouts/Customer.vue';
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import AhlamsHeroSection from '@/Components/Customer/AhlamsHeroSection.vue';

const featuredProducts = ref([]);
const loading = ref(true);
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

onMounted(async () => {
  try {
    // Simulate API call
    setTimeout(() => {
      featuredProducts.value = [
        {
          id: 1,
          title: 'Elegant Evening Dress',
          description: 'A stunning evening dress crafted with premium fabrics and intricate details.',
          price: '199.99',
          image_url: '/images/logo.jpeg'
        },
        {
          id: 2,
          title: 'Casual Chic Blouse',
          description: 'Versatile blouse perfect for both office wear and casual outings.',
          price: '89.99',
          image_url: '/images/logo.jpeg'
        },
        {
          id: 3,
          title: 'Summer Floral Skirt',
          description: 'Lightweight floral skirt ideal for summer days and garden parties.',
          price: '79.99',
          image_url: '/images/logo.jpeg'
        },
        {
          id: 4,
          title: 'Premium Tuxedo Jacket',
          description: 'Sophisticated jacket for the modern woman who demands style.',
          price: '249.99',
          image_url: '/images/logo.jpeg'
        }
      ];
      loading.value = false;
    }, 1000);
  } catch (error) {
    console.error('Error loading products:', error);
    loading.value = false;
  }
});
</script>

<template>
  <CustomerLayout>
    <!-- Hero Section -->
    <AhlamsHeroSection />

    <!-- Featured Products -->
    <section class="py-16 bg-gradient-to-b from-brand-bg to-white">
      <div class="container mx-auto px-4">
        <div class="text-center mb-12">
          <h2 class="font-brand-elegant text-4xl text-brand-primary mb-4">Featured Collections</h2>
          <p class="text-brand-secondary text-lg max-w-2xl mx-auto">Discover our most exquisite designs crafted with elegance and precision</p>
        </div>

        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-brand-gold"></div>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
          <div v-for="product in featuredProducts" :key="product.id" class="group">
            <div class="bg-white rounded-2xl overflow-hidden border border-brand-gold border-opacity-20 transition-all duration-300 group-hover:shadow-lg">
              <div class="relative aspect-square overflow-hidden">
                <img 
                  :src="product.image_url || '/images/logo.jpeg'" 
                  :alt="product.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  onerror="this.src='/images/logo.jpeg'"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                
                <div class="absolute top-3 right-3 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <button 
                    @click="addToWishlist(product.id)"
                    class="p-2 bg-white rounded-full shadow-lg hover:bg-brand-rose hover:text-white transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                  </button>
                </div>
              </div>
              
              <div class="p-6">
                <h3 class="font-brand-elegant text-xl font-semibold text-brand-primary mb-2">{{ product.title }}</h3>
                <p class="text-brand-secondary text-sm mb-4 line-clamp-2">{{ product.description }}</p>
                
                <div class="flex justify-between items-center">
                  <span class="text-brand-rose font-bold text-lg">${{ product.price }}</span>
                  <Link 
                    :href="route('tshirt.page', { slug: product.id })" 
                    class="px-4 py-2 bg-gradient-to-r from-brand-primary to-brand-secondary text-white rounded-lg hover:opacity-90 transition-opacity text-sm font-medium"
                  >
                    View Details
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center mt-12">
          <Link 
            :href="route('products.index')" 
            class="inline-block px-8 py-4 bg-gradient-to-r from-brand-primary to-brand-secondary text-white rounded-xl hover:opacity-90 transition-opacity font-semibold text-lg"
          >
            View All Collections
          </Link>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <h2 class="font-brand-elegant text-4xl text-brand-primary mb-6">About Ahlam's Girls</h2>
            <p class="text-brand-secondary text-lg mb-6 leading-relaxed">
              Founded with a vision to bring elegance and sophistication to women's fashion, Ahlam's Girls represents the epitome of luxury and craftsmanship. Our designs blend timeless elegance with contemporary style, creating pieces that celebrate the modern woman.
            </p>
            <p class="text-brand-secondary text-lg mb-8 leading-relaxed">
              Every creation is meticulously crafted with premium materials and attention to detail, ensuring that each piece tells a story of grace, beauty, and refinement.
            </p>
            
            <div class="grid grid-cols-2 gap-6">
              <div class="text-center p-6 bg-gradient-to-br from-brand-primary to-brand-secondary rounded-xl">
                <div class="text-3xl font-bold text-white mb-2">500+</div>
                <p class="text-brand-gold font-medium">Happy Customers</p>
              </div>
              <div class="text-center p-6 bg-gradient-to-br from-brand-secondary to-brand-bg rounded-xl">
                <div class="text-3xl font-bold text-white mb-2">200+</div>
                <p class="text-brand-gold font-medium">Unique Designs</p>
              </div>
            </div>
          </div>
          
          <div class="relative">
            <div class="aspect-square bg-gradient-to-br from-brand-rose to-brand-lavender rounded-2xl overflow-hidden">
              <div class="w-full h-full flex items-center justify-center">
                <div class="text-center text-white">
                  <div class="text-6xl mb-4">👗</div>
                  <h3 class="font-brand-elegant text-2xl font-bold">Elegance Redefined</h3>
                  <p class="mt-2 opacity-90">Luxury Meets Fashion</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16 bg-gradient-to-b from-white to-brand-bg">
      <div class="container mx-auto px-4">
        <div class="text-center mb-12">
          <h2 class="font-brand-elegant text-4xl text-brand-primary mb-4">What Our Customers Say</h2>
          <p class="text-brand-secondary text-lg max-w-2xl mx-auto">Hear from women who have experienced the elegance of Ahlam's Girls</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="(testimonial, index) in testimonials" :key="index" class="bg-white p-6 rounded-2xl border border-brand-gold border-opacity-20">
            <div class="flex mb-4">
              <svg v-for="i in 5" :key="i" class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
              </svg>
            </div>
            <p class="text-brand-secondary mb-6 italic">"{{ testimonial.quote }}"</p>
            <div class="flex items-center">
              <div class="w-12 h-12 rounded-full bg-gradient-to-r from-brand-rose to-brand-lavender flex items-center justify-center text-white font-bold mr-4">
                {{ testimonial.name.charAt(0) }}
              </div>
              <div>
                <p class="font-semibold text-brand-primary">{{ testimonial.name }}</p>
                <p class="text-sm text-brand-secondary">{{ testimonial.role }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </CustomerLayout>
</template>

<style scoped>
/* HomePage specific styles can be added here if needed */
</style>
