<script setup>
import { Head } from "@inertiajs/vue3";
import Customer from "@/Layouts/Customer.vue";

defineOptions({ layout: Customer });

const policies = [
    {
        title: "1. Information We Collect",
        content: "We may collect the following types of personal information:",
        notes: [
            "Personal Identifiable Information: Name, email address, phone number, shipping address, and payment details.",
            "Automatically Collected Information: IP address, browser type, device type, and browsing activity through cookies or other tracking technologies."
        ]
    },
    {
        title: "2. How We Use Your Information",
        content: "We use the information to:",
        notes: [
            "Process and fulfill your orders.",
            "Send updates about your purchase or promotional offers (with your consent).",
            "Improve website functionality and enhance user experience."
        ]
    },
    {
        title: "3. Sharing Your Information",
        content: "We do not sell or rent your personal information. However, we may share it with:",
        notes: [
            "Third-party payment processors to securely process your transactions.",
            "Shipping carriers to deliver your orders.",
            "Legal authorities when required by law."
        ]
    },
    {
        title: "4. Cookies",
        content: "Our website uses cookies to:",
        notes: [
            "Keep your shopping cart intact.",
            "Analyze website usage for improvement."
        ]
    },
    {
        title: "5. Your Rights",
        content: "You have the right to:",
        notes: [
            "Access, update, or delete your personal information.",
            "Opt-out of receiving marketing communications by clicking 'Unsubscribe' in our emails."
        ]
    },
    {
        title: "6. Contact Us",
        content: "If you have any questions about this Privacy Policy, contact us at:",
        notes: [
            "Email: support@d-shirt.com",
            "Phone: +123 4567 89"
        ]
    }
];

</script>

<template>
    <div class="container mx-auto px-4 py-16 max-w-4xl">
        <Head title="Privacy Policy" />
        <div class="mb-12 space-y-2 text-center">
            <h1 class="text-5xl font-bold text-gray-800 font-main">Privacy Policy</h1>
            <h2>Effective Date: <span class="font-bold font-secondary">November 28, 2024</span></h2>
            <p class="font-main">At <span class="font-semibold">D-shirts</span>, we value your privacy and are committed to protecting your personal information. This Privacy Policy outlines how we collect, use, and safeguard your data when you visit our website or make a purchase.</p>
        </div>

        <section v-for="policy in policies" :key="policy.title" class="bg-white shadow-md rounded-lg p-6 mb-4">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 font-second">
                {{ policy.title }}
            </h2>
            <p class="text-gray-600 leading-relaxed">
                {{ policy.content }}
            </p>
            <div>
                <ul class="list-disc list-inside text-gray-600 space-y-2">
                    <li v-for="note in policy.notes" :key="note">{{ note }}</li>
                </ul>
            </div>
        </section>

    </div>
</template>

<style scoped></style>
