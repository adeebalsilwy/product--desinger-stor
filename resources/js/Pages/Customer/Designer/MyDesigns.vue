<template>
  <CustomerLayout>
    <div class="designs-page">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">
            <span class="title-icon">🎨</span>
            My Creations
          </h1>
          <p class="page-subtitle">Discover your beautifully crafted designs</p>
        </div>
        <div class="header-actions">
          <Link 
            :href="route('designer.create', { productType: 'dress' })"
            class="btn-create-new"
          >
            <span class="btn-icon">✨</span>
            Create New Design
          </Link>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="designs.data.length === 0" class="empty-state">
        <div class="empty-state-content">
          <div class="empty-state-icon">🧵</div>
          <h3 class="empty-state-title">No designs yet</h3>
          <p class="empty-state-description">Begin your creative journey with elegant designs</p>
          <Link 
            :href="route('designer.create', { productType: 'dress' })"
            class="btn-get-started"
          >
            Start Creating
          </Link>
        </div>
      </div>

      <!-- Designs Grid -->
      <div v-else class="designs-grid">
        <div 
          v-for="design in designs.data" 
          :key="design.id"
          class="design-card"
        >
          <!-- Design Preview -->
          <div class="card-preview">
            <img 
              v-if="design.preview_url" 
              :src="design.preview_url" 
              :alt="design.name" 
              class="preview-image"
            />
            <div v-else class="preview-placeholder">
              <span class="placeholder-icon">✂️</span>
            </div>
            
            <!-- Quick Actions Overlay -->
            <div class="overlay-actions">
              <button 
                @click="editDesign(design.id)"
                class="action-btn edit"
                title="Edit Design"
              >
                ✏️
              </button>
              <button 
                @click="duplicateDesign(design.id)"
                class="action-btn duplicate"
                title="Duplicate Design"
              >
                📋
              </button>
              <button 
                @click="deleteDesign(design.id)"
                class="action-btn delete"
                title="Delete Design"
              >
                🗑️
              </button>
            </div>
          </div>
          
          <!-- Card Content -->
          <div class="card-content">
            <h3 class="design-name">{{ design.name }}</h3>
            <p class="design-type">{{ design.productType?.name || 'Custom Creation' }}</p>
            
            <div class="card-divider"></div>
            
            <div class="card-footer">
              <span class="design-date">{{ formatDate(design.created_at) }}</span>
              <div class="card-actions">
                <button 
                  @click="editDesign(design.id)"
                  class="btn-edit"
                >
                  Refine
                </button>
                <button 
                  @click="exportDesign(design.id)"
                  class="btn-export"
                >
                  Download
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="designs.last_page > 1" class="pagination-container">
        <nav class="pagination">
          <Link
            :href="designs.prev_page_url"
            :class="{'disabled': !designs.prev_page_url}"
            class="pagination-btn"
          >
            <span>←</span> Previous
          </Link>
          
          <span class="pagination-info">
            Page {{ designs.current_page }} of {{ designs.last_page }}
          </span>
          
          <Link
            :href="designs.next_page_url"
            :class="{'disabled': !designs.next_page_url}"
            class="pagination-btn"
          >
            Next <span>→</span>
          </Link>
        </nav>
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
    
    editDesign(designId) {
      window.location.href = route('designer.edit', { design: designId });
    },
    
    duplicateDesign(designId) {
      if (!confirm('Are you sure you want to duplicate this design?')) return;
      
      axios.post(`/api/designs/${designId}/duplicate`)
        .then(response => {
          // Show success message
          alert('Design duplicated successfully!');
          // Refresh the page to show the new design
          this.$inertia.reload();
        })
        .catch(error => {
          console.error('Failed to duplicate design:', error);
          alert('Failed to duplicate design');
        });
    },
    
    deleteDesign(designId) {
      if (!confirm('Are you sure you want to delete this design? This action cannot be undone.')) return;
      
      axios.delete(`/api/designs/${designId}`)
        .then(response => {
          alert('Design deleted successfully!');
          this.$inertia.reload();
        })
        .catch(error => {
          console.error('Failed to delete design:', error);
          alert('Failed to delete design');
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

<style scoped>
.designs-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 3rem;
  background: rgba(255, 255, 255, 0.95);
  padding: 2rem;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.header-content {
  flex: 1;
}

.page-title {
  font-size: 2.5rem;
  font-weight: bold;
  color: #1a202c;
  margin: 0 0 0.5rem 0;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.title-icon {
  font-size: 3rem;
}

.page-subtitle {
  font-size: 1.125rem;
  color: #718096;
  margin: 0;
}

.header-actions {
  margin-left: 2rem;
}

.btn-create-new {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-decoration: none;
  border-radius: 15px;
  font-weight: 600;
  font-size: 1rem;
  transition: all 0.3s;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-create-new:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.btn-icon {
  font-size: 1.5rem;
}

.empty-state {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 20px;
  padding: 4rem 2rem;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.empty-state-content {
  max-width: 500px;
  margin: 0 auto;
}

.empty-state-icon {
  font-size: 5rem;
  margin-bottom: 1.5rem;
}

.empty-state-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1a202c;
  margin: 0 0 1rem 0;
}

.empty-state-description {
  font-size: 1.125rem;
  color: #718096;
  margin: 0 0 2rem 0;
}

.btn-get-started {
  display: inline-block;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-decoration: none;
  border-radius: 15px;
  font-weight: 600;
  font-size: 1.125rem;
  transition: all 0.3s;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-get-started:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.designs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.design-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.3s;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.design-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.2);
}

.card-preview {
  position: relative;
  aspect-ratio: 1;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  overflow: hidden;
}

.preview-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: all 0.3s;
}

.design-card:hover .preview-image {
  transform: scale(1.05);
}

.preview-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 4rem;
}

.overlay-actions {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  opacity: 0;
  transition: opacity 0.3s;
}

.design-card:hover .overlay-actions {
  opacity: 1;
}

.action-btn {
  width: 50px;
  height: 50px;
  border: none;
  border-radius: 50%;
  background: white;
  font-size: 1.5rem;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-btn:hover {
  transform: scale(1.2);
}

.action-btn.edit:hover {
  background: #667eea;
}

.action-btn.duplicate:hover {
  background: #48bb78;
}

.action-btn.delete:hover {
  background: #f56565;
}

.card-content {
  padding: 1.5rem;
}

.design-name {
  font-size: 1.25rem;
  font-weight: bold;
  color: #1a202c;
  margin: 0 0 0.5rem 0;
}

.design-type {
  font-size: 0.875rem;
  color: #718096;
  margin: 0 0 1rem 0;
}

.card-divider {
  height: 1px;
  background: #e2e8f0;
  margin: 1rem 0;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.design-date {
  font-size: 0.875rem;
  color: #a0aec0;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-edit {
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-edit:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-export {
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-export:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);
}

.pagination-container {
  background: rgba(255, 255, 255, 0.95);
  padding: 1.5rem;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1.5rem;
}

.pagination-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-decoration: none;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.2s;
}

.pagination-btn:hover:not(.disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(102, 126, 234, 0.4);
}

.pagination-btn.disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.pagination-info {
  font-size: 0.9375rem;
  color: #4a5568;
  font-weight: 600;
}

@media (max-width: 768px) {
  .designs-page {
    padding: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .header-actions {
    margin-left: 0;
    width: 100%;
  }
  
  .btn-create-new {
    width: 100%;
    justify-content: center;
  }
  
  .designs-grid {
    grid-template-columns: 1fr;
  }
  
  .pagination {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>
