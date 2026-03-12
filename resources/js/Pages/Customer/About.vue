<template>
  <CustomerLayout>
    <div class="min-h-screen bg-gradient-to-br from-brand-bg to-brand-primary py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
          <h1 class="font-brand-elegant text-4xl text-white mb-4">About Ahlam's Girls</h1>
          <p class="text-brand-gold text-xl">Our Story of Elegance and Sophistication</p>
        </div>

        <div class="bg-white bg-opacity-90 backdrop-blur-sm rounded-2xl p-8 border border-brand-gold border-opacity-20 mb-12">
          <div class="prose prose-lg max-w-none text-brand-secondary">
            <h2 class="font-brand-elegant text-2xl text-brand-primary mb-6">Our Vision</h2>
            <p class="mb-6">
              Founded with a vision to bring elegance and sophistication to women's fashion, Ahlam's Girls represents the epitome of luxury and craftsmanship. Our designs blend timeless elegance with contemporary style, creating pieces that celebrate the modern woman.
            </p>

            <h2 class="font-brand-elegant text-2xl text-brand-primary mt-8 mb-6">Our Mission</h2>
            <p class="mb-6">
              We strive to create exquisite fashion that empowers women to feel confident, beautiful, and elegant. Every creation is meticulously crafted with premium materials and attention to detail, ensuring that each piece tells a story of grace, beauty, and refinement.
            </p>

            <h2 class="font-brand-elegant text-2xl text-brand-primary mt-8 mb-6">Our Commitment</h2>
            <p class="mb-6">
              At Ahlam's Girls, we are committed to delivering exceptional quality, personalized service, and innovative designs that reflect the latest trends in luxury fashion. We believe that every woman deserves to feel special and elegant in her attire.
            </p>

            <h2 class="font-brand-elegant text-2xl text-brand-primary mt-8 mb-6">The Ahlam's Girls Experience</h2>
            <p>
              "Elegance, Sewn to Perfection" - This is more than our tagline; it's our promise. We invite you to experience the luxury of Ahlam's Girls, where every detail is carefully considered to create the perfect ensemble for the sophisticated woman.
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20 text-center">
            <div class="text-4xl mb-4">🧵</div>
            <h3 class="font-brand-elegant text-xl text-brand-primary mb-2">Premium Quality</h3>
            <p class="text-brand-secondary">Crafted with the finest materials for lasting elegance</p>
          </div>

          <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20 text-center">
            <div class="text-4xl mb-4">🎨</div>
            <h3 class="font-brand-elegant text-xl text-brand-primary mb-2">Custom Designs</h3>
            <p class="text-brand-secondary">Personalized creations tailored to your style</p>
          </div>

          <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20 text-center">
            <div class="text-4xl mb-4">💎</div>
            <h3 class="font-brand-elegant text-xl text-brand-primary mb-2">Luxury Experience</h3>
            <p class="text-brand-secondary">Boutique service with premium attention to detail</p>
          </div>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup>
import CustomerLayout from '@/Layouts/Customer.vue';
</script>