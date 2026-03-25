<script setup>
import { Link } from "@inertiajs/vue3";
import Cart from "@/Icons/Cart.vue";
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const cartCount = computed(() => page.props.cart.length);

</script>

<template>
    <!-- cart -->
    <Link
        :href="route('cart')"
        class="fixed bg-brand-accent hover:bg-brand-secondary md:w-16 md:h-16 w-12 h-12 md:top-4 top-2 md:right-16 right-4 flex justify-center items-center rounded-full p-1 cursor-pointer group z-[9999]"
    >
        <Cart
            class="w-7 h-7 text-gray-200 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
        />
        <div
            class="absolute top-0 right-0 bg-brand-gold p-2 rounded-full md:w-6 md:h-6 w-5 h-5 flex justify-center items-center text-brand-primary border border-white group-hover:bg-brand-lavender"
        >
            <p>{{ cartCount }}</p>
        </div>
    </Link>
</template>

<style scoped></style>
