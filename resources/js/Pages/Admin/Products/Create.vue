<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="font-brand-elegant text-3xl text-brand-primary font-bold">Create New Product</h1>
        <p class="text-brand-secondary">Add a new product to your collection</p>
      </div>

      <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20">
        <form @submit.prevent="handleCreateProduct">
          <!-- Product Type Selection -->
          <div class="mb-8">
            <h3 class="font-brand-elegant text-xl text-brand-primary font-semibold mb-4">Product Type</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex items-center">
                <input
                  v-model="createForm.is_template_based"
                  type="radio"
                  id="legacy_product"
                  :value="false"
                  class="w-4 h-4 text-brand-primary focus:ring-brand-gold"
                />
                <label for="legacy_product" class="ml-2 block text-sm font-medium text-brand-primary">
                  Legacy T-Shirt
                </label>
              </div>
              <div class="flex items-center">
                <input
                  v-model="createForm.is_template_based"
                  type="radio"
                  id="template_product"
                  :value="true"
                  class="w-4 h-4 text-brand-primary focus:ring-brand-gold"
                />
                <label for="template_product" class="ml-2 block text-sm font-medium text-brand-primary">
                  Template-Based Product
                </label>
              </div>
            </div>
          </div>

          <!-- Basic Information Section -->
          <div class="mb-8">
            <h3 class="font-brand-elegant text-xl text-brand-primary font-semibold mb-6">Basic Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-brand-primary mb-2">Product Name *</label>
                <input
                  v-model="createForm.title"
                  type="text"
                  class="w-full p-3 border border-brand-gold border-opacity-30 rounded-lg bg-white bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                  placeholder="Enter product name"
                  required
                />
                <div v-if="errors.name || errors.title" class="text-brand-rose text-sm mt-1">
                  {{ errors.name || errors.title }}
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-brand-primary mb-2">Price ($)</label>
                <input
                  v-model="createForm.price"
                  type="number"
                  min="0"
                  step="0.01"
                  class="w-full p-3 border border-brand-gold border-opacity-30 rounded-lg bg-white bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                  placeholder="0.00"
                />
                <div v-if="errors.price" class="text-brand-rose text-sm mt-1">{{ errors.price }}</div>
              </div>
            </div>
            
            <div class="mt-6">
              <label class="block text-sm font-medium text-brand-primary mb-2">Description *</label>
              <textarea
                v-model="createForm.description"
                rows="4"
                class="w-full p-3 border border-brand-gold border-opacity-30 rounded-lg bg-white bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                placeholder="Enter product description"
                required
              ></textarea>
              <div v-if="errors.description" class="text-brand-rose text-sm mt-1">{{ errors.description }}</div>
            </div>
          </div>

          <!-- Advanced Settings for Template-Based Products -->
          <div v-if="createForm.is_template_based" class="mb-8">
            <h3 class="font-brand-elegant text-xl text-brand-primary font-semibold mb-6">Advanced Settings</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-brand-primary mb-2">Product Type *</label>
                <select
                  v-model="createForm.product_type_id"
                  class="w-full p-3 border border-brand-gold border-opacity-30 rounded-lg bg-white bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                >
                  <option value="">Select Product Type</option>
                  <option v-for="type in productTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                </select>
                <div v-if="errors.product_type_id" class="text-brand-rose text-sm mt-1">{{ errors.product_type_id }}</div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-brand-primary mb-2">Status</label>
                <div class="flex items-center space-x-4">
                  <label class="inline-flex items-center cursor-pointer">
                    <input 
                      type="checkbox" 
                      v-model="createForm.is_active"
                      class="sr-only peer"
                    >
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-primary rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-primary"></div>
                    <span class="ml-3 text-sm font-medium text-brand-secondary">
                      {{ createForm.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </label>
                </div>
                <div v-if="errors.is_active" class="text-brand-rose text-sm mt-1">{{ errors.is_active }}</div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
              <div>
                <label class="block text-sm font-medium text-brand-primary mb-2">SKU</label>
                <input
                  v-model="createForm.sku"
                  type="text"
                  class="w-full p-3 border border-brand-gold border-opacity-30 rounded-lg bg-white bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                  placeholder="Enter SKU"
                />
                <div v-if="errors.sku" class="text-brand-rose text-sm mt-1">{{ errors.sku }}</div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-brand-primary mb-2">Inventory Count</label>
                <input
                  v-model="createForm.inventory_count"
                  type="number"
                  min="0"
                  class="w-full p-3 border border-brand-gold border-opacity-30 rounded-lg bg-white bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                  placeholder="Enter inventory count"
                />
                <div v-if="errors.inventory_count" class="text-brand-rose text-sm mt-1">{{ errors.inventory_count }}</div>
              </div>
            </div>

            <div class="mt-6">
              <label class="block text-sm font-medium text-brand-primary mb-2">Design Template</label>
              <select
                v-model="createForm.design_template_id"
                class="w-full p-3 border border-brand-gold border-opacity-30 rounded-lg bg-white bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
              >
                <option value="">Select Design Template</option>
                <option v-for="template in designTemplates" :key="template.id" :value="template.id">{{ template.name }}</option>
              </select>
              <div v-if="errors.design_template_id" class="text-brand-rose text-sm mt-1">{{ errors.design_template_id }}</div>
            </div>

            <div class="mt-6">
              <label class="block text-sm font-medium text-brand-primary mb-2">Template Category</label>
              <input
                v-model="createForm.template_category"
                type="text"
                class="w-full p-3 border border-brand-gold border-opacity-30 rounded-lg bg-white bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
                placeholder="Enter template category"
              />
              <div v-if="errors.template_category" class="text-brand-rose text-sm mt-1">{{ errors.template_category }}</div>
            </div>
          </div>

          <!-- Legacy T-Shirt Settings -->
          <div v-else class="mb-8">
            <h3 class="font-brand-elegant text-xl text-brand-primary font-semibold mb-6">Legacy Settings</h3>
            
            <div class="flex items-center space-x-4">
              <label class="inline-flex items-center cursor-pointer">
                <input 
                  type="checkbox" 
                  v-model="createForm.listed"
                  class="sr-only peer"
                >
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-primary rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-primary"></div>
                <span class="ml-3 text-sm font-medium text-brand-secondary">
                  {{ createForm.listed ? 'Listed' : 'Unlisted' }}
                </span>
              </label>
            </div>
            <div v-if="errors.listed" class="text-brand-rose text-sm mt-1">{{ errors.listed }}</div>
          </div>

          <!-- Images Section -->
          <div class="mb-8">
            <h3 class="font-brand-elegant text-xl text-brand-primary font-semibold mb-6">Product Images</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <!-- Main Image -->
              <div>
                <label class="block text-sm font-medium text-brand-primary mb-2">Main Image *</label>
                <div class="border-2 border-dashed border-brand-gold border-opacity-30 rounded-xl p-6 text-center bg-white bg-opacity-30">
                  <input
                    type="file"
                    accept="image/*"
                    @change="handleImageUpload($event, 'mainImage')"
                    class="hidden"
                    ref="mainImageInput"
                  />
                  <div v-if="!previewImages.mainImage" class="space-y-4">
                    <div class="mx-auto w-16 h-16 bg-brand-gold bg-opacity-20 rounded-full flex items-center justify-center">
                      <svg class="w-8 h-8 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                    </div>
                    <p class="text-brand-secondary">Click to upload main image</p>
                    <button
                      type="button"
                      @click="$refs.mainImageInput.click()"
                      class="px-4 py-2 bg-gradient-to-r from-brand-primary to-brand-secondary text-white rounded-lg hover:opacity-90 transition-opacity"
                    >
                      Select Image
                    </button>
                  </div>
                  <div v-else class="space-y-4">
                    <img :src="previewImages.mainImage" alt="Main image preview" class="mx-auto max-h-64 rounded-lg object-contain" />
                    <button
                      type="button"
                      @click="removeImage('mainImage')"
                      class="px-4 py-2 bg-gradient-to-r from-brand-rose to-red-600 text-white rounded-lg hover:opacity-90 transition-opacity"
                    >
                      Remove Image
                    </button>
                  </div>
                </div>
                <div v-if="errors.mainImage" class="text-brand-rose text-sm mt-1">{{ errors.mainImage }}</div>
              </div>

              <!-- Additional Images -->
              <div>
                <label class="block text-sm font-medium text-brand-primary mb-2">Additional Images</label>
                <div class="grid grid-cols-2 gap-4">
                  <div v-for="n in [2, 3, 4, 5]" :key="n">
                    <div class="border-2 border-dashed border-brand-gold border-opacity-30 rounded-xl p-3 text-center bg-white bg-opacity-30">
                      <input
                        type="file"
                        accept="image/*"
                        @change="handleImageUpload($event, `image${n-1}`)"
                        class="hidden"
                        :ref="el => imageRefs[n-2] = el"
                      />
                      <div v-if="!previewImages[`image${n-1}`]" class="space-y-2">
                        <div class="mx-auto w-12 h-12 bg-brand-gold bg-opacity-20 rounded-full flex items-center justify-center">
                          <svg class="w-6 h-6 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                          </svg>
                        </div>
                        <button
                          type="button"
                          @click="imageRefs[n-2]?.click()"
                          class="text-xs px-2 py-1 bg-brand-gold bg-opacity-20 text-brand-gold rounded hover:bg-opacity-30 transition-opacity"
                        >
                          Img {{ n }}
                        </button>
                      </div>
                      <div v-else class="space-y-2">
                        <img :src="previewImages[`image${n-1}`]" alt="Image preview" class="mx-auto max-h-24 rounded-lg object-contain" />
                        <button
                          type="button"
                          @click="removeImage(`image${n-1}`)"
                          class="text-xs px-2 py-1 bg-brand-rose bg-opacity-20 text-brand-rose rounded hover:bg-opacity-30 transition-opacity"
                        >
                          Remove
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Section -->
          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="processing"
              class="px-8 py-3 bg-gradient-to-r from-brand-primary to-brand-secondary text-white rounded-xl hover:opacity-90 transition-opacity font-semibold text-lg"
              :class="{ 'opacity-70 cursor-not-allowed': processing }"
            >
              <span v-if="processing">Creating Product...</span>
              <span v-else>Create Product</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/Admin.vue';
import { ref, reactive, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

// Form data using Inertia's useForm
const createForm = useForm({
  title: '',
  price: 0,
  description: '',
  is_template_based: false,
  product_type_id: '',
  is_active: true,
  sku: '',
  inventory_count: 0,
  design_template_id: '',
  template_category: '',
  listed: true,
  mainImage: null,
  secondImage: null,
  thirdImage: null,
  forthImage: null,
  fifthImage: null
});

// Refs for file inputs
const mainImageInput = ref(null);
const imageRefs = ref([]);

// Preview images
const previewImages = reactive({
  mainImage: null,
  image1: null,
  image2: null,
  image3: null,
  image4: null
});

// State
const processing = ref(false);
const errors = ref({});
const productTypes = ref([]);
const designTemplates = ref([]);

// Fetch product types and design templates when component mounts
onMounted(async () => {
  try {
    // Fetch product categories
    const typesResponse = await fetch('/api/v1/product-categories');
    if (typesResponse.ok) {
      productTypes.value = await typesResponse.json();
    } else {
      console.error('Failed to fetch product categories:', typesResponse.status, await typesResponse.text());
      // Provide fallback empty array
      productTypes.value = [];
    }
    
    // Fetch design templates
    const templatesResponse = await fetch('/api/v1/design-templates');
    if (templatesResponse.ok) {
      designTemplates.value = await templatesResponse.json();
    } else {
      console.error('Failed to fetch design templates:', templatesResponse.status, await templatesResponse.text());
      // Provide fallback empty array
      designTemplates.value = [];
    }
  } catch (error) {
    console.error('Error fetching product categories and templates:', error);
    // Provide fallback empty arrays
    productTypes.value = [];
    designTemplates.value = [];
  }
});

// Handle image upload
const handleImageUpload = (event, fieldName) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      previewImages[fieldName] = e.target.result;
      
      // Set the actual form data
      if (fieldName === 'mainImage') {
        createForm.mainImage = file;
      } else if (fieldName === 'image1') {
        createForm.secondImage = file;
      } else if (fieldName === 'image2') {
        createForm.thirdImage = file;
      } else if (fieldName === 'image3') {
        createForm.forthImage = file;
      } else if (fieldName === 'image4') {
        createForm.fifthImage = file;
      }
    };
    reader.readAsDataURL(file);
  }
};

// Remove image
const removeImage = (fieldName) => {
  previewImages[fieldName] = null;
  
  // Clear the actual form data
  if (fieldName === 'mainImage') {
    createForm.mainImage = null;
    if (mainImageInput.value) mainImageInput.value.value = '';
  } else if (fieldName === 'image1') {
    createForm.secondImage = null;
    if (imageRefs.value[0]) imageRefs.value[0].value = '';
  } else if (fieldName === 'image2') {
    createForm.thirdImage = null;
    if (imageRefs.value[1]) imageRefs.value[1].value = '';
  } else if (fieldName === 'image3') {
    createForm.forthImage = null;
    if (imageRefs.value[2]) imageRefs.value[2].value = '';
  } else if (fieldName === 'image4') {
    createForm.fifthImage = null;
    if (imageRefs.value[3]) imageRefs.value[3].value = '';
  }
};

// Handle form submission
const handleCreateProduct = () => {
  processing.value = true;
  errors.value = {};

  // Log form data for debugging
  console.log('Creating product with data:', {
    title: createForm.title,
    name: createForm.title, // Will be same as title
    price: createForm.price,
    description: createForm.description,
    is_template_based: createForm.is_template_based,
    product_type_id: createForm.product_type_id,
    design_template_id: createForm.design_template_id,
    hasMainImage: !!createForm.mainImage,
    hasOtherImages: !!createForm.secondImage || !!createForm.thirdImage || !!createForm.forthImage || !!createForm.fifthImage
  });

  // Create FormData for file uploads
  const formData = new FormData();
  
  // Append images to formData
  if (createForm.mainImage) formData.append('mainImage', createForm.mainImage);
  if (createForm.secondImage) formData.append('secondImage', createForm.secondImage);
  if (createForm.thirdImage) formData.append('thirdImage', createForm.thirdImage);
  if (createForm.forthImage) formData.append('forthImage', createForm.forthImage);
  if (createForm.fifthImage) formData.append('fifthImage', createForm.fifthImage);

  // Append other form fields to formData - only include fields relevant to the product type
  if (createForm.is_template_based) {
    // For template-based products
    formData.append('name', createForm.title); // Use name for template-based
    formData.append('title', createForm.title); // Also include title for consistency
    formData.append('price', createForm.price);
    formData.append('description', createForm.description);
    formData.append('is_template_based', createForm.is_template_based);
    
    // Append template-based specific fields
    formData.append('product_type_id', createForm.product_type_id || '');
    formData.append('is_active', createForm.is_active);
    if (createForm.sku) formData.append('sku', createForm.sku);
    if (createForm.inventory_count) formData.append('inventory_count', createForm.inventory_count);
    if (createForm.design_template_id) formData.append('design_template_id', createForm.design_template_id);
    if (createForm.template_category) formData.append('template_category', createForm.template_category);
  } else {
    // For legacy t-shirts
    formData.append('title', createForm.title); // Use title for legacy
    formData.append('name', createForm.title); // Also include name for consistency
    formData.append('price', createForm.price);
    formData.append('description', createForm.description);
    formData.append('is_template_based', createForm.is_template_based);
    formData.append('listed', createForm.listed);
  }

  // Submit using Inertia
  createForm.post('/admin/products', {
    data: formData,
    forceFormData: true,
    onSuccess: (page) => {
      processing.value = false;
      console.log('Product created successfully:', page.props);
    },
    onError: (errs) => {
      processing.value = false;
      console.error('Product creation failed:', errs);
      
      // Log all errors to console
      Object.entries(errs).forEach(([field, message]) => {
        console.error(`Validation error in ${field}:`, message);
      });
    }
  });
};
</script>