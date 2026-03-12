<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    siteName: {
        type: String,
        default: "Ahlam's Girls"
    },
    tagline: {
        type: String,
        default: 'Elegance, Sewn to Perfection'
    },
    description: {
        type: String,
        default: 'Discover exquisite women\'s fashion with our curated collection of elegant dresses and sophisticated designs'
    }
});

const scrollToCollection = () => {
   const shopSection = document.getElementById('collection');
    if (shopSection) {
        shopSection.scrollIntoView({ behavior: 'smooth' });
    }
};
</script>

<template>
    <section class="relative gradient-brand min-h-screen flex items-center overflow-hidden">
        <!-- Background Decorative Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Elegant Woman Silhouette(Left) -->
            <div class="absolute -left-20 top-1/4 opacity-10">
                <svg width="600" height="800" viewBox="0 0 600 800" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M300 50 C 250 50, 220 100, 220 180 C 220 240, 250 280, 250 350 C 250 420, 200 500, 180 600 C 160 700, 150 750, 140 800 L 460 800 C 450 750, 440 700, 420 600 C 400 500, 350 420, 350 350 C 350 280, 380 240, 380 180 C 380 100, 350 50, 300 50 Z" 
                          fill="url(#womanGradient)" opacity="0.3"/>
                    <defs>
                        <linearGradient id="womanGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#d4af37" stop-opacity="0.3"/>
                            <stop offset="100%" stop-color="#e94560" stop-opacity="0.3"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            
            <!-- Transparent Flowers (Right) -->
            <div class="absolute -right-40 top-20 opacity-15 animate-float">
                <svg width="400" height="400" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="200" cy="200" r="80" fill="#f8b4c8" opacity="0.3"/>
                    <circle cx="200" cy="200" r="60" fill="#b19cd9" opacity="0.3"/>
                    <circle cx="200" cy="200" r="40" fill="#d4af37" opacity="0.3"/>
                    <!-- Petals -->
                    <ellipse cx="200" cy="120" rx="30" ry="60" fill="#f8b4c8" opacity="0.4"/>
                    <ellipse cx="200" cy="280" rx="30" ry="60" fill="#f8b4c8" opacity="0.4"/>
                    <ellipse cx="140" cy="200" rx="60" ry="30" fill="#b19cd9" opacity="0.4"/>
                    <ellipse cx="260" cy="200" rx="60" ry="30" fill="#b19cd9" opacity="0.4"/>
                </svg>
            </div>
            
            <!-- Additional Flower (Bottom Left) -->
            <div class="absolute -left-20 bottom-40 opacity-10 animate-float animation-delay-2000">
                <svg width="300" height="300" viewBox="0 0 300 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="150" cy="150" r="60" fill="#e94560" opacity="0.2"/>
                    <circle cx="150" cy="150" r="40" fill="#f8b4c8" opacity="0.3"/>
                    <circle cx="150" cy="150" r="20" fill="#d4af37" opacity="0.4"/>
                </svg>
            </div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Content Section -->
                <div class="text-center lg:text-left space-y-8">
                    <!-- Brand Name with Script Font -->
                    <div class="space-y-2">
                        <h1 class="text-6xl md:text-7xl lg:text-8xl font-brand-script text-brand-gold leading-tight drop-shadow-lg">
                            Ahlam's
                        </h1>
                        <div class="flex items-center justify-center lg:justify-start space-x-4">
                            <div class="h-px w-12 bg-brand-gold"></div>
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-brand-elegant text-white font-bold tracking-wide">
                                GIRLS
                            </h2>
                            <div class="h-px w-12 bg-brand-gold"></div>
                        </div>
                    </div>
                    
                    <!-- Tagline -->
                    <p class="text-2xl md:text-3xl text-brand-rose font-brand-elegant italic max-w-3xl drop-shadow">
                        {{ tagline }}
                    </p>
                    
                    <!-- Description -->
                    <p class="text-lg md:text-xl text-gray-200 max-w-2xl leading-relaxed">
                        {{ description }}
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <Link
                            href="#collection"
                            @click="scrollToCollection"
                            class="btn-luxury inline-flex items-center justify-center px-10 py-5 text-lg font-semibold rounded-full transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:-translate-y-1"
                        >
                            Shop Collection
                            <svg class="ml-2 -mr-1 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </Link>
                        
                        <Link
                            :href="route('designer.create', { productType: 'dress' })"
                            class="inline-flex items-center justify-center px-10 py-5 border-2 border-brand-gold text-lg font-semibold rounded-full text-brand-gold bg-transparent hover:bg-brand-gold hover:text-brand-primary transition-all duration-300 shadow-xl hover:shadow-2xl"
                        >
                            Design Your Dress
                            <svg class="ml-2 -mr-1 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </Link>
                    </div>

                    <!-- Features -->
                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-brand-gold border-opacity-30">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-brand-gold mb-2">✦</div>
                            <div class="text-sm text-brand-rose font-medium">Premium Quality</div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-brand-rose mb-2">♥</div>
                            <div class="text-sm text-brand-rose font-medium">Handcrafted</div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-brand-lavender mb-2">★</div>
                            <div class="text-sm text-brand-rose font-medium">Exclusive Designs</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Image Section -->
                <div class="relative hidden lg:block">
                    <!-- Central Elegant Dress Display -->
                    <div class="relative z-10 transform transition-all duration-700 hover:scale-105">
                        <div class="relative">
                            <!-- Dress Silhouette Placeholder -->
                            <div class="w-80 h-[500px] mx-auto gradient-luxury rounded-t-full rounded-b-3xl shadow-2xl flex items-center justify-center">
                                <span class="text-white text-6xl">👗</span>
                            </div>
                            <!-- Decorative Elements Around Dress -->
                            <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 text-brand-gold text-5xl animate-sparkle">✨</div>
                            <div class="absolute top-20 -right-10 text-brand-rose text-4xl animate-float">🌸</div>
                            <div class="absolute bottom-20 -left-10 text-brand-lavender text-4xl animate-float animation-delay-1000">🌺</div>
                        </div>
                    </div>
                    
                    <!-- Side Elements -->
                    <div class="absolute top-1/4 -left-10 transform rotate-[-10deg] opacity-60">
                        <div class="w-32 h-40 bg-gradient-to-br from-brand-gold to-brand-accent rounded-lg shadow-xl"></div>
                    </div>
                    
                    <div class="absolute bottom-1/4 -right-10 transform rotate-[10deg] opacity-60">
                        <div class="w-32 h-40 bg-gradient-to-br from-brand-rose to-brand-lavender rounded-lg shadow-xl"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Wave Divider -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" 
                      fill="#f8f9fa"/>
            </svg>
        </div>
    </section>
</template>

<style scoped>
/* Floating Animation */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Sparkle Animation */
@keyframes sparkle {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.2);
    }
}

.animate-sparkle {
    animation: sparkle 2s ease-in-out infinite;
}

.animation-delay-1000 {
    animation-delay: 1s;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

/* Custom Shadow */
.shadow-3xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
</style>
