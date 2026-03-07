<template>
  <CustomerLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Designs</h1>
        <p class="text-gray-600">View and manage your saved designs</p>
      </div>

      <div v-if="designs.data.length === 0" class="text-center py-12">
        <div class="text-gray-400 text-6xl mb-4">🎨</div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No designs yet</h3>
        <p class="text-gray-600 mb-6">Start creating custom designs for your products</p>
        <Link 
          :href="route('designer.create', { productType: 't-shirt' })"
          class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
          Create Your First Design
        </Link>
      </div>

      <div v-else>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div 
            v-for="design in designs.data" 
            :key="design.id"
            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
          >
            <div class="aspect-square bg-gray-100 relative">
              <img 
                v-if="design.preview_url" 
                :src="design.preview_url" 
                :alt="design.name" 
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                No Preview
              </div>
              
              <div class="absolute top-2 right-2 flex gap-1">
                <Link 
                  :href="route('designer.edit', { design: design.id })"
                  class="p-2 bg-white rounded-full shadow-md hover:bg-gray-50"
                >
                  <span class="text-gray-600">✏️</span>
                </Link>
                <button 
                  @click="duplicateDesign(design.id)"
                  class="p-2 bg-white rounded-full shadow-md hover:bg-gray-50"
                >
                  <span class="text-gray-600">📋</span>
                </button>
              </div>
            </div>
            
            <div class="p-4">
              <h3 class="font-semibold text-lg mb-1 truncate">{{ design.name }}</h3>
              <p class="text-sm text-gray-600 mb-3">
                {{ design.productType?.name || 'Unknown Product' }}
              </p>
              
              <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">
                  {{ formatDate(design.created_at) }}
                </span>
                <div class="flex gap-2">
                  <Link 
                    :href="route('designer.edit', { design: design.id })"
                    class="px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded hover:bg-blue-200"
                  >
                    Edit
                  </Link>
                  <button 
                    @click="exportDesign(design.id)"
                    class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded hover:bg-green-200"
                  >
                    Export
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="designs.last_page > 1" class="mt-8 flex justify-center">
          <nav class="flex items-center gap-2">
            <Link
              :href="designs.prev_page_url"
              :class="{'opacity-50 pointer-events-none': !designs.prev_page_url}"
              class="px-3 py-1 border rounded hover:bg-gray-50"
            >
              Previous
            </Link>
            
            <span class="px-2">Page {{ designs.current_page }} of {{ designs.last_page }}</span>
            
            <Link
              :href="designs.next_page_url"
              :class="{'opacity-50 pointer-events-none': !designs.next_page_url}"
              class="px-3 py-1 border rounded hover:bg-gray-50"
            >
              Next
            </Link>
          </nav>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script>
import CustomerLayout from '@/Layouts/Customer.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

export default {
  name: 'MyDesigns',
  
  components: {
    CustomerLayout,
    Link,
  },
  
  props: {
    designs: Object,
  },
  
  methods: {
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      });
    },
    
    duplicateDesign(designId) {
      axios.post(`/api/designs/${designId}/duplicate`)
        .then(response => {
          // Refresh the page to show the new design
          this.$inertia.reload();
        })
        .catch(error => {
          console.error('Failed to duplicate design:', error);
          alert('Failed to duplicate design');
        });
    },
    
    exportDesign(designId) {
      axios.post(`/api/designs/${designId}/export`, { format: 'high_res' })
        .then(response => {
          window.open(response.data.data.url, '_blank');
        })
        .catch(error => {
          console.error('Failed to export design:', error);
          alert('Failed to export design');
        });
    },
  },
};
</script>