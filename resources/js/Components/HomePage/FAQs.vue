<script setup>
import { ref } from "vue";

const faqs = ref([
    {
        question: "How do I choose the right size?",
        answer: "We offer a detailed size guide on each product page to help you find the perfect fit. If you’re unsure, we recommend sizing up for a looser fit, or check reviews for fit tips from other customers.",
        isOpen: false,
    },
    {
        question: "How long will it take to receive my order?",
        answer: "We process and ship your order within 24 hours, and you can expect delivery in 3 days or less depending on your location.",
        isOpen: false,
    },
    {
        question: "Can I change or cancel my order after placing it?",
        answer: "Once your order is placed, we begin processing it immediately, but you can contact us within an hour of placing your order to request changes or cancellations.",
        isOpen: false,
    },
    {
        question: "Do you offer international shipping?",
        answer: "Currently, we ship to a select range of countries. Please check our shipping page for the full list of locations we serve.",
        isOpen: false,
    },
    {
        question: "What is your return policy?",
        answer: "We offer free returns within 14 days of receiving your order if the T-shirt is unworn and in its original condition. See our returns page for more details.",
        isOpen: false,
    },
    {
        question: "Are the shirts made from eco-friendly materials?",
        answer: "Yes! Our T-shirts are made from high-quality, sustainable materials, perfect for both your style and the planet.",
        isOpen: false,
    },
    {
        question: "Do you restock sold-out items?",
        answer: "We restock popular designs periodically. You can sign up for notifications on product pages to be alerted when your favorite design is back in stock!",
        isOpen: false,
    },
]);

const toggleFaq = (index) => {
    faqs.value[index].isOpen = !faqs.value[index].isOpen;
};
</script>

<template>
    <section class="py-10 bg-slate-200 sm:py-16 lg:py-12">
        <div class="px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
            <div class="max-w-2xl mx-auto text-center">
                <h2
                    class="text-3xl font-main font-bold leading-tight text-slate-800 sm:text-4xl lg:text-3xl"
                >
                    Got Questions? We've Got Answers!
                </h2>
                <p
                    class="max-w-xl mx-auto font-secondary mt-4 text-base leading-relaxed text-gray-600"
                >
                    Here are some frequently asked questions to help you with
                    your shopping experience.
                </p>
            </div>

            <div class="max-w-3xl mx-auto mt-8 space-y-4 md:mt-16">
                <div
                    v-for="(faq, index) in faqs"
                    :key="index"
                    class="bg-white border border-gray-200 shadow-lg cursor-pointer hover:bg-gray-50"
                    @mouseenter="toggleFaq(index)"
                    @mouseleave="toggleFaq(index)"
                >
                    <div
                        class="flex items-center justify-between w-full px-4 py-5 sm:p-6"
                    >
                        <span class="flex text-lg font-semibold text-black">{{
                            faq.question
                        }}</span>
                        <svg
                            class="w-6 h-6 text-gray-400 transition-transform duration-300"
                            :class="{ 'rotate-180': faq.isOpen }"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </div>
                    <div
                        v-show="faq.isOpen"
                        class="px-4 pb-5 sm:px-6 sm:pb-6 overflow-hidden transition-all duration-500 ease-in-out"
                        :style="
                            faq.isOpen
                                ? { maxHeight: '1000px', opacity: 1 }
                                : { maxHeight: '0', opacity: 0 }
                        "
                    >
                        <p v-html="faq.answer"></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
