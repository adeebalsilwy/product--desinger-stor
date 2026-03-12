<script setup>
import Footer from "@/Components/HomePage/Footer.vue";
import Cart from "@/Components/Cart.vue";
import Navbar from "@/Components/Customer/Navbar.vue";
import { router } from '@inertiajs/vue3';

router.on('navigate', () => {
    // Reload only the cart prop
    router.reload({ only: ['cart'] });
});

</script>

<template>
    <div class="bg-gradient-to-br from-gray-50 to-gray-100 relative min-h-screen">
        <!-- Navigation -->
        <div class="sticky top-0 z-50 shadow-xl">
            <Navbar />
        </div>
        
        <!-- Cart (for mobile overlay) -->
        <Cart />

        <!-- Page Content -->
        <main class="bg-white mx-auto shadow-lg">
            <slot />
        </main>

        <!-- Footer -->
        <div class="mt-16">
            <Footer />
        </div>
    </div>
</template>
