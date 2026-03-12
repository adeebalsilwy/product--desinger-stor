<script setup>
import Arrow from "@/Icons/Arrow.vue";
import { ref, computed } from "vue";

const props = defineProps({
    images: {
        type: Array,
        required: true,
    },
});

const currentImageIndex = ref(0);
const direction = ref("right");

const navigateNext = () => {
    direction.value = "right";
    currentImageIndex.value =
        (currentImageIndex.value + 1) % props.images.length;
};

const navigatePrev = () => {
    direction.value = "left";
    currentImageIndex.value =
        (currentImageIndex.value - 1 + props.images.length) %
        props.images.length;
};

const selectImage = (index) => {
    direction.value = index > currentImageIndex.value ? "right" : "left";
    currentImageIndex.value = index;
};

// Handle image loading errors
const handleImageError = (event) => {
    event.target.src = '/images/logo.jpeg';
};
</script>

<template>
    <div class="relative w-full max-w-lg mx-auto overflow-hidden rounded-2xl shadow-2xl">
        <!-- Main Image with Slide Transition -->
        <div class="relative w-full aspect-square overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
            <transition
                :name="direction === 'right' ? 'slide-right' : 'slide-left'"
                mode="out-in"
            >
                <img
                    :src="images[currentImageIndex]"
                    :key="currentImageIndex"
                    class="absolute w-full h-full object-cover transition-all duration-300"
                    @error="handleImageError"
                />
            </transition>
            
            <!-- Image Counter -->
            <div class="absolute bottom-4 right-4 bg-black/50 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm">
                {{ currentImageIndex + 1 }} / {{ images.length }}
            </div>
        </div>

        <!-- Navigation Arrows -->
        <button
            v-if="images.length > 1"
            @click="navigatePrev"
            class="absolute left-4 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-white hover:shadow-xl transition-all duration-300 border border-white/50"
            :disabled="images.length <= 1"
        >
            <Arrow class="w-5 h-5 text-gray-700 hover:text-gray-900 transition-colors" />
        </button>
        <button
            v-if="images.length > 1"
            @click="navigateNext"
            class="absolute right-4 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-white hover:shadow-xl transition-all duration-300 border border-white/50"
            :disabled="images.length <= 1"
        >
            <Arrow class="w-5 h-5 text-gray-700 hover:text-gray-900 transition-colors rotate-180" />
        </button>

        <!-- Thumbnail Navigation -->
        <div v-if="images.length > 1" class="flex justify-center mt-4 space-x-2 px-4">
            <div
                v-for="(image, index) in images"
                :key="index"
                @click="selectImage(index)"
                class="w-16 h-16 p-1 cursor-pointer border-2 transition-all rounded-lg bg-gray-100 hover:bg-gray-200 flex-shrink-0"
                :class="{
                    'border-blue-500 bg-blue-50 scale-105 shadow-md': index === currentImageIndex,
                    'border-gray-300 hover:border-gray-400': index !== currentImageIndex,
                }"
            >
                <img 
                    :src="image" 
                    class="w-full h-full object-cover rounded-md" 
                    @error="handleImageError"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Slide Transitions */
.slide-right-enter-active,
.slide-right-leave-active,
.slide-left-enter-active,
.slide-left-leave-active {
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.slide-right-enter-from {
    opacity: 0;
    transform: translateX(100%) scale(0.95);
}
.slide-right-leave-to {
    opacity: 0;
    transform: translateX(-100%) scale(0.95);
}

.slide-left-enter-from {
    opacity: 0;
    transform: translateX(-100%) scale(0.95);
}
.slide-left-leave-to {
    opacity: 0;
    transform: translateX(100%) scale(0.95);
}

/* 3D Product Effects */
.product-3d-viewer {
    transform-style: preserve-3d;
    perspective: 1200px;
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.product-3d-viewer:hover {
    transform: perspective(1200px) rotateX(5deg) rotateY(5deg) translateZ(20px);
    box-shadow: 
        0 30px 60px -12px rgba(0, 0, 0, 0.25),
        0 0 0 1px rgba(0, 0, 0, 0.05),
        inset 0 0 100px rgba(255, 255, 255, 0.1);
}

/* Enhanced button effects */
.floating-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.floating-btn:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
}
</style>