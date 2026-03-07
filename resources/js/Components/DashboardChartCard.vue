<script setup>
import { Link } from "@inertiajs/vue3";
import ApexCharts from "apexcharts";
import { onMounted, ref } from "vue";

const props = defineProps({
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
    chartData: {
        type: Array,
        default() {
            return [30, 40, 35, 50, 49, 60, 70, 91, 25];
        },
    },
});

onMounted(() => {
    const options = {
        chart: {
            Animations: {enabled: false},
            type: "area",
            height: "80%",
            width: "100%",
            sparkline: {
                enabled: true,
            },
            toolbar: {
                show: false,
            },
        },
        series: [
            {
                name: "last 9 months",
                data: props.chartData,
            },
        ],

        yaxis: {
            labels: {
                show: false,
            },
        },
        grid: {
            show: false,
        },
        stroke: {
            curve: "smooth",
            width: 2,
        },
        colors: ["#10B981"],
        tooltip: {
            enabled: false,
        },
    };

    const chart = new ApexCharts(
        document.querySelector(`#${props.title.toLowerCase()}`),
        options
    );
    chart.render();
});
</script>

<template>
    <Link :href="route(routeName)" preserve-scroll>
        <div
            class="pt-4 bg-neumorphic rounded-lg h-32 smooth overflow-hidden flex flex-col justify-between neumorphic"
            :class="[
                $page.component.startsWith(activeComponent)
                    ? 'bg-neumorphic-active'
                    : 'bg-neumorphic hover:bg-neumorphic-hover',
            ]"
        >
            <div class="w-full flex justify-between px-4">
                <!-- Title/Icon -->
                <div class="w-1/2 flex gap-1">
                    <p
                        class="font-bold text-2xl transition-colors duration-200 text-neumorphic-text"
                        :class="[
                            $page.component.startsWith(activeComponent)
                                ? 'text-neumorphic-accent'
                                : 'text-neumorphic-text',
                        ]"
                    >
                        {{ title }}
                    </p>
                </div>

                <!-- Revenue -->
                <div class="w-1/2 text-end">
                    <p
                        class="font-bold text-2xl transition-colors duration-200"
                        :class="[
                            $page.component.startsWith(activeComponent)
                                ? 'text-neumorphic-text'
                                : 'text-neumorphic-text',
                        ]"
                    >
                        {{ count }}
                    </p>
                </div>
            </div>

            <!-- Chart -->
            <div :id="props.title.toLowerCase()"></div>
        </div>
    </Link>
</template>