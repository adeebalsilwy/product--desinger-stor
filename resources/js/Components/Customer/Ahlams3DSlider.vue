<template>
  <div class="w-full h-full relative">
    <!-- 3D Logo and Templates Display -->
    <div class="w-full h-full flex items-center justify-center perspective-1000">
      <!-- Main 3D Logo -->
      <div class="logo-container relative w-64 h-80 flex items-center justify-center">
        <div class="logo-3d bg-gradient-to-br from-brand-gold to-brand-accent rounded-2xl shadow-2xl flex items-center justify-center transform-style-3d w-full h-full">
          <div class="text-center p-4">
            <div class="text-4xl font-brand-script text-white mb-2">Ahlam's</div>
            <div class="text-3xl font-bold text-white">GIRLS</div>
            <div class="text-sm text-white/80 mt-2">3D Experience</div>
          </div>
        </div>
        
        <!-- Rotating Template Cards Around Logo -->
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="template-circle relative w-80 h-80">
            <!-- Template 1 -->
            <div class="absolute w-24 h-32 bg-gradient-to-br from-brand-rose to-brand-lavender rounded-lg shadow-lg transform rotate-y-0 translate-z-20 hover:scale-110 transition-transform duration-500 cursor-pointer"
                 style="transform: rotateY(0deg) translateZ(80px);">
              <div class="w-full h-full flex items-center justify-center text-white font-bold text-xs">Template 1</div>
            </div>
            
            <!-- Template 2 -->
            <div class="absolute w-24 h-32 bg-gradient-to-br from-brand-lavender to-brand-cream rounded-lg shadow-lg transform rotate-y-60 translate-z-20 hover:scale-110 transition-transform duration-500 cursor-pointer"
                 style="transform: rotateY(60deg) translateZ(80px);">
              <div class="w-full h-full flex items-center justify-center text-brand-primary font-bold text-xs">Template 2</div>
            </div>
            
            <!-- Template 3 -->
            <div class="absolute w-24 h-32 bg-gradient-to-br from-brand-cream to-brand-gold rounded-lg shadow-lg transform rotate-y-120 translate-z-20 hover:scale-110 transition-transform duration-500 cursor-pointer"
                 style="transform: rotateY(120deg) translateZ(80px);">
              <div class="w-full h-full flex items-center justify-center text-brand-primary font-bold text-xs">Template 3</div>
            </div>
            
            <!-- Template 4 -->
            <div class="absolute w-24 h-32 bg-gradient-to-br from-brand-gold to-brand-accent rounded-lg shadow-lg transform rotate-y-180 translate-z-20 hover:scale-110 transition-transform duration-500 cursor-pointer"
                 style="transform: rotateY(180deg) translateZ(80px);">
              <div class="w-full h-full flex items-center justify-center text-white font-bold text-xs">Template 4</div>
            </div>
            
            <!-- Template 5 -->
            <div class="absolute w-24 h-32 bg-gradient-to-br from-brand-accent to-brand-rose rounded-lg shadow-lg transform rotate-y-240 translate-z-20 hover:scale-110 transition-transform duration-500 cursor-pointer"
                 style="transform: rotateY(240deg) translateZ(80px);">
              <div class="w-full h-full flex items-center justify-center text-white font-bold text-xs">Template 5</div>
            </div>
            
            <!-- Template 6 -->
            <div class="absolute w-24 h-32 bg-gradient-to-br from-brand-rose to-brand-gold rounded-lg shadow-lg transform rotate-y-300 translate-z-20 hover:scale-110 transition-transform duration-500 cursor-pointer"
                 style="transform: rotateY(300deg) translateZ(80px);">
              <div class="w-full h-full flex items-center justify-center text-white font-bold text-xs">Template 6</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Controls -->
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2 z-20">
      <button class="w-2 h-2 rounded-full bg-white bg-opacity-50 hover:bg-opacity-100 transition-all"></button>
      <button class="w-2 h-2 rounded-full bg-white bg-opacity-50 hover:bg-opacity-100 transition-all"></button>
      <button class="w-2 h-2 rounded-full bg-white bg-opacity-50 hover:bg-opacity-100 transition-all"></button>
      <button class="w-2 h-2 rounded-full bg-white bg-opacity-50 hover:bg-opacity-100 transition-all"></button>
    </div>
  </div>
</template>

<script setup>
// Simple 3D CSS animation controller
import { onMounted, onUnmounted } from 'vue';

let animationInterval = null;

onMounted(() => {
  // Add continuous rotation animation
  const templateCircle = document.querySelector('.template-circle');
  if (templateCircle) {
    let rotation = 0;
    animationInterval = setInterval(() => {
      rotation += 0.5;
      templateCircle.style.transform = `rotateX(10deg) rotateY(${rotation}deg)`;
    }, 50);
  }
});

onUnmounted(() => {
  if (animationInterval) {
    clearInterval(animationInterval);
  }
});
</script>

<style scoped>
.perspective-1000 {
  perspective: 1000px;
}

.transform-style-3d {
  transform-style: preserve-3d;
}

.logo-container {
  animation: float 4s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px) rotateY(0deg); }
  50% { transform: translateY(-10px) rotateY(5deg); }
}

.template-circle {
  animation: rotateCircle 20s infinite linear;
}

@keyframes rotateCircle {
  0% { transform: rotateX(10deg) rotateY(0deg); }
  100% { transform: rotateX(10deg) rotateY(360deg); }
}

/* Hover effects */
.logo-3d:hover {
  transform: scale(1.05) rotateY(5deg);
}
</style>