<script setup>
import { Link } from "@inertiajs/vue3";
import Github from "@/Icons/Github.vue";
import Twitter from "@/Icons/Twitter.vue";
import Website from "@/Icons/Website.vue";
</script>

<template>
    <div class="bg-slate-200 w-full md:h-44 h-full md:py-0 py-12 border-t border-t-slate-300">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 flex md:flex-row flex-col md:gap-0 gap-6 justify-between items-center h-full"
        >
            <div
                class="md:w-1/3 w-full flex md:justify-start justify-center gap-6 md:order-1 order-3"
            >
                <a href="https://github.com/Mohamed-Galdi" target="_blank">
                    <Github
                        class="w-6 h-6 text-slate-600 hover:text-teal-600 hover:scale-105"
                    />
                </a>
                <a href="https://x.com/GaldiMohamed" target="_blank">
                    <Twitter
                        class="w-6 h-6 text-slate-600 hover:text-teal-600 hover:scale-105"
                    />
                </a>
                <a href="https://www.galdi.dev/" target="_blank">
                    <Website
                        class="w-5 h-5 text-slate-600 hover:text-teal-600 hover:scale-105"
                    />
                </a>
            </div>
            <div
                class="md:w-1/3 w-full flex flex-col justify-center items-center gap-2 md:order-2 order-2"
            >
            <Link :href="route('home')">
                <img src="../../../../public/assets/logo/d-shirts.png" alt="" class="w-12" />
            </Link>
                <p class="text-slate-800 font-main text-sm">
                    © 2025 All rights reserved.
                </p>
            </div>
            <div
                class="md:w-1/3 w-full flex md:justify-end justify-center md:gap-3 gap-5 md:order-3 order-1"
            >
                <Link
                    :href="route('privacy-policy')"
                    class="text-slate-500 font-main hover:text-teal-600 underline underline-offset-4"
                    >Privacy policy</Link
                >
                <Link
                    :href="route('terms-of-use')"
                    class="text-slate-500 font-main hover:text-teal-600 underline underline-offset-4"
                    >Terms of use</Link
                >
            </div>
        </div>
    </div>
</template>
