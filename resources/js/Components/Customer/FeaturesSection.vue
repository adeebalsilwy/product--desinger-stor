<script setup>
import { inject } from 'vue';

// Use the global settings provided by the plugin
const settings = inject('brandSettings');
const siteName = settings ? settings.value?.site_name : "Ahlam's Girls";
</script>

<template>
  <section class="py-16 bg-gradient-to-br from-brand-bg to-brand-primary">
    <div class="container mx-auto px-4">
      <div class="text-center mb-12">
        <h2 class="font-brand-elegant text-4xl text-white mb-4">Why Choose {{ siteName || "Ahlam's Girls" }}</h2>
        <p class="text-brand-gold text-xl max-w-2xl mx-auto">Experience luxury and elegance in every stitch</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="text-center p-6 bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl border border-brand-gold border-opacity-20">
          <div class="text-4xl mb-4">🧵</div>
          <h3 class="font-brand-elegant text-xl text-white mb-2">Premium Quality</h3>
          <p class="text-brand-gold">Crafted with the finest materials for lasting elegance</p>
        </div>

        <div class="text-center p-6 bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl border border-brand-gold border-opacity-20">
          <div class="text-4xl mb-4">🎨</div>
          <h3 class="font-brand-elegant text-xl text-white mb-2">Custom Designs</h3>
          <p class="text-brand-gold">Personalized creations tailored to your style</p>
        </div>

        <div class="text-center p-6 bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl border border-brand-gold border-opacity-20">
          <div class="text-4xl mb-4">🚚</div>
          <h3 class="font-brand-elegant text-xl text-white mb-2">Fast Delivery</h3>
          <p class="text-brand-gold">Swift shipping to bring elegance to your doorstep</p>
        </div>

        <div class="text-center p-6 bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl border border-brand-gold border-opacity-20">
          <div class="text-4xl mb-4">💎</div>
          <h3 class="font-brand-elegant text-xl text-white mb-2">Luxury Experience</h3>
          <p class="text-brand-gold">Boutique service with premium attention to detail</p>
        </div>
      </div>
    </div>
  </section>
</template>
