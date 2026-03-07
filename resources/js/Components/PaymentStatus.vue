<script setup>
const props = defineProps({
    type: String,
});

const types = {
    unpaid: {
        label: "Unpaid",
        bg: "bg-yellow-100",
        text: "text-yellow-800",
        border: "border-yellow-400",
        darkBg: "bg-yellow-900",
        darkText: "text-yellow-300",
        emoji: "⌛",
    },

    paid: {
        label: "Paid",
        bg: "bg-green-100",
        text: "text-green-800",
        border: "border-green-400",
        darkBg: "bg-green-900",
        darkText: "text-green-300",
        emoji: "✅",
    },
};

// Dynamically select the current type based on the prop
const currentType = types[props.type] || types.pending; // Default to 'idea' if type is not provided
</script>

<template>
    <div
        :class="`inline-flex gap-2 items-center ${currentType.bg} ${currentType.text} border ${currentType.border} text-xs font-medium px-2.5 py-0.5 rounded-full `"
    >
        <!-- //////////////////////// -->
        <!-- <span
      class="flex size-2 items-center justify-center rounded-full "
      :class="currentType.darkBg"
      aria-label="notification"
    >
      <span
        class="size-2 animate-ping rounded-full motion-reduce:animate-none "
        :class="currentType.darkBg"
      ></span>
    </span> -->
        <!-- //////////////////////// -->
        <p>{{ currentType.label }}</p>
        <p>{{ currentType.emoji }}</p>
    </div>
</template>
