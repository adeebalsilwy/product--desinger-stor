<template>
    <section id="shop" class="py-16 bg-gradient-to-br from-gray-50 to-white relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -left-40 w-80 h-80 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
            <div class="absolute -bottom-40 -right-40 w-80 h-80 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse animation-delay-2000"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <div class="inline-block mb-4">
                    <div class="w-20 h-1 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full mx-auto"></div>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600">
                    {{ title }}
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    {{ subtitle }}
                </p>
            </div>

            <!-- Advanced Filters -->
            <div v-if="showAdvancedFilters" class="bg-white rounded-2xl shadow-lg p-6 mb-12 border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search products..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            />
                            <svg class="absolute left-3 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select
                            v-model="selectedCategory"
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        >
                            <option value="all">All Categories</option>
                            <option value="t-shirt">T-Shirts</option>
                            <option value="hoodie">Hoodies</option>
                            <option value="accessory">Accessories</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select
                            v-model="selectedSort"
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        >
                            <option value="featured">Featured</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="newest">Newest First</option>
                            <option value="rating">Highest Rated</option>
                        </select>
                    </div>

                    <!-- View Mode -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">View</label>
                        <div class="flex rounded-lg border border-gray-300 overflow-hidden">
                            <button
                                @click="viewMode = 'grid'"
                                :class="[
                                    viewMode === 'grid' 
                                        ? 'bg-blue-500 text-white' 
                                        : 'bg-white text-gray-700 hover:bg-gray-50',
                                    'flex-1 py-3 px-4 text-center transition-colors'
                                ]"
                            >
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </button>
                            <button
                                @click="viewMode = 'list'"
                                :class="[
                                    viewMode === 'list' 
                                        ? 'bg-blue-500 text-white' 
                                        : 'bg-white text-gray-700 hover:bg-gray-50',
                                    'flex-1 py-3 px-4 text-center transition-colors'
                                ]"
                            >
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Info -->
                <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                    <p class="text-sm text-gray-600">
                        Showing {{ paginatedProducts.length }} of {{ filteredProducts.length }} products
                    </p>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Items per page:</span>
                        <select
                            v-model="itemsPerPage"
                            class="text-sm border border-gray-300 rounded px-2 py-1"
                        >
                            <option :value="8">8</option>
                            <option :value="12">12</option>
                            <option :value="16">16</option>
                            <option :value="24">24</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Products Grid/List -->
            <div v-if="paginatedProducts.length > 0">
                <!-- Grid View -->
                <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <div 
                        v-for="(product, index) in paginatedProducts" 
                        :key="product.id"
                        class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-gray-100 hover:border-blue-200 transform hover:-translate-y-2"
                        :style="{ animationDelay: `${index * 0.1}s` }"
                    >
                        <!-- Product Image with Enhanced 3D Effect -->
                        <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 product-3d-container">
                            <!-- 3D Background Elements -->
                            <div class="absolute inset-0">
                                <div class="absolute top-0 left-0 w-32 h-32 bg-gradient-to-r from-blue-400/20 to-purple-400/20 rounded-full blur-3xl animate-pulse"></div>
                                <div class="absolute bottom-0 right-0 w-40 h-40 bg-gradient-to-r from-purple-400/20 to-pink-400/20 rounded-full blur-3xl animate-pulse animation-delay-2000"></div>
                            </div>
                            
                            <!-- Main Product Image with 3D Transform -->
                            <div class="relative z-10 transform transition-all duration-700 group-hover:scale-105 group-hover:rotate-2 group-hover:translate-z-10 product-3d-hover">
                                <Link :href="route('product.page', { slug: product.slug })">
                                    <div class="aspect-w-3 aspect-h-4 bg-gradient-to-br from-white to-gray-50 flex items-center justify-center relative overflow-hidden rounded-t-2xl">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent z-10"></div>
                                        <img 
                                            :src="product.images?.[0]?.url || '/assets/empty.png'" 
                                            :alt="product.title"
                                            class="w-full h-64 object-cover group-hover:scale-110 transition-all duration-700 shadow-lg"
                                            loading="lazy"
                                        />
                                        <!-- 3D Shadow Effect -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    </div>
                                </Link>
                            </div>
                            
                            <!-- Floating 3D Elements -->
                            <div class="absolute inset-0 pointer-events-none">
                                <div class="absolute top-6 left-6 w-8 h-8 bg-blue-500 rounded-full opacity-30 blur-md animate-bounce animation-delay-1000"></div>
                                <div class="absolute top-12 right-8 w-6 h-6 bg-purple-500 rounded-full opacity-40 blur-sm animate-ping animation-delay-1500"></div>
                                <div class="absolute bottom-8 left-10 w-10 h-10 bg-pink-500 rounded-full opacity-25 blur-lg animate-pulse animation-delay-2000"></div>
                                <div class="absolute bottom-6 right-6 w-4 h-4 bg-cyan-500 rounded-full opacity-50 blur-sm animate-bounce animation-delay-500"></div>
                            </div>
                            
                            <!-- 3D Floating Elements -->
                            <div class="absolute inset-0 pointer-events-none">
                                <div class="absolute top-4 left-4 w-16 h-16 bg-blue-500 rounded-full opacity-20 blur-xl animate-ping"></div>
                                <div class="absolute bottom-4 right-4 w-20 h-20 bg-purple-500 rounded-full opacity-15 blur-2xl animate-pulse"></div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="absolute top-4 right-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">
                                <button 
                                    @click="addToCart(product)"
                                    class="w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-blue-50 hover:scale-110 transition-all duration-300 border border-white/50"
                                    title="Add to Cart"
                                >
                                    <svg class="w-5 h-5 text-gray-700 hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </button>
                                <button 
                                    @click="quickView(product)"
                                    class="w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-gray-50 hover:scale-110 transition-all duration-300 border border-white/50"
                                    title="Quick View"
                                >
                                    <svg class="w-5 h-5 text-gray-700 hover:text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Badge -->
                            <div class="absolute top-4 left-4 z-20">
                                <span class="px-3 py-1 bg-gradient-to-r from-blue-500 to-purple-500 text-white text-xs font-medium rounded-full shadow-lg">
                                    New
                                </span>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-3">
                                <Link 
                                    :href="route('product.page', { slug: product.slug })"
                                    class="text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors line-clamp-2 group-hover:underline"
                                >
                                    {{ textHelper.limitText(product.title, 50) }}
                                </Link>
                            </div>
                            
                            <div class="flex items-center mb-4">
                                <div class="flex items-center">
                                    <div class="flex">
                                        <svg v-for="i in 5" :key="i" class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-500">(128)</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-2xl font-bold text-gray-900">{{ formatPrice(product.price) }}</span>
                                    <span v-if="product.original_price" class="ml-2 text-lg text-gray-500 line-through">{{ formatPrice(product.original_price) }}</span>
                                </div>
                                
                                <button 
                                    @click="addToCart(product)"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- List View -->
                <div v-else class="space-y-4">
                    <div 
                        v-for="(product, index) in paginatedProducts" 
                        :key="product.id"
                        class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-200"
                        :style="{ animationDelay: `${index * 0.05}s` }"
                    >
                        <div class="flex flex-col md:flex-row">
                            <!-- Image -->
                            <div class="md:w-1/3 relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100">
                                <Link :href="route('product.page', { slug: product.slug })">
                                    <img 
                                        :src="product.images?.[0]?.url || '/assets/empty.png'" 
                                        :alt="product.title"
                                        class="w-full h-64 md:h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                    />
                                </Link>
                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1 bg-gradient-to-r from-blue-500 to-purple-500 text-white text-xs font-medium rounded-full">
                                        New
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="md:w-2/3 p-6 flex flex-col justify-between">
                                <div>
                                    <Link 
                                        :href="route('product.page', { slug: product.slug })"
                                        class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors mb-2"
                                    >
                                        {{ product.title }}
                                    </Link>
                                    <div class="flex items-center mb-4">
                                        <div class="flex">
                                            <svg v-for="i in 5" :key="i" class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500">(128 reviews)</span>
                                    </div>
                                    <p class="text-gray-600 mb-4 line-clamp-3">
                                        {{ product.description || 'Premium quality product with excellent design and comfort.' }}
                                    </p>
                                </div>
                                
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                    <div>
                                        <span class="text-2xl font-bold text-gray-900">{{ formatPrice(product.price) }}</span>
                                        <span v-if="product.original_price" class="ml-2 text-lg text-gray-500 line-through">{{ formatPrice(product.original_price) }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <button 
                                            @click="quickView(product)"
                                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                                        >
                                            Quick View
                                        </button>
                                        <button 
                                            @click="addToCart(product)"
                                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300"
                                        >
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="totalPages > 1" class="mt-12 flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <button
                            @click="changePage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            Previous
                        </button>
                        
                        <div class="flex space-x-1">
                            <button
                                v-for="page in Math.min(5, totalPages)"
                                :key="page"
                                @click="changePage(page)"
                                :class="[
                                    page === currentPage
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-white text-gray-700 hover:bg-gray-50',
                                    'w-10 h-10 rounded-lg border border-gray-300 transition-colors'
                                ]"
                            >
                                {{ page }}
                            </button>
                        </div>
                        
                        <button
                            @click="changePage(currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            Next
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20">
                <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
                <p class="text-gray-600 mb-6">Try adjusting your search or filter criteria</p>
                <button
                    @click="searchQuery = ''; selectedCategory = 'all'; selectedSort = 'featured'"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Clear Filters
                </button>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-16">
                <Link
                    :href="route('home') + '#shop'"
                    class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-lg font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:-translate-y-1"
                >
                    View All Products
                    <svg class="ml-3 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </Link>
            </div>
        </div>
        
        <!-- Quick View Modal -->
        <QuickViewModal
            :product="selectedProduct"
            :is-open="showQuickView"
            @close="showQuickView = false"
            @customize="customizeProduct"
            @add-to-cart="handleQuickViewAddToCart"
        />
    </section>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useTextHelpers } from '@/plugins/textHelpers';
import QuickViewModal from '@/Components/Customer/QuickViewModal.vue';

const page = usePage();
const defaultProductType = computed(() => page.props.defaultProductType || 't-shirt');

const props = defineProps({
    products: {
        type: Array,
        default: () => []
    },
    title: {
        type: String,
        default: 'Featured Products'
    },
    subtitle: {
        type: String,
        default: 'Discover our premium collection'
    },
    showCategoryFilter: {
        type: Boolean,
        default: true
    },
    showAdvancedFilters: {
        type: Boolean,
        default: true
    }
});

// Debug logging
import { onMounted } from 'vue';

onMounted(() => {
    console.log('ProductsGrid mounted with products:', props.products);
    const invalidProducts = props.products.filter(p => !p || !p.id);
    if (invalidProducts.length > 0) {
        console.warn('Invalid products found:', invalidProducts);
    }
});

const textHelper = useTextHelpers();

// Quick View states
const showQuickView = ref(false);
const selectedProduct = ref(null);

// Filter states
const selectedCategory = ref('all');
const selectedSort = ref('featured');
const searchQuery = ref('');
const viewMode = ref('grid'); // grid or list
const itemsPerPage = ref(12);
const currentPage = ref(1);

// Available categories
const categories = [
    { id: 'all', name: 'All Products' },
    { id: 't-shirt', name: 'T-Shirts' },
    { id: 'hoodie', name: 'Hoodies' },
    { id: 'accessory', name: 'Accessories' },
    { id: 'premium', name: 'Premium' }
];

// Sort options
const sortOptions = [
    { id: 'featured', name: 'Featured' },
    { id: 'price-low', name: 'Price: Low to High' },
    { id: 'price-high', name: 'Price: High to Low' },
    { id: 'newest', name: 'Newest First' },
    { id: 'rating', name: 'Highest Rated' }
];

// Computed filtered and sorted products
const filteredProducts = computed(() => {
    let filtered = [...props.products];
    
    // Category filter
    if (selectedCategory.value !== 'all') {
        filtered = filtered.filter(product => 
            product.category?.toLowerCase().includes(selectedCategory.value) ||
            product.title?.toLowerCase().includes(selectedCategory.value)
        );
    }
    
    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(product =>
            product.title?.toLowerCase().includes(query) ||
            product.description?.toLowerCase().includes(query) ||
            product.category?.toLowerCase().includes(query)
        );
    }
    
    // Sort products
    switch (selectedSort.value) {
        case 'price-low':
            filtered.sort((a, b) => a.price - b.price);
            break;
        case 'price-high':
            filtered.sort((a, b) => b.price - a.price);
            break;
        case 'newest':
            filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            break;
        case 'rating':
            // Assuming products have rating property
            filtered.sort((a, b) => (b.rating || 0) - (a.rating || 0));
            break;
        default:
            // Featured - keep original order or sort by ID
            filtered.sort((a, b) => a.id - b.id);
    }
    
    return filtered;
});

// Paginated products
const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredProducts.value.slice(start, end);
});

// Total pages
const totalPages = computed(() => {
    return Math.ceil(filteredProducts.value.length / itemsPerPage.value);
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price);
};

const addToCart = (product) => {
    // This would integrate with your cart system
    console.log('Adding to cart:', product);
};

const quickView = (product) => {
    // Validate product before showing quick view
    console.log('QuickView called with product:', product);
    if (!product || !product.id) {
        console.warn('Invalid product for QuickView:', product);
        return;
    }
    if (!product.images || product.images.length === 0) {
        console.warn('Product missing images:', product);
    }
    selectedProduct.value = product;
    showQuickView.value = true;
};

const customizeProduct = (product) => {
    // Redirect to designer with this product as base
    window.location.href = route('designer.create', { productType: defaultProductType.value }) + '?baseProduct=' + product.id;
};

const handleQuickViewAddToCart = (data) => {
    // Handle the quick view add to cart
    console.log('Quick view add to cart:', data);
    addToCart(data.product);
    showQuickView.value = false;
};

const changePage = (page) => {
    currentPage.value = page;
    // Scroll to products section
    document.getElementById('shop')?.scrollIntoView({ behavior: 'smooth' });
};

// Watch for filter changes to reset to first page
watch([selectedCategory, searchQuery, selectedSort], () => {
    currentPage.value = 1;
});
</script>

<style scoped>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes pulse {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.1; }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes rotate3d {
    0% { transform: perspective(1000px) rotateY(0deg); }
    100% { transform: perspective(1000px) rotateY(360deg); }
}

@keyframes shimmer {
    0% { background-position: -1000px 0; }
    100% { background-position: 1000px 0; }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-pulse-slow {
    animation: pulse 4s ease-in-out infinite;
}

.animate-slide-in {
    animation: slideIn 0.6s ease-out forwards;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-1000 {
    animation-delay: 1s;
}

.animation-delay-1500 {
    animation-delay: 1.5s;
}

.animation-delay-500 {
    animation-delay: 0.5s;
}

/* Advanced 3D Container Effects */
.product-3d-container {
    transform-style: preserve-3d;
    perspective: 1200px;
    overflow: hidden;
}

/* Enhanced 3D Hover Effects */
.product-3d-hover {
    transform-style: preserve-3d;
    perspective: 1200px;
    will-change: transform;
}

.product-3d-hover:hover {
    transform: perspective(1200px) rotateX(8deg) rotateY(8deg) translateZ(30px) translateX(5px);
    box-shadow: 
        0 25px 50px -12px rgba(0, 0, 0, 0.25),
        0 0 0 1px rgba(0, 0, 0, 0.05),
        inset 0 0 100px rgba(255, 255, 255, 0.1);
}

/* 3D Depth Layers */
.product-3d-depth-1 {
    transform: translateZ(10px);
}

.product-3d-depth-2 {
    transform: translateZ(20px);
}

.product-3d-depth-3 {
    transform: translateZ(30px);
}

/* Parallax Mouse Effect */
.product-3d-parallax {
    transform: perspective(1000px);
    transition: transform 0.1s ease-out;
}

/* 3D Rotation Animation */
@keyframes rotate3d {
    0% { transform: perspective(1000px) rotateY(0deg) rotateX(0deg); }
    25% { transform: perspective(1000px) rotateY(5deg) rotateX(3deg); }
    50% { transform: perspective(1000px) rotateY(0deg) rotateX(0deg); }
    75% { transform: perspective(1000px) rotateY(-5deg) rotateX(-3deg); }
    100% { transform: perspective(1000px) rotateY(0deg) rotateX(0deg); }
}

/* Floating Elements Animation */
@keyframes float3d {
    0%, 100% { transform: translateY(0px) translateZ(0px); }
    50% { transform: translateY(-15px) translateZ(10px); }
}

.animate-float-3d {
    animation: float3d 4s ease-in-out infinite;
}

/* 3D Card Effect */
.card-3d-advanced {
    transform: perspective(1000px) translateZ(0px);
    transform-style: preserve-3d;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.card-3d-advanced:hover {
    transform: perspective(1000px) translateZ(40px) rotateX(6deg) rotateY(6deg) translateX(8px) translateY(-8px);
    box-shadow: 
        0 40px 60px -12px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.1),
        inset 0 0 100px rgba(255, 255, 255, 0.15);
}

/* Shimmer Effect */
.shimmer {
    background: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite linear;
}

/* Advanced Transitions */
.transition-all-300 {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.transition-transform-700 {
    transition: transform 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Glass Morphism Effect */
.glass-effect {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* 3D Card Effect */
.card-3d {
    transform: perspective(1000px);
    transform-style: preserve-3d;
}

.card-3d:hover {
    transform: perspective(1000px) translateZ(30px) rotateX(5deg) rotateY(5deg);
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .card-3d:hover {
        transform: perspective(1000px) translateZ(15px) rotateX(3deg) rotateY(3deg);
    }
    
    .animate-slide-in {
        animation: slideIn 0.4s ease-out forwards;
    }
}
</style>
