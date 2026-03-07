
<script setup>
import DecodeTextEffect from "@/Components/DecodeTextEffect.vue";
import { Head } from "@inertiajs/vue3";
import HowSection from "@/Components/HomePage/HowSection.vue";
import TshirtsSection from "@/Components/HomePage/TshirtsSection.vue";
import FAQs from "@/Components/HomePage/FAQs.vue";
import Testimonials from "@/Components/HomePage/TestimonialSection.vue";
import Footer from "@/Components/HomePage/Footer.vue";
import Cart from "@/Components/Cart.vue";

const props = defineProps({
    tshirts: {
        type: Array,
        required: true,
    },
});

const generateRandomCode = () => {
    const characters = "{}[]()<>/*-+=!@#$%^&*";
    // Calculate characters based on viewport width
    const minChars = 20; // Minimum characters per column
    return Array(minChars)
        .fill(0)
        .map(() => characters[Math.floor(Math.random() * characters.length)])
        .join("");
};
</script>

<template>
    <div class="relative min-h-screen overflow-x-hidden bg-slate-200">
        <Head title="Home" />
        <Cart />
        <!-- Background Effects -->
        <div class="fixed inset-0 w-full h-screen opacity-15">
            <div
                class="absolute top-20 left-20 w-72 h-72 bg-green-800 rounded-full mix-blend-multiply filter blur-xl animate-blob"
            ></div>
            <div
                class="absolute top-40 right-20 w-72 h-72 bg-green-800 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000"
            ></div>
            <div
                class="absolute bottom-40 left-1/2 w-72 h-72 bg-green-800 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000"
            ></div>
        </div>

        <!-- Code background effect -->
        <div
            class="fixed inset-0 opacity-15 font-mono text-xs overflow-hidden pointer-events-none text-green-900"
        >
            <div class="absolute inset-0 flex">
                <!-- Multiple columns of animated code -->
                <div
                    v-for="column in 12"
                    :key="column"
                    class="flex-1 animate-matrix"
                    :style="`animation-delay: -${column * 2}s`"
                >
                    <div
                        v-for="i in 100"
                        :key="i"
                        class="whitespace-nowrap px-1"
                    >
                        {{ generateRandomCode() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- First Layer (Fixed) -->
        <div class="fixed inset-0 w-full h-screen">
            <!-- Title Section -->
            <div
                class="absolute top-[10%] left-1/2 -translate-x-1/2 text-center z-10 px-4 md:space-y-4 space-x-1 w-full"
            >
                <DecodeTextEffect
                    text="Threads That Speak Your Language"
                    class="md:text-6xl text-2xl w-full text-teal-600 font-bespoke font-bold"
                />
                <DecodeTextEffect
                    text="Premium dev shirts, bug-free guarantee"
                    class="md:text-2xl text-base text-gray-600 font-secondary font-bold w-full"
                />
            </div>

            <!-- Desktop T-shirts Layout -->
            <div
                class="hidden md:flex absolute bottom-10 w-full justify-center items-end"
            >
                <img
                    src="../../../../public/assets/home_images/left.png"
                    alt="T-shirt Left"
                    class="w-1/4 animate-fade-slide-left"
                />
                <img
                    src="../../../../public/assets/home_images/middle.png"
                    alt="T-shirt Middle"
                    class="w-1/4 animate-fade-slide-up"
                />
                <img
                    src="../../../../public/assets/home_images/right.png"
                    alt="T-shirt Right"
                    class="w-1/4 animate-fade-slide-right"
                />
            </div>

            <!-- Mobile T-shirts Layout -->
            <div
                class="md:hidden absolute bottom-0 w-full flex flex-col items-center gap-4 pb-20"
            >
                <img
                    src="../../../../public/assets/home_images/middle.png"
                    alt="T-shirt Middle"
                    class="w-1/2 max-w-[300px] object-contain relative z-0 hover:scale-110 transition-transform duration-300 filter drop-shadow-xl animate-fade-slide-up"
                />
                <div class="flex w-full justify-center gap-4">
                    <img
                        src="../../../../public/assets/home_images/left.png"
                        alt="T-shirt Left"
                        class="w-1/3 max-w-[200px] object-contain transform rotate-[-15deg] origin-bottom relative z-0 hover:scale-105 transition-transform duration-300 filter drop-shadow-lg animate-fade-slide-left"
                    />
                    <img
                        src="../../../../public/assets/home_images/right.png"
                        alt="T-shirt Right"
                        class="w-1/3 max-w-[200px] object-contain transform rotate-[15deg] origin-bottom relative z-0 hover:scale-105 transition-transform duration-300 filter drop-shadow-lg animate-fade-slide-right"
                    />
                </div>
            </div>
        </div>

        <!-- Second Layer (Curved) -->
        <div class="relative min-h-screen h-full md:mt-[75vh] mt-[85vh]">
            <!-- The Curve -->
            <div
                class="absolute border-t-2 border-teal-500 top-0 w-full min-h-[200vh] bg-slate-200 z-20 curved-section custom-rounded"
            >
                <!-- Start Shopping Title -->
                <div class="h-44 w-full flex justify-center items-center">
                    <p class="text-teal-800 text-2xl font-secondary font-bold">
                        Start Shopping Now
                    </p>
                </div>
                <!-- Tshirts Section -->
                <TshirtsSection :tshirts="tshirts" />
                <!-- How to buy -->
                <HowSection />
                <!-- FAQs -->
                <FAQs />
                <!-- Testimonials -->
                <Testimonials />
                <!-- Footer -->
                <Footer />
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

@keyframes matrix {
    0% {
        transform: translateY(-50%);
    }
    100% {
        transform: translateY(0);
    }
}

@keyframes fadeSlideUp {
    0% {
        opacity: 0;
        transform: translateY(30px) scale(110%);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(110%);
    }
}

@keyframes fadeSlideLeft {
    0% {
        opacity: 0;
        transform: translate(30px, 0) rotate(-15deg);
    }
    100% {
        opacity: 1;
        transform: translate(-2rem, 0) rotate(-15deg);
    }
}

@keyframes fadeSlideRight {
    0% {
        opacity: 0;
        transform: translate(-30px, 0) rotate(15deg);
    }
    100% {
        opacity: 1;
        transform: translate(2rem, 0) rotate(15deg);
    }
}

.custom-rounded {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
}

@media (min-width: 768px) {
    .custom-rounded {
        border-top-left-radius: 50rem 15rem;
        border-top-right-radius: 50rem 15rem;
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animate-matrix {
    animation: matrix 20s linear infinite;
    will-change: transform;
}
.flex-1 {
    min-width: 0; /* Ensures flex items can shrink below their content size */
}

.animate-fade-slide-up {
    animation: fadeSlideUp 1s ease-out forwards;
}
.animate-fade-slide-up:hover {
    scale: 130%;
}

.animate-fade-slide-left {
    animation: fadeSlideLeft 1s ease-out forwards;
    animation-delay: 0.3s;
    opacity: 0;
}
.animate-fade-slide-left:hover {
    scale: 130%;
}

.animate-fade-slide-right {
    animation: fadeSlideRight 1s ease-out forwards;
    animation-delay: 0.3s;
    opacity: 0;
}
.animate-fade-slide-right:hover {
    scale: 130%;
}

.curved-section {
    position: relative;
    &::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
    }
}

@media (max-width: 640px) {
    .animate-matrix {
        font-size: 0.5rem; /* Smaller text on mobile */
    }
}

/* Add soft hover effect to images */
img {
    transition: all 0.3s ease;
}
</style>
