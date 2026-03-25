<template>
  <CustomerLayout>
    <div class="designs-page">
      <!-- Header Section -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">
            <span class="title-icon">🎨</span>
            {{ $t('designer.my_designs_title') }}
          </h1>
          <p class="page-subtitle">{{ $t('designer.my_designs_subtitle') }}</p>
        </div>
        <div class="header-actions">
          <Link 
            :href="route('designer.select-product-type')"
            class="btn-create-new"
          >
            <span class="btn-icon">✨</span>
            {{ $t('designer.create_new') }}
          </Link>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="designs.data.length === 0" class="empty-state">
        <div class="empty-state-content">
          <div class="empty-state-icon">🧵</div>
          <h3 class="empty-state-title">{{ $t('designer.empty_title') }}</h3>
          <p class="empty-state-description">{{ $t('designer.empty_subtitle') }}</p>
          <Link 
            :href="route('designer.select-product-type')"
            class="btn-get-started"
          >
            {{ $t('designer.start_creating') }}
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
              v-if="design.preview_url || design.thumbnail_url" 
              :src="getImageUrl(design.preview_url || design.thumbnail_url)" 
              :alt="design.name" 
              class="preview-image"
              @error="handleImageError"
            />
            <div v-else class="preview-placeholder">
              <span class="placeholder-icon">✂️</span>
            </div>
            
            <!-- Quick Actions Overlay -->
            <div class="overlay-actions">
              <button 
                @click="previewDesign(design.id)"
                class="action-btn preview"
                :title="$t('designer.preview_title')"
              >
                👁️
              </button>
              <button 
                @click="viewOnModel(design)"
                class="action-btn model"
                :title="$t('designer.view_on_model')"
              >
                3D
              </button>
              <button 
                @click="editDesign(design.id)"
                class="action-btn edit"
                :title="$t('designer.edit_title')"
              >
                ✏️
              </button>
              <button 
                @click="duplicateDesign(design.id)"
                class="action-btn duplicate"
                :title="$t('designer.duplicate_title')"
              >
                📋
              </button>
              <button 
                @click="deleteDesign(design.id)"
                class="action-btn delete"
                :title="$t('designer.delete_title')"
              >
                🗑️
              </button>
              <button 
                @click="saveDesign(design.id)"
                class="action-btn save"
                :title="$t('designer.save_design')"
              >
                💾
              </button>
            </div>
          </div>
          
          <!-- Card Content -->
          <div class="card-content">
            <h3 class="design-name">{{ design.name }}</h3>
            <p class="design-type">{{ design.productType?.name || design.product_type?.name || $t('designer.custom_creation') }}</p>
            
            <div class="card-divider"></div>
            
            <div class="card-footer">
              <span class="design-date">{{ formatDate(design.created_at) }}</span>
              <div class="card-actions">
                <button 
                  @click="editDesign(design.id)"
                  class="btn-edit"
                >
                  {{ $t('designer.refine') }}
                </button>
                <button 
                  @click="exportDesign(design.id)"
                  class="btn-export"
                >
                  {{ $t('designer.download') }}
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
            v-if="designs.prev_page_url"
            :href="designs.prev_page_url"
            class="pagination-btn"
          >
            <span>←</span> {{ $t('designer.previous') }}
          </Link>
          <span v-else class="pagination-btn disabled">
            <span>←</span> {{ $t('designer.previous') }}
          </span>
          
          <span class="pagination-info">
            {{ $t('designer.page') }} {{ designs.current_page }} {{ $t('designer.of') }} {{ designs.last_page }}
          </span>
          
          <Link
            v-if="designs.next_page_url"
            :href="designs.next_page_url"
            class="pagination-btn"
          >
            {{ $t('designer.next') }} <span>→</span>
          </Link>
          <span v-else class="pagination-btn disabled">
            {{ $t('designer.next') }} <span>→</span>
          </span>
        </nav>
      </div>
    </div>
    
    <!-- Preview Modal -->
    <div v-if="previewModalOpen" class="modal-overlay" @click="closePreviewModal">
      <div class="modal-content" @click.stop>
        <button class="modal-close" @click="closePreviewModal">×</button>
        <div class="preview-modal-body">
          <h3>{{ previewDesignData?.name || 'Design Preview' }}</h3>
          <div class="preview-image-container" v-if="previewDesignData?.preview_url || previewDesignData?.thumbnail_url">
            <img :src="getImageUrl(previewDesignData.preview_url || previewDesignData.thumbnail_url)" :alt="previewDesignData?.name" class="large-preview-image" />
          </div>
          <div v-else class="preview-placeholder-large">No Preview Available</div>
        </div>
      </div>
    </div>
    
    <!-- 3D Model Viewer Modal -->
    <div v-if="modelViewerOpen" class="modal-overlay" @click="closeModelViewer">
      <div class="model-viewer-modal" @click.stop>
        <button class="modal-close" @click="closeModelViewer">×</button>
        <div class="model-viewer-header">
          <h3>{{ selectedDesign?.name || '3D Model View' }}</h3>
          <div class="model-selector">
            <label for="model-select">Select Model:</label>
            <select id="model-select" v-model="selectedModelId" @change="changeModel">
              <option value="">Choose a model...</option>
              <option value="eve">Eve Character Model</option>
              <option value="fmel_fbx">Female Model 4.0</option>
              <option value="kangana">Kangana Ranaout</option>
              <option value="rihanna_3ds">Rihanna 3DS</option>
              <option value="xander">Xander Avaturn Model</option>
            </select>
          </div>
        </div>
        <div class="model-viewer-container">
          <Product3DViewer
            ref="product3DViewer"
            :product-name="selectedDesign?.name || 'Custom Design'"
            :product-image="designImageUrl"
            :height="500"
            :auto-rotate="true"
            :show-grid="false"
          />
        </div>
        <div class="model-controls">
          <button @click="resetCameraView" class="control-btn">Reset Camera</button>
          <button @click="toggleAutoRotate" class="control-btn">Toggle Auto-Rotate</button>
          <button @click="saveDesignFromViewer" class="control-btn-save">Save Design</button>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script>
import CustomerLayout from '@/Layouts/Customer.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import Product3DViewer from '@/Components/Product3DViewer.vue';

export default {
  name: 'MyDesigns',
  
  components: {
    CustomerLayout,
    Link,
    Product3DViewer,
  },
  
  props: {
    designs: Object,
    defaultProductType: {
      type: String,
      default: 't-shirt',
    },
  },
  
  data() {
    return {
      previewModalOpen: false,
      previewDesignData: null,
      modelViewerOpen: false,
      selectedDesign: null,
      selectedModelId: 'kangana',
      designImageUrl: '',
      viewerInstance: null,
    };
  },

  methods: {
    getImageUrl(url) {
      if (!url) return '';
      // If the URL starts with /storage or http, return as is
      if (url.startsWith('/storage') || url.startsWith('http')) {
        return url;
      }
      // Otherwise, prepend /storage
      return `/storage/${url}`;
    },

    handleImageError(event) {
      // Set a default image if the preview image fails to load
      event.target.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 24 24"><rect width="24" height="24" fill="%23e0e5ec"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="12" fill="%23666">No Preview</text></svg>';
    },

    formatDate(dateString) {
      const locale = this.$page?.props?.locale === 'ar' ? 'ar-EG' : 'en-US';
      return new Date(dateString).toLocaleDateString(locale, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      });
    },
    
    editDesign(designId) {
      window.location.href = route('designer.edit', { design: designId });
    },

    async previewDesign(designId) {
      try {
        // Show loading state or spinner
        this.previewDesignData = null;
        this.previewModalOpen = true;
        
        // Fetch full design data for preview
        const response = await this.apiCall('get', `/api/designs/${designId}`);
        this.previewDesignData = response.data.data;
      } catch (error) {
        console.error('Failed to load design for preview:', error);
        // Still show modal with error info
        this.previewDesignData = { id: designId, name: 'Preview Error', preview_url: null };
      }
    },

    // View design on 3D model
    async viewOnModel(design) {
      this.selectedDesign = design;
      this.designImageUrl = this.getImageUrl(design.preview_url || design.thumbnail_url);
      this.modelViewerOpen = true;
      this.selectedModelId = 'kangana'; // Reset to default model
      
      // Wait for the modal to render and the Product3DViewer component to mount
      await this.$nextTick();
      setTimeout(() => {
        if (this.$refs.product3DViewer) {
          this.viewerInstance = this.$refs.product3DViewer;
        }
      }, 100);
    },

    closePreviewModal() {
      this.previewModalOpen = false;
      this.previewDesignData = null;
    },

    closeModelViewer() {
      this.modelViewerOpen = false;
      this.selectedDesign = null;
      this.selectedModelId = 'kangana';
      this.designImageUrl = '';
      this.viewerInstance = null;
    },

    changeModel() {
      // The Product3DViewer component handles model changing internally via watch
      // This method is called when the select changes, but the actual model change
      // is handled by the component's watch on selectedModelId
    },

    resetCameraView() {
      if (this.viewerInstance && typeof this.viewerInstance.resetView === 'function') {
        this.viewerInstance.resetView();
      }
    },

    toggleAutoRotate() {
      if (this.viewerInstance && typeof this.viewerInstance.toggleAutoRotate === 'function') {
        this.viewerInstance.toggleAutoRotate();
      }
    },

    saveDesignFromViewer() {
      if (this.selectedDesign) {
        this.saveDesign(this.selectedDesign.id);
      }
    },
    
    async duplicateDesign(designId) {
      if (!confirm(this.$t('designer.confirm_duplicate'))) return;
      
      try {
        const response = await this.apiCall('post', `/api/designs/${designId}/duplicate`);
        alert(this.$t('designer.duplicate_success'));
        this.$inertia.reload();
      } catch (error) {
        console.error('Failed to duplicate design:', error);
        const errorMessage = error.response?.data?.message || error.response?.data?.error || 'Failed to duplicate design';
        alert(`${this.$t('designer.duplicate_failed')}: ${errorMessage}`);
      }
    },
    
    async deleteDesign(designId) {
      if (!confirm(this.$t('designer.confirm_delete'))) return;
      
      try {
        const response = await this.apiCall('delete', `/api/designs/${designId}`);
        alert(this.$t('designer.delete_success'));
        this.$inertia.reload();
      } catch (error) {
        console.error('Failed to delete design:', error);
        const errorMessage = error.response?.data?.message || error.response?.data?.error || 'Failed to delete design';
        alert(`${this.$t('designer.delete_failed')}: ${errorMessage}`);
      }
    },

    async saveDesign(designId) {
      try {
        // For now, just trigger a save action - the actual implementation would depend on your backend API
        const response = await this.apiCall('post', `/api/designs/${designId}/save`);
        alert(this.$t('designer.save_success'));
      } catch (error) {
        console.error('Failed to save design:', error);
        const errorMessage = error.response?.data?.message || error.response?.data?.error || 'Failed to save design';
        alert(`${this.$t('designer.save_failed')}: ${errorMessage}`);
      }
    },
    
    async exportDesign(designId) {
      try {
        const response = await this.apiCall('post', `/api/designs/${designId}/export`, { format: 'high_res' });

        if (response.data.data?.url) {
          window.open(response.data.data.url, '_blank');
        } else {
          alert(this.$t('designer.export_failed'));
        }
      } catch (error) {
        console.error('Failed to export design:', error);
        const errorMessage = error.response?.data?.message || error.response?.data?.error || 'Failed to export design';
        alert(`${this.$t('designer.export_failed')}: ${errorMessage}`);
      }
    },
    
    // Centralized API call method to handle CSRF tokens properly
    async apiCall(method, url, data = null) {
      // Get CSRF token from meta tag
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      if (!csrfToken) {
        throw new Error('CSRF token not found. Please refresh the page.');
      }
      
      const config = {
        method,
        url,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken,
          'Content-Type': 'application/json',
        },
        withCredentials: true, // Important for session-based authentication
      };
      
      // Add data for POST/PUT requests
      if (data && (method.toLowerCase() === 'post' || method.toLowerCase() === 'put' || method.toLowerCase() === 'patch')) {
        config.data = data;
      }

      return await axios(config);
    },
  },
};
</script>

<style scoped>
.designs-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #e0e5ec 0%, #c9d6ff 100%);
  padding: 2rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 3rem;
  background: #e0e5ec;
  padding: 2rem;
  border-radius: 20px;
  box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff;
}

.header-content {
  flex: 1;
}

.page-title {
  font-size: 2.5rem;
  font-weight: bold;
  color: #4a5568;
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
  color: #6d7587;
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
  background: #e0e5ec;
  color: #4a7eff;
  text-decoration: none;
  border-radius: 15px;
  font-weight: 600;
  font-size: 1rem;
  transition: all 0.3s;
  box-shadow: 5px 5px 10px #a3b1c6, -5px -5px 10px #ffffff;
}

.btn-create-new:hover {
  box-shadow: inset 5px 5px 10px #a3b1c6, inset -5px -5px 10px #ffffff;
}

.btn-icon {
  font-size: 1.5rem;
}

.empty-state {
  background: #e0e5ec;
  border-radius: 20px;
  padding: 4rem 2rem;
  text-align: center;
  box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff;
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
  color: #4a5568;
  margin: 0 0 1rem 0;
}

.empty-state-description {
  font-size: 1.125rem;
  color: #6d7587;
  margin: 0 0 2rem 0;
}

.btn-get-started {
  display: inline-block;
  padding: 1rem 2rem;
  background: #e0e5ec;
  color: #4a7eff;
  text-decoration: none;
  border-radius: 15px;
  font-weight: 600;
  font-size: 1.125rem;
  transition: all 0.3s;
  box-shadow: 5px 5px 10px #a3b1c6, -5px -5px 10px #ffffff;
}

.btn-get-started:hover {
  box-shadow: inset 5px 5px 10px #a3b1c6, inset -5px -5px 10px #ffffff;
}

.designs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.design-card {
  background: #e0e5ec;
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.3s;
  box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff;
}

.design-card:hover {
  box-shadow: inset 4px 4px 8px #a3b1c6, inset -4px -4px 8px #ffffff;
}

.card-preview {
  position: relative;
  aspect-ratio: 1;
  background: #e0e5ec;
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
  background: #f0f0f0;
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
  gap: 0.5rem;
  opacity: 0;
  transition: opacity 0.3s;
}

.design-card:hover .overlay-actions {
  opacity: 1;
}

.action-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 50%;
  background: #e0e5ec;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 5px 5px 10px rgba(0,0,0,0.2), -5px -5px 10px rgba(255,255,255,0.4);
}

.action-btn:hover {
  transform: scale(1.2);
}

.action-btn.edit:hover {
  background: #667eea;
  color: white;
}

.action-btn.duplicate:hover {
  background: #48bb78;
  color: white;
}

.action-btn.delete:hover {
  background: #f56565;
  color: white;
}

.action-btn.preview:hover {
  background: #ed8936;
  color: white;
}

.action-btn.model:hover {
  background: #805ad5;
  color: white;
}

.action-btn.save:hover {
  background: #4299e1;
  color: white;
}

.card-content {
  padding: 1.5rem;
}

.design-name {
  font-size: 1.25rem;
  font-weight: bold;
  color: #4a5568;
  margin: 0 0 0.5rem 0;
}

.design-type {
  font-size: 0.875rem;
  color: #6d7587;
  margin: 0 0 1rem 0;
}

.card-divider {
  height: 1px;
  background: #c9d6ff;
  margin: 1rem 0;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.design-date {
  font-size: 0.875rem;
  color: #6d7587;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-edit {
  padding: 0.5rem 1rem;
  background: #e0e5ec;
  color: #4a7eff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 4px 4px 8px #a3b1c6, -4px -4px 8px #ffffff;
}

.btn-edit:hover {
  box-shadow: inset 4px 4px 8px #a3b1c6, inset -4px -4px 8px #ffffff;
}

.btn-export {
  padding: 0.5rem 1rem;
  background: #e0e5ec;
  color: #6d7587;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 4px 4px 8px #a3b1c6, -4px -4px 8px #ffffff;
}

.btn-export:hover {
  box-shadow: inset 4px 4px 8px #a3b1c6, inset -4px -4px 8px #ffffff;
}

.pagination-container {
  background: #e0e5ec;
  padding: 1.5rem;
  border-radius: 15px;
  box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff;
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
  background: #e0e5ec;
  color: #4a7eff;
  text-decoration: none;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.2s;
  box-shadow: 4px 4px 8px #a3b1c6, -4px -4px 8px #ffffff;
}

.pagination-btn:hover:not(.disabled) {
  box-shadow: inset 4px 4px 8px #a3b1c6, inset -4px -4px 8px #ffffff;
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

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 15px;
  max-width: 80vw;
  max-height: 80vh;
  overflow: auto;
  position: relative;
  padding: 2rem;
}

.model-viewer-modal {
  background: white;
  border-radius: 15px;
  max-width: 90vw;
  max-height: 90vh;
  width: 900px;
  height: 700px;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
}

.model-viewer-header {
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.model-selector {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.model-selector select {
  padding: 0.5rem;
  border-radius: 5px;
  border: 1px solid #ccc;
}

.model-viewer-container {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fa;
  overflow: hidden;
}

.model-viewer {
  width: 100%;
  height: 100%;
  min-height: 400px;
}

.loading-spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(255, 255, 255, 0.9);
  padding: 1rem 2rem;
  border-radius: 10px;
  font-weight: bold;
  color: #4a5568;
  z-index: 10;
}

.error-message {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(255, 240, 240, 0.95);
  padding: 1rem 2rem;
  border-radius: 10px;
  font-weight: bold;
  color: #e53e3e;
  border: 1px solid #feb2b2;
  z-index: 10;
  text-align: center;
  max-width: 80%;
}

.model-controls {
  padding: 1rem;
  background: #f8f9fa;
  border-top: 1px solid #eee;
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  justify-content: center;
}

.control-btn {
  padding: 0.5rem 1rem;
  background: #e0e5ec;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.875rem;
  transition: all 0.2s;
  box-shadow: 4px 4px 8px #a3b1c6, -4px -4px 8px #ffffff;
}

.control-btn:hover {
  box-shadow: inset 4px 4px 8px #a3b1c6, inset -4px -4px 8px #ffffff;
}

.control-btn-save {
  padding: 0.5rem 1.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 600;
  transition: all 0.2s;
  box-shadow: 4px 4px 8px #a3b1c6, -4px -4px 8px #ffffff;
}

.control-btn-save:hover {
  transform: translateY(-2px);
  box-shadow: 6px 6px 12px #a3b1c6, -6px -6px 12px #ffffff;
}

.modal-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  font-size: 2rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #666;
  z-index: 10;
}

.preview-modal-body h3 {
  margin-top: 0;
  margin-bottom: 1.5rem;
  color: #4a5568;
}

.preview-image-container {
  text-align: center;
}

.large-preview-image {
  max-width: 100%;
  max-height: 60vh;
  object-fit: contain;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.preview-placeholder-large {
  width: 100%;
  height: 400px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0f0f0;
  font-size: 1.5rem;
  color: #666;
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

  .modal-content {
    max-width: 95vw;
    max-height: 90vh;
    margin: 1rem;
  }
  
  .model-viewer-modal {
    width: 95vw;
    height: 80vh;
    max-height: 80vh;
  }
  
  .model-viewer-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .model-controls {
    flex-direction: column;
  }
  
  .control-btn {
    width: 100%;
  }
}
</style>