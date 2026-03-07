<template>
  <div class="designer-container">
    <!-- Toolbar -->
    <div class="toolbar neumorphic">
      <button @click="addText" class="tool-btn neumorphic-btn" title="Add Text">
        <span>T</span> Add Text
      </button>
      <button @click="triggerImageUpload" class="tool-btn neumorphic-btn" title="Upload Image">
        📷 Upload Image
      </button>
      <input 
        ref="imageInput" 
        type="file" 
        accept="image/*" 
        style="display: none"
        @change="handleImageUpload"
      />
      <button @click="showCliparts = true" class="tool-btn neumorphic-btn" title="Cliparts">
        🎨 Cliparts
      </button>
      <button @click="saveDesign" class="tool-btn primary neumorphic-btn" title="Save Design">
        💾 Save
      </button>
      <button @click="exportDesign" class="tool-btn success neumorphic-btn" title="Export">
        📤 Export
      </button>
    </div>

    <!-- Main Canvas Area -->
    <div class="canvas-wrapper neumorphic">
      <canvas ref="canvas"></canvas>
    </div>

    <!-- Properties Panel (when object selected) -->
    <div v-if="selectedObject" class="properties-panel neumorphic">
      <h4 class="text-neumorphic-text">Properties</h4>
      
      <!-- Text Properties -->
      <div v-if="selectedObject.type === 'i-text'">
        <label class="text-neumorphic-text">Font Family:</label>
        <select v-model="selectedObject.fontFamily" @change="updateCanvas" class="neumorphic-btn w-full p-2 mb-2">
          <option v-for="font in fonts" :key="font" :value="font">
            {{ font }}
          </option>
        </select>
        
        <label class="text-neumorphic-text">Color:</label>
        <input type="color" v-model="selectedObject.fill" @change="updateCanvas" class="neumorphic-btn w-full p-1 mb-2">
        
        <label class="text-neumorphic-text">Font Size: {{ selectedObject.fontSize }}</label>
        <input 
          type="range" 
          v-model.number="selectedObject.fontSize" 
          min="8" 
          max="200"
          @input="updateCanvas"
          class="w-full mb-2"
        >
      </div>
      
      <!-- Common Properties -->
      <label class="text-neumorphic-text">Opacity: {{ Math.round(selectedObject.opacity * 100) }}%</label>
      <input 
        type="range" 
        v-model.number="selectedObject.opacity" 
        min="0" 
        max="1" 
        step="0.1"
        @input="updateCanvas"
        class="w-full mb-4"
      >
      
      <div class="layer-controls">
        <button @click="bringForward" class="neumorphic-btn w-full mb-2">↑ Forward</button>
        <button @click="sendBackwards" class="neumorphic-btn w-full mb-2">↓ Backward</button>
        <button @click="deleteObject" class="danger neumorphic-btn w-full bg-red-500 text-white">🗑️ Delete</button>
      </div>
    </div>

    <!-- Clipart Modal -->
    <div v-if="showCliparts" class="modal-overlay" @click.self="showCliparts = false">
      <div class="modal neumorphic">
        <h3 class="text-neumorphic-text">Choose Clipart</h3>
        <div class="clipart-grid">
          <div 
            v-for="clipart in cliparts" 
            :key="clipart.id"
            class="clipart-item neumorphic-btn"
            @click="addClipart(clipart)"
          >
            <img :src="clipart.image_url" :alt="clipart.title" class="w-full h-20 object-contain">
          </div>
        </div>
        <button @click="showCliparts = false" class="close-btn neumorphic-btn">Close</button>
      </div>
    </div>
  </div>
</template>

<script>
import { fabric } from 'fabric';
import axios from 'axios';

export default {
  name: 'ProductDesigner',
  
  props: {
    productTypeId: {
      type: Number,
      required: true
    },
    initialDesign: {
      type: Object,
      default: null
    }
  },
  
  data() {
    return {
      canvas: null,
      selectedObject: null,
      fonts: [],
      cliparts: [],
      showCliparts: false,
      isDirty: false,
      currentDesignId: null,
      saveTimeout: null,
    };
  },
  
  async mounted() {
    this.initializeCanvas();
    await this.loadAssets();
    
    if (this.initialDesign) {
      this.currentDesignId = this.initialDesign.id;
      this.loadDesign(this.initialDesign);
    }
  },
  
  methods: {
    initializeCanvas() {
      this.canvas = new fabric.Canvas(this.$refs.canvas, {
        width: 800,
        height: 800,
        backgroundColor: '#ffffff',
        preserveObjectStacking: true,
      });
      
      // Event listeners
      this.canvas.on('selection:created', (e) => this.handleSelection(e));
      this.canvas.on('selection:updated', (e) => this.handleSelection(e));
      this.canvas.on('selection:cleared', () => this.selectedObject = null);
      this.canvas.on('object:modified', () => this.isDirty = true);
      this.canvas.on('object:added', () => this.isDirty = true);
      this.canvas.on('object:removed', () => this.isDirty = true);
    },
    
    handleSelection(e) {
      if (e.selected && e.selected.length > 0) {
        this.selectedObject = e.selected[0];
      } else {
        this.selectedObject = null;
      }
    },
    
    addText() {
      const text = new fabric.IText('Your Text Here', {
        left: 100,
        top: 100,
        fontFamily: 'Arial',
        fill: '#000000',
        fontSize: 40,
      });
      
      this.canvas.add(text);
      this.canvas.setActiveObject(text);
      this.isDirty = true;
      this.$emit('changed');
    },
    
    triggerImageUpload() {
      this.$refs.imageInput.click();
    },
    
    handleImageUpload(e) {
      const file = e.target.files[0];
      if (!file) return;
      
      const reader = new FileReader();
      reader.onload = (event) => {
        fabric.Image.fromURL(event.target.result, (img) => {
          img.set({
            left: 100,
            top: 100,
            scaleX: 0.5,
            scaleY: 0.5,
          });
          this.canvas.add(img);
          this.canvas.setActiveObject(img);
          this.isDirty = true;
          this.$emit('changed');
        }, { crossOrigin: 'anonymous' });
      };
      reader.readAsDataURL(file);
      
      // Reset input
      e.target.value = '';
    },
    
    addImageFromUrl(url) {
      fabric.Image.fromURL(url, (img) => {
        img.set({
          left: 100,
          top: 100,
          scaleX: 0.5,
          scaleY: 0.5,
        });
        this.canvas.add(img);
        this.canvas.setActiveObject(img);
        this.isDirty = true;
        this.$emit('changed');
      }, { crossOrigin: 'anonymous' });
    },
    
    addClipart(clipart) {
      fabric.Image.fromURL(clipart.image_url, (img) => {
        img.set({
          left: 100,
          top: 100,
        });
        this.canvas.add(img);
        this.canvas.setActiveObject(img);
        this.showCliparts = false;
        this.isDirty = true;
        this.$emit('changed');
      }, { crossOrigin: 'anonymous' });
    },
    
    updateCanvas() {
      if (this.selectedObject) {
        this.selectedObject.setCoords();
        this.canvas.renderAll();
        this.isDirty = true;
        this.$emit('changed');
      }
    },
    
    bringForward() {
      if (this.selectedObject) {
        this.canvas.bringForward(this.selectedObject);
        this.isDirty = true;
        this.$emit('changed');
      }
    },
    
    sendBackwards() {
      if (this.selectedObject) {
        this.canvas.sendBackwards(this.selectedObject);
        this.isDirty = true;
        this.$emit('changed');
      }
    },
    
    deleteObject() {
      if (this.selectedObject) {
        this.canvas.remove(this.selectedObject);
        this.selectedObject = null;
        this.isDirty = true;
        this.$emit('changed');
      }
    },
    
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
    
    async saveDesign() {
      const designData = this.canvas.toJSON();
      
      try {
        let response;
        if (this.currentDesignId) {
          // Update existing design
          response = await axios.put(`/api/designs/${this.currentDesignId}`, {
            design_data: designData,
          });
        } else {
          // Create new design
          response = await axios.post('/api/designs', {
            product_type_id: this.productTypeId,
            name: 'My Design',
            design_data: designData,
          });
        }
        
        this.currentDesignId = response.data.data.id;
        this.isDirty = false;
        
        // Emit saved event with the design data
        this.$emit('saved', response.data.data);
        
        // Show toast notification
        if (typeof this.$toast !== 'undefined') {
          this.$toast.success('Design saved successfully!');
        }
      } catch (error) {
        console.error('Failed to save design:', error);
        if (typeof this.$toast !== 'undefined') {
          this.$toast.error('Failed to save design');
        }
      }
    },
    
    autoSave() {
      if (!this.isDirty) return;
      
      // Debounce auto-save
      clearTimeout(this.saveTimeout);
      this.saveTimeout = setTimeout(() => {
        this.saveDesign();
      }, 2000);
    },
    
    async exportDesign() {
      if (!this.currentDesignId) {
        await this.saveDesign();
      }
      
      try {
        const response = await axios.post(`/api/designs/${this.currentDesignId}/export`, {
          format: 'high_res',
        });
        
        window.open(response.data.data.url, '_blank');
        
        if (typeof this.$toast !== 'undefined') {
          this.$toast.success('Export started!');
        }
      } catch (error) {
        console.error('Export failed:', error);
        if (typeof this.$toast !== 'undefined') {
          this.$toast.error('Export failed');
        }
      }
    },
    
    loadDesign(design) {
      this.canvas.loadFromJSON(design.design_data, () => {
        this.canvas.renderAll();
      });
    },
    
    async loadTemplate(template) {
      // Load the template as an image on the canvas
      try {
        // Create a new fabric image from the template
        fabric.Image.fromURL(template.preview_url, (img) => {
          // Calculate scaling to fit canvas
          const canvasWidth = this.canvas.getWidth();
          const canvasHeight = this.canvas.getHeight();
          
          // Scale the image to fit within canvas while maintaining aspect ratio
          const scaleX = canvasWidth / img.width;
          const scaleY = canvasHeight / img.height;
          const scale = Math.min(scaleX, scaleY, 1); // Don't upscale images larger than canvas
          
          img.set({
            left: canvasWidth / 2,
            top: canvasHeight / 2,
            originX: 'center',
            originY: 'center',
            scaleX: scale * 0.8, // Add some padding
            scaleY: scale * 0.8,
          });
          
          // Clear the canvas first
          this.canvas.clear();
          
          // Add the template image
          this.canvas.add(img);
          
          // Render the canvas
          this.canvas.renderAll();
          
          this.isDirty = true;
          this.$emit('changed');
        }, { crossOrigin: 'anonymous' });
      } catch (error) {
        console.error('Failed to load template:', error);
      }
    },
  },
  
  beforeUnmount() {
    if (this.canvas) {
      this.canvas.dispose();
    }
  },
};
</script>

<style scoped>
.designer-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background: #e0e5ec;
}

.toolbar {
  display: flex;
  gap: 0.5rem;
  padding: 0.75rem;
  flex-wrap: wrap;
}

.tool-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  transition: all 0.2s;
  min-height: 40px;
}

.tool-btn:hover {
  transform: translateY(-2px);
}

.tool-btn.primary {
  background: #3b82f6;
  color: white;
}

.tool-btn.primary:hover {
  background: #2563eb;
}

.tool-btn.success {
  background: #10b981;
  color: white;
}

.tool-btn.success:hover {
  background: #059669;
}

.canvas-wrapper {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem;
}

canvas {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  background: white;
  border: 1px solid #cbd5e1;
}

.properties-panel {
  width: 250px;
  padding: 1rem;
  overflow-y: auto;
}

.properties-panel h4 {
  margin: 0 0 1rem 0;
  padding-bottom: 0.5rem;
}

.properties-panel label {
  display: block;
  margin-bottom: 0.25rem;
  font-size: 0.875rem;
}

.properties-panel input,
.properties-panel select {
  width: 100%;
  padding: 0.5rem;
  border: none;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.layer-controls {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-top: 1rem;
}

.layer-controls button {
  padding: 0.5rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  min-height: 40px;
}

.layer-controls button.danger {
  background: #ef4444;
  color: white;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal {
  background: white;
  border-radius: 0.5rem;
  padding: 1.5rem;
  max-width: 80vw;
  max-height: 80vh;
  overflow-y: auto;
  width: 600px;
}

.clipart-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: 1rem;
  margin: 1rem 0;
}

.clipart-item {
  cursor: pointer;
  border-radius: 0.375rem;
  transition: all 0.2s;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.clipart-item:hover {
  transform: scale(1.05);
}

.clipart-item img {
  width: 100%;
  height: 100px;
  object-fit: contain;
}

.close-btn {
  padding: 0.5rem 1rem;
  background: #64748b;
  color: white;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  width: 100%;
  margin-top: 1rem;
}

/* Neumorphism styles */
.neumorphic {
  border-radius: 15px;
  background: #e0e5ec;
  box-shadow: 9px 9px 16px #b8bec7, -9px -9px 16px #ffffff;
}

.neumorphic-btn {
  border: none;
  border-radius: 10px;
  background: #e0e5ec;
  box-shadow: 5px 5px 10px #b8bec7, -5px -5px 10px #ffffff;
  transition: all 0.3s ease;
  cursor: pointer;
  padding: 0.5rem 1rem;
}

.neumorphic-btn:hover {
  box-shadow: 2px 2px 5px #b8bec7, -2px -2px 5px #ffffff;
}

.neumorphic-btn:active {
  box-shadow: inset 2px 2px 5px #b8bec7, inset -2px -2px 5px #ffffff;
}

.text-neumorphic-text {
  color: #6d7587;
}
</style>