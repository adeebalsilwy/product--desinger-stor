<script setup>
import { Head } from "@inertiajs/vue3";
import Customer from "@/Layouts/Customer.vue";

defineOptions({ layout: Customer });
</script>

<template>
    <div class="bg-gray-50 min-h-screen py-12">
        <Head title="Shipping Policy" />
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Shipping Policy</h1>
                <div class="prose max-w-none">
                    <p class="text-gray-600 mb-6">We offer reliable shipping services to deliver your custom t-shirts worldwide.</p>
                    
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Shipping Options</h2>
                    <ul class="list-disc pl-6 text-gray-600 mb-6">
                        <li>Standard Shipping: 5-7 business days</li>
                        <li>Express Shipping: 2-3 business days</li>
                        <li>International Shipping: 7-14 business days</li>
                    </ul>
                    
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Shipping Costs</h2>
                    <p class="text-gray-600 mb-6">Shipping costs are calculated at checkout based on your location and order size.</p>
                    
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Tracking</h2>
                    <p class="text-gray-600">All orders include tracking information sent to your email once shipped.</p>
                </div>
            </div>
        </div>
    </div>
</template>