<script setup>
const props = defineProps({
    product: {
        type: Object,
        default: null
    }
});

// Mock review data - in real app this would come from API
const reviews = [
    { rating: 5, count: 127, text: "Excellent Quality" },
    { rating: 4, count: 34, text: "Very Good" },
    { rating: 3, count: 8, text: "Good" },
    { rating: 2, count: 2, text: "Fair" },
    { rating: 1, count: 1, text: "Poor" }
];

const averageRating = 4.7;
const totalReviews = reviews.reduce((sum, review) => sum + review.count, 0);

const getRatingPercentage = (count) => {
    return Math.round((count / totalReviews) * 100);
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <span class="text-3xl font-bold text-brand-primary mr-2">{{ averageRating }}</span>
                    <div class="flex flex-col items-start">
                        <div class="flex">
                            <svg
                                v-for="i in 5"
                                :key="i"
                                class="w-5 h-5"
                                :class="i <= Math.floor(averageRating) ? 'text-brand-gold' : 'text-gray-300'"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <span class="text-sm text-brand-primary/70 mt-1">{{ totalReviews }} reviews</span>
                    </div>
                </div>
            </div>
            
            <div v-if="props.product && props.product.is_template_based" class="hidden sm:block">
                <div class="flex items-center px-3 py-2 bg-brand-gold/10 text-brand-gold rounded-lg border border-brand-gold/20">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium">Design Verified</span>
                </div>
            </div>
        </div>

        <!-- Rating Breakdown -->
        <div class="space-y-2">
            <div v-for="review in reviews" :key="review.rating" class="flex items-center space-x-3">
                <div class="flex items-center w-12">
                    <span class="text-sm font-medium text-brand-primary">{{ review.rating }}</span>
                    <svg class="w-4 h-4 text-brand-gold ml-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div 
                        class="h-full bg-brand-gold rounded-full transition-all duration-300"
                        :style="{ width: getRatingPercentage(review.count) + '%' }"
                    ></div>
                </div>
                <span class="text-sm text-brand-primary/60 w-8 text-right">{{ review.count }}</span>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
