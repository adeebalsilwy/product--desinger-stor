<script setup>
import { onMounted, ref, onUnmounted } from "vue";
import ApexCharts from "apexcharts";

const props = defineProps({
    data: {
        type: Array,
        required: true,
    },
    xKey: {
        type: String,
        default: 'x',
    },
    yKey: {
        type: String,
        default: 'y',
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
    // Guard clause: check if data exists and is an array
    if (!props.data || !Array.isArray(props.data) || props.data.length === 0) {
        console.warn('BarChart: No valid data provided');
        return;
    }
    
    // Filter out items that don't have the required keys
    const validItems = props.data.filter(item => 
        item && 
        item[props.xKey] !== undefined && 
        item[props.xKey] !== null &&
        item[props.yKey] !== undefined && 
        item[props.yKey] !== null
    );
    
    if (validItems.length === 0) {
        console.warn('BarChart: No valid items after filtering');
        return;
    }
    
    const categories = validItems.map(item => String(item[props.xKey] || ''));
    const seriesData = validItems.map(item => {
        const value = item[props.yKey];
        // Convert to number and handle edge cases
        const numValue = parseFloat(value);
        return isNaN(numValue) ? 0 : numValue;
    });
    
    const options = {
        chart: {
            type: 'bar',
            height: props.height,
            animations: { enabled: false },
            toolbar: { show: false },
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded',
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent'],
        },
        series: [{
            name: 'Data',
            data: seriesData,
        }],
        xaxis: {
            categories: categories,
        },
        yaxis: {
            labels: {
                formatter: props.formatter,
            },
        },
        fill: {
            opacity: 1,
        },
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