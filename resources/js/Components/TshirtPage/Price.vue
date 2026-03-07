<script setup>
const props = defineProps({
    price: {
        type: Number,
        required: true,
    },
});

const prevPrice = Math.round(props.price + props.price * 0.4);
</script>

<template>
    <div class="flex justify-start items-end gap-2 ">
        <div>
            <p class="text-3xl text-gray-400 font-bold line-through">${{ prevPrice }}</p>
        </div>
        <div>
            <p class="text-4xl text-teal-600 font-bold">${{ props.price }}</p>
        </div>
    </div>
</template>

<style scoped></style>
