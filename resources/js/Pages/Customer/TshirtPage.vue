<script setup>
import { Head, useForm, usePage, router } from "@inertiajs/vue3";
import { computed, ref, nextTick } from "vue";
import Customer from "@/Layouts/Customer.vue";
import ImageCarousel from "@/Components/ProductImageCarousel.vue";
import Product3DViewer from "@/Components/Product3DViewer.vue";
import Price from "@/Components/TshirtPage/Price.vue";
import Reviews from "@/Components/TshirtPage/Reviews.vue";
import InputNumber from "primevue/inputnumber";
import Description from "@/Components/TshirtPage/Description.vue";
import Quality from "@/Icons/Quality.vue";
import Card from "@/Icons/Card.vue";
import World from "@/Icons/World.vue";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";

defineOptions({ layout: Customer });

const props = defineProps({
    tshirt: {
        type: Object,
        required: true,
    },
    product: {
        type: Object,
        default: null,
    },
});

const toast = useToast();
const page = usePage();

const currentProduct = computed(() => props.product || props.tshirt || {});

const productName = computed(() => {
    return currentProduct.value?.name || currentProduct.value?.title || "Product";
});

const productPrice = computed(() => {
    return Number(currentProduct.value?.price || 0);
});

const productDescription = computed(() => {
    return currentProduct.value?.description || "No description available";
});

const productImages = computed(() => {
    let images = [];

    if (Array.isArray(currentProduct.value?.images)) {
        images = currentProduct.value.images;
    } else if (
        currentProduct.value?.tshirt &&
        Array.isArray(currentProduct.value.tshirt.images)
    ) {
        images = currentProduct.value.tshirt.images;
    }

    const imageUrls = images
        .map((image) => {
            if (typeof image === "string") return image;
            if (image?.url) return image.url;
            if (image?.path) return image.path;
            if (image?.image_url) return image.image_url;
            if (image?.original_url) return image.original_url;
            return null;
        })
        .filter((url) => !!url && url !== "/images/logo.jpeg");

    if (!imageUrls.length) {
        return ["/images/placeholder-product.png"];
    }

    return imageUrls;
});

const mainProductImage = computed(() => {
    return productImages.value?.[0] || "/images/placeholder-product.png";
});

const sizes = ["XS", "S", "M", "L", "XL", "XXL"];
const cartDuplicationError = computed(() => page.props.errors?.cart);

const show3DViewer = ref(false);
const is3DMode = ref(true);
const selectedTryOnImage = ref(mainProductImage.value);
const product3DViewerRef = ref(null);

const cartForm = useForm({
    tshirtId: currentProduct.value?.id,
    tshirtTitle: productName.value,
    tshirtImage: mainProductImage.value,
    tshirtPrice: Number(productPrice.value),
    size: "",
    quantity: 1,
});

const normalizedProductSlug = computed(() => {
    if (currentProduct.value?.slug) return currentProduct.value.slug;

    const raw = currentProduct.value?.title || currentProduct.value?.name || "";
    return raw
        .toLowerCase()
        .trim()
        .replace(/\s+/g, "-")
        .replace(/[^a-z0-9\u0600-\u06FF-]/g, "");
});

const productUrl = computed(() => {
    return `/product/${normalizedProductSlug.value || currentProduct.value?.id}`;
});

function handleAddToCart() {
    if (page.props.auth?.user) {
        const userRole = page.props.auth.user.role || "customer";

        if (["admin", "staff"].includes(userRole.toLowerCase())) {
            toast.add({
                severity: "warn",
                summary: "Admin Test Mode",
                detail: "You're logged in as admin. This is a test cart operation. Real orders require customer login.",
                life: 5000,
            });
        }
    }

    if (!cartForm.size) {
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

function customizeProduct() {
    if (page.props.auth?.user) {
        const userRole = page.props.auth.user.role || "customer";

        if (["admin", "staff"].includes(userRole.toLowerCase())) {
            toast.add({
                severity: "info",
                summary: "Admin Test Mode",
                detail: "Testing designer functionality as admin. Customer login required for real orders.",
                life: 3000,
            });
        }
    }

    let productTypeSlug = "t-shirt";
    let productId = currentProduct.value?.id || null;

    if (currentProduct.value?.productType?.slug) {
        productTypeSlug = currentProduct.value.productType.slug;
    }

    const productSlug = normalizedProductSlug.value || null;

    if (productSlug) {
        router.visit(route("designer.create", { productType: productTypeSlug, product: productSlug }));
    } else if (productId) {
        router.visit(route("designer.create", { productType: productTypeSlug, product: productId }));
    } else {
        router.visit(route("designer.create", { productType: productTypeSlug }));
    }
}

async function shareProduct() {
    const shareData = {
        title: productName.value,
        text: `Check out this amazing ${productName.value}!`,
        url: window.location.href,
    };

    if (navigator.share && navigator.canShare?.(shareData)) {
        try {
            await navigator.share(shareData);
            return;
        } catch (err) {
            fallbackCopyToClipboard();
            return;
        }
    }

    fallbackCopyToClipboard();
}

async function fallbackCopyToClipboard() {
    try {
        await navigator.clipboard.writeText(window.location.href);
        toast.add({
            severity: "success",
            summary: "Success",
            detail: "Product link copied to clipboard!",
            life: 3000,
        });
    } catch (err) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to copy link to clipboard",
            life: 3000,
        });
    }
}

async function viewIn3D() {
    if (!productImages.value?.length) {
        toast.add({
            severity: "info",
            summary: "3D Preview",
            detail: "No images available for 3D preview",
            life: 3000,
        });
        return;
    }

    selectedTryOnImage.value = mainProductImage.value;
    show3DViewer.value = true;
    is3DMode.value = true;

    await nextTick();

    // Initialize the 3D viewer with the selected image
    if (product3DViewerRef.value?.loadProductTextureWithBackgroundRemoval) {
        product3DViewerRef.value.loadProductTextureWithBackgroundRemoval(selectedTryOnImage.value, true);
    }
    
    if (product3DViewerRef.value?.resetView) {
        product3DViewerRef.value.resetView();
    }
}

function close3DViewer() {
    show3DViewer.value = false;
}

function toggleViewerMode() {
    if (product3DViewerRef.value?.toggleAutoRotate) {
        product3DViewerRef.value.toggleAutoRotate();
    }
}

function selectTryOnImage(image) {
    selectedTryOnImage.value = image;
}
</script>

<template>
    <Head :title="productName" />

    <div class="min-h-screen bg-[radial-gradient(circle_at_top_right,rgba(109,93,252,0.08),transparent_22%),radial-gradient(circle_at_bottom_left,rgba(244,114,182,0.08),transparent_25%),linear-gradient(135deg,#faf7f2_0%,#ffffff_55%,#f7f1ff_100%)]">
        <!-- Header -->
        <div class="sticky top-0 z-30 border-b border-brand-gold/15 bg-white/85 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-brand-primary font-brand-elegant">
                            {{ productName }}
                        </h1>
                        <div class="mt-2 flex flex-wrap items-center gap-3">
                            <span class="text-brand-gold font-bold text-xl">
                                {{ productPrice }} SAR
                            </span>

                            <span
                                v-if="currentProduct?.is_template_based"
                                class="px-3 py-1 bg-brand-gold/10 text-brand-gold text-xs font-semibold rounded-full border border-brand-gold/20"
                            >
                                Template Design
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            @click="shareProduct"
                            class="p-3 rounded-xl border border-brand-gold/25 text-brand-gold hover:bg-brand-gold/10 transition-all"
                            title="Share Product"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                        </button>

                        <button
                            @click="viewIn3D"
                            class="p-3 rounded-xl border border-brand-gold/25 text-brand-gold hover:bg-brand-gold/10 transition-all"
                            title="3D Preview"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 xl:gap-14">
                <!-- Left -->
                <div class="lg:sticky lg:top-24 lg:self-start space-y-6">
                    <div class="bg-white rounded-3xl shadow-[0_25px_70px_rgba(20,25,50,0.08)] border border-brand-gold/10 overflow-hidden">
                        <div class="flex border-b border-brand-gold/10">
                            <button
                                @click="is3DMode = false"
                                :class="[
                                    'flex-1 py-4 font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2',
                                    !is3DMode
                                        ? 'bg-gradient-to-r from-brand-gold/10 to-brand-accent/10 text-brand-gold border-b-2 border-brand-gold'
                                        : 'text-brand-secondary hover:text-brand-primary hover:bg-brand-primary/5'
                                ]"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Gallery View
                            </button>

                            <button
                                @click="is3DMode = true"
                                :class="[
                                    'flex-1 py-4 font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2',
                                    is3DMode
                                        ? 'bg-gradient-to-r from-brand-gold/10 to-brand-accent/10 text-brand-gold border-b-2 border-brand-gold'
                                        : 'text-brand-secondary hover:text-brand-primary hover:bg-brand-primary/5'
                                ]"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                3D Female Model
                            </button>
                        </div>

                        <div class="p-5 sm:p-6 bg-[radial-gradient(circle_at_top,rgba(109,93,252,0.06),transparent_30%),linear-gradient(135deg,#faf7f2_0%,#ffffff_50%,#f7f1ff_100%)]">
                            <div v-show="!is3DMode" class="transition-all duration-500">
                                <ImageCarousel
                                    :images="productImages"
                                    :product-name="productName"
                                    class="w-full"
                                />
                            </div>

                            <div v-show="is3DMode" class="transition-all duration-500">
                                <!-- <Product3DViewer
                                    ref="product3DViewerRef"
                                    :images="productImages"
                                    :product-name="productName"
                                    :product-image="selectedTryOnImage"
                                    model-url="/models/female-avatar.glb"
                                    :auto-rotate="true"
                                    :show-grid="false"
                                />  -->
                            <Product3DViewer
                                    ref="product3DViewerRef"
                                    :product-name="productName"
                                    :images="productImages"
                                    :product-image="selectedTryOnImage"
                                    :height="680"
                                    :auto-rotate="true"
                                    :show-grid="false"
                                />
                            </div>
                        </div>

                        <!-- image selector for try-on -->
                        <div class="px-5 sm:px-6 pb-6">
                            <div class="mb-3 flex items-center justify-between">
                                <h3 class="text-sm font-bold text-brand-primary">Choose image for try-on</h3>
                                <span class="text-xs text-brand-secondary">أفضل نتيجة مع صورة شفافة</span>
                            </div>

                            <div class="grid grid-cols-4 sm:grid-cols-5 gap-3">
                                <button
                                    v-for="(image, index) in productImages"
                                    :key="`${image}-${index}`"
                                    @click="selectTryOnImage(image)"
                                    class="aspect-square rounded-2xl overflow-hidden border-2 transition-all bg-white"
                                    :class="selectedTryOnImage === image ? 'border-brand-gold shadow-lg ring-4 ring-brand-gold/10' : 'border-brand-gold/10 hover:border-brand-gold/40'"
                                >
                                    <img
                                        :src="image"
                                        :alt="`${productName} image ${index + 1}`"
                                        class="w-full h-full object-cover"
                                    />
                                </button>
                            </div>
                        </div>

                        <!-- badges -->
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

                <!-- Right -->
                <div class="space-y-8">
                    <div class="bg-white rounded-3xl shadow-[0_25px_70px_rgba(20,25,50,0.08)] border border-brand-gold/10 p-8">
                        <Price :price="productPrice" :product="currentProduct" />

                        <div class="mt-6 pt-6 border-t border-brand-gold/10">
                            <Reviews :product="currentProduct" />
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-[0_25px_70px_rgba(20,25,50,0.08)] border border-brand-gold/10 p-8">
                        <h3 class="text-xl font-bold text-brand-primary mb-6 font-brand-elegant">
                            Select Size
                        </h3>

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

                    <div class="bg-white rounded-3xl shadow-[0_25px_70px_rgba(20,25,50,0.08)] border border-brand-gold/10 p-8">
                        <h3 class="text-xl font-bold text-brand-primary mb-6 font-brand-elegant">
                            Quantity
                        </h3>

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

                        <div
                            v-if="cartDuplicationError"
                            class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"
                        >
                            {{ cartDuplicationError }}
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-[0_25px_70px_rgba(20,25,50,0.08)] border border-brand-gold/10 overflow-hidden">
                        <Description
                            :title="productName"
                            :description="productDescription"
                        />
                    </div>

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

        <!-- Modal -->
        <div
            v-if="show3DViewer"
            class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
        >
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-7xl max-h-[95vh] overflow-hidden flex flex-col">
                <div class="flex items-center justify-between p-6 border-b border-brand-gold/20 bg-gradient-to-r from-brand-cream to-brand-lavender/30">
                    <div class="flex items-center gap-4">
                        <button
                            @click="close3DViewer"
                            class="p-2 rounded-lg hover:bg-brand-primary/10 transition-colors"
                        >
                            <svg class="w-6 h-6 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <div>
                            <h3 class="text-2xl font-bold text-brand-primary font-brand-elegant">
                                3D Female Model Preview
                            </h3>
                            <p class="text-sm text-brand-secondary mt-1">{{ productName }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            @click="toggleViewerMode"
                            class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary/90 transition-colors font-semibold flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Toggle Rotation
                        </button>

                        <a
                            :href="productUrl"
                            class="px-4 py-2 border border-brand-gold/30 text-brand-gold rounded-lg hover:bg-brand-gold/10 transition-colors font-semibold"
                        >
                            Open Product
                        </a>
                    </div>
                </div>

                <div class="flex-1 bg-[radial-gradient(circle_at_top_right,rgba(109,93,252,0.08),transparent_25%),linear-gradient(135deg,#faf7f2_0%,#ffffff_60%,#f7f1ff_100%)] relative overflow-hidden min-h-[650px]">
                    <Product3DViewer
                        ref="product3DViewerRef"
                        :images="productImages"
                        :product-name="productName"
                        :product-image="selectedTryOnImage"
                        model-url="/models/female-avatar.glb"
                        :auto-rotate="true"
                        :show-grid="false"
                        :height="700"
                        fullscreen
                    />
                </div>

                <div class="p-6 border-t border-brand-gold/20 bg-brand-cream/30">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-full bg-brand-gold/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-brand-primary">Drag to Rotate</span>
                        </div>

                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-full bg-brand-gold/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-brand-primary">Scroll to Zoom</span>
                        </div>

                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-full bg-brand-gold/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-brand-primary">360° View</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Toast />
    </div>
</template>