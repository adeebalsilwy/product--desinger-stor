<template>
  <CustomerLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Design {{ product?.name || productType.name }}
        </h1>
        <p class="text-gray-600">
          Create your custom design for {{ product?.name || productType.name }}
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Left Panel - Tools & Assets -->
        <div class="lg:col-span-1 bg-white rounded-lg shadow-md p-6 h-fit">
          <div class="space-y-6">
            <!-- Templates Section -->
            <div class="border-b pb-4">
              <h3 class="font-semibold text-lg mb-3">Templates</h3>
              
              <div class="space-y-2">
                <button 
                  @click="showTemplates = true"
                  class="w-full flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg transition-colors"
                >
                  <span class="text-lg">📋</span>
                  Browse Templates
                </button>
              </div>
            </div>
            
            <!-- Text Tool -->
            <div class="border-b pb-4">
              <h3 class="font-semibold text-lg mb-3">Add Elements</h3>
              
              <div class="space-y-2">
                <button 
                  @click="addText"
                  class="w-full flex items-center gap-2 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors"
                >
                  <span class="text-lg">T</span>
                  Add Text
                </button>
                
                <button 
                  @click="triggerImageUpload"
                  class="w-full flex items-center gap-2 px-4 py-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition-colors"
                >
                  <span class="text-lg">📷</span>
                  Upload Image
                </button>
                
                <button 
                  @click="showCliparts = true"
                  class="w-full flex items-center gap-2 px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg transition-colors"
                >
                  <span class="text-lg">🎨</span>
                  Add Clipart
                </button>
              </div>
            </div>

            <!-- Recent Designs -->
            <div class="border-b pb-4">
              <h3 class="font-semibold text-lg mb-3">Recent Designs</h3>
              <div class="space-y-2">
                <button 
                  v-for="design in recentDesigns"
                  :key="design.id"
                  @click="loadDesign(design)"
                  class="w-full text-left p-2 hover:bg-gray-50 rounded text-sm"
                >
                  {{ design.name }}
                </button>
              </div>
            </div>

            <!-- Assets -->
            <div>
              <h3 class="font-semibold text-lg mb-3">My Assets</h3>
              <div class="space-y-2 max-h-40 overflow-y-auto">
                <div 
                  v-for="asset in userAssets"
                  :key="asset.id"
                  class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer"
                  @click="addImageFromAsset(asset)"
                >
                  <img :src="asset.thumbnail_url" class="w-8 h-8 object-cover rounded" />
                  <span class="text-sm">{{ asset.original_filename }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Canvas Area -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-semibold">Design Canvas</h2>
              <div class="flex gap-2">
                <button 
                  @click="saveDesign"
                  :disabled="!hasChanges"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  💾 Save Design
                </button>
                <button 
                  @click="exportDesign"
                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                >
                  📤 Export
                </button>
              </div>
            </div>
            
            <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
              <ProductDesigner 
                ref="designer"
                :product-type-id="productType.id"
                @saved="onDesignSaved"
                @changed="onDesignChanged"
              />
            </div>
          </div>
        </div>

        <!-- Right Panel - Properties & Preview -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Properties Panel -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold text-lg mb-4">Properties</h3>
            <div v-if="selectedObject">
              <div class="space-y-4">
                <div v-if="selectedObject.type === 'i-text'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Font</label>
                  <select 
                    v-model="selectedObject.fontFamily" 
                    @change="updateCanvas"
                    class="w-full p-2 border rounded"
                  >
                    <option v-for="font in fonts" :value="font">{{ font }}</option>
                  </select>
                  
                  <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">Color</label>
                  <input 
                    type="color" 
                    v-model="selectedObject.fill" 
                    @change="updateCanvas"
                    class="w-full h-10"
                  />
                  
                  <label class="block text-sm font-medium text-gray-700 mt-3 mb-1">
                    Size: {{ selectedObject.fontSize }}
                  </label>
                  <input 
                    type="range" 
                    v-model.number="selectedObject.fontSize" 
                    min="8" 
                    max="200"
                    @input="updateCanvas"
                    class="w-full"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Opacity: {{ Math.round(selectedObject.opacity * 100) }}%
                  </label>
                  <input 
                    type="range" 
                    v-model.number="selectedObject.opacity" 
                    min="0" 
                    max="1"
                    step="0.1"
                    @input="updateCanvas"
                    class="w-full"
                  />
                </div>
                
                <div class="flex gap-2 pt-2">
                  <button @click="bringForward" class="flex-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">
                    ↑ Forward
                  </button>
                  <button @click="sendBackwards" class="flex-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">
                    ↓ Backward
                  </button>
                  <button @click="deleteObject" class="flex-1 px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded text-sm">
                    🗑️ Delete
                  </button>
                </div>
              </div>
            </div>
            <div v-else class="text-gray-500 text-center py-8">
              Select an element to edit properties
            </div>
          </div>

          <!-- Preview -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold text-lg mb-4">Preview</h3>
            <div class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center">
              <img 
                v-if="previewUrl" 
                :src="previewUrl" 
                alt="Design Preview" 
                class="max-w-full max-h-full object-contain"
              />
              <div v-else class="text-gray-500">
                No preview available
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Templates Modal -->
      <div v-if="showTemplates" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] m-4 overflow-hidden">
          <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-xl font-semibold">Choose a Template</h3>
            <button @click="showTemplates = false" class="text-gray-500 hover:text-gray-700 text-2xl">×</button>
          </div>
          <div class="p-4 max-h-[70vh] overflow-y-auto">
            <div class="mb-4 flex justify-between items-center">
              <input
                v-model="templateSearch"
                type="text"
                placeholder="Search templates..."
                class="px-4 py-2 border border-gray-300 rounded-lg w-64"
              />
              <select
                v-model="templateCategory"
                class="px-4 py-2 border border-gray-300 rounded-lg"
              >
                <option value="">All Categories</option>
                <option v-for="category in templateCategories" :key="category" :value="category">
                  {{ category.charAt(0).toUpperCase() + category.slice(1) }}
                </option>
              </select>
            </div>
            
            <div v-if="loadingTemplates" class="flex justify-center items-center h-40">
              <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
            </div>
            
            <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
              <div 
                v-for="template in templates" 
                :key="template.id"
                class="border rounded-lg overflow-hidden cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all"
                @click="useTemplate(template)"
              >
                <div class="aspect-square bg-gray-100">
                  <img :src="template.thumbnail_url || template.preview_url" :alt="template.name" class="w-full h-full object-cover" />
                </div>
                <div class="p-2">
                  <div class="font-medium text-sm truncate">{{ template.name }}</div>
                  <div class="text-xs text-gray-500">{{ template.category || 'Uncategorized' }}</div>
                </div>
              </div>
            </div>
            
            <!-- Pagination -->
            <div v-if="!loadingTemplates && templates.length > 0" class="mt-4 flex justify-center">
              <nav class="flex items-center space-x-2">
                <button
                  @click="fetchTemplates(currentTemplatePage - 1)"
                  :disabled="currentTemplatePage === 1"
                  class="px-3 py-1 rounded border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Previous
                </button>
                
                <span class="mx-2">
                  {{ currentTemplatePage }} of {{ totalTemplatePages }}
                </span>
                
                <button
                  @click="fetchTemplates(currentTemplatePage + 1)"
                  :disabled="currentTemplatePage === totalTemplatePages"
                  class="px-3 py-1 rounded border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Next
                </button>
              </nav>
            </div>
          </div>
          <div class="p-4 border-t text-center">
            <button @click="showTemplates = false" class="px-4 py-2 bg-gray-200 rounded-lg">
              Close
            </button>
          </div>
        </div>
      </div>

      <!-- Clipart Modal -->
      <div v-if="showCliparts" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-96 m-4 overflow-hidden">
          <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-xl font-semibold">Choose Clipart</h3>
            <button @click="showCliparts = false" class="text-gray-500 hover:text-gray-700 text-2xl">×</button>
          </div>
          <div class="p-4 max-h-80 overflow-y-auto">
            <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
              <div 
                v-for="clipart in cliparts" 
                :key="clipart.id"
                class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:ring-2 hover:ring-blue-500"
                @click="addClipart(clipart)"
              >
                <img :src="clipart.image_url" :alt="clipart.title" class="w-full h-full object-cover" />
              </div>
            </div>
          </div>
          <div class="p-4 border-t text-center">
            <button @click="showCliparts = false" class="px-4 py-2 bg-gray-200 rounded-lg">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script>
import CustomerLayout from '@/Layouts/Customer.vue';
import ProductDesigner from '@/Components/Designer/ProductDesigner.vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

export default {
  name: 'DesignerCreate',
  
  components: {
    CustomerLayout,
    ProductDesigner,
  },
  
  props: {
    productType: Object,
    product: Object,
    printAreas: Array,
  },
  
  data() {
    return {
      showCliparts: false,
      showTemplates: false,
      cliparts: [],
      fonts: [],
      userAssets: [],
      recentDesigns: [],
      selectedObject: null,
      previewUrl: null,
      hasChanges: false,
      templates: [],
      loadingTemplates: false,
      templateSearch: '',
      templateCategory: '',
      currentTemplatePage: 1,
      totalTemplatePages: 1,
      templateCategories: ['t-shirt', 'hoodie', 'mug', 'poster', 'other'],
    };
  },
  
  async created() {
    await this.loadAssets();
    await this.loadUserAssets();
    await this.loadRecentDesigns();
    await this.fetchTemplates(1);
    
    // Watch for template filters
    this.$watch('templateSearch', this.onTemplateFilterChange);
    this.$watch('templateCategory', this.onTemplateFilterChange);
  },
  
  methods: {
    async loadAssets() {
      try {
        const [fontsRes, clipartsRes] = await Promise.all([
          axios.get('/api/v1/fonts'),
          axios.get('/api/v1/cliparts'),
        ]);
        
        this.fonts = fontsRes.data.data.map(f => f.name);
        this.cliparts = clipartsRes.data.data;
      } catch (error) {
        console.error('Failed to load assets:', error);
      }
    },
    
    async loadUserAssets() {
      try {
        const response = await axios.get('/api/assets');
        this.userAssets = response.data.data;
      } catch (error) {
        console.error('Failed to load user assets:', error);
      }
    },
    
    async loadRecentDesigns() {
      try {
        const response = await axios.get('/api/designs');
        this.recentDesigns = response.data.data.slice(0, 5); // Last 5 designs
      } catch (error) {
        console.error('Failed to load recent designs:', error);
      }
    },
    
    async fetchTemplates(page = 1) {
      this.loadingTemplates = true;
      try {
        const response = await axios.get('/admin/templates', {
          params: {
            page,
            search: this.templateSearch,
            category: this.templateCategory
          }
        });
        
        this.templates = response.data.templates;
        this.currentTemplatePage = response.data.pagination.current_page;
        this.totalTemplatePages = response.data.pagination.last_page;
      } catch (error) {
        console.error('Failed to load templates:', error);
      } finally {
        this.loadingTemplates = false;
      }
    },
    
    onTemplateFilterChange() {
      this.fetchTemplates(1);
    },
    
    async useTemplate(template) {
      // Load the template into the designer
      this.showTemplates = false;
      
      // Call the designer component method to load template
      this.$refs.designer.loadTemplate(template);
    },
    
    addText() {
      this.$refs.designer.addText();
    },
    
    triggerImageUpload() {
      this.$refs.designer.triggerImageUpload();
    },
    
    addClipart(clipart) {
      this.showCliparts = false;
      this.$refs.designer.addClipart(clipart);
    },
    
    addImageFromAsset(asset) {
      this.$refs.designer.addImageFromUrl(asset.file_url);
    },
    
    saveDesign() {
      this.$refs.designer.saveDesign();
    },
    
    exportDesign() {
      this.$refs.designer.exportDesign();
    },
    
    onDesignSaved(design) {
      this.hasChanges = false;
      // Use Inertia router to navigate
      this.$inertia.visit(route('designer.my-designs'));
    },
    
    onDesignChanged() {
      this.hasChanges = true;
    },
    
    loadDesign(design) {
      this.$refs.designer.loadDesign(design);
    },
    
    updateCanvas() {
      this.$refs.designer.updateCanvas();
      this.hasChanges = true;
    },
    
    bringForward() {
      this.$refs.designer.bringForward();
      this.hasChanges = true;
    },
    
    sendBackwards() {
      this.$refs.designer.sendBackwards();
      this.hasChanges = true;
    },
    
    deleteObject() {
      this.$refs.designer.deleteObject();
      this.hasChanges = true;
    },
  },
};
</script>