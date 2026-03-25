<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: 'Threads That Speak Your Language'
    },
    subtitle: {
        type: String,
        default: 'Premium dev shirts, bug-free guarantee'
    },
    ctaText: {
        type: String,
        default: 'Start Shopping'
    },
    ctaLink: {
        type: String,
        default: '#shop'
    }
});

const heroImages = ref([
    '/assets/home_images/left.png',
    '/assets/home_images/middle.png',
    '/assets/home_images/right.png'
]);

const page = usePage();
const defaultProductType = computed(() => page.props.defaultProductType || 't-shirt');

const scrollToShop = () => {
    const shopSection = document.getElementById('shop');
    if (shopSection) {
        shopSection.scrollIntoView({ behavior: 'smooth' });
    }
};
</script>

<template>
    <section class="relative bg-gradient-to-br from-blue-50 via-white to-purple-50 overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute top-40 left-1/2 w-80 h-80 bg-pink-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Content -->
                <div class="text-center lg:text-left space-y-8">
                    <div class="space-y-4">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                            <span class="block">{{ title }}</span>
                        </h1>
                        <p class="text-xl md:text-2xl text-gray-600 max-w-2xl">
                            {{ subtitle }}
                        </p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <Link
                            :href="ctaLink"
                            @click="ctaLink === '#shop' ? scrollToShop() : null"
                            class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                        >
                            {{ ctaText }}
                            <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </Link>
                        
                        <Link
                            :href="route('designer.create', { productType: defaultProductType })"
                            class="inline-flex items-center justify-center px-8 py-4 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all duration-300 shadow-md hover:shadow-lg"
                        >
                            Design Your Own
                            <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </Link>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-8 pt-8 border-t border-gray-200">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">1000+</div>
                            <div class="text-sm text-gray-600">Happy Customers</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600">5000+</div>
                            <div class="text-sm text-gray-600">Designs Created</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-pink-600">24/7</div>
                            <div class="text-sm text-gray-600">Support</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Images -->
                <div class="relative hidden lg:block">
                    <div class="relative">
                        <!-- Left T-shirt -->
                        <div class="absolute -top-10 -left-10 transform rotate-[-15deg] transition-all duration-700 hover:rotate-[-10deg] hover:scale-105">
                            <img 
                                :src="heroImages[0]" 
                                alt="T-shirt Design" 
                                class="w-48 h-auto drop-shadow-2xl"
                            />
                        </div>
                        
                        <!-- Center T-shirt -->
                        <div class="relative z-10 transform transition-all duration-700 hover:scale-110">
                            <img 
                                :src="heroImages[1]" 
                                alt="Featured T-shirt" 
                                class="w-64 h-auto mx-auto drop-shadow-2xl"
                            />
                        </div>
                        
                        <!-- Right T-shirt -->
                        <div class="absolute -top-10 -right-10 transform rotate-[15deg] transition-all duration-700 hover:rotate-[10deg] hover:scale-105">
                            <img 
                                :src="heroImages[2]" 
                                alt="T-shirt Design" 
                                class="w-48 h-auto drop-shadow-2xl"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Elements -->
        <div class="absolute top-1/4 left-4 text-blue-400 opacity-20 animate-pulse">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
            </svg>
        </div>
        
        <div class="absolute bottom-1/4 right-4 text-purple-400 opacity-20 animate-pulse animation-delay-1000">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
            </svg>
        </div>
    </section>
</template>

<style scoped>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animation-delay-1000 {
    animation-delay: 1s;
}
</style>
