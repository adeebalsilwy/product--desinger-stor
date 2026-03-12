<script setup>
import { Head, Link } from "@inertiajs/vue3";

// Import the Lottie player
import { onMounted, nextTick } from "vue";

onMounted(async () => {
    // Dynamically load the Lottie player script
    const script = document.createElement("script");
    script.src =
        "https://unpkg.com/@lottiefiles/lottie-player@2.0.8/dist/lottie-player.js";
    script.async = true;
    document.head.appendChild(script);

    // Wait for DOM to be ready and handle video autoplay issues
    await nextTick();
    
    // Get all video elements
    const videos = document.querySelectorAll('video');
    
    // Try to play each video with proper error handling
    videos.forEach(video => {
        // Add event listener for error handling
        video.addEventListener('error', (e) => {
            console.warn('Video failed to play:', e);
        });
        
        // Try to play the video with proper handling
        const playPromise = video.play();
        if (playPromise !== undefined) {
            playPromise.catch(error => {
                // Auto-play was prevented, handle it gracefully
                console.warn('Autoplay prevented:', error);
                // Mute the video and try again if needed
                video.muted = true;
                video.play().catch(err => {
                    console.warn('Even muted autoplay failed:', err);
                });
            });
        }
    });
});
</script>

<template>
    <div class="relative w-full h-screen overflow-hidden">
        <Head title="Thank You" />
        <!-- Video Background -->
        <video
            muted
            loop
            playsinline
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
                    class="text-3xl md:text-4xl font-main font-semibold text-slate-700"
                >
                    Thanks for purchasing! 🥰
                </h1>
                <p class="text-lg md:text-xl text-slate-600">
                    Your order is being processed and will be shipped soon. 🚚
                </p>
                <p class="text-lg md:text-xl text-slate-600">
                    Check your email for updates and further details. Thanks for
                    choosing us! 💖
                </p>

                <!-- Back to Shop Button -->
                <div class="z-50">
                    <Link
                        :href="route('home')"
                        class="btn w-44"
                        >Back To Shop</Link
                    >
                </div>
            </div>

            <!-- Lottie Animations -->
            <lottie-player
                src="https://lottie.host/b558db53-88a8-4f67-b4bc-d273dc097157/w7r2XaO97U.json"
                background="#FFFFFF00"
                speed="1"
                class="w-60 md:w-96 absolute left-0 md:left-44 -z-50"
                loop
                autoplay
            ></lottie-player>
            <lottie-player
                src="https://lottie.host/b558db53-88a8-4f67-b4bc-d273dc097157/w7r2XaO97U.json"
                background="#FFFFFF00"
                speed="1"
                class="w-60 md:w-96 absolute right-0 md:right-44 -z-50"
                loop
                autoplay
            ></lottie-player>
            <lottie-player
                src="https://lottie.host/b558db53-88a8-4f67-b4bc-d273dc097157/w7r2XaO97U.json"
                background="#FFFFFF00"
                speed="1"
                class="w-32 h-64 md:w-44 -z-50 md:h-80 absolute top-4 md:top-0"
                loop
                autoplay
            ></lottie-player>
        </div>
    </div>
</template>