<script setup>
import Footer from "@/Components/HomePage/Footer.vue";
import Cart from "@/Components/Cart.vue";
import { router } from '@inertiajs/vue3';

router.on('navigate', () => {
    // Reload only the cart prop
    router.reload({ only: ['cart'] });
});

</script>

<template>
    <div class="bg-[#f5f5f5] relative">

        <Cart />

        <!-- Page Content -->
        <div class="bg-slate-200 mx-auto min-h-screen">
            <slot />
        </div>

        <!-- Footer -->
        <Footer />
    </div>
</template>
