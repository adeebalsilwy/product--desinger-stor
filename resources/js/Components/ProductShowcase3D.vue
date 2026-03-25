<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    products: {
        type: Array,
        required: true,
        default: () => []
    },
    title: {
        type: String,
        default: 'Featured Products'
    },
    subtitle: {
        type: String,
        default: 'Discover our premium collection'
    }
});

const hoveredProduct = ref(null);
const rotationY = ref(0);
const rotationX = ref(0);

// Handle mouse move for 3D effect
const handleMouseMove = (e, productId) => {
    hoveredProduct.value = productId;
    const card = e.currentTarget;
    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;
    
    rotationY.value = ((x - centerX) / centerX) * 10;
    rotationX.value = -((y - centerY) / centerY) * 10;
};

const handleMouseLeave = () => {
    hoveredProduct.value = null;
    rotationY.value = 0;
    rotationX.value = 0;
};

const getTransformStyle = (productId) => {
    if (hoveredProduct.value !== productId) {
        return 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
    }
    return `perspective(1000px) rotateX(${rotationX.value}deg) rotateY(${rotationY.value}deg) scale3d(1.05, 1.05, 1.05)`;
};
</script>

<template>
    <section class="py-20 bg-gradient-to-b from-brand-bg via-white to-brand-cream relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-brand-gold/5 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-brand-rose/5 rounded-full blur-3xl animate-pulse delay-1000"></div>
            <div class="absolute top-1/2 left-1/2 w-[800px] h-[800px] bg-brand-lavender/5 rounded-full blur-3xl animate-pulse delay-500"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <div class="inline-block mb-4">
                    <span class="px-4 py-2 bg-brand-gold/10 text-brand-gold rounded-full text-sm font-semibold border border-brand-gold/20">
                        ✨ Premium Collection
                    </span>
                </div>
                <h2 class="font-brand-elegant text-5xl md:text-6xl text-brand-primary mb-6 tracking-tight">
                    {{ title }}
                </h2>
                <p class="text-brand-secondary text-xl max-w-3xl mx-auto leading-relaxed">
                    {{ subtitle }}
                </p>
            </div>

            <!-- Loading State -->
            <div v-if="!products || products.length === 0" class="flex justify-center items-center py-20">
                <div class="text-center">
                    <div class="relative w-24 h-24 mx-auto mb-4">
                        <div class="absolute inset-0 border-4 border-brand-gold/20 rounded-full"></div>
                        <div class="absolute top-0 left-0 w-24 h-24 border-4 border-brand-gold border-t-transparent rounded-full animate-spin"></div>
                    </div>
                    <p class="text-brand-primary font-semibold animate-pulse">Loading exquisite collections...</p>
                </div>
            </div>

            <!-- Products Grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <div 
                    v-for="product in products" 
                    :key="product.id"
                    class="group relative"
                    @mousemove="handleMouseMove($event, product.id)"
                    @mouseleave="handleMouseLeave"
                >
                    <!-- 3D Card Container -->
                    <div 
                        class="bg-white rounded-3xl overflow-hidden shadow-xl border border-brand-gold/10 transition-all duration-500 ease-out"
                        :style="getTransformStyle(product.id)"
                        style="transform-style: preserve-3d;"
                    >
                        <!-- Image Container with Parallax Effect -->
                        <div class="relative aspect-square overflow-hidden bg-gradient-to-br from-brand-cream via-white to-brand-lavender/30">
                            <!-- Shine Effect on Hover -->
                            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/0 to-white/0 group-hover:via-white/20 group-hover:to-white/10 transition-all duration-700 z-10 pointer-events-none"></div>
                            
                            <!-- Product Image -->
                            <img 
                                :src="product.image_url || product.thumbnail_url || '/images/logo.jpeg'" 
                                :alt="product.title || product.name"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-in-out"
                                @error="$event.target.src='/images/logo.jpeg'"
                            />

                            <!-- Overlay Gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-20"></div>

                            <!-- Floating Action Buttons -->
                            <div class="absolute top-4 right-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-all duration-500 translate-x-4 group-hover:translate-x-0 z-30">
                                <button 
                                    class="p-3 bg-white/95 backdrop-blur-sm rounded-full shadow-lg hover:bg-brand-gold hover:text-white transition-all duration-300 transform hover:scale-110"
                                    title="Add to Wishlist"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                                <button 
                                    class="p-3 bg-white/95 backdrop-blur-sm rounded-full shadow-lg hover:bg-brand-primary hover:text-white transition-all duration-300 transform hover:scale-110"
                                    title="Quick View"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Badge -->
                            <div v-if="product.is_featured || product.is_template_based" class="absolute top-4 left-4 z-30">
                                <span class="px-3 py-1.5 bg-brand-gold text-white text-xs font-bold rounded-full shadow-lg border border-white/20">
                                    {{ product.is_featured ? 'Featured' : 'Template Design' }}
                                </span>
                            </div>

                            <!-- Add to Cart Button (Appears on Hover) -->
                            <div class="absolute bottom-0 left-0 right-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-500 z-30">
                                <Link 
                                    :href="route('product.page', { slug: product.slug || product.id })"
                                    class="w-full py-3 bg-white/95 backdrop-blur-sm text-brand-primary font-bold rounded-xl shadow-lg hover:bg-brand-gold hover:text-white transition-all duration-300 text-center block"
                                >
                                    View Details
                                </Link>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="p-6 relative">
                            <!-- Decorative Element -->
                            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-12 h-12 bg-gradient-to-br from-brand-gold to-brand-accent rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500 shadow-lg"></div>

                            <h3 class="font-brand-elegant text-2xl font-semibold text-brand-primary mb-2 line-clamp-1 group-hover:text-brand-gold transition-colors duration-300">
                                {{ product.title || product.name }}
                            </h3>
                            <p class="text-brand-secondary text-sm mb-4 line-clamp-2 leading-relaxed">
                                {{ product.description || 'Elegant design crafted with premium materials' }}
                            </p>
                            
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl font-bold text-brand-rose">${{ Number(product.price || product.sale_price || 0).toFixed(2) }}</span>
                                    <span v-if="product.sale_price && product.sale_price < product.price" class="text-lg text-brand-secondary line-through">
                                        ${{ Number(product.price).toFixed(2) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1 text-yellow-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-brand-secondary">{{ product.rating || '4.9' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shadow Under Card -->
                    <div class="absolute -bottom-4 left-4 right-4 h-8 bg-black/10 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 transform group-hover:scale-105"></div>
                </div>
            </div>

            <!-- View All Button -->
            <div v-if="products && products.length > 0" class="text-center mt-16">
                <Link 
                    :href="route('products.index')"
                    class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-brand-primary via-brand-secondary to-brand-primary text-white font-bold text-lg rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 group"
                >
                    View All Collections
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* Smooth animations */
* {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom scrollbar for webkit */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #d4af37;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #b8941f;
}
</style>
