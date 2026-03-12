<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="font-brand-elegant text-3xl text-brand-primary font-bold">Design Management</h1>
        <p class="text-brand-secondary">Manage all user designs and templates</p>
      </div>

      <!-- Advanced Design Tools -->
      <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20 mb-6">
        <h3 class="font-brand-elegant text-lg text-brand-primary font-semibold mb-4">Advanced Design Tools</h3>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
          <button 
            @click="openDesigner()" 
            class="px-4 py-3 bg-gradient-to-r from-brand-primary to-brand-secondary text-white rounded-xl flex flex-col items-center justify-center hover:opacity-90 transition-opacity"
          >
            <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
            <span>New Design</span>
          </button>
          <button 
            @click="batchProcess()" 
            class="px-4 py-3 bg-gradient-to-r from-brand-secondary to-brand-accent text-white rounded-xl flex flex-col items-center justify-center hover:opacity-90 transition-opacity"
          >
            <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
            <span>Batch Process</span>
          </button>
          <button 
            @click="convertToTemplate()" 
            class="px-4 py-3 bg-gradient-to-r from-brand-accent to-brand-rose text-white rounded-xl flex flex-col items-center justify-center hover:opacity-90 transition-opacity"
          >
            <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v2a2 2 0 002 2z" /></svg>
            <span>To Template</span>
          </button>
          <button 
            @click="exportSelected()" 
            class="px-4 py-3 bg-gradient-to-r from-brand-gold to-brand-accent text-brand-primary rounded-xl flex flex-col items-center justify-center hover:opacity-90 transition-opacity"
          >
            <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
            <span>Export</span>
          </button>
          <button 
            @click="importDesign()" 
            class="px-4 py-3 bg-gradient-to-r from-brand-rose to-brand-lavender text-white rounded-xl flex flex-col items-center justify-center hover:opacity-90 transition-opacity"
          >
            <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
            <span>Import</span>
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-brand-primary mb-1">Search</label>
            <input
              v-model="localFilters.search"
              type="text"
              placeholder="Search designs..."
              class="w-full p-2 border border-brand-gold border-opacity-30 rounded-lg bg-white bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-brand-primary mb-1">Product Type</label>
            <select
              v-model="localFilters.product_type"
              class="w-full p-2 border border-brand-gold border-opacity-30 rounded-lg bg-white bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
            >
              <option value="">All Types</option>
              <option v-for="type in productTypes" :key="type.id" :value="type.id">
                {{ type.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-brand-primary mb-1">User</label>
            <select
              v-model="localFilters.user_id"
              class="w-full p-2 border border-brand-gold border-opacity-30 rounded-lg bg-white bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
            >
              <option value="">All Users</option>
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-brand-primary mb-1">Visibility</label>
            <select
              v-model="localFilters.is_public"
              class="w-full p-2 border border-brand-gold border-opacity-30 rounded-lg bg-white bg-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent"
            >
              <option value="">All</option>
              <option value="1">Public</option>
              <option value="0">Private</option>
            </select>
          </div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button
            @click="applyFilters"
            class="px-4 py-2 bg-gradient-to-r from-brand-primary to-brand-secondary text-white rounded-lg hover:opacity-90 transition-opacity"
          >
            Apply Filters
          </button>
          <button
            @click="clearFilters"
            class="px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-lg hover:opacity-90 transition-opacity"
          >
            Clear Filters
          </button>
        </div>
      </div>

      <!-- Designs Grid -->
      <div v-if="designs.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div
          v-for="design in designs.data"
          :key="design.id"
          :class="{ 'ring-2 ring-brand-gold': selectedDesigns.includes(design.id) }"
          class="bg-white rounded-2xl overflow-hidden border border-brand-gold border-opacity-20 relative group"
        >
          <div class="aspect-w-3 aspect-h-4 bg-gradient-to-br from-brand-rose to-brand-lavender relative">
            <img
              :src="design.preview_url || design.thumbnail_url || '/images/logo.jpeg'"
              :alt="design.name"
              class="w-full h-48 object-cover"
              onerror="this.src='/images/logo.jpeg'"
            />
            <input
              type="checkbox"
              :checked="selectedDesigns.includes(design.id)"
              @change="toggleDesignSelection(design.id)"
              class="absolute top-2 left-2 rounded text-brand-primary"
            />
            <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
              <button
                @click="viewDesign(design)"
                class="p-2 bg-white bg-opacity-90 rounded-full shadow-lg hover:bg-brand-gold hover:text-white transition-colors"
                title="View Design"
              >
                <svg class="h-4 w-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
              </button>
              <button
                @click="editDesign(design)"
                class="p-2 bg-white bg-opacity-90 rounded-full shadow-lg hover:bg-brand-gold hover:text-white transition-colors"
                title="Edit Design"
              >
                <svg class="h-4 w-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.414 15H9v-2.586l.586-.586z"></path>
                </svg>
              </button>
            </div>
            <div class="absolute bottom-2 left-2 bg-gradient-to-r from-brand-gold to-brand-accent text-brand-primary text-xs px-2 py-1 rounded-full">
              {{ design.is_public ? 'Public' : 'Private' }}
            </div>
          </div>
          
          <div class="p-4">
            <h3 class="font-brand-elegant font-semibold text-brand-primary mb-1 truncate">{{ design.name }}</h3>
            <p class="text-sm text-brand-secondary mt-1 truncate">{{ design.user?.name || 'Unknown User' }}</p>
            <p class="text-xs text-brand-secondary mt-1">{{ design.productType?.name || 'Unknown Type' }}</p>
            
            <div class="mt-4 flex justify-between items-center">
              <span class="text-xs text-brand-secondary">{{ formatDate(design.created_at) }}</span>
              <div class="flex gap-2">
                <button
                  @click="toggleVisibility(design)"
                  class="px-2 py-1 bg-brand-gold bg-opacity-20 text-brand-gold text-xs rounded-lg hover:bg-opacity-30 transition-colors"
                  title="Toggle Visibility"
                >
                  {{ design.is_public ? '🔒' : '🔓' }}
                </button>
                <button
                  @click="makeFeatured(design)"
                  class="px-2 py-1 bg-brand-rose bg-opacity-20 text-brand-rose text-xs rounded-lg hover:bg-opacity-30 transition-colors"
                  title="Make Template"
                >
                  🏷️
                </button>
                <button
                  @click="duplicateDesign(design)"
                  class="px-2 py-1 bg-brand-accent bg-opacity-20 text-brand-accent text-xs rounded-lg hover:bg-opacity-30 transition-colors"
                  title="Duplicate"
                >
                  📋
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-else class="text-center py-12 bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl border border-brand-gold border-opacity-20">
        <div class="text-5xl mb-4">🎨</div>
        <h3 class="font-brand-elegant text-xl text-brand-primary font-semibold mb-2">No Designs Found</h3>
        <p class="text-brand-secondary mb-4">Get started by creating your first design</p>
        <button
          @click="openDesigner()"
          class="px-6 py-3 bg-gradient-to-r from-brand-primary to-brand-secondary text-white rounded-xl hover:opacity-90 transition-opacity"
        >
          Create Your First Design
        </button>
      </div>
      
      <!-- Pagination -->
      <div v-if="designs.data.length > 0" class="mt-8 flex justify-center">
        <nav class="flex items-center space-x-2">
          <button
            @click="changePage(designs.prev_page_url)"
            :disabled="!designs.prev_page_url"
            class="px-3 py-1 rounded-lg border border-brand-gold border-opacity-30 text-brand-primary disabled:opacity-50 disabled:cursor-not-allowed hover:bg-brand-gold hover:text-white transition-colors"
          >
            Previous
          </button>
          
          <span class="mx-2 text-brand-primary">
            Page {{ designs.current_page }} of {{ designs.last_page }} ({{ designs.total }} total)
          </span>
          
          <button
            @click="changePage(designs.next_page_url)"
            :disabled="!designs.next_page_url"
            class="px-3 py-1 rounded-lg border border-brand-gold border-opacity-30 text-brand-primary disabled:opacity-50 disabled:cursor-not-allowed hover:bg-brand-gold hover:text-white transition-colors"
          >
            Next
          </button>
        </nav>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/Admin.vue';
import { router } from '@inertiajs/vue3';

export default {
  name: 'AdminDesignsIndex',
  
  components: {
    AdminLayout,
  },
  
  props: {
    designs: Object,
    filters: Object,
    productTypes: Array,
    users: Array,
  },
  
  data() {
    return {
      // Local filters to allow live updates before submission
      localFilters: { ...this.filters },
      selectedDesigns: [],
      selectAllChecked: false,
    };
  },
  
  watch: {
    selectedDesigns() {
      this.selectAllChecked = this.selectedDesigns.length === this.designs.data.length;
    }
  },
  
  methods: {
    applyFilters() {
      router.get('/admin/designs', this.localFilters, {
        preserveScroll: true,
        replace: true,
      });
    },
    
    clearFilters() {
      router.get('/admin/designs', {}, {
        preserveScroll: true,
        replace: true,
      });
    },
    
    changePage(url) {
      if (url) {
        router.get(url, {}, { preserveScroll: true });
      }
    },
    
    toggleVisibility(design) {
      router.patch(`/admin/designs/${design.id}/toggle-visibility`, {}, {
        onSuccess: () => {
          // Refresh the page to update the design list
          router.reload();
        }
      });
    },
    
    makeFeatured(design) {
      router.patch(`/admin/designs/${design.id}/make-featured`, {}, {
        onSuccess: () => {
          // Refresh the page to update the design list
          router.reload();
        }
      });
    },
    
    deleteDesign(design) {
      if (confirm(`Are you sure you want to delete the design "${design.name}"?`)) {
        router.delete(`/admin/designs/${design.id}`, {
          onSuccess: () => {
            // Refresh the page to update the design list
            router.reload();
          }
        });
      }
    },
    
    viewDesign(design) {
      router.visit(`/admin/designs/${design.id}`);
    },
    
    editDesign(design) {
      router.visit(`/designer/edit/${design.id}`);
    },
    
    duplicateDesign(design) {
      if (confirm(`Are you sure you want to duplicate the design "${design.name}"?`)) {
        router.post(`/admin/designs/${design.id}/duplicate`, {}, {
          onSuccess: () => {
            router.reload();
          }
        });
      }
    },
    
    selectAll() {
      if (this.selectAllChecked) {
        this.selectedDesigns = this.designs.data.map(d => d.id);
      } else {
        this.selectedDesigns = [];
      }
    },
    
    toggleDesignSelection(designId) {
      const index = this.selectedDesigns.indexOf(designId);
      if (index > -1) {
        this.selectedDesigns.splice(index, 1);
      } else {
        this.selectedDesigns.push(designId);
      }
    },
    
    openDesigner() {
      // Redirect to designer with t-shirt as default product type
      router.visit('/designer/t-shirt');
    },
    
    batchProcess() {
      if (this.selectedDesigns.length === 0) {
        alert('Please select at least one design to batch process.');
        return;
      }
      
      if (confirm(`Are you sure you want to batch process ${this.selectedDesigns.length} designs?`)) {
        // Batch process selected designs
        router.post('/admin/designs/batch-process', {
          design_ids: this.selectedDesigns
        }, {
          onSuccess: () => {
            router.reload();
          }
        });
      }
    },
    
    convertToTemplate() {
      if (this.selectedDesigns.length === 0) {
        alert('Please select at least one design to convert to template.');
        return;
      }
      
      if (confirm(`Are you sure you want to convert ${this.selectedDesigns.length} designs to templates?`)) {
        router.post('/admin/designs/convert-to-templates', {
          design_ids: this.selectedDesigns
        }, {
          onSuccess: () => {
            router.reload();
          }
        });
      }
    },
    
    exportSelected() {
      if (this.selectedDesigns.length === 0) {
        alert('Please select at least one design to export.');
        return;
      }
      
      // Create a form to submit the export request
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '/admin/designs/export-selected';
      form.style.display = 'none';
      
      const csrfToken = document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content');
      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_token';
      csrfInput.value = csrfToken;
      form.appendChild(csrfInput);
      
      this.selectedDesigns.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'design_ids[]';
        input.value = id;
        form.appendChild(input);
      });
      
      document.body.appendChild(form);
      form.submit();
      document.body.removeChild(form);
    },
    
    importDesign() {
      // Redirect to import page
      router.visit('/admin/designs/import');
    },
    
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString();
    }
  },
};
</script>