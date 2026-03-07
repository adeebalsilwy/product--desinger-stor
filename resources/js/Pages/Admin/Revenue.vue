<script setup>
import Admin from "@/Layouts/Admin.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import ApexCharts from "apexcharts";
import { shade } from "@primevue/themes";

defineOptions({ layout: Admin });

const props = defineProps({
    allTimeData: {
        type: Object,
        required: true,
    },
    lastYearData: {
        type: Object,
        required: true,
    },
    lastMonthData: {
        type: Object,
        required: true,
    },
    lastWeekData: {
        type: Object,
        required: true,
    },
});

const generateSeries = () => {
    const dateFormat =
        selectedFilter.value === "month" || selectedFilter.value === "week"
            ? "dd MMM yyyy" // Daily format
            : "MMM yyyy"; // Monthly format

    return [
        {
            name: "Orders",
            type: "line",
            color: "#d98018",
            data: data.value.map((item) => ({
                x: new Date(item.date),
                y: item.orders,
            })),
        },
        {
            name: "Tshirts",
            type: "line",
            color: "#e0bf77",
            data: data.value.map((item) => ({
                x: new Date(item.date),
                y: item.tshirts,
            })),
        },
        {
            name: "Income",
            type: "column",
            color: "#10B981",
            data: data.value.map((item) => ({
                x: new Date(item.date),
                y: item.income,
            })),
        },
        {
            name: "Cumulative Income",
            type: "area",
            color: "#3d91fe",
            data: data.value.map((item) => ({
                x: new Date(item.date),
                y: item.cumulative_income,
            })),
        },
    ];
};

const data = ref(props.lastMonthData);
const selectedFilter = ref("month");
let chart = null;

const initializeChart = () => {
    const options = {
        chart: {
            height: 500,
            background: "#20293c",
            foreColor: "#fff",
            zoom: {
                enabled: false,
            },
            toolbar: {
                show: false,
            },
        },

        series: generateSeries(),
        stroke: {
            width: [4, 4, 1, 5],
        },
        plotOptions: {
            bar: {
                columnWidth: "40%",
            },
        },
        xaxis: {
            type: "datetime",
            labels: {
                format:
                    selectedFilter.value === "month" ||
                    selectedFilter.value === "week"
                        ? "dd MMM yyyy" // Daily format
                        : "MMM yyyy", // Monthly format
            },
        },
        yaxis: [
            {
                seriesName: "Orders",
                title: {
                    text: "Orders & Tshirts",
                },
                min: 0,
            },
            {
                seriesName: "Orders",
                show: true,
            },
            {
                seriesName: "Cumulative Income",
                opposite: true,
            },
            {
                seriesName: "Cumulative Income",
                opposite: true,
                title: {
                    text: "Monthly and Cumulative Income",
                },
                show: true,
                min: 0,
            },
        ],
    };

    chart = new ApexCharts(document.querySelector("#incomeChart"), options);
    chart.render();
};

watch(selectedFilter, (newFilter) => {
    // Update the data based on the selected filter
    switch (newFilter) {
        case "all":
            data.value = props.allTimeData;
            break;
        case "year":
            data.value = props.lastYearData;
            break;
        case "month":
            data.value = props.lastMonthData;
            break;
        case "week":
            data.value = props.lastWeekData;
            break;
    }

    // Update the chart with new data and date format
    if (chart) {
        chart.updateOptions({
            xaxis: {
                labels: {
                    format:
                        newFilter === "month" || newFilter === "week"
                            ? "dd MMM yyyy"
                            : "MMM yyyy",
                },
            },
        });
        chart.updateSeries(generateSeries());
    }
});

onMounted(() => {
    initializeChart();
});
</script>

<template>
    <div>
        <Head title="Revenue" />
        <!-- Filters -->
        <div class="flex gap-2 mb-2 mt-9">
            <div>
                <label
                    class="cursor-pointer p-1 rounded-lg border-2 border-slate-500"
                    :class="
                        selectedFilter === 'all'
                            ? 'bg-slate-700  text-slate-200'
                            : 'bg-white text-slate-600 hover:bg-slate-100'
                    "
                    for="all"
                    >All Time</label
                >
                <input
                    class="hidden"
                    type="radio"
                    name="dataFilter"
                    v-model="selectedFilter"
                    value="all"
                    id="all"
                />
            </div>
            <div>
                <label
                    class="cursor-pointer p-1 rounded-lg border-2 border-slate-500"
                    :class="
                        selectedFilter === 'year'
                            ? 'bg-slate-700  text-slate-200'
                            : 'bg-white text-slate-600 hover:bg-slate-100'
                    "
                    for="year"
                    >Last Year</label
                >
                <input
                    class="hidden"
                    type="radio"
                    name="dataFilter"
                    v-model="selectedFilter"
                    value="year"
                    id="year"
                />
            </div>
            <div>
                <label
                    class="cursor-pointer p-1 rounded-lg border-2 border-slate-500"
                    :class="
                        selectedFilter === 'month'
                            ? 'bg-slate-700  text-slate-200'
                            : 'bg-white text-slate-600 hover:bg-slate-100'
                    "
                    for="month"
                    >Last Month</label
                >
                <input
                    class="hidden"
                    type="radio"
                    name="dataFilter"
                    v-model="selectedFilter"
                    value="month"
                    id="month"
                />
            </div>
            <div>
                <label
                    class="cursor-pointer p-1 rounded-lg border-2 border-slate-500"
                    :class="
                        selectedFilter === 'week'
                            ? 'bg-slate-700  text-slate-200'
                            : 'bg-white text-slate-600 hover:bg-slate-100'
                    "
                    for="week"
                    >Last Week</label
                >
                <input
                    class="hidden"
                    type="radio"
                    name="dataFilter"
                    v-model="selectedFilter"
                    value="week"
                    id="week"
                />
            </div>
        </div>

        <!--  Income Chart  -->
        <div class="h-[800px]" id="incomeChart"></div>
    </div>
</template>
