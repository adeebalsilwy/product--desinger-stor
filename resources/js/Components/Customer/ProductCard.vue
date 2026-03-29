<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    product: Object,
});

const getImageUrl = (image) => {
    if (!image) return '/images/placeholder-product.jpg';
    
    // Handle case where image is an object with a url property
    const imageUrl = typeof image === 'object' && image.url ? image.url : image;
    
    if (typeof imageUrl === 'string') {
        if (imageUrl.startsWith('http')) return imageUrl;
        return `/storage/${imageUrl.replace('/storage/', '')}`;
    }
    
    return '/images/placeholder-product.jpg';
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'SAR'
    }).format(price);
};

const getFirstImage = () => {
    // First try to use product thumbnail_url
    if (props.product.thumbnail_url) {
        return getImageUrl(props.product.thumbnail_url);
    }
    
    // Then try to use product images relationship
    if (props.product.images && props.product.images.length > 0) {
        const sortedImages = [...props.product.images].sort((a, b) => {
            const orderA = typeof a === 'object' ? (a.order || 0) : 0;
            const orderB = typeof b === 'object' ? (b.order || 0) : 0;
            return orderA - orderB;
        });
        
        const firstImage = sortedImages[0];
        const imageUrl = typeof firstImage === 'object' && firstImage.url ? firstImage.url : firstImage;
        return getImageUrl(imageUrl);
    }
    
    // If no product images, try to use design template image
    if (props.product.designTemplate && props.product.designTemplate.thumbnail_url) {
        return getImageUrl(props.product.designTemplate.thumbnail_url);
    }
    
    if (props.product.designTemplate && props.product.designTemplate.preview_url) {
        return getImageUrl(props.product.designTemplate.preview_url);
    }
    
    // Fallback to placeholder
    return '/images/placeholder-product.jpg';
};
</script>

<template>
    <div class="bg-neumorphic rounded-xl overflow-hidden transition-all hover:neumorphic-btn group">
        <!-- Product Image Container with Model Display -->
        <div class="relative overflow-hidden model-display-container">
            <img
                :src="getFirstImage()"
                :alt="product.name"
                class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
            />
            
            <!-- Template Badge with Glow Effect -->
            <div v-if="product.is_template_based" class="template-badge-glow">
                <span class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-4 py-2 rounded-full text-xs font-bold shadow-lg flex items-center gap-1">
                    <i class="pi pi-star-fill"></i>
                    Template
                </span>
            </div>
            
            <!-- Professional Model Overlay (for template products) -->
            <div v-if="product.is_template_based" class="model-overlay">
                <div class="model-icon-wrapper">
                    <i class="pi pi-user"></i>
                </div>
            </div>
            
            <!-- Quick View Button -->
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <Link
                    :href="route('product.page', { slug: product.slug ? product.slug : product.id })"
                    class="px-6 py-3 bg-white text-brand-primary rounded-lg font-medium hover:bg-brand-gold hover:text-white transition-all flex items-center"
                >
                    <i class="pi pi-eye mr-2"></i>
                    View Details
                </Link>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="p-5">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-brand-elegant text-lg text-brand-primary font-semibold line-clamp-2">
                    {{ product.name }}
                </h3>
                <span class="text-brand-accent font-bold text-lg">
                    {{ formatPrice(product.price) }}
                </span>
            </div>
            
            <p class="text-brand-secondary text-sm mb-4 line-clamp-2">
                {{ product.description }}
            </p>
            
            <!-- Product Type -->
            <div v-if="product.productType" class="flex items-center justify-between">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-brand-secondary bg-opacity-10 text-brand-secondary">
                    <i class="pi pi-tag mr-1"></i>
                    {{ product.productType.name }}
                </span>
                
                <!-- Designer Link -->
                <Link
                    v-if="product.is_template_based"
                    :href="route('designer.create', { 
                        productType: product.productType && product.productType.slug ? product.productType.slug : product.productType ? product.productType.id : '',
                        product: product.id 
                    })"
                    class="text-brand-accent hover:text-brand-primary text-sm font-medium flex items-center"
                >
                    <i class="pi pi-palette mr-1"></i>
                    Design
                </Link>
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-4 pt-4 border-t border-brand-gold border-opacity-20">
                <div class="flex space-x-2">
                    <Link
                        :href="route('product.page', { slug: product.slug ? product.slug : product.id })"
                        class="flex-1 px-4 py-2 btn-primary rounded-lg text-center font-medium hover:opacity-90 transition-all flex items-center justify-center"
                    >
                        <i class="pi pi-shopping-cart mr-2"></i>
                        View
                    </Link>
                    
                    <Link
                        v-if="product.is_template_based"
                        :href="route('designer.create', { 
                            productType: product.productType && product.productType.slug ? product.productType.slug : product.productType ? product.productType.id : '',
                            product: product.id 
                        })"
                        class="px-4 py-2 btn-accent rounded-lg font-medium hover:opacity-90 transition-all flex items-center"
                    >
                        <i class="pi pi-palette"></i>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Model Display Container */
.model-display-container {
    position: relative;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

body.night-mode .model-display-container {
    background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
}

/* Template Badge with Glow */
.template-badge-glow {
    position: absolute;
    top: 15px;
    left: 15px;
    z-index: 10;
    animation: badge-glow 2s ease-in-out infinite;
}

@keyframes badge-glow {
    0%, 100% {
        filter: drop-shadow(0 0 5px rgba(102, 126, 234, 0.5));
    }
    50% {
        filter: drop-shadow(0 0 15px rgba(102, 126, 234, 0.8));
    }
}

/* Model Overlay Icon */
.model-overlay {
    position: absolute;
    bottom: 15px;
    right: 15px;
    z-index: 5;
}

.model-icon-wrapper {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    animation: icon-float 3s ease-in-out infinite;
}

@keyframes icon-float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-5px);
    }
}

.model-icon-wrapper i {
    animation: icon-pulse 2s ease-in-out infinite;
}

@keyframes icon-pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}
</style>