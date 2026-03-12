<template>
    <div class="container mx-auto px-4 py-8">
        <Head title="Products" />
        <Toast />
        <ConfirmDialog group="templating" class="w-full md:w-1/2 lg:w-1/3">
            <template #message="slotProps">
                <div class="flex flex-col items-center">
                    <div class="bg-rose-500 rounded-full p-3 mb-4">
                        <svg
                            class="h-8 w-8 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">
                        Are you sure?
                    </h2>
                    <p class="text-gray-600 mb-6">
                        Deleting this product will also remove all related
                        images and orders. If you just want to hide it from the
                        public, you can uncheck the "Listed" checkbox on the
                        edit page.
                    </p>
                </div>
            </template>
        </ConfirmDialog>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="font-brand-elegant text-3xl text-brand-primary font-bold">Product Management</h1>
            <p class="text-brand-secondary">Manage all products and inventory</p>
        </div>

        <!-- Add Product button -->
        <div class="w-full flex justify-end mb-6">
            <Link
                :href="route('admin.products.create')"
                class="px-6 py-3 bg-gradient-to-r from-brand-primary to-brand-secondary text-white rounded-xl hover:opacity-90 transition-opacity font-medium"
            >
                Add Product
            </Link>
        </div>

        <!-- Products List -->
        <div
            v-if="products.data.length > 0"
            class="w-full overflow-x-auto grid lg:grid-cols-3 md:grid-cols-2 grid-flow-cols-1 gap-8 pb-8 pt-6"
        >
            <div
                v-for="product in products.data"
                :key="product.id"
                class="bg-white rounded-2xl shadow-lg overflow-hidden border border-brand-gold border-opacity-20 transition-all duration-300 hover:shadow-xl"
            >
                <div class="relative">
                    <img
                        :src="product.mainImage?.url || '/images/logo.jpeg'"
                        class="w-full h-48 object-cover rounded-t-2xl"
                        @error="setDefaultImage"
                    />
                    <div class="absolute top-3 left-3">
                        <ListingStatus :is-listed="!!product.listed" />
                    </div>
                    <Link
                        :href="route('admin.products.show', product.id)"
                        class="absolute top-3 right-3 text-sm text-white bg-black/50 rounded-full p-2 hover:bg-black/70 transition-all"
                    >
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </Link>
                </div>
                
                <div class="p-5">
                    <h3 class="font-brand-elegant text-lg font-semibold text-brand-primary mb-2 truncate">{{ textHelper.limitText(product.title, 30) }}</h3>
                    
                    <div class="flex justify-between items-center mb-4">
                        <div class="bg-gradient-to-r from-brand-primary to-brand-secondary text-white px-3 py-1 rounded-lg text-sm font-bold">
                            ${{ product.price }}
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-brand-secondary">Sells: {{ product.totalSells }}</div>
                            <div class="text-xs font-bold text-brand-rose">${{ product.totalRevenue }}</div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-4 gap-2 mb-4">
                        <template v-for="i in 4" :key="i">
                            <div
                                class="bg-gray-100 rounded-lg overflow-hidden"
                            >
                                <template
                                    v-if="
                                        Object.values(product.otherImages).find(
                                            (img) => img.order === i + 1
                                        )
                                    "
                                >
                                    <img
                                        :src="
                                            Object.values(product.otherImages).find(
                                                (img) => img.order === i + 1
                                            ).url
                                        "
                                        class="w-full h-16 object-cover"
                                        @error="setDefaultImage"
                                    />
                                </template>
                                <div v-else class="w-full h-16 flex items-center justify-center bg-gray-200">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <div class="flex gap-2 w-full">
                        <Link
                            :href="route('admin.products.edit', product.id)"
                            class="flex-1 py-2 text-white text-sm font-semibold bg-gradient-to-r from-brand-primary to-brand-secondary rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center gap-2"
                        >
                            <Pen class="w-4 h-4" />
                            Edit
                        </Link>
                        <button
                            class="flex-1 py-2 text-white text-sm font-semibold bg-gradient-to-r from-brand-rose to-red-600 rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center gap-2"
                            @click="confirmDeleteProduct(product.id)"
                        >
                            <Remove class="w-4 h-4" />
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-12 border border-brand-gold border-opacity-20 text-center">
            <div class="text-6xl mb-6">🛍️</div>
            <h3 class="font-brand-elegant text-2xl text-brand-primary font-semibold mb-4">No Products Yet!</h3>
            <p class="text-brand-secondary mb-6 max-w-md mx-auto">Get started by adding your first product to the catalog</p>
            <Link
                :href="route('admin.products.create')"
                class="px-6 py-3 bg-gradient-to-r from-brand-primary to-brand-secondary text-white rounded-xl hover:opacity-90 transition-opacity font-medium"
            >
                Add Your First Product
            </Link>
        </div>

        <!-- Pagination -->
        <div
            v-if="products.data.length > 0"
            class="mt-8 mb-12 flex flex-col md:flex-row justify-between items-center w-full gap-4"
        >
            <!-- results -->
            <div>
                <p class="text-base text-brand-secondary">
                    Showing
                    <span class="text-brand-primary font-bold text-lg">{{ products.from }}</span>
                    to
                    <span class="text-brand-primary font-bold text-lg">{{ products.to }}</span>
                    of {{ products.total }} products
                </p>
            </div>
            
            <nav>
                <div class="flex items-center -space-x-px h-10 text-sm">
                    <template
                        v-for="(link, index) in products.links"
                        :key="link.url"
                    >
                        <Link
                            :preserve-scroll="true"
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            class="flex items-center justify-center px-4 h-10 leading-tight border border-brand-gold border-opacity-30 transition-colors"
                            :class="{
                                'text-brand-secondary bg-white hover:bg-brand-gold hover:bg-opacity-10': !link.active,
                                'bg-gradient-to-r from-brand-primary to-brand-secondary text-white': link.active,
                                'rounded-l-lg': index === 0,
                                'rounded-r-lg': index === products.links.length - 1,
                            }"
                        />
                        <p
                            v-else
                            v-html="link.label"
                            class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-slate-200 border border-gray-300"
                            :class="{
                                'rounded-l-lg': index === 0,
                                'rounded-r-lg': index === products.links.length - 1,
                            }"
                        />
                    </template>
                </div>
            </nav>
        </div>
    </div>
</template>

<script setup>
import AdminLayout from '@/Layouts/Admin.vue';
import { Head, router, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import { useTextHelpers } from "@/plugins/textHelpers";
import Pen from "@/Icons/Pen.vue";
import Remove from "@/Icons/Remove.vue";
import ListingStatus from "@/Components/ListingStatus.vue";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";

defineOptions({ layout: AdminLayout });

const props = defineProps({
    products: {
        type: Object,
    },
});

const textHelper = useTextHelpers();

const toast = useToast();

// Confirmation dialog
const confirmDelete = useConfirm();

const confirmDeleteProduct = (productId) => {
    confirmDelete.require({
        group: "templating",
        message: "Are you sure you want to proceed?",
        header: "Delete this Product ?",
        rejectProps: {
            label: "Cancel",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Delete",
            severity: "danger",
        },
        accept: () => {
            router.delete(route("admin.products.destroy", productId), {
                onSuccess: page => {
                    toast.add({
                        severity: "success",
                        summary: "Success",
                        detail: "Product deleted successfully",
                        life: 3000,
                    });
                },
                onError: errors => {
                    toast.add({
                        severity: "error",
                        summary: "Error",
                        detail: errors.authorization,
                        life: 3000,
                    });
                },
            });
        },
    });
};

function setDefaultImage(event) {
    event.target.src = '/images/logo.jpeg';
}
</script>