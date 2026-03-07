<script setup>
const props = defineProps({
  isListed: Boolean,
});

const options = {
  listed: {
    label: "Listed",
    bg: "bg-green-100",
    text: "text-green-800",
    border: "border-green-400",
    darkBg: "bg-green-900",
    darkText: "text-green-300",
  },
  notListed: {
    label: "Unlisted",
    bg: "bg-red-100",
    text: "text-red-800",
    border: "border-red-400",
    darkBg: "bg-red-900",
    darkText: "text-red-300",
  },
};


// Dynamically select the current type based on the prop
const currentState = props.isListed ? options.listed : options.notListed;
</script>

<template>
  <div
    :class="`inline-flex gap-2 items-center ${currentState.bg} ${currentState.text} border ${currentState.border} text-xs font-medium px-2.5 py-0.5 rounded-full `"
  >
    <!-- //////////////////////// -->
    <span
      class="flex size-2 items-center justify-center rounded-full "
      :class="currentState.darkBg"
      aria-label="notification"
    >
      <span
        class="size-2 animate-ping rounded-full motion-reduce:animate-none "
        :class="currentState.darkBg"
      ></span>
    </span>
    <!-- //////////////////////// -->
    <p>{{ currentState.label }}</p>
  </div>
</template>
