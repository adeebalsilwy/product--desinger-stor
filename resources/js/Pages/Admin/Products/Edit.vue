<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="font-brand-elegant text-3xl text-brand-primary font-bold">Edit Product</h1>
        <p class="text-brand-secondary">Update product information and settings</p>
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

      <!-- Product Edit Form -->
      <div v-else class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20">
        <!-- Product Type Badge -->
        <div class="mb-6">
          <span class="inline-block px-3 py-1 rounded-full text-sm font-medium" 
                :class="product.type === 'legacy_tshirt' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
            {{ product.type === 'legacy_tshirt' ? 'Legacy T-Shirt' : 'Template-Based Product' }}
          </span>
        </div>

        <form @submit.prevent="submitForm" class="space-y-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column - Basic Information -->
            <div>
              <h2 class="font-brand-elegant text-xl text-brand-primary font-semibold mb-4">Basic Information</h2>
              
              <!-- Title Field -->
              <div class="mb-4">
                <label class="block text-brand-secondary font-medium mb-2" for="title">Title *</label>
                <input 
                  id="title"
                  v-model="formData.title"
                  type="text"
                  class="w-full px-4 py-3 rounded-xl border border-brand-gold border-opacity-30 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent bg-white bg-opacity-50"
                  :class="{ 'border-red-500': errors.name || errors.title }"
                  placeholder="Enter product title"
                  required
                />
                <div v-if="errors.name || errors.title" class="text-red-500 text-sm mt-1">{{ errors.name || errors.title }}</div>
              </div>
              
              <!-- Description Field -->
              <div class="mb-4">
                <label class="block text-brand-secondary font-medium mb-2" for="description">Description *</label>
                <textarea 
                  id="description"
                  v-model="formData.description"
                  rows="4"
                  class="w-full px-4 py-3 rounded-xl border border-brand-gold border-opacity-30 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent bg-white bg-opacity-50"
                  :class="{ 'border-red-500': errors.description }"
                  placeholder="Enter product description"
                  required
                ></textarea>
                <div v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description }}</div>
              </div>
              
              <!-- Price Field -->
              <div class="mb-4">
                <label class="block text-brand-secondary font-medium mb-2" for="price">Price ($)</label>
                <input 
                  id="price"
                  v-model.number="formData.price"
                  type="number"
                  step="0.01"
                  min="0"
                  class="w-full px-4 py-3 rounded-xl border border-brand-gold border-opacity-30 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent bg-white bg-opacity-50"
                  :class="{ 'border-red-500': errors.price }"
                  placeholder="Enter price"
                />
                <div v-if="errors.price" class="text-red-500 text-sm mt-1">{{ errors.price }}</div>
              </div>
              
              <!-- Status Toggle -->
              <div class="mb-4">
                <label class="block text-brand-secondary font-medium mb-2">Status</label>
                <div class="flex items-center space-x-4">
                  <label class="inline-flex items-center cursor-pointer">
                    <input 
                      type="checkbox" 
                      v-model="formData.isActive"
                      class="sr-only peer"
                    >
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-primary rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-primary"></div>
                    <span class="ml-3 text-sm font-medium text-brand-secondary">
                      {{ formData.isActive ? 'Active' : 'Inactive' }}
                    </span>
                  </label>
                </div>
                <div v-if="errors.listed || errors.is_active" class="text-red-500 text-sm mt-1">
                  {{ errors.listed || errors.is_active }}
                </div>
              </div>
              
              <!-- SKU Field -->
              <div v-if="product.sku !== undefined" class="mb-4">
                <label class="block text-brand-secondary font-medium mb-2" for="sku">SKU</label>
                <input 
                  id="sku"
                  v-model="formData.sku"
                  type="text"
                  class="w-full px-4 py-3 rounded-xl border border-brand-gold border-opacity-30 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent bg-white bg-opacity-50"
                  :class="{ 'border-red-500': errors.sku }"
                  placeholder="Enter SKU"
                />
                <div v-if="errors.sku" class="text-red-500 text-sm mt-1">{{ errors.sku }}</div>
              </div>
              
              <!-- Inventory Count Field -->
              <div v-if="product.inventory_count !== undefined" class="mb-4">
                <label class="block text-brand-secondary font-medium mb-2" for="inventory_count">Inventory Count</label>
                <input 
                  id="inventory_count"
                  v-model.number="formData.inventory_count"
                  type="number"
                  min="0"
                  class="w-full px-4 py-3 rounded-xl border border-brand-gold border-opacity-30 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent bg-white bg-opacity-50"
                  :class="{ 'border-red-500': errors.inventory_count }"
                  placeholder="Enter inventory count"
                />
                <div v-if="errors.inventory_count" class="text-red-500 text-sm mt-1">{{ errors.inventory_count }}</div>
              </div>
            </div>

            <!-- Right Column - Advanced Settings & Images -->
            <div>
              <h2 class="font-brand-elegant text-xl text-brand-primary font-semibold mb-4">Advanced Settings</h2>
              
              <!-- Product Type Selection (for template-based products) -->
              <div v-if="product.type === 'product'" class="mb-4">
                <label class="block text-brand-secondary font-medium mb-2" for="product_type_id">Product Type *</label>
                <select 
                  id="product_type_id"
                  v-model="formData.product_type_id"
                  class="w-full px-4 py-3 rounded-xl border border-brand-gold border-opacity-30 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent bg-white bg-opacity-50"
                  :class="{ 'border-red-500': errors.product_type_id }"
                >
                  <option value="">Select Product Type</option>
                  <option v-for="type in productTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                </select>
                <div v-if="errors.product_type_id" class="text-red-500 text-sm mt-1">{{ errors.product_type_id }}</div>
              </div>
              
              <!-- Design Template Selection (for template-based products) -->
              <div v-if="product.type === 'product'" class="mb-4">
                <label class="block text-brand-secondary font-medium mb-2" for="design_template_id">Design Template</label>
                <select 
                  id="design_template_id"
                  v-model="formData.design_template_id"
                  class="w-full px-4 py-3 rounded-xl border border-brand-gold border-opacity-30 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent bg-white bg-opacity-50"
                  :class="{ 'border-red-500': errors.design_template_id }"
                >
                  <option value="">Select Design Template</option>
                  <option v-for="template in designTemplates" :key="template.id" :value="template.id">{{ template.name }}</option>
                </select>
                <div v-if="errors.design_template_id" class="text-red-500 text-sm mt-1">{{ errors.design_template_id }}</div>
              </div>
              
              <!-- Template Category (for template-based products) -->
              <div v-if="product.type === 'product'" class="mb-4">
                <label class="block text-brand-secondary font-medium mb-2" for="template_category">Template Category</label>
                <input 
                  id="template_category"
                  v-model="formData.template_category"
                  type="text"
                  class="w-full px-4 py-3 rounded-xl border border-brand-gold border-opacity-30 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent bg-white bg-opacity-50"
                  :class="{ 'border-red-500': errors.template_category }"
                  placeholder="Enter template category"
                />
                <div v-if="errors.template_category" class="text-red-500 text-sm mt-1">{{ errors.template_category }}</div>
              </div>
              
              <!-- Images Section -->
              <div class="mb-4">
                <h3 class="font-brand-elegant text-lg text-brand-primary font-semibold mb-4">Product Images</h3>
                
                <!-- Main Image -->
                <div v-if="product.mainImage || product.thumbnail_url" class="mb-4">
                  <label class="block text-brand-secondary font-medium mb-2">Main Image</label>
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
                <div v-if="hasOtherImages()" class="mt-4">
                  <label class="block text-brand-secondary font-medium mb-2">Additional Images</label>
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-6">
            <button
              type="submit"
              :disabled="processing"
              class="w-full md:w-auto px-8 py-3 bg-gradient-to-r from-brand-primary to-brand-secondary text-white rounded-xl hover:opacity-90 transition-opacity font-medium"
              :class="{ 'opacity-70 cursor-not-allowed': processing }"
            >
              <span v-if="processing">Updating...</span>
              <span v-else>Update Product</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/Admin.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  productTypes: {
    type: Array,
    default: () => []
  },
  designTemplates: {
    type: Array,
    default: () => []
  }
});

// Initialize form data based on product type
const initialFormData = {
  title: props.product.title || props.product.name || '',
  description: props.product.description || '',
  price: props.product.price || 0,
  isActive: props.product.is_active !== undefined ? props.product.is_active : props.product.listed,
  sku: props.product.sku || '',
  inventory_count: props.product.inventory_count || 0,
  product_type_id: props.product.product_type_id || '',
  design_template_id: props.product.design_template_id || '',
  template_category: props.product.template_category || ''
};

// Create reactive references for form data and errors
const formData = ref({ ...initialFormData });
const errors = ref({});
const processing = ref(false);

// Function to submit the form
const submitForm = () => {
  processing.value = true;
  
  // Log form data for debugging
  console.log('Updating product with data:', {
    id: props.product.id,
    title: formData.value.title,
    description: formData.value.description,
    price: formData.value.price,
    isActive: formData.value.isActive,
    productType: props.product.type,
    originalProduct: props.product
  });
  
  // Prepare form data based on product type
  const submitData = {
    ...formData.value,
    // Map the generic isActive field to the appropriate field name based on product type
    [props.product.type === 'legacy_tshirt' ? 'listed' : 'is_active']: formData.value.isActive
  };
  
  // Submit using Inertia
  router.put(route('admin.products.update', props.product.id), submitData, {
    onSuccess: (page) => {
      processing.value = false;
      console.log('Product updated successfully:', page.props);
    },
    onError: (validationErrors) => {
      processing.value = false;
      errors.value = validationErrors;
      console.error('Product update failed:', validationErrors);
      
      // Log all errors to console
      Object.entries(validationErrors).forEach(([field, message]) => {
        console.error(`Validation error in ${field}:`, message);
      });
    }
  });
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

const setDefaultImage = (event) => {
  event.target.src = '/images/logo.jpeg';
};
</script>