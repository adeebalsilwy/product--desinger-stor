<template>
  <div class="professional-model-showcase">
    <!-- Main Model Container -->
    <div class="model-container">
      <!-- Background Elements -->
      <div class="model-background">
        <div class="gradient-overlay"></div>
        <div class="floating-particles">
          <div class="particle" v-for="i in 12" :key="i" :style="getParticleStyle(i)"></div>
        </div>
      </div>
      
      <!-- Professional Model -->
      <div class="model-figure" :class="{ 'model-loaded': modelLoaded }">
        <!-- Head -->
        <div class="model-head">
          <div class="head-highlight"></div>
          <div class="hair-strands">
            <div class="strand" v-for="i in 8" :key="i" :style="getStrandStyle(i)"></div>
          </div>
        </div>
        
        <!-- Torso -->
        <div class="model-torso">
          <div class="torso-shading"></div>
          <div class="collar-detail"></div>
        </div>
        
        <!-- Arms -->
        <div class="model-arms">
          <div class="arm left-arm">
            <div class="arm-shading"></div>
          </div>
          <div class="arm right-arm">
            <div class="arm-shading"></div>
          </div>
        </div>
        
        <!-- Legs -->
        <div class="model-legs">
          <div class="leg left-leg">
            <div class="leg-shading"></div>
          </div>
          <div class="leg right-leg">
            <div class="leg-shading"></div>
          </div>
        </div>
        
        <!-- Base Platform -->
        <div class="model-platform">
          <div class="platform-glow"></div>
          <div class="platform-reflection"></div>
        </div>
      </div>
      
      <!-- Floating Products Display -->
      <div class="floating-products">
        <div class="floating-item" v-for="(item, index) in floatingItems" 
             :key="index" 
             :style="getFloatingItemStyle(index)"
             @mouseenter="hoveredItem = index"
             @mouseleave="hoveredItem = null">
          <div class="item-content">
            <i :class="item.icon"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const modelLoaded = ref(false)
const hoveredItem = ref(null)

const floatingItems = ref([
  { icon: 'pi pi-star-fill', color: '#FFD700' },
  { icon: 'pi pi-heart', color: '#FF6B6B' },
  { icon: 'pi pi-shopping-cart', color: '#4ECDC4' },
  { icon: 'pi pi-tag', color: '#45B7D1' },
  { icon: 'pi pi-crown', color: '#96CEB4' },
  { icon: 'pi pi-gift', color: '#FFEAA7' }
])

onMounted(() => {
  // Simulate model loading
  setTimeout(() => {
    modelLoaded.value = true
  }, 300)
})

const getParticleStyle = (index) => {
  const delay = (index * 0.2) % 2
  const size = 4 + (index % 3) * 2
  return {
    width: `${size}px`,
    height: `${size}px`,
    animationDelay: `${delay}s`,
    left: `${10 + (index * 15) % 80}%`,
    top: `${20 + (index * 12) % 60}%`
  }
}

const getStrandStyle = (index) => {
  return {
    transform: `rotate(${index * 45}deg) translateY(-5px)`,
    animationDelay: `${index * 0.1}s`
  }
}

const getFloatingItemStyle = (index) => {
  const angle = (index * 60) % 360
  const radius = 120
  const x = Math.cos((angle * Math.PI) / 180) * radius
  const y = Math.sin((angle * Math.PI) / 180) * radius
  const delay = index * 0.2
  
  return {
    transform: `translate(${x}px, ${y}px)`,
    animationDelay: `${delay}s`,
    zIndex: hoveredItem.value === index ? 10 : 5
  }
}
</script>

<style scoped>
.professional-model-showcase {
  position: relative;
  width: 100%;
  height: 600px;
  display: flex;
  align-items: center;
  justify-content: center;
  perspective: 1200px;
}

.model-container {
  position: relative;
  width: 100%;
  height: 100%;
  max-width: 500px;
  transform-style: preserve-3d;
}

/* Background */
.model-background {
  position: absolute;
  inset: 0;
  border-radius: 20px;
  overflow: hidden;
}

.gradient-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, 
    rgba(102, 126, 234, 0.1) 0%, 
    rgba(126, 75, 162, 0.15) 50%,
    rgba(85, 104, 211, 0.1) 100%);
  backdrop-filter: blur(20px);
}

.floating-particles {
  position: absolute;
  inset: 0;
}

.particle {
  position: absolute;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 50%;
  opacity: 0.6;
  animation: float-particle 4s ease-in-out infinite;
}

@keyframes float-particle {
  0%, 100% {
    transform: translateY(0px) scale(1);
    opacity: 0.3;
  }
  50% {
    transform: translateY(-20px) scale(1.2);
    opacity: 0.7;
  }
}

/* Main Model Figure */
.model-figure {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 200px;
  height: 450px;
  opacity: 0;
  transform: translate(-50%, -50%) scale(0.8);
  transition: all 1s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.model-figure.model-loaded {
  opacity: 1;
  transform: translate(-50%, -50%) scale(1);
}

/* Head */
.model-head {
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 90px;
  background: linear-gradient(135deg, #f8d1a8 0%, #e8b896 50%, #d4a574 100%);
  border-radius: 40px 40px 35px 35px;
  box-shadow: 
    inset -8px -8px 20px rgba(0, 0, 0, 0.1),
    inset 8px 8px 20px rgba(255, 255, 255, 0.4),
    0 15px 35px rgba(0, 0, 0, 0.2);
  z-index: 8;
}

.head-highlight {
  position: absolute;
  top: 15px;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 40px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.5) 0%, transparent 100%);
  border-radius: 30px;
}

.hair-strands {
  position: absolute;
  top: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 60px;
}

.strand {
  position: absolute;
  top: 0;
  left: 50%;
  width: 8px;
  height: 40px;
  background: linear-gradient(to bottom, #2c1810, #4a2c1f);
  border-radius: 4px;
  transform-origin: top center;
  animation: hair-flow 3s ease-in-out infinite;
}

@keyframes hair-flow {
  0%, 100% { transform: rotate(0deg); }
  25% { transform: rotate(5deg); }
  75% { transform: rotate(-5deg); }
}

/* Torso */
.model-torso {
  position: absolute;
  top: 85px;
  left: 50%;
  transform: translateX(-50%);
  width: 180px;
  height: 220px;
  background: linear-gradient(135deg, #2c3e50 0%, #1a2530 100%);
  border-radius: 40px 40px 30px 30px;
  box-shadow: 
    inset -12px -12px 30px rgba(0, 0, 0, 0.3),
    inset 12px 12px 30px rgba(255, 255, 255, 0.1),
    0 25px 50px rgba(0, 0, 0, 0.3);
  z-index: 7;
}

.torso-shading {
  position: absolute;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  width: 150px;
  height: 180px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
  border-radius: 30px;
}

.collar-detail {
  position: absolute;
  top: 15px;
  left: 50%;
  transform: translateX(-50%);
  width: 120px;
  height: 8px;
  background: linear-gradient(90deg, #c0c0c0, #e0e0e0, #c0c0c0);
  border-radius: 4px;
}

/* Arms */
.model-arms {
  position: absolute;
  top: 100px;
  left: 50%;
  transform: translateX(-50%);
  width: 280px;
  height: 200px;
  z-index: 6;
}

.arm {
  position: absolute;
  width: 50px;
  height: 200px;
  background: linear-gradient(135deg, #f8d1a8 0%, #e8b896 50%, #d4a574 100%);
  border-radius: 25px;
  box-shadow: 
    inset -5px -5px 15px rgba(0, 0, 0, 0.1),
    inset 5px 5px 15px rgba(255, 255, 255, 0.3),
    0 10px 25px rgba(0, 0, 0, 0.2);
}

.left-arm {
  left: 0;
  transform: rotate(20deg);
  transform-origin: top center;
  animation: arm-sway-left 4s ease-in-out infinite;
}

.right-arm {
  right: 0;
  transform: rotate(-20deg);
  transform-origin: top center;
  animation: arm-sway-right 4s ease-in-out infinite;
}

.arm-shading {
  position: absolute;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  width: 30px;
  height: 160px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
  border-radius: 15px;
}

@keyframes arm-sway-left {
  0%, 100% { transform: rotate(20deg); }
  50% { transform: rotate(25deg); }
}

@keyframes arm-sway-right {
  0%, 100% { transform: rotate(-20deg); }
  50% { transform: rotate(-25deg); }
}

/* Legs */
.model-legs {
  position: absolute;
  top: 290px;
  left: 50%;
  transform: translateX(-50%);
  width: 120px;
  height: 160px;
  z-index: 5;
}

.leg {
  position: absolute;
  width: 45px;
  height: 160px;
  background: linear-gradient(135deg, #2c3e50 0%, #1a2530 100%);
  border-radius: 22px;
  box-shadow: 
    inset -5px -5px 15px rgba(0, 0, 0, 0.3),
    inset 5px 5px 15px rgba(255, 255, 255, 0.1),
    0 15px 30px rgba(0, 0, 0, 0.25);
}

.left-leg {
  left: 0;
  animation: leg-move-left 4s ease-in-out infinite;
}

.right-leg {
  right: 0;
  animation: leg-move-right 4s ease-in-out infinite;
}

.leg-shading {
  position: absolute;
  top: 15px;
  left: 50%;
  transform: translateX(-50%);
  width: 25px;
  height: 130px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.15) 0%, transparent 100%);
  border-radius: 12px;
}

@keyframes leg-move-left {
  0%, 100% { transform: rotate(0deg); }
  25% { transform: rotate(-5deg); }
  75% { transform: rotate(3deg); }
}

@keyframes leg-move-right {
  0%, 100% { transform: rotate(0deg); }
  25% { transform: rotate(5deg); }
  75% { transform: rotate(-3deg); }
}

/* Platform */
.model-platform {
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 250px;
  height: 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  opacity: 0.4;
  filter: blur(15px);
  z-index: 1;
}

.platform-glow {
  position: absolute;
  inset: -20px;
  background: radial-gradient(circle, rgba(102, 126, 234, 0.3) 0%, transparent 70%);
  border-radius: 50%;
  animation: platform-glow 3s ease-in-out infinite;
}

.platform-reflection {
  position: absolute;
  top: -30px;
  left: 50%;
  transform: translateX(-50%);
  width: 200px;
  height: 8px;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  border-radius: 50%;
  filter: blur(2px);
}

@keyframes platform-glow {
  0%, 100% { opacity: 0.5; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.1); }
}

/* Floating Products */
.floating-products {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 300px;
  height: 300px;
}

.floating-item {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 
    0 10px 30px rgba(102, 126, 234, 0.3),
    inset 5px 5px 10px rgba(255, 255, 255, 0.2),
    inset -5px -5px 10px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  animation: float-item 6s ease-in-out infinite;
}

.floating-item:hover {
  transform: translate(-50%, -50%) scale(1.2);
  box-shadow: 
    0 15px 40px rgba(102, 126, 234, 0.5),
    inset 8px 8px 15px rgba(255, 255, 255, 0.3),
    inset -8px -8px 15px rgba(0, 0, 0, 0.2);
}

.item-content {
  font-size: 20px;
  color: white;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

@keyframes float-item {
  0%, 100% { 
    transform: translate(-50%, -50%) translateY(0px) rotate(0deg);
  }
  25% { 
    transform: translate(-50%, -50%) translateY(-15px) rotate(5deg);
  }
  50% { 
    transform: translate(-50%, -50%) translateY(-25px) rotate(0deg);
  }
  75% { 
    transform: translate(-50%, -50%) translateY(-15px) rotate(-5deg);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .professional-model-showcase {
    height: 450px;
  }
  
  .model-figure {
    width: 150px;
    height: 340px;
  }
  
  .model-head {
    width: 60px;
    height: 70px;
  }
  
  .model-torso {
    width: 140px;
    height: 170px;
    top: 65px;
  }
  
  .model-arms {
    width: 210px;
    height: 150px;
    top: 75px;
  }
  
  .arm {
    width: 38px;
    height: 150px;
  }
  
  .model-legs {
    width: 90px;
    height: 120px;
    top: 220px;
  }
  
  .leg {
    width: 35px;
    height: 120px;
  }
  
  .floating-products {
    width: 240px;
    height: 240px;
  }
  
  .floating-item {
    width: 40px;
    height: 40px;
  }
  
  .item-content {
    font-size: 16px;
  }
}

/* Night Mode Support */
body.night-mode .gradient-overlay {
  background: linear-gradient(135deg, 
    rgba(110, 174, 255, 0.1) 0%, 
    rgba(92, 138, 230, 0.15) 50%,
    rgba(74, 126, 255, 0.1) 100%);
}

body.night-mode .model-head {
  background: linear-gradient(135deg, #d1b89d 0%, #b89f86 50%, #9e8770 100%);
}

body.night-mode .model-torso,
body.night-mode .leg {
  background: linear-gradient(135deg, #3a4b63 0%, #253245 100%);
}

body.night-mode .platform-glow {
  background: radial-gradient(circle, rgba(110, 174, 255, 0.3) 0%, transparent 70%);
}
</style>