<script setup>
import { Link } from "@inertiajs/vue3";
import { useTextHelpers } from "@/plugins/textHelpers";

const textHelper = useTextHelpers();

const props = defineProps({
    tshirts: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div
        class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 justify-center gap-8 w-full max-w-7xl mx-auto md:px-4 px-8"
    >
        <Link
            :href="route('tshirt.page', { slug: tshirt.slug })"
            v-for="tshirt in props.tshirts"
            :key="tshirt.id"
            class="group relative cursor-pointer"
        >
            <!-- Background Layer -->
            <div
                class="absolute bg-slate-800 w-full h-full z-0 scale-[99%]"
            ></div>

            <!-- Content Layer -->
            <div
                class="relative p-3 bg-slate-200 z-50 border-2 border-slate-200 hover:border-slate-800 w-full h-full hover:translate-x-4 hover:-translate-y-4 transition-all duration-500 ease-in-out"
            >
                <img
                    :src="tshirt.images[0].url"
                    alt="T-shirt Image"
                    class="w-full object-contain"
                />
                <p class="font-main text-xl font-semibold text-slate-800">
                    {{ textHelper.limitText(tshirt.title, 30) }}
                </p>
                <p class="text-teal-700 font-secondary font-bold text-xl">
                    ${{ tshirt.price }}
                </p>
            </div>
        </Link>
    </div>
</template>

<style scoped></style>
