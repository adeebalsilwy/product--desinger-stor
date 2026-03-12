<template>
  <div
    class="flex flex-col justify-between p-4 rounded-lg h-32 shadow-lg"
    :class="{
      'bg-white border border-gray-200': true,
      'border-blue-500': color === 'blue',
      'border-emerald-500': color === 'emerald',
      'border-amber-500': color === 'amber',
      'border-rose-500': color === 'rose',
      'border-indigo-500': color === 'indigo',
    }"
  >
    <div class="flex justify-between items-start">
      <div>
        <p class="text-gray-500 text-sm font-medium">{{ label }}</p>
        <p class="font-bold text-2xl mt-1 text-gray-800">
          {{ value }}
        </p>
      </div>
      <div
        :class="{
          'text-blue-500': color === 'blue',
          'text-emerald-500': color === 'emerald',
          'text-amber-500': color === 'amber',
          'text-rose-500': color === 'rose',
          'text-indigo-500': color === 'indigo',
        }"
        class="text-xl"
      >
        {{ icon }}
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  value: {
    type: [Number, String],
    required: true,
  },
  label: {
    type: String,
    required: true,
  },
  icon: {
    type: String,
    required: true,
  },
  color: {
    type: String,
    default: 'blue',
  },
});
</script>