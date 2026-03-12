<script setup>
import { Head } from "@inertiajs/vue3";
import Customer from "@/Layouts/Customer.vue";

defineOptions({ layout: Customer });
</script>

<template>
    <div class="bg-gray-50 min-h-screen py-12">
        <Head title="Frequently Asked Questions" />
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h1>
                
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">How do I create a custom design?</h3>
                        <p class="text-gray-600">Visit our Design Studio, choose your product, and use our intuitive design tools to create your unique t-shirt. You can add text, images, and choose from various colors and fonts.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">What is the shipping time?</h3>
                        <p class="text-gray-600">Standard shipping takes 5-7 business days. Express shipping options are available for 2-3 business days delivery.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">What is your return policy?</h3>
                        <p class="text-gray-600">We offer a 30-day return policy for unworn, unwashed items with original tags attached. Returns must be in original condition.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">How do I track my order?</h3>
                        <p class="text-gray-600">Once your order ships, you'll receive a tracking number via email. You can track your package on our website or the carrier's website.</p>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Do you offer bulk discounts?</h3>
                        <p class="text-gray-600">Yes! We offer volume discounts for orders of 10+ items. Contact our sales team for custom pricing quotes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>