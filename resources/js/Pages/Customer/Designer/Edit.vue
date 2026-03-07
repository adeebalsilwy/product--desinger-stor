<template>
  <CustomerLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Design</h1>
        <p class="text-gray-600">Editing: {{ design.name }}</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Left Panel - Tools & Assets -->
        <div class="lg:col-span-1 bg-white rounded-lg shadow-md p-6 h-fit">
          <div class="space-y-6">
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

            <!-- Save Options -->
            <div class="border-b pb-4">
              <h3 class="font-semibold text-lg mb-3">Actions</h3>
              <div class="space-y-2">
                <button 
                  @click="saveDesign"
                  :disabled="!hasChanges"
                  class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  💾 Save Changes
                </button>
                
                <button 
                  @click="duplicateDesign"
                  class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700"
                >
                  📋 Duplicate
                </button>
                
                <button 
                  @click="saveAsTemplate"
                  class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700"
                >
                  🏷️ Save as Template
                </button>
                
                <button 
                  @click="deleteDesign"
                  class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                >
                  🗑️ Delete Design
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
              <h2 class="text-xl font-semibold">Edit Design</h2>
              <div class="flex gap-2">
                <button 
                  @click="exportDesign"
                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                >
                  📤 Export
                </button>
                <button 
                  @click="addToCart"
                  class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700"
                >
                  🛒 Add to Cart
                </button>
              </div>
            </div>
            
            <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
              <ProductDesigner 
                ref="designer"
                :product-type-id="design.product_type_id"
                :initial-design="design"
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
                v-if="design.preview_url" 
                :src="design.preview_url" 
                alt="Design Preview" 
                class="max-w-full max-h-full object-contain"
              />
              <div v-else class="text-gray-500">
                No preview available
              </div>
            </div>
            
            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Design Name</label>
              <input 
                v-model="design.name"
                @blur="updateDesignName"
                type="text" 
                class="w-full p-2 border rounded"
                placeholder="Enter design name"
              />
            </div>
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

export default {
  name: 'DesignerEdit',
  
  components: {
    CustomerLayout,
    ProductDesigner,
  },
  
  props: {
    design: Object,
    printAreas: Array,
  },
  
  data() {
    return {
      showCliparts: false,
      cliparts: [],
      fonts: [],
      userAssets: [],
      selectedObject: null,
      hasChanges: false,
    };
  },
  
  async created() {
    await this.loadAssets();
    await this.loadUserAssets();
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
    
    duplicateDesign() {
      axios.post(`/api/designs/${this.design.id}/duplicate`)
        .then(response => {
          this.$inertia.visit(`/designer/edit/${response.data.data.id}`);
        })
        .catch(error => {
          console.error('Failed to duplicate design:', error);
        });
    },
    
    saveAsTemplate() {
      axios.post(`/api/designs/${this.design.id}/save-as-template`, {
        name: `${this.design.name} Template`,
        is_premium: false,
      })
      .then(response => {
        alert('Design saved as template successfully!');
      })
      .catch(error => {
        console.error('Failed to save as template:', error);
      });
    },
    
    deleteDesign() {
      if (confirm('Are you sure you want to delete this design?')) {
        axios.delete(`/api/designs/${this.design.id}`)
          .then(response => {
            this.$inertia.visit('/designer/my-designs');
          })
          .catch(error => {
            console.error('Failed to delete design:', error);
          });
      }
    },
    
    exportDesign() {
      this.$refs.designer.exportDesign();
    },
    
    addToCart() {
      // Add design to cart
      axios.post('/cart', {
        product_id: this.design.product_type_id,
        design_id: this.design.id,
        quantity: 1,
      })
      .then(response => {
        alert('Design added to cart!');
        this.$inertia.visit('/cart');
      })
      .catch(error => {
        console.error('Failed to add to cart:', error);
      });
    },
    
    onDesignSaved(design) {
      this.hasChanges = false;
      this.design = design;
    },
    
    onDesignChanged() {
      this.hasChanges = true;
    },
    
    updateDesignName() {
      axios.put(`/api/designs/${this.design.id}`, {
        name: this.design.name,
      })
      .catch(error => {
        console.error('Failed to update design name:', error);
      });
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