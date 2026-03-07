<script setup>
import { Head, Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="relative w-full h-screen overflow-hidden">
        <Head title="Failed Payment" />
        <!-- Video Background -->
        <video
            autoplay
            muted
            loop
            class="absolute w-full h-full top-0 left-0 object-cover -z-10"
        >
            <source
                src="../../../../public/assets/thanks-bg-vid.mp4"
                type="video/mp4"
            />
            Your browser does not support the video tag.
        </video>

        <!-- Content -->
        <div
            class="relative w-full h-full z-10 flex flex-col items-center justify-center p-4"
        >
            <div class="space-y-4 text-center">
                <!-- Logo -->
                <img
                    src="../../../../public/assets/logo/d-shirts.png"
                    alt="Logo"
                    class="w-10 mx-auto"
                />

                <!-- Main Text -->
                <h1
                    class="text-3xl md:text-4xl font-main font-semibold text-rose-600"
                >
                    Payment Failed! 😢
                </h1>
                <p class="text-lg md:text-xl text-slate-600">
                    Unfortunately, your payment could not be processed. 💔
                </p>
                <p class="text-lg md:text-xl text-slate-600">
                    Please check your payment details or try again later. If you
                    need help, feel free to contact us!
                </p>

                <!-- Action Buttons -->
                <div class="flex md:flex-row flex-col md:space-x-4 space-y-4 md:space-y-0 space-x-0 justify-center">
                    <Link
                        :href="route('home')"
                        class="btn md:w-44 w-full "
                        >Back To Shop</Link
                    >
                    <Link
                        :href="route('home')"
                        class="btn md:w-44 w-full "
                        >Contact Support</Link
                    >
                </div>
            </div>
        </div>
    </div>
</template>
