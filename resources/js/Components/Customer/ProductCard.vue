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
    // First try to use product images
    if (props.product.images && props.product.images.length > 0) {
        const firstImage = props.product.images[0];
        // Check if the image is an object with a url property, or a string
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
        <!-- Product Image -->
        <div class="relative overflow-hidden">
            <img
                :src="getFirstImage()"
                :alt="product.name"
                class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
            />
            
            <!-- Template Badge -->
            <div v-if="product.is_template_based" class="absolute top-3 left-3">
                <span class="bg-brand-accent text-white px-3 py-1 rounded-full text-xs font-semibold">
                    <i class="pi pi-star-fill mr-1"></i>
                    Template
                </span>
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
</style>