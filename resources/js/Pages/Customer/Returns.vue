<script setup>
import { Head } from "@inertiajs/vue3";
import Customer from "@/Layouts/Customer.vue";

defineOptions({ layout: Customer });
</script>

<template>
    <div class="bg-gray-50 min-h-screen py-12">
        <Head title="Returns Policy" />
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Returns Policy</h1>
                <div class="prose max-w-none">
                    <p class="text-gray-600 mb-6">We want you to be completely satisfied with your purchase.</p>
                    
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Return Eligibility</h2>
                    <ul class="list-disc pl-6 text-gray-600 mb-6">
                        <li>30-day return window from delivery date</li>
                        <li>Items must be unworn, unwashed, and with original tags</li>
                        <li>Original packaging and receipt required</li>
                    </ul>
                    
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Return Process</h2>
                    <p class="text-gray-600 mb-6">Contact our customer service team to initiate a return. We'll provide a return shipping label and instructions.</p>
                    
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Refunds</h2>
                    <p class="text-gray-600">Refunds are processed within 5-7 business days after we receive your returned item.</p>
                </div>
            </div>
        </div>
    </div>
</template>