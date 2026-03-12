<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  title: String,
  subtitle: String,
  series: Array,
  chartType: {
    type: String,
    default: 'line'
  },
  height: {
    type: [String, Number],
    default: 350
  },
  chartOptions: Object
});
</script>

<template>
  <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20">
    <div class="flex justify-between items-center mb-6">
      <h3 class="font-brand-elegant text-xl text-brand-primary">{{ title }}</h3>
      <div class="text-brand-gold">{{ subtitle }}</div>
    </div>
    <div class="h-80">
      <apexchart :type="chartType" :height="height" :options="chartOptions" :series="series"></apexchart>
    </div>
  </div>
</template>
