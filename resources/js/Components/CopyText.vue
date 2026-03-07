<script setup>
import { ref } from 'vue';

// Props
const props = defineProps({
  text: {
    type: String,
    required: true,
  },
  message: {
    type: String,
    default: 'Copied!',
  },
});

// State to track if text is copied
const copied = ref(false);

// Copy function
const copyToClipboard = async (event) => {
  event.stopPropagation(); // Prevent the click event from bubbling up

  try {
    await navigator.clipboard.writeText(props.text);
    copied.value = true;

    // Reset copied status after 2 seconds
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (error) {
    console.error('Failed to copy text: ', error);
  }
};
</script>

<template>
  <div @click="copyToClipboard" class="flex items-center justify-center gap-2 cursor-pointer">
    <div class="font-bold relative">
      <span v-if="copied" class="text-green-500 text-xs absolute -top-5 text-nowrap">{{ message }}</span>
      <p class="text-sm">{{ props.text }}</p>
    </div>
  </div>
</template>
