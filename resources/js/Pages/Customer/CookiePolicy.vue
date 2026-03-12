<script setup>
import { Head } from "@inertiajs/vue3";
import Customer from "@/Layouts/Customer.vue";

defineOptions({ layout: Customer });
</script>

<template>
    <div class="bg-gray-50 min-h-screen py-12">
        <Head title="Cookie Policy" />
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Cookie Policy</h1>
                <div class="prose max-w-none">
                    <p class="text-gray-600 mb-6">This website uses cookies to enhance your browsing experience.</p>
                    
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">What are Cookies?</h2>
                    <p class="text-gray-600 mb-6">Cookies are small text files stored on your device when you visit our website.</p>
                    
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Types of Cookies We Use</h2>
                    <ul class="list-disc pl-6 text-gray-600 mb-6">
                        <li>Essential cookies for website functionality</li>
                        <li>Performance cookies for analytics</li>
                        <li>Marketing cookies for personalized advertising</li>
                    </ul>
                    
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Managing Cookies</h2>
                    <p class="text-gray-600">You can manage cookie preferences through your browser settings.</p>
                </div>
            </div>
        </div>
    </div>
</template>