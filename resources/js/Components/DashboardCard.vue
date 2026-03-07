<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    routeName: {
        type: String,
        required: true,
    },
    activeComponent: {
        type: String,
        required: true,
    },
    icon: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    count: {
        type: [Number, String],
        required: true,
    },
});
</script>

<template>
    <Link :href="route(routeName)" preserve-scroll>
        <div
            class="flex justify-between bg-neumorphic p-4 rounded-lg h-32 hover:scale-[0.99] smooth neumorphic"
            :class="{
                'border-neumorphic-accent border-2 bg-neumorphic-active hover:border-neumorphic-accent shadow-none scale-[0.99]':
                    $page.component.startsWith(activeComponent),
            }"
        >
            <div
                class="w-1/2 flex flex-col justify-end items-start h-full gap-y-3"
            >
                <component
                    :is="icon"
                    class="text-neumorphic-text"
                    :class="{
                        'text-neumorphic-accent':
                            $page.component.startsWith(activeComponent),
                    }"
                />
                <p
                    class="text-neumorphic-text font-bold text-2xl"
                    :class="{
                        'text-neumorphic-accent':
                            $page.component.startsWith(activeComponent),
                    }"
                >
                    {{ title }}
                </p>
            </div>
            <div
                class="w-1/2 flex flex-col justify-center items-end pe-8 h-full gap-y-3"
            >
                <p
                    class="text-neumorphic-text font-bold text-4xl"
                    :class="{
                        'text-neumorphic-accent':
                            $page.component.startsWith(activeComponent),
                    }"
                >
                    {{ count }}
                </p>
            </div>
        </div>
    </Link>
</template>

<style scoped></style>