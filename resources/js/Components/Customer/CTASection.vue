<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: 'Ready to Create Your Perfect Shirt?'
    },
    subtitle: {
        type: String,
        default: 'Join thousands of satisfied customers and start designing today'
    },
    primaryButtonText: {
        type: String,
        default: 'Start Designing'
    },
    secondaryButtonText: {
        type: String,
        default: 'View Collection'
    }
});
</script>

<template>
    <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, white 2px, transparent 2px), radial-gradient(circle at 75% 75%, white 2px, transparent 2px); background-size: 50px 50px;"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                    {{ title }}
                </h2>
                <p class="text-xl text-blue-100 mb-10">
                    {{ subtitle }}
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        :href="route('designer.create', { productType: 't-shirt' })"
                        class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-medium rounded-lg text-blue-600 bg-white hover:bg-gray-50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                    >
                        {{ primaryButtonText }}
                        <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </Link>
                    
                    <Link
                        :href="route('home') + '#shop'"
                        class="inline-flex items-center justify-center px-8 py-4 border border-white text-base font-medium rounded-lg text-white bg-transparent hover:bg-white hover:text-blue-600 transition-all duration-300"
                    >
                        {{ secondaryButtonText }}
                        <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </Link>
                </div>
            </div>
            
            <!-- Floating Elements -->
            <div class="absolute top-10 left-10 text-blue-300 opacity-30 animate-pulse">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                </svg>
            </div>
            
            <div class="absolute bottom-10 right-10 text-purple-300 opacity-30 animate-pulse animation-delay-1000">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
    </section>
</template>

<style scoped>
.animation-delay-1000 {
    animation-delay: 1s;
}
</style>