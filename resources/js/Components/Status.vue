<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: String,
  type: String
});

// Use either status or type prop, prioritizing status
const currentStatus = computed(() => props.status || props.type);

const statusClasses = computed(() => {
  if (!currentStatus.value) return 'bg-gray-100 text-gray-800 border border-gray-200';
  
  switch(currentStatus.value.toLowerCase()) {
    case 'active':
    case 'completed':
    case 'delivered':
    case 'paid':
      return 'bg-green-100 text-green-800 border border-green-200';
    case 'inactive':
    case 'cancelled':
    case 'failed':
      return 'bg-red-100 text-red-800 border border-red-200';
    case 'pending':
    case 'processing':
    case 'draft':
      return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
    case 'shipped':
    case 'in transit':
      return 'bg-blue-100 text-blue-800 border border-blue-200';
    case 'refunded':
      return 'bg-purple-100 text-purple-800 border border-purple-200';
    default:
      return 'bg-gray-100 text-gray-800 border border-gray-200';
  }
});

const dotColorClass = computed(() => {
  if (!currentStatus.value) return 'bg-gray-500';
  
  switch(currentStatus.value.toLowerCase()) {
    case 'active':
    case 'completed':
    case 'delivered':
    case 'paid':
      return 'bg-green-500';
    case 'inactive':
    case 'cancelled':
    case 'failed':
      return 'bg-red-500';
    case 'pending':
    case 'processing':
    case 'draft':
      return 'bg-yellow-500';
    case 'shipped':
    case 'in transit':
      return 'bg-blue-500';
    case 'refunded':
      return 'bg-purple-500';
    default:
      return 'bg-gray-500';
  }
});
</script>

<template>
  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize" 
        :class="statusClasses">
    <span class="w-2 h-2 rounded-full mr-2" :class="dotColorClass"></span>
    {{ currentStatus }}
  </span>
</template>