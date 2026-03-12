<script setup>
import { ref } from 'vue';

const testimonials = [
    {
        id: 1,
        name: 'Sarah Johnson',
        role: 'Software Engineer',
        company: 'TechCorp',
        content: 'The quality of these shirts is exceptional! I\'ve ordered multiple times and every piece exceeds expectations. The designs are unique and the fabric is incredibly comfortable.',
        rating: 5,
        avatar: 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=100&h=100&fit=crop&crop=face',
        verified: true
    },
    {
        id: 2,
        name: 'Michael Chen',
        role: 'Product Designer',
        company: 'Design Studio',
        content: 'As a designer, I appreciate the attention to detail in both the product quality and the design process. The custom design tool is intuitive and produces professional results.',
        rating: 5,
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face',
        verified: true
    },
    {
        id: 3,
        name: 'Emma Rodriguez',
        role: 'Marketing Manager',
        company: 'GrowthCo',
        content: 'Our team loves these shirts for company events! The branding options are perfect and the customer service team went above and beyond to help with our bulk order.',
        rating: 5,
        avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=face',
        verified: true
    },
    {
        id: 4,
        name: 'David Kim',
        role: 'Startup Founder',
        company: 'InnovateLab',
        content: 'Fast shipping and excellent quality. The shirts arrived perfectly packaged and look even better in person. Will definitely be ordering more for our team swag.',
        rating: 5,
        avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&crop=face',
        verified: true
    }
];

const currentTestimonial = ref(0);

const nextTestimonial = () => {
    currentTestimonial.value = (currentTestimonial.value + 1) % testimonials.length;
};

const prevTestimonial = () => {
    currentTestimonial.value = (currentTestimonial.value - 1 + testimonials.length) % testimonials.length;
};

const getStars = (rating) => {
    return Array(rating).fill('★');
};
</script>

<template>
    <section class="py-20 bg-gradient-to-br from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    What Our Customers Say
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Don't just take our word for it - hear from our satisfied customers
                </p>
            </div>

            <!-- Testimonials Carousel -->
            <div class="relative max-w-4xl mx-auto">
                <!-- Main Testimonial -->
                <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 text-center">
                    <!-- Rating -->
                    <div class="flex justify-center mb-6">
                        <div class="flex text-2xl text-yellow-400">
                            <span v-for="star in getStars(testimonials[currentTestimonial].rating)" :key="star">
                                {{ star }}
                            </span>
                        </div>
                    </div>

                    <!-- Testimonial Content -->
                    <blockquote class="text-xl md:text-2xl text-gray-700 mb-8 leading-relaxed">
                        "{{ testimonials[currentTestimonial].content }}"
                    </blockquote>

                    <!-- Author -->
                    <div class="flex flex-col items-center">
                        <img 
                            :src="testimonials[currentTestimonial].avatar" 
                            :alt="testimonials[currentTestimonial].name"
                            class="w-16 h-16 rounded-full mb-4 ring-4 ring-white shadow-lg"
                        />
                        <div>
                            <div class="font-bold text-gray-900 text-lg">
                                {{ testimonials[currentTestimonial].name }}
                            </div>
                            <div class="text-gray-600">
                                {{ testimonials[currentTestimonial].role }} at {{ testimonials[currentTestimonial].company }}
                            </div>
                            <div v-if="testimonials[currentTestimonial].verified" class="mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Verified Customer
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Arrows -->
                <button 
                    @click="prevTestimonial"
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 transition-colors"
                >
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <button 
                    @click="nextTestimonial"
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 transition-colors"
                >
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <!-- Dots Indicator -->
                <div class="flex justify-center mt-8 space-x-2">
                    <button 
                        v-for="(testimonial, index) in testimonials"
                        :key="testimonial.id"
                        @click="currentTestimonial = index"
                        :class="[
                            'w-3 h-3 rounded-full transition-colors',
                            currentTestimonial === index 
                                ? 'bg-blue-600' 
                                : 'bg-gray-300 hover:bg-gray-400'
                        ]"
                    >
                    </button>
                </div>
            </div>

            <!-- Trust Badges -->
            <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="p-6 bg-white rounded-2xl shadow-sm">
                    <div class="text-3xl font-bold text-blue-600 mb-2">4.9/5</div>
                    <div class="text-gray-600 text-sm">Average Rating</div>
                </div>
                <div class="p-6 bg-white rounded-2xl shadow-sm">
                    <div class="text-3xl font-bold text-green-600 mb-2">2,500+</div>
                    <div class="text-gray-600 text-sm">Reviews</div>
                </div>
                <div class="p-6 bg-white rounded-2xl shadow-sm">
                    <div class="text-3xl font-bold text-purple-600 mb-2">98%</div>
                    <div class="text-gray-600 text-sm">Recommend</div>
                </div>
                <div class="p-6 bg-white rounded-2xl shadow-sm">
                    <div class="text-3xl font-bold text-pink-600 mb-2">24/7</div>
                    <div class="text-gray-600 text-sm">Support</div>
                </div>
            </div>
        </div>
    </section>
</template>