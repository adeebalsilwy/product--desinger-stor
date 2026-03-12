<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import CustomerLayout from '@/Layouts/Customer.vue';
import ProductCard from '@/Components/Customer/ProductCard.vue';

const props = defineProps({
    products: Object,
});

const searchQuery = ref('');
const selectedCategory = ref('all');

// Filter products based on search and category
const filteredProducts = computed(() => {
    let filtered = props.products.data || [];
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(product => 
            product.name.toLowerCase().includes(query) ||
            product.description.toLowerCase().includes(query)
        );
    }
    
    if (selectedCategory.value !== 'all') {
        filtered = filtered.filter(product => 
            product.product_type_id == selectedCategory.value
        );
    }
    
    return filtered;
});

const categories = computed(() => {
    const types = {};
    (props.products.data || []).forEach(product => {
        if (product.productType) {
            types[product.productType.id] = product.productType.name;
        }
    });
    return types;
});
</script>

<template>
    <Head title="Our Products - Ahlam's Girls" />
    <CustomerLayout>
        <div class="min-h-screen bg-brand-bg">
            <!-- Hero Section -->
            <div class="bg-gradient-brand py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 class="font-brand-elegant text-4xl md:text-5xl text-brand-gold mb-4">
                        Our Collection
                    </h1>
                    <p class="text-brand-text text-xl max-w-3xl mx-auto">
                        Discover our elegant collection of custom-designed apparel and fashion pieces
                    </p>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="bg-neumorphic rounded-xl p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-brand-primary mb-2">
                                Search Products
                            </label>
                            <div class="relative">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search by name or description..."
                                    class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                />
                                <i class="pi pi-search absolute right-3 top-3.5 text-brand-secondary"></i>
                            </div>
                        </div>
                        
                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-brand-primary mb-2">
                                Filter by Category
                            </label>
                            <select
                                v-model="selectedCategory"
                                class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                            >
                                <option value="all">All Categories</option>
                                <option 
                                    v-for="(name, id) in categories" 
                                    :key="id" 
                                    :value="id"
                                >
                                    {{ name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div v-if="filteredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <ProductCard
                        v-for="product in filteredProducts"
                        :key="product.id"
                        :product="product"
                    />
                </div>

                <!-- No Products Found -->
                <div v-else class="text-center py-16">
                    <div class="bg-neumorphic rounded-xl p-12 max-w-md mx-auto">
                        <i class="pi pi-search text-5xl text-brand-secondary mb-4"></i>
                        <h3 class="text-2xl font-brand-elegant text-brand-primary mb-2">
                            No Products Found
                        </h3>
                        <p class="text-brand-secondary mb-6">
                            We couldn't find any products matching your search criteria.
                        </p>
                        <button
                            @click="searchQuery = ''; selectedCategory = 'all'"
                            class="px-6 py-3 btn-luxury rounded-lg font-medium hover:opacity-90 transition-all"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="products.links && products.links.length > 3" class="mt-12 flex justify-center">
                    <nav class="inline-flex rounded-md shadow neumorphic">
                        <Link
                            v-for="(link, index) in products.links"
                            :key="index"
                            :href="link.url || '#'"
                            :class="{
                                'px-4 py-2 text-sm font-medium border border-brand-gold border-opacity-20': true,
                                'bg-brand-accent text-white': link.url && link.active,
                                'bg-neumorphic text-brand-primary hover:bg-brand-secondary hover:text-white': link.url && !link.active,
                                'bg-neumorphic text-brand-secondary cursor-default': !link.url
                            }"
                            v-html="link.label"
                            :disabled="!link.url"
                        />
                    </nav>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>