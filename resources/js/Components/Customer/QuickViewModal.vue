<script setup>
import { ref, computed, onBeforeMount } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import ImageCarousel from '@/Components/ImageCarousel.vue';
import Price from '@/Components/TshirtPage/Price.vue';
import Quality from '@/Icons/Quality.vue';
import Card from '@/Icons/Card.vue';
import World from '@/Icons/World.vue';

const props = defineProps({
    product: {
        type: Object,
        default: null
    },
    isOpen: {
        type: Boolean,
        default: false
    }
});

// Debug logging
import { onMounted, watch } from 'vue';

onBeforeMount(() => {
    // Prevent mounting if product is null or invalid
    if (!props.product || !props.product.id) {
        console.warn('QuickViewModal not mounted: invalid product', props.product);
        return;
    }
});

onMounted(() => {
    console.log('QuickViewModal mounted:', { product: props.product, isOpen: props.isOpen });
});

watch(() => props.product, (newProduct) => {
    console.log('Product changed:', newProduct);
    if (!newProduct) {
        console.warn('Received null product');
    } else if (!newProduct.id) {
        console.warn('Product missing ID:', newProduct);
    }
}, { immediate: true });

const emit = defineEmits(['close', 'customize', 'addToCart']);

const toast = useToast();
const selectedSize = ref('');
const quantity = ref(1);

const cartForm = useForm({
    tshirtId: props.product?.id || 0,
    tshirtTitle: props.product?.title || '',
    tshirtImage: props.product?.images?.[0]?.url || '',
    tshirtPrice: props.product?.price || 0,
    size: selectedSize,
    quantity: quantity
});



watch(() => props.product, (newProduct) => {
    if (newProduct) {
        cartForm.tshirtId = newProduct.id || 0;
        cartForm.tshirtTitle = newProduct.title || '';
        cartForm.tshirtImage = newProduct.images?.[0]?.url || '';
        cartForm.tshirtPrice = newProduct.price || 0;
    } else {
        cartForm.tshirtId = 0;
        cartForm.tshirtTitle = '';
        cartForm.tshirtImage = '';
        cartForm.tshirtPrice = 0;
    }
}, { immediate: true });

const sizes = ["XS", "S", "M", "L", "XL", "XXL"];

// Computed properties for safe access
const productTitle = computed(() => props.product?.title || 'Product');
const productPrice = computed(() => props.product?.price || 0);
const productImages = computed(() => props.product?.images?.map(img => img.url) || []);
const productSlug = computed(() => props.product?.slug || '');

const closeModal = () => {
    emit('close');
};

const handleAddToCart = () => {
    if (!props.product) return;
    
    if (!selectedSize.value) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Please select a size",
            life: 3000
        });
        return;
    }

    cartForm.size = selectedSize.value;
    cartForm.quantity = quantity.value;
    
    cartForm.post(route("cart.add"), {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "Added to cart successfully",
                life: 3000
            });
            closeModal();
        },
        onError: (errors) => {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: errors.cart || "Failed to add to cart",
                life: 3000
            });
        }
    });
};

const customizeProduct = () => {
    if (!props.product) return;
    try {
        emit('customize', props.product);
        closeModal();
    } catch (error) {
        console.error('Error customizing product:', error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to customize product",
            life: 3000
        });
    }
};

const shareProduct = () => {
    if (!props.product || !productSlug.value) return;
    
    const shareUrl = `${window.location.origin}/t-shirt/${productSlug.value}`;
    const shareTitle = productTitle.value;
    
    if (navigator.share) {
        navigator.share({
            title: shareTitle,
            text: `Check out this amazing ${shareTitle}!`,
            url: shareUrl
        });
    } else {
        navigator.clipboard.writeText(shareUrl);
        toast.add({
            severity: "success",
            summary: "Success",
            detail: "Product link copied to clipboard",
            life: 3000
        });
    }
};
</script>

<template>
    <div 
        v-if="isOpen && product && product.id" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="closeModal"
    >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        
        <!-- Modal Content with 3D Effects -->
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden transform transition-all duration-500 ease-out scale-100 opacity-100">
            <!-- 3D Background Elements -->
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-0 left-0 w-64 h-64 bg-gradient-to-r from-blue-400/10 to-purple-400/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-0 right-0 w-80 h-80 bg-gradient-to-r from-purple-400/10 to-pink-400/10 rounded-full blur-3xl animate-pulse animation-delay-2000"></div>
            </div>
            
            <!-- Close Button -->
            <button 
                @click="closeModal"
                class="absolute top-4 right-4 z-20 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-gray-50 hover:scale-110 transition-all duration-300"
            >
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <div class="grid lg:grid-cols-2 gap-8 p-8 relative z-10">
                <!-- Product Images -->
                <div class="product-3d-container">
                    <div class="relative rounded-2xl overflow-hidden shadow-xl product-3d-hover">
                        <ImageCarousel 
                            :images="productImages" 
                            class="rounded-xl"
                        />
                    </div>
                </div>
                
                <!-- Product Details -->
                <div class="space-y-6">
                    <!-- Header -->
                    <div class="border-b border-gray-200 pb-6">
                        <h2 class="text-3xl font-bold text-gray-900 mb-3">{{ productTitle }}</h2>
                        <Price :price="productPrice" class="text-2xl font-bold text-gray-900" />
                        <div class="flex items-center mt-3">
                            <div class="flex">
                                <svg v-for="i in 5" :key="i" class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </div>
                            <span class="ml-2 text-gray-600">(128 reviews)</span>
                        </div>
                    </div>
                    
                    <!-- Size Selection -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Size</h3>
                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                            <div v-for="size in sizes" :key="size">
                                <input
                                    type="radio"
                                    name="quickview-size"
                                    :value="size"
                                    v-model="selectedSize"
                                    :id="`quickview-size-${size}`"
                                    class="sr-only"
                                />
                                <label
                                    :for="`quickview-size-${size}`"
                                    class="block text-center py-3 px-2 border rounded-lg cursor-pointer transition-all duration-200"
                                    :class="[
                                        selectedSize === size
                                            ? 'border-blue-500 bg-blue-50 text-blue-700 shadow-md'
                                            : 'border-gray-300 text-gray-700 hover:border-gray-400 hover:bg-gray-50'
                                    ]"
                                >
                                    {{ size }}
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quantity -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quantity</h3>
                        <div class="flex items-center border border-gray-300 rounded-lg w-32">
                            <button
                                type="button"
                                @click="quantity = Math.max(1, quantity - 1)"
                                class="px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-l-lg transition-colors"
                                :disabled="quantity <= 1"
                            >
                                -
                            </button>
                            <span class="px-4 py-2 text-center flex-1">{{ quantity }}</span>
                            <button
                                type="button"
                                @click="quantity = Math.min(10, quantity + 1)"
                                class="px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-r-lg transition-colors"
                                :disabled="quantity >= 10"
                            >
                                +
                            </button>
                        </div>
                    </div>
                    
                    <!-- Features -->
                    <div class="space-y-3 pt-4">
                        <div class="flex items-center gap-3 text-gray-600">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <Quality class="w-4 h-4 text-blue-600" />
                            </div>
                            <span>Premium quality material</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <Card class="w-4 h-4 text-green-600" />
                            </div>
                            <span>100% Secured Payment</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <World class="w-4 h-4 text-purple-600" />
                            </div>
                            <span>Free shipping worldwide</span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-6">
                        <button
                            @click="handleAddToCart"
                            :disabled="cartForm.processing || !selectedSize || !product"
                            :class="[
                                cartForm.processing || !selectedSize || !product
                                    ? 'cursor-not-allowed bg-gray-400'
                                    : 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 hover:shadow-lg hover:-translate-y-0.5',
                                'flex-1 py-3 px-6 rounded-lg font-semibold text-white transition-all duration-300 transform'
                            ]"
                        >
                            <span v-if="cartForm.processing">Adding to Cart...</span>
                            <span v-else>Add to Cart - ${{ (productPrice * quantity).toFixed(2) }}</span>
                        </button>
                        
                        <button
                            @click="customizeProduct"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            Customize
                        </button>
                        
                        <button
                            @click="shareProduct"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* 3D Modal Effects */
.product-3d-container {
    transform-style: preserve-3d;
    perspective: 1200px;
}

.product-3d-hover {
    transform-style: preserve-3d;
    perspective: 1200px;
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.product-3d-hover:hover {
    transform: perspective(1200px) rotateX(5deg) rotateY(5deg) translateZ(20px);
    box-shadow: 
        0 30px 60px -12px rgba(0, 0, 0, 0.25),
        0 0 0 1px rgba(0, 0, 0, 0.05),
        inset 0 0 100px rgba(255, 255, 255, 0.1);
}

.animation-delay-2000 {
    animation-delay: 2s;
}

/* Modal Entrance Animation */
.modal-enter-active {
    transition: all 0.3s ease;
}

.modal-leave-active {
    transition: all 0.3s ease;
}

.modal-enter-from {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
}

.modal-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
}
</style>