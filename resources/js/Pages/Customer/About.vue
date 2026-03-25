<template>
  <CustomerLayout>
    <div class="about-page-container">
      <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
          <h1 class="about-title">{{ $t('about.title') }}</h1>
          <p class="about-subtitle">{{ $t('about.subtitle') }}</p>
        </div>

        <div class="neumorphic-card main-content">
          <div class="prose prose-lg max-w-none text-brand-secondary">
            <h2 class="section-title">{{ $t('about.vision_title') }}</h2>
            <p class="section-text">
              {{ $t('about.vision_text') }}
            </p>

            <h2 class="section-title">{{ $t('about.mission_title') }}</h2>
            <p class="section-text">
              {{ $t('about.mission_text') }}
            </p>

            <h2 class="section-title">{{ $t('about.commitment_title') }}</h2>
            <p class="section-text">
              {{ $t('about.commitment_text') }}
            </p>

            <h2 class="section-title">{{ $t('about.experience_title') }}</h2>
            <p class="section-text">
              {{ $t('about.experience_text') }}
            </p>
          </div>
        </div>

        <div class="features-grid">
          <div class="neumorphic-box feature-box">
            <div class="feature-icon">🧵</div>
            <h3 class="feature-title">{{ $t('about.features.premium_title') }}</h3>
            <p class="feature-text">{{ $t('about.features.premium_text') }}</p>
          </div>

          <div class="neumorphic-box feature-box">
            <div class="feature-icon">🎨</div>
            <h3 class="feature-title">{{ $t('about.features.custom_title') }}</h3>
            <p class="feature-text">{{ $t('about.features.custom_text') }}</p>
          </div>

          <div class="neumorphic-box feature-box">
            <div class="feature-icon">💎</div>
            <h3 class="feature-title">{{ $t('about.features.luxury_title') }}</h3>
            <p class="feature-text">{{ $t('about.features.luxury_text') }}</p>
          </div>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/Customer.vue';
</script>

<style scoped>
/* About Page Container */
.about-page-container {
  background: linear-gradient(135deg, #e0e5ec 0%, #c9d6ff 100%);
  min-height: 100vh;
  padding: 50px 20px;
  position: relative;
}

body.night-mode .about-page-container {
  background: linear-gradient(135deg, #1e1e1e 0%, #3a3a5a 100%);
}

/* About Title */
.about-title {
  font-family: 'Dancing Script', cursive;
  font-size: 48px;
  color: #4a5568;
  margin-bottom: 10px;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

body.night-mode .about-title {
  color: #ffffff;
}

.about-subtitle {
  font-size: 20px;
  color: #4a5568;
}

body.night-mode .about-subtitle {
  color: #ffffff;
}

/* Neumorphic Card */
.neumorphic-card {
  background: #e0e5ec;
  border-radius: 20px;
  padding: 40px;
  box-shadow:
    8px 8px 15px #a3b1c6,
    -8px -8px 15px #ffffff;
  margin-bottom: 30px;
}

body.night-mode .neumorphic-card {
  background: #2d3748;
  box-shadow:
    8px 8px 15px #1a202c,
    -8px -8px 15px #4a5568;
}

.main-content {
  border: none;
}

/* Section Title */
.section-title {
  font-family: 'Dancing Script', cursive;
  font-size: 32px;
  color: #4a7eff;
  margin-bottom: 20px;
}

body.night-mode .section-title {
  color: #6ea8ff;
}

/* Section Text */
.section-text {
  font-size: 16px;
  line-height: 1.8;
  color: #4a5568;
  margin-bottom: 25px;
}

body.night-mode .section-text {
  color: #e2e8f0;
}

/* Features Grid */
.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 25px;
  margin-top: 40px;
}

.feature-box {
  background: #e0e5ec;
  border-radius: 20px;
  padding: 30px;
  box-shadow:
    8px 8px 15px #a3b1c6,
    -8px -8px 15px #ffffff;
  transition: all 0.3s ease;
  text-align: center;
}

.feature-box:hover {
  box-shadow:
    inset 4px 4px 8px #a3b1c6,
    inset -4px -4px 8px #ffffff;
  transform: translateY(-5px);
}

body.night-mode .feature-box {
  background: #2d3748;
  box-shadow:
    8px 8px 15px #1a202c,
    -8px -8px 15px #4a5568;
}

body.night-mode .feature-box:hover {
  box-shadow:
    inset 4px 4px 8px #1a202c,
    inset -4px -4px 8px #4a5568;
}

/* Feature Icon */
.feature-icon {
  font-size: 48px;
  margin-bottom: 15px;
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

/* Feature Title */
.feature-title {
  font-family: 'Dancing Script', cursive;
  font-size: 24px;
  color: #4a7eff;
  margin-bottom: 10px;
}

body.night-mode .feature-title {
  color: #6ea8ff;
}

/* Feature Text */
.feature-text {
  font-size: 15px;
  color: #4a5568;
  line-height: 1.6;
}

body.night-mode .feature-text {
  color: #cbd5e0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .about-page-container {
    padding: 30px 15px;
  }
  
  .about-title {
    font-size: 36px;
  }
  
  .neumorphic-card {
    padding: 25px;
  }
  
  .features-grid {
    grid-template-columns: 1fr;
  }
}
</style>
