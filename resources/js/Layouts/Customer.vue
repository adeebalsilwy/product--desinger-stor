<script setup>
import Footer from "@/Components/HomePage/Footer.vue";
import Cart from "@/Components/Cart.vue";
import Navbar from "@/Components/Customer/Navbar.vue";
import { router } from '@inertiajs/vue3';

const props = defineProps({
    showNav: {
        type: Boolean,
        default: true,
    },
    showFooter: {
        type: Boolean,
        default: true,
    },
    showCart: {
        type: Boolean,
        default: true,
    },
    contentClass: {
        type: String,
        default: 'customer-content mx-auto',
    },
});

router.on('navigate', () => {
    // Reload only the cart prop
    router.reload({ only: ['cart'] });
});

</script>

<template>
    <div class="customer-shell relative min-h-screen" :dir="$page.props.locale === 'ar' ? 'rtl' : 'ltr'">
        <!-- Navigation -->
        <div v-if="props.showNav" class="sticky top-0 z-50 shadow-xl">
            <Navbar />
        </div>
        
        <!-- Cart (for mobile overlay) -->
        <!-- <Cart v-if="props.showCart" /> -->

        <!-- Page Content -->
        <main :class="props.contentClass">
            <slot />
        </main>

        <!-- Footer -->
        <div v-if="props.showFooter" class="mt-16">
            <Footer />
        </div>
    </div>
</template>
