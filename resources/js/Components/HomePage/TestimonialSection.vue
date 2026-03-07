<script setup>
import { ref } from "vue";
import TestimonialCard from "./TestimonialCard.vue";

const testimonials = ref([
    {
        id: 1,
        quote: "“I’m a web developer and these tees are the perfect mix of comfort and geeky style. Plus, they arrived in record time!”",
        avatar: "assets/Testimonials/Testimonial_1.jpg",
        name: "Leslie Alexander",
        country: "Brazil",
    },
    {
        id: 2,
        quote: "“The T-shirts are amazing! The fabric is comfortable, and the designs are super cool. I get compliments all the time!”",
        avatar: "assets/Testimonials/Testimonial_2.jpg",
        name: "Jordan Lorence",
        country: "USA",
    },
    {
        id: 3,
        quote: "“Fantastic quality and great customer service. I love the unique designs on every shirt I get!”",
        avatar: "assets/Testimonials/Testimonial_3.jpg",
        name: "David Gallagher",
        country: "Germany",
    },
]);
</script>
<template>
    <section class="py-12 bg-gray-50 sm:py-16 lg:py-20">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col items-center">
                <div class="text-center">
                    <p class="text-lg font-medium text-gray-600 font-pj">
                        Hear from developers around the world who’ve rocked our
                        tees and loved them!
                    </p>
                    <h2
                        class="mt-4 text-3xl font-bold text-gray-900 sm:text-4xl xl:text-5xl font-pj"
                    >
                        Our happy clients say about us
                    </h2>
                </div>

                <div class="relative mt-10 md:mt-24 md:order-2">
                    <div
                        class="absolute -inset-x-1 inset-y-16 md:-inset-x-2 md:-inset-y-6"
                    >
                        <div
                            class="w-full h-full max-w-5xl mx-auto rounded-3xl opacity-30 blur-lg filter"
                            style="
                                background: linear-gradient(
                                    90deg,
                                    #44ff9a -0.55%,
                                    #44b0ff 22.86%,
                                    #8b44ff 48.36%,
                                    #ff6644 73.33%,
                                    #ebff70 99.34%
                                );
                            "
                        ></div>
                    </div>

                    <div
                        class="relative grid max-w-lg grid-cols-1 gap-6 mx-auto md:max-w-none lg:gap-10 md:grid-cols-3"
                    >
                        <TestimonialCard
                            v-for="testimonial in testimonials"
                            :key="testimonial.id"
                            :testimonial="testimonial"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
