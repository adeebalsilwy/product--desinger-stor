<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    routeName: {
        type: String,
        required: true,
    },
    activeComponent: {
        type: String,
        required: true,
    },
    icon: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    count: {
        type: [Number, String],
        required: true,
    },
    color: {
        type: String,
        default: 'accent', // Default to accent for brand consistency
    },
});
</script>

<template>
  <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20 transition-all duration-300 hover:scale-[1.02]">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-2xl font-bold text-brand-primary">{{ title }}</p>
        <p class="text-3xl font-bold text-brand-gold mt-2">{{ value }}</p>
      </div>
      <div class="text-4xl" :class="iconClass">{{ icon }}</div>
    </div>
    <div class="mt-4">
      <p class="text-sm text-brand-secondary">{{ description }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  value: [String, Number],
  description: String,
  icon: String,
  iconClass: {
    type: String,
    default: 'text-brand-rose'
  }
});
</script>

<style scoped></style>
