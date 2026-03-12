<script setup>
const props = defineProps({
    price: {
        type: Number,
        required: true,
    },
    product: {
        type: Object,
        default: null
    }
});

const prevPrice = Math.round(props.price + props.price * 0.4);
</script>

<template>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
        <div class="flex items-end gap-3">
            <div>
                <p class="text-2xl sm:text-3xl text-brand-primary/50 font-bold line-through">{{ prevPrice }} SAR</p>
                <p class="text-sm text-brand-primary/60 mt-1">Original Price</p>
            </div>
            <div>
                <p class="text-3xl sm:text-4xl text-brand-gold font-bold">{{ props.price }} SAR</p>
                <p class="text-sm text-brand-gold font-medium mt-1">Special Offer</p>
            </div>
        </div>
        
        <div v-if="product && product.is_template_based" class="px-3 py-2 bg-gradient-to-r from-brand-gold/10 to-brand-accent/10 rounded-lg border border-brand-gold/20">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-brand-gold mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                <span class="text-brand-gold font-medium text-sm">Template Design Available</span>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
