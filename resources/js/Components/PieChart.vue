<script setup>
import { onMounted, ref, onUnmounted } from "vue";
import ApexCharts from "apexcharts";

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
    formatter: {
        type: Function,
        default: (val) => val,
    },
    height: {
        type: String,
        default: '350px',
    }
});

const chartRef = ref(null);
let chartInstance = null;

onMounted(() => {
    // Guard clause: check if data exists and is an object
    if (!props.data || typeof props.data !== 'object') {
        console.warn('PieChart: Invalid data provided');
        return;
    }
    
    // Filter and validate data
    const validData = {};
    let hasValidData = false;
    
    for (const [key, value] of Object.entries(props.data)) {
        if (value !== null && value !== undefined) {
            const numValue = parseFloat(value);
            if (!isNaN(numValue)) {
                validData[key] = numValue;
                hasValidData = true;
            }
        }
    }
    
    // Only render if we have valid data
    if (!hasValidData) {
        console.warn('PieChart: No valid data after validation');
        return;
    }
    
    const labels = Object.keys(validData);
    const series = Object.values(validData);
    
    const options = {
        chart: {
            type: 'pie',
            height: props.height,
            animations: { enabled: false },
        },
        labels: labels,
        series: series,
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }],
        tooltip: {
            y: {
                formatter: props.formatter,
            },
        },
    };

    chartInstance = new ApexCharts(chartRef.value, options);
    chartInstance.render();
});

onUnmounted(() => {
    if (chartInstance) {
        chartInstance.destroy();
    }
});
</script>

<template>
    <div ref="chartRef"></div>
</template>