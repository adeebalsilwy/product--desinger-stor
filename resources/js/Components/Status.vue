<script setup>
const props = defineProps({
  type: String,
});

const types = {
  pending: {
    label: "Pending",
    bg: "bg-neumorphic-badge",
    text: "text-neumorphic-text",
    border: "border-neumorphic-text",
    darkBg: "bg-neumorphic-active",
    darkText: "text-neumorphic-text",
    emoji: "⏸️",
  },
  processing: {
    label: "Processing",
    bg: "bg-neumorphic-badge",
    text: "text-neumorphic-text",
    border: "border-neumorphic-text",
    darkBg: "bg-neumorphic-active",
    darkText: "text-neumorphic-text",
    emoji: "♻️",
  },
  shipped: {
    label: "Shipped",
    bg: "bg-neumorphic-badge",
    text: "text-neumorphic-text",
    border: "border-neumorphic-text",
    darkBg: "bg-neumorphic-active",
    darkText: "text-neumorphic-text",
    emoji: "🚚",
  },
  delivered: {
    label: "Delivered",
    bg: "bg-neumorphic-badge",
    text: "text-neumorphic-text",
    border: "border-neumorphic-text",
    darkBg: "bg-neumorphic-active",
    darkText: "text-neumorphic-text",
    emoji: "✅",
  },
  cancelled: {
    label: "Cancelled",
    bg: "bg-neumorphic-badge",
    text: "text-neumorphic-text",
    border: "border-neumorphic-text",
    darkBg: "bg-neumorphic-active",
    darkText: "text-neumorphic-text",
    emoji: "❌",
  },
  active: {
    label: "Active",
    bg: "bg-neumorphic-badge",
    text: "text-neumorphic-text",
    border: "border-neumorphic-text",
    darkBg: "bg-neumorphic-active",
    darkText: "text-neumorphic-text",
    emoji: "🟢",
  },
  inactive: {
    label: "Inactive",
    bg: "bg-neumorphic-badge",
    text: "text-neumorphic-text",
    border: "border-neumorphic-text",
    darkBg: "bg-neumorphic-active",
    darkText: "text-neumorphic-text",
    emoji: "🔴",
  },
};

// Dynamically select the current type based on the prop
const currentType = types[props.type] || types.pending; // Default to 'pending' if type is not provided
</script>

<template>
  <div
    :class="`inline-flex gap-2 items-center ${currentType.bg} ${currentType.text} border ${currentType.border} text-xs font-medium px-2.5 py-0.5 rounded-full neumorphic-btn`"
  >
    <p>{{ currentType.label }}</p>
  </div>
</template>