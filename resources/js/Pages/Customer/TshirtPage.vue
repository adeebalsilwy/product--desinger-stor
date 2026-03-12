<script setup>
import { Head } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Customer from "@/Layouts/Customer.vue";
import ImageCarousel from "@/Components/ProductImageCarousel.vue";
import Price from "@/Components/TshirtPage/Price.vue";
import Reviews from "@/Components/TshirtPage/Reviews.vue";
import InputNumber from "primevue/inputnumber";
import Description from "@/Components/TshirtPage/Description.vue";
import Quality from "@/Icons/Quality.vue";
import Card from "@/Icons/Card.vue";
import World from "@/Icons/World.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import { usePage, router } from "@inertiajs/vue3";

// Add 3D viewer imports
import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';

defineOptions({ layout: Customer });

const props = defineProps({
    tshirt: {
        type: Object,
        required: true,
    },
    product: {
        type: Object,
        default: null
    }
});

// Create a unified product object that works with both legacy and new structure
const currentProduct = computed(() => {
    // Use the product if available (new structure), otherwise fall back to tshirt (legacy)
    return props.product || props.tshirt;
});

// Ensure we have the right property names regardless of structure
const productName = computed(() => currentProduct.value.name || currentProduct.value.title);
const productPrice = computed(() => Number(currentProduct.value.price));
const productDescription = computed(() => currentProduct.value.description);
const productImages = computed(() => {
    // Handle both structures and extract image URLs
    let images = [];
    
    if (Array.isArray(currentProduct.value.images)) {
        images = currentProduct.value.images;
    } else if (currentProduct.value.tshirt && Array.isArray(currentProduct.value.tshirt.images)) {
        images = currentProduct.value.tshirt.images;
    }
    
    // Extract URLs from image objects
    const imageUrls = images.map(image => {
        if (typeof image === 'string') {
            return image;
        } else if (image && image.url) {
            return image.url;
        } else if (image && image.path) {
            return image.path;
        }
        return null;
    }).filter(url => url && url !== '/images/logo.jpeg');
    
    // Return at least one fallback image if no valid images
    return imageUrls.length > 0 ? imageUrls : ['/images/logo.jpeg'];
});

// 3D viewer state
const show3DViewer = ref(false);
const threeDContainer = ref(null);

const page = usePage();
const cartDuplicationError = computed(() => page.props.errors?.cart);

const toast = useToast();

const mainImage = productImages.value && productImages.value.length > 0 ? [productImages.value[0]] : [];

const cartForm = useForm({
    tshirtId: currentProduct.value.id,
    tshirtTitle: productName.value,
    tshirtImage: mainImage[0] || '',
    tshirtPrice: Number(productPrice.value),
    size: "",
    quantity: 1,
});

function handleAddToCart() {
    // Check if user is logged in
    if (page.props.auth && page.props.auth.user) {
        // Check if user is admin/staff
        const userRole = page.props.auth.user.role || 'customer';
        
        if (['admin', 'staff'].includes(userRole.toLowerCase())) {
            // Admin warning notification indicating test mode
            toast.add({
                severity: "warn",
                summary: "Admin Test Mode",
                detail: "You're logged in as admin. This is a test cart operation. Real orders require customer login.",
                life: 5000,
            });
            // Allow admin to proceed with test functionality
        }
    }
    if (cartForm.size === "") {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Please select a size",
            life: 3000,
        });
        return;
    }

    cartForm.post(route("cart.add"), {
        onSuccess: () => {
             if (!page.props.errors || Object.keys(page.props.errors).length === 0) {
                toast.add({
                    severity: "success",
                    summary: "Success",
                    detail: "Added to cart successfully",
                    life: 3000,
                });
            } else {
                toast.add({
                    severity: "warn",
                    summary: "Warning",
                    detail: page.props.errors.cart || "An error occurred",
                    life: 3000,
                });
            }
        },
    });
}

const sizes = ["XS", "S", "M", "L", "XL", "XXL"];

// Professional editing functions
const customizeProduct = () => {
    // Check if user is admin for test mode indication
    if (page.props.auth && page.props.auth.user) {
        const userRole = page.props.auth.user.role || 'customer';
        
        if (['admin', 'staff'].includes(userRole.toLowerCase())) {
            toast.add({
                severity: "info",
                summary: "Admin Test Mode",
                detail: "Testing designer functionality as admin. Customer login required for real orders.",
                life: 3000,
            });
        }
    }
    
    // Redirect to designer with this product as base
    // Use the product type and product slug from the current product structure
    const productType = currentProduct.value.productType?.slug || 't-shirt';
    const productSlug = currentProduct.value.slug || 
                       (currentProduct.value.title ? currentProduct.value.title.toLowerCase().replace(/\s+/g, '-') : 'product-' + currentProduct.value.id);
    
    console.log('Navigating to designer:', { productType, productSlug, productId: currentProduct.value.id });
    router.visit(route('designer.create', {productType: productType, product: productSlug}));
};

const shareProduct = async () => {
    const shareData = {
        title: productName.value,
        text: `Check out this amazing ${productName.value}!`,
        url: window.location.href
    };
    
    if (navigator.share && navigator.canShare(shareData)) {
        try {
            await navigator.share(shareData);
        } catch (err) {
            console.log('Error sharing:', err);
            // Fallback to copying to clipboard
            fallbackCopyToClipboard();
        }
    } else {
        // Fallback: copy to clipboard
        fallbackCopyToClipboard();
    }
};

const fallbackCopyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(window.location.href);
        toast.add({
            severity: "success",
            summary: "Success",
            detail: "Product link copied to clipboard!",
            life: 3000
        });
    } catch (err) {
        console.error('Failed to copy: ', err);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to copy link to clipboard",
            life: 3000
        });
    }
};

// Enhanced 3D Preview Function
const viewIn3D = () => {
    if (productImages.value && productImages.value.length > 0) {
        show3DViewer.value = true;
        // Initialize 3D viewer after DOM update
        setTimeout(() => {
            initialize3DViewer();
        }, 100);
    } else {
        toast.add({
            severity: "info",
            summary: "3D Preview",
            detail: "No images available for 3D preview",
            life: 3000
        });
    }
};

// Initialize 3D Viewer
const initialize3DViewer = () => {
    if (!threeDContainer.value || !productImages.value || productImages.value.length === 0) return;

    // Clear previous content
    threeDContainer.value.innerHTML = '';

    // Set up Three.js scene
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, threeDContainer.value.clientWidth / threeDContainer.value.clientHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    
    renderer.setSize(threeDContainer.value.clientWidth, threeDContainer.value.clientHeight);
    renderer.setClearColor(0x000000, 0);
    threeDContainer.value.appendChild(renderer.domElement);

    // Add lighting
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);
    
    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
    directionalLight.position.set(1, 1, 1);
    scene.add(directionalLight);

    // Create product geometry (simplified box for demonstration)
    const geometry = new THREE.BoxGeometry(2, 3, 0.1);
    
    // Load product image as texture
    const textureLoader = new THREE.TextureLoader();
    const imageUrl = productImages.value[0];
    
    textureLoader.load(imageUrl, (texture) => {
        const material = new THREE.MeshStandardMaterial({ 
            map: texture,
            side: THREE.DoubleSide
        });
        
        const productMesh = new THREE.Mesh(geometry, material);
        scene.add(productMesh);

        // Position camera
        camera.position.z = 5;

        // Add orbit controls
        const controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;

        // Animation loop
        const animate = () => {
            requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        };
        animate();

        // Handle window resize
        const handleResize = () => {
            camera.aspect = threeDContainer.value.clientWidth / threeDContainer.value.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(threeDContainer.value.clientWidth, threeDContainer.value.clientHeight);
        };

        window.addEventListener('resize', handleResize);

        // Cleanup function
        return () => {
            window.removeEventListener('resize', handleResize);
            if (threeDContainer.value && renderer.domElement) {
                threeDContainer.value.removeChild(renderer.domElement);
            }
        };
    });
};

// Close 3D viewer
const close3DViewer = () => {
    show3DViewer.value = false;
};
</script>

<template>
    <Head :title="productName" />
    
    <div class="min-h-screen bg-gradient-to-br from-brand-cream via-white to-brand-cream">
        <!-- Professional Product Header -->
        <div class="bg-white border-b border-brand-gold/20 sticky top-0 z-30 backdrop-blur-sm bg-white/90">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-brand-primary font-brand-elegant">{{ productName }}</h1>
                        <div class="flex items-center mt-1">
                            <span class="text-brand-gold font-semibold text-lg">{{ productPrice }} SAR</span>
                            <span v-if="currentProduct.is_template_based" class="ml-3 px-2 py-1 bg-brand-gold/10 text-brand-gold text-xs font-medium rounded-full border border-brand-gold/20">
                                Template Design
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <button 
                            @click="shareProduct"
                            class="p-2 rounded-lg border border-brand-gold/30 text-brand-gold hover:bg-brand-gold/10 transition-colors"
                            title="Share Product"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">\                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                        </button>
                        
                        <button 
                            @click="viewIn3D"
                            class="p-2 rounded-lg border border-brand-gold/30 text-brand-gold hover:bg-brand-gold/10 transition-colors"
                            title="3D Preview"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">\                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Product Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Product Images Section -->
                <div class="lg:sticky lg:top-24 lg:self-start">
                    <div class="bg-white rounded-2xl shadow-xl border border-brand-gold/10 overflow-hidden">
                        <ImageCarousel 
                            :images="productImages" 
                            :product-name="productName"
                            class="w-full"
                        />
                        
                        <!-- Product Badges -->
                        <div class="p-6 border-t border-brand-gold/10">
                            <div class="flex flex-wrap gap-2">
                                <div class="flex items-center px-3 py-1 bg-brand-primary/5 text-brand-primary rounded-full text-sm border border-brand-primary/10">
                                    <Quality class="w-4 h-4 mr-2" />
                                    Premium Quality
                                </div>
                                <div class="flex items-center px-3 py-1 bg-brand-gold/10 text-brand-gold rounded-full text-sm border border-brand-gold/20">
                                    <Card class="w-4 h-4 mr-2" />
                                    Secure Payment
                                </div>
                                <div class="flex items-center px-3 py-1 bg-brand-accent/10 text-brand-accent rounded-full text-sm border border-brand-accent/20">
                                    <World class="w-4 h-4 mr-2" />
                                    Worldwide Shipping
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Details Section -->
                <div class="space-y-8">
                    <!-- Price and Rating -->
                    <div class="bg-white rounded-2xl shadow-xl border border-brand-gold/10 p-8">
                        <Price 
                            :price="productPrice" 
                            :product="currentProduct"
                        />
                        
                        <div class="mt-6 pt-6 border-t border-brand-gold/10">
                            <Reviews :product="currentProduct" />
                        </div>
                    </div>

                    <!-- Size Selection -->
                    <div class="bg-white rounded-2xl shadow-xl border border-brand-gold/10 p-8">
                        <h3 class="text-xl font-bold text-brand-primary mb-6 font-brand-elegant">Select Size</h3>
                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                            <button
                                v-for="size in sizes"
                                :key="size"
                                @click="cartForm.size = size"
                                :class="[
                                    'py-3 px-4 border-2 rounded-xl font-semibold transition-all duration-200',
                                    cartForm.size === size
                                        ? 'border-brand-gold bg-brand-gold/10 text-brand-gold shadow-md'
                                        : 'border-brand-primary/20 text-brand-primary hover:border-brand-gold hover:bg-brand-gold/5'
                                ]"
                            >
                                {{ size }}
                            </button>
                        </div>
                        
                        <div v-if="cartForm.errors.size" class="mt-3 text-red-500 text-sm">
                            {{ cartForm.errors.size }}
                        </div>
                    </div>

                    <!-- Quantity and Add to Cart -->
                    <div class="bg-white rounded-2xl shadow-xl border border-brand-gold/10 p-8">
                        <h3 class="text-xl font-bold text-brand-primary mb-6 font-brand-elegant">Quantity</h3>
                        <div class="flex items-center space-x-4">
                            <InputNumber 
                                v-model="cartForm.quantity" 
                                :min="1" 
                                :max="10"
                                showButtons
                                buttonLayout="horizontal" 
                                :step="1"
                                decrementButtonClass="p-button-text p-button-rounded"
                                incrementButtonClass="p-button-text p-button-rounded"
                                incrementButtonIcon="pi pi-plus"
                                decrementButtonIcon="pi pi-minus"
                                class="w-32"
                            />
                            
                            <button
                                @click="handleAddToCart"
                                :disabled="cartForm.processing"
                                class="flex-1 bg-gradient-to-r from-brand-gold to-brand-accent hover:from-brand-gold/90 hover:to-brand-accent/90 text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            >
                                <span v-if="cartForm.processing" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Adding to Cart...
                                </span>
                                <span v-else class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                                    </svg>
                                    Add to Cart
                                </span>
                            </button>
                        </div>
                        
                        <div v-if="cartDuplicationError" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                            {{ cartDuplicationError }}
                        </div>
                    </div>

                    <!-- Product Description -->
                    <div class="bg-white rounded-2xl shadow-xl border border-brand-gold/10 overflow-hidden">
                        <Description 
                            :title="productName" 
                            :description="productDescription || 'No description available'" 
                        />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button
                            @click="customizeProduct"
                            class="flex-1 bg-gradient-to-r from-brand-primary to-brand-primary/80 hover:from-brand-primary/90 hover:to-brand-primary/70 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            Customize Design
                        </button>
                        
                        <button
                            @click="shareProduct"
                            class="flex-1 bg-white border-2 border-brand-gold text-brand-gold hover:bg-brand-gold hover:text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            Share Product
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3D Viewer Modal -->
        <div v-if="show3DViewer" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-brand-gold/20">
                    <h3 class="text-2xl font-bold text-brand-primary font-brand-elegant">3D Product Preview</h3>
                    <button 
                        @click="close3DViewer"
                        class="p-2 rounded-lg hover:bg-brand-primary/10 transition-colors"
                    >
                        <svg class="w-6 h-6 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div ref="threeDContainer" class="w-full h-96 bg-gradient-to-br from-brand-cream to-white flex items-center justify-center">
                    <div v-if="!productImages || productImages.length === 0" class="text-center text-brand-primary/60">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p>No product images available for 3D preview</p>
                    </div>
                </div>
                
                <div class="p-6 border-t border-brand-gold/20 bg-brand-cream/30">
                    <p class="text-brand-primary/70 text-sm text-center">
                        Use your mouse to rotate and zoom the 3D model. Scroll to zoom in/out.
                    </p>
                </div>
            </div>
        </div>

        <!-- Toast Notifications -->
        <Toast />
    </div>
</template>