<script setup>
import { Head } from "@inertiajs/vue3";
import Customer from "@/Layouts/Customer.vue";

defineOptions({ layout: Customer });

const terms = [
    {
        title: "1. Use of the Website",
        content: "By accessing or using our website, you agree to:",
        notes: [
            "Use this site for lawful purposes only.",
            "Refrain from misusing the site for fraudulent activities.",
        ],
    },
    {
        title: "2. Account Responsibility",
        content: "You are responsible for:",
        notes: [
            "Maintaining the confidentiality of your account login credentials.",
            "Notifying us immediately of unauthorized account access or breaches.",
        ],
    },
    {
        title: "3. Product Information",
        content: "All T-shirts displayed on our website are subject to:",
        notes: [
            "Availability and may go out of stock without notice.",
            "Changes in product prices and descriptions without prior notice.",
        ],
    },
    {
        title: "4. Payment Terms",
        content:
            "All payments must be made in full before we process and ship your order. We accept:",
        notes: [
            "Visa, MasterCard, PayPal.",
        ],
    },
    {
        title: "5. Return and Refund Policy",
        content:
            "You can return items within 30 days of delivery if they meet our return criteria:",
        notes: [
            "Refunds will be processed within 7–10 business days upon approval.",
            "Return shipping costs may apply unless otherwise specified.",
        ],
    },
    {
        title: "6. Intellectual Property",
        content:
            "All content on this website (logos, images, text, etc.) is owned by D-Shirts and:",
        notes: [
            "Protected by intellectual property laws.",
            "May not be reproduced or redistributed without explicit consent.",
        ],
    },
    {
        title: "7. Limitation of Liability",
        content: "We are not responsible for:",
        notes: [
            "Errors in product descriptions or pricing.",
            "Delays caused by third-party services like shipping carriers.",
        ],
    },
    {
        title: "8. Changes to Terms",
        content:
            "We may update these terms periodically. Continued use of the website indicates acceptance of:",
        notes: [
            "The updated terms and conditions.",
            "Any policy changes posted on this website.",
        ],
    },
    {
        title: "9. Governing Law",
        content:
            "These terms are governed by and construed in accordance with the laws of:",
        notes: ["The Internet"],
    },
    {
        title: "10. Contact Us",
        content:
            "For questions or concerns regarding these Terms of Use, contact us at:",
        notes: ["Email: support@d-shirt.com", "Phone: +123 4567 89"],
    },
];
</script>

<template>
    <div class="container mx-auto px-4 py-16 max-w-4xl">
        <Head title="Terms Of Use" />
        <div class="mb-12 space-y-2 text-center">
            <h1 class="text-5xl font-bold text-gray-800 font-main">
                Terms Of Use
            </h1>
            <h2>
                Effective Date:
                <span class="font-bold font-secondary">November 28, 2024</span>
            </h2>
            <p class="font-main">
                Welcome to <span class="font-semibold">D-shirts</span>! By
                accessing or using our website, you agree to the following terms
                and conditions:
            </p>
        </div>

        <section
            v-for="term in terms"
            :key="term.title"
            class="bg-white shadow-md rounded-lg p-6 mb-4"
        >
            <h2 class="text-xl font-semibold text-gray-700 mb-4 font-second">
                {{ term.title }}
            </h2>
            <p class="text-gray-600 leading-relaxed">
                {{ term.content }}
            </p>
            <div>
                <ul class="list-disc list-inside text-gray-600 space-y-2">
                    <li v-for="note in term.notes" :key="note">{{ note }}</li>
                </ul>
            </div>
        </section>
    </div>
</template>

<style scoped></style>
