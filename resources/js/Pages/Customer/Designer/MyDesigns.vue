<template>
  <CustomerLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8 text-center">
        <h1 class="font-brand-elegant text-4xl text-brand-primary mb-2">My Creations</h1>
        <p class="text-brand-secondary text-lg">Discover your beautifully crafted designs</p>
      </div>

      <div v-if="designs.data.length === 0" class="text-center py-12 bg-gradient-elegant rounded-2xl">
        <div class="text-brand-accent text-6xl mb-4">🧵</div>
        <h3 class="font-brand-elegant text-2xl text-brand-primary mb-2">No designs yet</h3>
        <p class="text-brand-secondary mb-6">Begin your creative journey with elegant designs</p>
        <Link 
          :href="route('designer.create', { productType: 'dress' })"
          class="inline-block px-8 py-4 btn-luxury rounded-xl text-lg font-semibold"
        >
          Start Creating
        </Link>
      </div>

      <div v-else>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
          <div 
            v-for="design in designs.data" 
            :key="design.id"
            class="bg-white rounded-2xl overflow-hidden transition-all duration-300 hover:scale-105 border border-brand-gold border-opacity-20"
          >
            <div class="aspect-square bg-gradient-to-br from-brand-rose to-brand-lavender relative">
              <img 
                v-if="design.preview_url" 
                :src="design.preview_url" 
                :alt="design.name" 
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-brand-secondary font-brand-elegant">
                <span class="text-4xl">✂️</span>
              </div>
              
              <div class="absolute top-3 right-3 flex gap-2">
                <Link 
                  :href="route('designer.edit', { design: design.id })"
                  class="p-3 bg-brand-primary bg-opacity-90 rounded-full backdrop-blur-sm hover:bg-opacity-100 transition-all"
                >
                  <span class="text-brand-gold text-lg">✏️</span>
                </Link>
                <button 
                  @click="duplicateDesign(design.id)"
                  class="p-3 bg-brand-primary bg-opacity-90 rounded-full backdrop-blur-sm hover:bg-opacity-100 transition-all"
                >
                  <span class="text-brand-gold text-lg">📋</span>
                </button>
              </div>
            </div>
            
            <div class="p-6">
              <h3 class="font-brand-elegant font-semibold text-xl mb-2 text-brand-primary">{{ design.name }}</h3>
              <p class="text-brand-secondary mb-4 text-sm font-medium">
                {{ design.productType?.name || 'Custom Creation' }}
              </p>
              
              <div class="flex justify-between items-center pt-4 border-t border-brand-gold border-opacity-20">
                <span class="text-sm text-brand-rose font-medium">
                  {{ formatDate(design.created_at) }}
                </span>
                <div class="flex gap-3">
                  <Link 
                    :href="route('designer.edit', { design: design.id })"
                    class="px-4 py-2 bg-brand-accent text-white text-sm rounded-lg hover:bg-opacity-90 transition-all font-medium"
                  >
                    Refine
                  </Link>
                  <button 
                    @click="exportDesign(design.id)"
                    class="px-4 py-2 bg-brand-gold text-brand-primary text-sm rounded-lg hover:bg-opacity-90 transition-all font-medium"
                  >
                    Download
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="designs.last_page > 1" class="mt-12 flex justify-center">
          <nav class="flex items-center gap-4">
            <Link
              :href="designs.prev_page_url"
              :class="{'opacity-50 pointer-events-none': !designs.prev_page_url}"
              class="px-6 py-3 border-2 border-brand-primary text-brand-primary rounded-xl hover:bg-brand-primary hover:text-white transition-all font-medium"
            >
              ← Previous
            </Link>
            
            <span class="px-4 text-brand-secondary font-medium">
              Page {{ designs.current_page }} of {{ designs.last_page }}
            </span>
            
            <Link
              :href="designs.next_page_url"
              :class="{'opacity-50 pointer-events-none': !designs.next_page_url}"
              class="px-6 py-3 border-2 border-brand-primary text-brand-primary rounded-xl hover:bg-brand-primary hover:text-white transition-all font-medium"
            >
              Next →
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
