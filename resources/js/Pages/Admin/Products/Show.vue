<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="font-brand-elegant text-3xl text-brand-primary font-bold">Product Details</h1>
        <p class="text-brand-secondary">View and manage product information</p>
      </div>

      <!-- Back Button -->
      <div class="mb-6">
        <Link :href="route('admin.products.index')" 
              class="inline-flex items-center px-4 py-2 text-brand-primary hover:text-brand-gold transition-colors">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Back to Products
        </Link>
      </div>

      <!-- Loading indicator if product is not loaded -->
      <div v-if="!product" class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-12 border border-brand-gold border-opacity-20 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-brand-primary mx-auto mb-4"></div>
        <p class="text-brand-secondary">Loading product details...</p>
      </div>

      <!-- Product Details Card -->
      <div v-else class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20 mb-8">
        <!-- Product Type Badge -->
        <div class="mb-6">
          <span class="inline-block px-3 py-1 rounded-full text-sm font-medium" 
                :class="product.type === 'legacy_tshirt' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
            {{ product.type === 'legacy_tshirt' ? 'Legacy T-Shirt' : 'Template-Based Product' }}
          </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Product Images Section -->
          <div>
            <h2 class="font-brand-elegant text-xl text-brand-primary font-semibold mb-4">Product Images</h2>
            
            <!-- Main Image -->
            <div class="mb-6">
              <div class="bg-gray-100 rounded-xl overflow-hidden aspect-square flex items-center justify-center">
                <img 
                  :src="getMainImageUrl()" 
                  :alt="product.title"
                  class="w-full h-full object-contain"
                  @error="setDefaultImage"
                />
              </div>
            </div>

            <!-- Additional Images -->
            <div v-if="hasOtherImages()" class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div 
                v-for="(image, index) in getOtherImagesArray()" 
                :key="index"
                class="bg-gray-100 rounded-lg overflow-hidden aspect-square flex items-center justify-center"
              >
                <img 
                  :src="image.url" 
                  :alt="`Product image ${index + 1}`"
                  class="w-full h-full object-contain"
                  @error="setDefaultImage"
                />
              </div>
            </div>
          </div>

          <!-- Product Information Section -->
          <div>
            <h2 class="font-brand-elegant text-xl text-brand-primary font-semibold mb-4">Product Information</h2>
            
            <div class="space-y-4">
              <div class="flex justify-between items-center pb-2 border-b border-brand-gold border-opacity-20">
                <span class="text-brand-secondary font-medium">Title:</span>
                <span class="text-brand-primary font-semibold">{{ product.title || 'N/A' }}</span>
              </div>
              
              <div class="flex justify-between items-center pb-2 border-b border-brand-gold border-opacity-20">
                <span class="text-brand-secondary font-medium">Price:</span>
                <span class="text-brand-rose font-bold text-lg">${{ product.price || 0 }}</span>
              </div>
              
              <div class="flex justify-between items-center pb-2 border-b border-brand-gold border-opacity-20">
                <span class="text-brand-secondary font-medium">Status:</span>
                <span class="px-3 py-1 rounded-full text-sm font-medium" 
                      :class="getStatusClass()">
                  {{ getStatusText() }}
                </span>
              </div>
              
              <div class="flex justify-between items-center pb-2 border-b border-brand-gold border-opacity-20">
                <span class="text-brand-secondary font-medium">Slug:</span>
                <span class="text-brand-primary font-mono text-sm break-all">{{ product.slug || 'N/A' }}</span>
              </div>
              
              <div class="flex justify-between items-center pb-2 border-b border-brand-gold border-opacity-20">
                <span class="text-brand-secondary font-medium">SKU:</span>
                <span class="text-brand-primary font-mono text-sm break-all">{{ product.sku || 'N/A' }}</span>
              </div>
              
              <div v-if="product.inventory_count !== undefined" class="flex justify-between items-center pb-2 border-b border-brand-gold border-opacity-20">
                <span class="text-brand-secondary font-medium">Inventory Count:</span>
                <span class="text-brand-primary font-semibold">{{ product.inventory_count || 0 }}</span>
              </div>
              
              <div v-if="product.product_type" class="flex justify-between items-center pb-2 border-b border-brand-gold border-opacity-20">
                <span class="text-brand-secondary font-medium">Product Type:</span>
                <span class="text-brand-primary font-semibold">{{ product.product_type }}</span>
              </div>
              
              <div v-if="product.design_template" class="flex justify-between items-center pb-2 border-b border-brand-gold border-opacity-20">
                <span class="text-brand-secondary font-medium">Design Template:</span>
                <span class="text-brand-primary font-semibold">{{ product.design_template }}</span>
              </div>
              
              <div v-if="product.template_category" class="flex justify-between items-center pb-2 border-b border-brand-gold border-opacity-20">
                <span class="text-brand-secondary font-medium">Template Category:</span>
                <span class="text-brand-primary font-semibold">{{ product.template_category }}</span>
              </div>
              
              <div class="flex justify-between items-center pb-2 border-b border-brand-gold border-opacity-20">
                <span class="text-brand-secondary font-medium">Created:</span>
                <span class="text-brand-primary">{{ formatDate(product.created_at) }}</span>
              </div>
              
              <div class="flex justify-between items-center pb-2 border-b border-brand-gold border-opacity-20">
                <span class="text-brand-secondary font-medium">Updated:</span>
                <span class="text-brand-primary">{{ formatDate(product.updated_at) }}</span>
              </div>
              
              <div v-if="product.totalSells !== undefined" class="flex justify-between items-center pb-2 border-b border-brand-gold border-opacity-20">
                <span class="text-brand-secondary font-medium">Total Sales:</span>
                <span class="text-brand-primary font-bold">{{ product.totalSells || 0 }}</span>
              </div>
              
              <div v-if="product.totalRevenue !== undefined" class="flex justify-between items-center pb-2 border-b border-brand-gold border-opacity-20">
                <span class="text-brand-secondary font-medium">Total Revenue:</span>
                <span class="text-brand-rose font-bold">${{ product.totalRevenue || 0 }}</span>
              </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
              <h3 class="font-brand-elegant text-lg text-brand-primary font-semibold mb-2">Description</h3>
              <p class="text-brand-secondary bg-gray-50 p-4 rounded-lg border border-brand-gold border-opacity-10">{{ product.description || 'No description available' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-wrap gap-4">
        <Link :href="route('admin.products.index')" 
              class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl hover:opacity-90 transition-opacity font-medium">
          Back to Products
        </Link>
        
        <Link :href="route('admin.products.edit', product.id)" 
              class="px-6 py-3 bg-gradient-to-r from-brand-primary to-brand-secondary text-white rounded-xl hover:opacity-90 transition-opacity font-medium">
          Edit Product
        </Link>
        
        <a :href="`/product/${product.slug}`" 
              target="_blank"
              class="px-6 py-3 bg-gradient-to-r from-brand-gold to-brand-accent text-brand-primary rounded-xl hover:opacity-90 transition-opacity font-medium">
          View on Site
        </a>
        
        <button 
          @click="confirmDeleteProduct"
          class="px-6 py-3 bg-gradient-to-r from-brand-rose to-red-600 text-white rounded-xl hover:opacity-90 transition-opacity font-medium">
          Delete Product
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/Admin.vue';
import { Link, router } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const confirm = useConfirm();
const toast = useToast();

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
});

const confirmDeleteProduct = () => {
  confirm.require({
    message: 'Are you sure you want to delete this product?',
    header: 'Delete Confirmation',
    icon: 'pi pi-exclamation-triangle',
    accept: () => {
      // Perform delete action using Inertia
      router.delete(route('admin.products.destroy', props.product.id), {
        onSuccess: () => {
          toast.add({
            severity: 'success',
            summary: 'Success',
            detail: 'Product deleted successfully',
            life: 3000
          });
        },
        onError: (errors) => {
          toast.add({
            severity: 'error',
            summary: 'Error',
            detail: errors?.authorization || 'Failed to delete product',
            life: 3000
          });
        }
      });
    },
    reject: () => {
      toast.add({
        severity: 'info',
        summary: 'Operation Cancelled',
        detail: 'Product deletion cancelled',
        life: 3000
      });
    }
  });
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const setDefaultImage = (event) => {
  event.target.src = '/images/logo.jpeg';
};

// Helper methods to handle both legacy and template-based products
const getMainImageUrl = () => {
  if (props.product.mainImage?.url) {
    return props.product.mainImage.url;
  } else if (props.product.thumbnail_url) {
    return props.product.thumbnail_url;
  }
  return '/images/logo.jpeg';
};

const hasOtherImages = () => {
  if (props.product.otherImages) {
    // For legacy t-shirts, otherImages is an object
    if (typeof props.product.otherImages === 'object') {
      return Object.keys(props.product.otherImages).length > 0;
    }
  }
  // For template-based products, check if there are images in the product
  if (props.product.images && props.product.images.length > 0) {
    return props.product.images.filter(img => img.order !== 1).length > 0;
  }
  return false;
};

const getOtherImagesArray = () => {
  if (props.product.otherImages) {
    // For legacy t-shirts, otherImages is an object
    if (typeof props.product.otherImages === 'object') {
      return Object.values(props.product.otherImages);
    }
  }
  // For template-based products, check if there are images
  if (props.product.images && props.product.images.length > 0) {
    return props.product.images.filter(img => img.order !== 1);
  }
  return [];
};

const getStatusClass = () => {
  const isActive = props.product.is_active !== undefined ? props.product.is_active : props.product.listed;
  return isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
};

const getStatusText = () => {
  const isActive = props.product.is_active !== undefined ? props.product.is_active : props.product.listed;
  return isActive ? 'Active' : 'Inactive';
};
</script>