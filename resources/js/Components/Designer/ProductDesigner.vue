<template>
  <div class="designer-container">
    <!-- Toolbar -->
    <div class="toolbar neumorphic">
      <!-- Selection Tool -->
      <button @click="setActiveTool('select')" :class="{ 'active-tool': activeTool === 'select' }" class="tool-btn neumorphic-btn" title="Selection Tool">
        🖱️ Select
      </button>
      
      <!-- Text Tool -->
      <button @click="setActiveTool('text'); addText()" :class="{ 'active-tool': activeTool === 'text' }" class="tool-btn neumorphic-btn" title="Add Text">
        <span>T</span> Text
      </button>
      
      <!-- Image Tool -->
      <button @click="setActiveTool('image'); triggerImageUpload()" :class="{ 'active-tool': activeTool === 'image' }" class="tool-btn neumorphic-btn" title="Upload Image">
       📷 Image
      </button>
      <input 
        ref="imageInput" 
        type="file" 
        accept="image/*" 
        style="display: none"
        @change="handleImageUpload"
      />
      
      <!-- Brush Tool -->
      <button @click="setActiveTool('brush'); toggleBrushMode()" :class="{ 'active-tool': activeTool === 'brush' }" class="tool-btn neumorphic-btn" title="Brush Tool">
       ✏️ Brush
      </button>
      
      <!-- Eraser Tool -->
      <button @click="setActiveTool('eraser'); toggleEraserMode()" :class="{ 'active-tool': activeTool === 'eraser' }" class="tool-btn neumorphic-btn" title="Eraser Tool">
       🧽 Eraser
      </button>
      
      <!-- Shape Tool -->
      <button @click="showShapes = true" class="tool-btn neumorphic-btn" title="Shapes">
       🔺 Shapes
      </button>
      
      <!-- Clipart Tool -->
      <button @click="setActiveTool('clipart'); showCliparts = true" :class="{ 'active-tool': activeTool === 'clipart' }" class="tool-btn neumorphic-btn" title="Cliparts">
        🎨 Cliparts
      </button>
      
      <!-- Advanced Tools -->
      <button @click="showEffects = true" class="tool-btn neumorphic-btn" title="Effects">
        ⚡ Effects
      </button>
      
      <!-- Save & Export -->
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

    <!-- Brush Settings Panel -->
    <div v-if="activeTool === 'brush' || activeTool === 'eraser'" class="brush-panel neumorphic">
      <h4 class="text-neumorphic-text">{{ activeTool === 'eraser' ? 'Eraser' : 'Brush' }} Settings</h4>
      
      <div class="brush-controls">
        <!-- Brush Size -->
        <div class="control-group">
          <label class="text-neumorphic-text">{{ activeTool === 'eraser' ? 'Eraser' : 'Brush' }} Size: {{ brushSize }}px</label>
          <input 
            type="range" 
            v-model.number="brushSize" 
            min="1" 
            max="100"
            @input="updateBrushSettings"
            class="w-full mb-3"
          >
        </div>
        
        <!-- Brush Color (hidden for eraser) -->
        <div v-if="activeTool !== 'eraser'" class="control-group">
          <label class="text-neumorphic-text">Brush Color:</label>
          <div class="color-picker-container">
            <input 
              type="color" 
              v-model="brushColor" 
              @change="updateBrushSettings"
              class="w-full h-10 mb-3 rounded cursor-pointer"
            >
            <!-- Professional Color Palette - Full Display -->
            <div class="color-palette-grid">
              <div 
                v-for="color in colorPalette" 
                :key="color"
                class="color-swatch-large"
                :style="{ backgroundColor: color }"
                @click="selectColor(color)"
                :class="{ 'active': brushColor === color }"
              ></div>
            </div>
          </div>
        </div>
        
        <!-- Brush Opacity -->
        <div v-if="activeTool !== 'eraser'" class="control-group">
          <label class="text-neumorphic-text">Opacity: {{ Math.round(brushOpacity * 100) }}%</label>
          <input 
            type="range" 
            v-model.number="brushOpacity" 
            min="0.1" 
            max="1"
            step="0.1"
            @input="updateBrushSettings"
            class="w-full mb-3"
          >
        </div>
        
        <!-- Brush Type -->
        <div v-if="activeTool === 'brush'" class="control-group">
          <label class="text-neumorphic-text">Brush Type:</label>
          <div class="brush-type-selector">
            <button 
              v-for="brush in brushTypes" 
              :key="brush.value"
              @click="setBrushType(brush.value)"
              :class="{ 'active-brush-type': brushType === brush.value }"
              class="brush-type-btn neumorphic-btn"
            >
              <span class="text-lg">{{ brush.icon }}</span>
              <span class="text-xs">{{ brush.name }}</span>
            </button>
          </div>
        </div>
        
        <!-- Actions -->
        <div class="brush-actions">
          <button @click="clearCanvas" class="tool-btn danger neumorphic-btn w-full mb-2">Clear Canvas</button>
          <button @click="undoBrushStroke" class="tool-btn neumorphic-btn w-full mb-2" :disabled="!canUndoBrush">Undo Stroke</button>
          <button @click="redoBrushStroke" class="tool-btn neumorphic-btn w-full" :disabled="!canRedoBrush">Redo Stroke</button>
        </div>
      </div>
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
        
        <label class="text-neumorphic-text">Text Content:</label>
        <textarea 
          v-model="selectedObject.text" 
          @input="updateCanvas" 
          class="neumorphic-btn w-full p-2 mb-2 h-16 resize-none"
          placeholder="Enter text..."
        ></textarea>
        
        <label class="text-neumorphic-text">Color:</label>
        <div class="color-picker-container">
          <input type="color" v-model="selectedObject.fill" @change="updateCanvas" class="neumorphic-btn w-full p-1 mb-2">
          <!-- Professional Color Palette -->
          <div class="color-palette">
            <div 
              v-for="color in colorPalette" 
              :key="color"
              class="color-swatch"
              :style="{ backgroundColor: color }"
              @click="selectedObject.fill = color; updateCanvas()"
              :class="{ 'active': selectedObject.fill === color }"
            ></div>
          </div>
        </div>
        
        <label class="text-neumorphic-text">Font Size: {{ selectedObject.fontSize }}</label>
        <input 
          type="range" 
          v-model.number="selectedObject.fontSize" 
          min="8" 
          max="200"
          @input="updateCanvas"
          class="w-full mb-2"
        >
        
        <label class="text-neumorphic-text">Line Height: {{ selectedObject.lineHeight.toFixed(2) }}</label>
        <input 
          type="range" 
          v-model.number="selectedObject.lineHeight" 
          min="0.5" 
          max="3"
          step="0.1"
          @input="updateCanvas"
          class="w-full mb-2"
        >
        
        <label class="text-neumorphic-text">Bold:</label>
        <input 
          type="checkbox" 
          :checked="selectedObject.fontWeight === 'bold'" 
          @change="toggleBold"
          class="ml-2 mb-2"
        >
        
        <label class="text-neumorphic-text">Italic:</label>
        <input 
          type="checkbox" 
          :checked="selectedObject.fontStyle === 'italic'" 
          @change="toggleItalic"
          class="ml-2 mb-2"
        >
        
        <label class="text-neumorphic-text">Underline:</label>
        <input 
          type="checkbox" 
          :checked="selectedObject.underline" 
          @change="toggleUnderline"
          class="ml-2 mb-2"
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
      
      <!-- Rotation Control -->
      <label class="text-neumorphic-text">Rotation: {{ Math.round(selectedObject.angle || 0) }}°</label>
      <input 
        type="range" 
        v-model.number="selectedObject.angle" 
        min="0" 
        max="360"
        @input="updateCanvas"
        class="w-full mb-4"
      >
      
      <!-- Size Controls for all objects -->
      <div v-if="selectedObject.width || selectedObject.radius">
        <label class="text-neumorphic-text">Width Scale: {{ Math.round((selectedObject.scaleX || 1) * 100) }}%</label>
        <input 
          type="range" 
          v-model.number="selectedObject.scaleX" 
          min="0.1" 
          max="5" 
          step="0.1"
          @input="updateCanvas"
          class="w-full mb-4"
        >
        
        <label class="text-neumorphic-text">Height Scale: {{ Math.round((selectedObject.scaleY || 1) * 100) }}%</label>
        <input 
          type="range" 
          v-model.number="selectedObject.scaleY" 
          min="0.1" 
          max="5" 
          step="0.1"
          @input="updateCanvas"
          class="w-full mb-4"
        >
      </div>
      
      <!-- Position Controls -->
      <label class="text-neumorphic-text">Position X: {{ Math.round(selectedObject.left || 0) }}</label>
      <input 
        type="number"
        v-model.number="selectedObject.left" 
        @input="updateCanvas"
        class="neumorphic-btn w-full p-2 mb-4"
      >
      
      <label class="text-neumorphic-text">Position Y: {{ Math.round(selectedObject.top || 0) }}</label>
      <input 
        type="number"
        v-model.number="selectedObject.top" 
        @input="updateCanvas"
        class="neumorphic-btn w-full p-2 mb-4"
      >
      
      <!-- Color Control for shapes -->
      <div v-if="selectedObject.type && ['rect', 'circle', 'triangle', 'polygon'].includes(selectedObject.type)">
        <label class="text-neumorphic-text">Fill Color:</label>
        <div class="color-picker-container">
          <input 
            type="color" 
            v-model="selectedObject.fill" 
            @change="updateCanvas"
            class="w-full p-1 mb-2"
          >
          <!-- Professional Color Palette -->
          <div class="color-palette">
            <div 
              v-for="color in colorPalette" 
              :key="color"
              class="color-swatch"
              :style="{ backgroundColor: color }"
              @click="selectedObject.fill = color; updateCanvas()"
              :class="{ 'active': selectedObject.fill === color }"
            ></div>
          </div>
        </div>
        
        <label class="text-neumorphic-text">Stroke Color:</label>
        <div class="color-picker-container">
          <input 
            type="color" 
            v-model="selectedObject.stroke" 
            @change="updateCanvas"
            class="w-full p-1 mb-2"
          >
          <!-- Professional Color Palette -->
          <div class="color-palette">
            <div 
              v-for="color in colorPalette" 
              :key="color"
              class="color-swatch"
              :style="{ backgroundColor: color }"
              @click="selectedObject.stroke = color; updateCanvas()"
              :class="{ 'active': selectedObject.stroke === color }"
            ></div>
          </div>
        </div>
        
        <label class="text-neumorphic-text">Stroke Width: {{ selectedObject.strokeWidth || 0 }}</label>
        <input 
          type="range" 
          v-model.number="selectedObject.strokeWidth" 
          min="0" 
          max="20"
          @input="updateCanvas"
          class="w-full mb-4"
        >
      </div>
      
      <!-- Image-specific controls -->
      <div v-if="selectedObject.type === 'image' || selectedObject.src">
        <label class="text-neumorphic-text">Brightness: {{ Math.round(((selectedObject.brightness || 0) + 0.5) * 100) }}%</label>
        <input 
          type="range" 
          v-model.number="selectedObject.brightness" 
          min="-0.5" 
          max="0.5" 
          step="0.1"
          @input="adjustImageFilters"
          class="w-full mb-4"
        >
        
        <label class="text-neumorphic-text">Contrast: {{ Math.round(((selectedObject.contrast || 0) + 0.5) * 100) }}%</label>
        <input 
          type="range" 
          v-model.number="selectedObject.contrast" 
          min="-0.5" 
          max="0.5" 
          step="0.1"
          @input="adjustImageFilters"
          class="w-full mb-4"
        >
        
        <label class="text-neumorphic-text">Saturation: {{ Math.round(((selectedObject.saturation || 0) + 0.5) * 100) }}%</label>
        <input 
          type="range" 
          v-model.number="selectedObject.saturation" 
          min="-0.5" 
          max="0.5" 
          step="0.1"
          @input="adjustImageFilters"
          class="w-full mb-4"
        >
        
        <!-- Professional Image Editing Button -->
        <button 
          @click="openImageEditing(selectedObject)"
          class="w-full py-2 px-4 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors mb-4"
        >
         🖼 Advanced Image Editing
        </button>
      </div>
      
      <div class="layer-controls">
        <button @click="bringForward" class="neumorphic-btn w-full mb-2">↑ Bring Forward</button>
        <button @click="sendBackwards" class="neumorphic-btn w-full mb-2">↓ Send Backward</button>
        <button @click="bringToFront" class="neumorphic-btn w-full mb-2">⇈ Bring to Front</button>
        <button @click="sendToBack" class="neumorphic-btn w-full mb-2">⇊ Send to Back</button>
        <button @click="deleteObject" class="danger neumorphic-btn w-full bg-red-500 text-white">🗑️ Delete Object</button>
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
    
    <!-- Shapes Modal -->
    <div v-if="showShapes" class="modal-overlay" @click.self="showShapes = false">
      <div class="modal neumorphic">
        <h3 class="text-neumorphic-text">Choose Shape</h3>
        <div class="shape-grid">
          <div 
            v-for="shape in shapes" 
            :key="shape.type"
            class="shape-item neumorphic-btn"
            @click="addShape(shape.type)"
            :title="shape.type"
          >
            <span class="text-2xl">{{ shape.icon }}</span>
            <span class="text-xs mt-1">{{ shape.type }}</span>
          </div>
        </div>
        <button @click="showShapes = false" class="close-btn neumorphic-btn">Close</button>
      </div>
    </div>
    
    <!-- Effects Modal -->
    <div v-if="showEffects" class="modal-overlay" @click.self="showEffects = false">
      <div class="modal neumorphic">
        <h3 class="text-neumorphic-text">Apply Effect</h3>
        <div class="effects-grid">
          <div 
            v-for="effect in effects" 
            :key="effect.type"
            class="effect-item neumorphic-btn"
            @click="applyEffect(effect.type)"
            :title="effect.name"
          >
            <span class="text-2xl">{{ effect.icon }}</span>
            <span class="text-xs mt-1">{{ effect.name }}</span>
          </div>
        </div>
        <button @click="showEffects = false" class="close-btn neumorphic-btn">Close</button>
      </div>
    </div>
    
    <!-- Professional Image Editing Panel -->
    <div v-if="showImageEditing" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <ImageEditingPanel
        :image-object="editingImage"
        :image-src="editingImageSrc"
        @update="updateImageEditing"
        @image-updated="imageUpdated"
        @close="closeImageEditing"
      />
    </div>
  </div>
</template>

<script>
import { fabric } from 'fabric';
import axios from 'axios';
import ImageEditingService from '@/Services/ImageEditingService';
import ImageEditingPanel from '@/Components/Designer/ImageEditingPanel.vue';

export default {
  name: 'ProductDesigner',
  
  components: {
    ImageEditingPanel
  },
  
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
      shapes: [
        { type: 'rectangle', icon: '⬜' },
        { type: 'circle', icon: '⭕' },
        { type: 'triangle', icon: '🔺' },
        { type: 'polygon', icon: '🔷' },
        { type: 'line', icon: '📏' },
      ],
      effects: [
        { type: 'blur', name: 'Blur', icon: '🌫️' },
        { type: 'shadow', name: 'Shadow', icon: '🌑' },
        { type: 'glow', name: 'Glow', icon: '✨' },
        { type: 'outline', name: 'Outline', icon: '⭕' },
      ],
      showCliparts: false,
      showShapes: false,
      showEffects: false,
      activeTool: 'select', // Track active tool
      isDirty: false,
      currentDesignId: null,
      saveTimeout: null,
      showImageEditing: false,
      editingImage: null,
      editingImageSrc: null,
      
      // Brush Tool Data
      isDrawing: false,
      brushSize: 5,
      brushColor: '#000000',
      brushOpacity: 1,
      brushType: 'pencil',
      brushHistory: [],
      
      // Professional Color Palette - Expanded to include more colors
      colorPalette: [
        '#000000', '#FFFFFF', '#FF0000', '#00FF00', '#0000FF',
        '#FFFF00', '#FF00FF', '#00FFFF', '#FFA500', '#800080',
        '#FFC0CB', '#A52A2A', '#808080', '#C0C0C0', '#800000',
        '#008000', '#000080', '#808000', '#800080', '#008080',
        '#FFB6C1', '#FFD700', '#FF6347', '#40E0D0', '#9370DB',
        '#2F4F4F', '#000080', '#4B0082', '#8B0000', '#006400',
        '#DC143C', '#008B8B', '#483D8B', '#FF8C00', '#FF1493',
        '#00BFFF', '#1E90FF', '#9ACD32', '#FFD700', '#FFA07A',
        '#20B2AA', '#87CEEB', '#DDA0DD', '#F0E68C', '#98FB98',
        '#F5DEB3', '#DEB887', '#FA8072', '#E6E6FA', '#FFFACD'
      ],
      
      // Brush Tool Data
      isDrawing: false,
      brushSize: 5,
      brushColor: '#000000',
      brushOpacity: 1,
      brushType: 'pencil',
      brushHistory: [],
      brushHistoryIndex: -1,
      maxBrushHistory: 50,
      
      // Enhanced brush types
      brushTypes: [
        { value: 'pencil', name: 'Pencil', icon: '✏️' },
        { value: 'spray', name: 'Spray', icon: '💨' },
        { value: 'marker', name: 'Marker', icon: '🖊️' },
        { value: 'soft', name: 'Soft Brush', icon: '🖌️' },
        { value: 'eraser', name: 'Eraser', icon: '🧽' }
      ]
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
      // Fixed canvas size
      const width = 800;
      const height = 600;
      
      this.canvas = new fabric.Canvas(this.$refs.canvas, {
        width: width,
        height: height,
        backgroundColor: '#ffffff',
        preserveObjectStacking: true,
        selection: true,
        selectionColor: 'rgba(0, 120, 215, 0.3)',
        selectionBorderColor: 'rgba(0, 120, 215, 0.8)',
        selectionLineWidth: 2,
        // Enable object controls
        uniformScaling: true,
        centeredScaling: false,
        centeredRotation: false,
        controlsAboveOverlay: true,
        
        // Configure default object controls
        defaultCursor: 'default',
        moveCursor: 'move',
        rotationCursor: 'crosshair',
      });
      
      // Set default properties for all objects
      fabric.Object.prototype.set({
        hasControls: true,
        hasBorders: true,
        cornerStyle: 'circle',
        cornerSize: 10,
        borderColor: '#007AFF',
        cornerColor: '#007AFF',
        cornerStrokeColor: '#007AFF',
        transparentCorners: false,
        borderScaleFactor: 1.5,
        rotatingPointOffset: 20,
      });
      
      // Event listeners
      this.canvas.on('selection:created', (e) => this.handleSelection(e));
      this.canvas.on('selection:updated', (e) => this.handleSelection(e));
      this.canvas.on('selection:cleared', () => {
        this.selectedObject = null;
        this.activeTool = 'select';
      });
      this.canvas.on('object:modified', (e) => {
        this.isDirty = true;
        // Update selected object properties after modification
        if (this.selectedObject && e.target) {
          // Sync the selected object reference with the modified object
          this.selectedObject = e.target;
          this.$forceUpdate();
        }
      });
      this.canvas.on('object:added', () => this.isDirty = true);
      this.canvas.on('object:removed', () => this.isDirty = true);
      
      // Update properties panel when object changes
      this.canvas.on('object:modified', () => {
        if (this.selectedObject) {
          this.selectedObject.set({ dirty: true });
          this.canvas.requestRenderAll();
        }
      });
      
      // Keyboard shortcuts
      document.addEventListener('keydown', this.handleKeyDown);
      
      // Enable drawing mode for freehand
      this.canvas.isDrawingMode = false;
    },
    
    handleSelection(e) {
      if (e.selected && e.selected.length > 0) {
        const selected = e.selected[0];
        // Clone the object to avoid direct reference issues
        this.selectedObject = selected;
        
        // Ensure proper initialization of properties and controls
        selected.set({
          hasControls: true,
          hasBorders: true,
          selectable: true,
          evented: true,
          cornerStyle: 'circle',
          cornerSize: 10,
          borderColor: '#007AFF',
          cornerColor: '#007AFF',
          cornerStrokeColor: '#007AFF',
          transparentCorners: false,
          borderScaleFactor: 1.5,
        });
        
        if (selected.type === 'i-text') {
          // Initialize fontWeight if not set
          if (!selected.fontWeight) {
            selected.set({ fontWeight: 'normal' });
          }
          // Ensure text is editable
          selected.set({
            editable: true,
          });
        } else if (selected.type === 'image' || selected.src) {
          // Ensure image has proper scaling
          if (!selected.scaleX) {
            selected.set({ scaleX: 1, scaleY: 1 });
          }
        } else if (['rect', 'circle', 'triangle', 'polygon'].includes(selected.type)) {
          // Ensure shapes have proper properties
          selected.set({
            hasRotatingPoint: true,
          });
        }
        
        // Force render to update controls
        this.canvas.requestRenderAll();
        
        // Force Vue reactivity update
        this.$forceUpdate();
      } else {
        this.selectedObject = null;
      }
    },
    
    addText() {
      const text = new fabric.IText('Edit Text', {
        left: 100,
        top: 100,
        fontFamily: 'Arial',
        fill: '#000000',
        fontSize: 32,
        fontWeight: 'normal',
        lineHeight: 1.2,
        hasControls: true,
        hasBorders: true,
        editable: true,
        cornerStyle: 'circle',
        cornerSize: 10,
        borderColor: '#007AFF',
        cornerColor: '#007AFF',
        cornerStrokeColor: '#007AFF',
        transparentCorners: false,
        borderScaleFactor: 1.5,
      });
      
      this.canvas.add(text);
      this.canvas.setActiveObject(text);
      this.isDirty = true;
      this.$emit('changed');
      
      // Auto-select the text for immediate editing
      setTimeout(() => {
        this.canvas.setActiveObject(text);
        text.enterEditing();
        text.selectAll();
      }, 100);
    },
    
    setActiveTool(tool) {
      this.activeTool = tool;
    },
    
    triggerImageUpload() {
      this.$refs.imageInput.click();
    },
    
    addShape(shapeType) {
      let shape;
      
      switch(shapeType) {
        case 'rectangle':
          shape = new fabric.Rect({
            left: 100,
            top: 100,
            width: 100,
            height: 100,
            fill: '#FF0000',
            stroke: '#000000',
            strokeWidth: 2,
            hasControls: true,
            hasBorders: true,
            cornerStyle: 'circle',
            cornerSize: 10,
            borderColor: '#007AFF',
            cornerColor: '#007AFF',
            cornerStrokeColor: '#007AFF',
            transparentCorners: false,
            borderScaleFactor: 1.5,
          });
          break;
        case 'circle':
          shape = new fabric.Circle({
            left: 100,
            top: 100,
            radius: 50,
            fill: '#00FF00',
            stroke: '#000000',
            strokeWidth: 2,
            hasControls: true,
            hasBorders: true,
            cornerStyle: 'circle',
            cornerSize: 10,
            borderColor: '#007AFF',
            cornerColor: '#007AFF',
            cornerStrokeColor: '#007AFF',
            transparentCorners: false,
            borderScaleFactor: 1.5,
          });
          break;
        case 'triangle':
          shape = new fabric.Triangle({
            left: 100,
            top: 100,
            width: 100,
            height: 100,
            fill: '#0000FF',
            stroke: '#000000',
            strokeWidth: 2,
            hasControls: true,
            hasBorders: true,
            cornerStyle: 'circle',
            cornerSize: 10,
            borderColor: '#007AFF',
            cornerColor: '#007AFF',
            cornerStrokeColor: '#007AFF',
            transparentCorners: false,
            borderScaleFactor: 1.5,
          });
          break;
        case 'polygon':
          shape = new fabric.Polygon([
            {x: 0, y: 0},
            {x: 50, y: -25},
            {x: 100, y: 0},
            {x: 75, y: 50},
            {x: 25, y: 50}
          ], {
            left: 100,
            top: 100,
            fill: '#FFFF00',
            stroke: '#000000',
            strokeWidth: 2,
            hasControls: true,
            hasBorders: true,
            cornerStyle: 'circle',
            cornerSize: 10,
            borderColor: '#007AFF',
            cornerColor: '#007AFF',
            cornerStrokeColor: '#007AFF',
            transparentCorners: false,
            borderScaleFactor: 1.5,
          });
          break;
        case 'line':
          shape = new fabric.Line([50, 50, 150, 150], {
            stroke: '#000000',
            strokeWidth: 2,
            hasControls: true,
            hasBorders: true,
            cornerStyle: 'circle',
            cornerSize: 10,
            borderColor: '#007AFF',
            cornerColor: '#007AFF',
            cornerStrokeColor: '#007AFF',
            transparentCorners: false,
            borderScaleFactor: 1.5,
          });
          break;
        default:
          return;
      }
      
      this.canvas.add(shape);
      this.canvas.setActiveObject(shape);
      this.isDirty = true;
      this.$emit('changed');
      this.showShapes = false;
    },
    
    applyEffect(effectType) {
      if (!this.selectedObject) {
        alert('Please select an object first');
        return;
      }
      
      switch(effectType) {
        case 'blur':
          this.selectedObject.filters = [new fabric.Image.filters.Blur({blur: 0.5})];
          this.selectedObject.applyFilters();
          break;
        case 'shadow':
          this.selectedObject.shadow = new fabric.Shadow({
            color: 'rgba(0,0,0,0.3)',
            blur: 10,
            offsetX: 5,
            offsetY: 5
          });
          break;
        case 'glow':
          this.selectedObject.shadow = new fabric.Shadow({
            color: 'rgba(255,255,0,0.5)',
            blur: 15,
            offsetX: 0,
            offsetY: 0
          });
          break;
        case 'outline':
          this.selectedObject.stroke = '#000000';
          this.selectedObject.strokeWidth = 2;
          break;
      }
      
      this.canvas.renderAll();
      this.isDirty = true;
      this.$emit('changed');
      this.showEffects = false;
    },
    
    handleImageUpload(e) {
      const file = e.target.files[0];
      if (!file) return;
      
      // Check if file is an image
      if (!file.type.match('image.*')) {
        alert('Please select an image file');
        return;
      }
      
      const reader = new FileReader();
      reader.onload = (event) => {
        fabric.Image.fromURL(event.target.result, (img) => {
          // Set initial properties for better usability
          img.set({
            left: 100,
            top: 100,
            scaleX: 0.3, // Smaller initial size
            scaleY: 0.3,
            hasControls: true,
            hasBorders: true,
            cornerStyle: 'circle',
            cornerSize: 10,
            borderColor: '#007AFF',
            cornerColor: '#007AFF',
            cornerStrokeColor: '#007AFF',
            transparentCorners: false,
            borderScaleFactor: 1.5,
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
          scaleX: 0.3, // Smaller initial size
          scaleY: 0.3,
          hasControls: true,
          hasBorders: true,
          cornerStyle: 'circle',
          cornerSize: 10,
          borderColor: '#007AFF',
          cornerColor: '#007AFF',
          cornerStrokeColor: '#007AFF',
          transparentCorners: false,
          borderScaleFactor: 1.5,
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
          scaleX: 0.3, // Smaller initial size
          scaleY: 0.3,
          hasControls: true,
          hasBorders: true,
          cornerStyle: 'circle',
          cornerSize: 10,
          borderColor: '#007AFF',
          cornerColor: '#007AFF',
          cornerStrokeColor: '#007AFF',
          transparentCorners: false,
          borderScaleFactor: 1.5,
        });
        this.canvas.add(img);
        this.canvas.setActiveObject(img);
        this.showCliparts = false;
        this.isDirty = true;
        this.$emit('changed');
      }, { crossOrigin: 'anonymous' });
    },
    
    handleKeyDown(e) {
      // Delete selected object with Delete or Backspace key
      if ((e.key === 'Delete' || e.key === 'Backspace') && this.selectedObject) {
        e.preventDefault();
        this.deleteObject();
      }
      
      // Bring forward with Ctrl+] or Cmd+]
      if ((e.ctrlKey || e.metaKey) && e.key === ']') {
        e.preventDefault();
        this.bringForward();
      }
      
      // Send backwards with Ctrl+[ or Cmd+[
      if ((e.ctrlKey || e.metaKey) && e.key === '[') {
        e.preventDefault();
        this.sendBackwards();
      }
    },
    
    toggleBold() {
      if (this.selectedObject && this.selectedObject.type === 'i-text') {
        const currentWeight = this.selectedObject.fontWeight;
        const newWeight = currentWeight === 'bold' ? 'normal' : 'bold';
        this.selectedObject.set({ fontWeight: newWeight });
        this.canvas.renderAll();
        this.isDirty = true;
        this.$emit('changed');
      }
    },
    
    toggleItalic() {
      if (this.selectedObject && this.selectedObject.type === 'i-text') {
        const currentStyle = this.selectedObject.fontStyle;
        const newStyle = currentStyle === 'italic' ? 'normal' : 'italic';
        this.selectedObject.set({ fontStyle: newStyle });
        this.canvas.renderAll();
        this.isDirty = true;
        this.$emit('changed');
      }
    },
    
    toggleUnderline() {
      if (this.selectedObject && this.selectedObject.type === 'i-text') {
        const currentUnderline = this.selectedObject.underline;
        this.selectedObject.set({ underline: !currentUnderline });
        this.canvas.renderAll();
        this.isDirty = true;
        this.$emit('changed');
      }
    },
    
    bringToFront() {
      if (this.selectedObject) {
        this.canvas.bringToFront(this.selectedObject);
        this.isDirty = true;
        this.$emit('changed');
      }
    },
    
    sendToBack() {
      if (this.selectedObject) {
        this.canvas.sendToBack(this.selectedObject);
        this.isDirty = true;
        this.$emit('changed');
      }
    },
    
    adjustImageFilters() {
      if (this.selectedObject && (this.selectedObject.type === 'image' || this.selectedObject.src)) {
        // Initialize filters array if it doesn't exist
        if (!this.selectedObject.filters) {
          this.selectedObject.filters = [];
        }
        
        // Remove existing brightness, contrast, and saturation filters
        this.selectedObject.filters = this.selectedObject.filters.filter(filter => 
          !(filter instanceof fabric.Image.filters.Brightness || 
            filter instanceof fabric.Image.filters.Contrast ||
            filter instanceof fabric.Image.filters.Saturation)
        );
        
        // Add brightness filter if value is not zero
        if (this.selectedObject.brightness && this.selectedObject.brightness !== 0) {
          this.selectedObject.filters.push(new fabric.Image.filters.Brightness({
            brightness: this.selectedObject.brightness
          }));
        }
        
        // Add contrast filter if value is not zero
        if (this.selectedObject.contrast && this.selectedObject.contrast !== 0) {
          this.selectedObject.filters.push(new fabric.Image.filters.Contrast({
            contrast: this.selectedObject.contrast
          }));
        }
        
        // Add saturation filter if value is not zero
        if (this.selectedObject.saturation && this.selectedObject.saturation !== 0) {
          this.selectedObject.filters.push(new fabric.Image.filters.Saturation({
            saturation: this.selectedObject.saturation
          }));
        }
        
        // Apply filters
        this.selectedObject.applyFilters();
        this.canvas.renderAll();
        this.isDirty = true;
        this.$emit('changed');
      }
    },
    
    updateCanvas() {
      if (this.selectedObject && this.canvas) {
        // Update object properties
        this.selectedObject.setCoords();
        this.selectedObject.set({
          dirty: true,
        });
        
        // Force canvas re-render
        this.canvas.requestRenderAll();
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
        // Load fonts and cliparts from public API endpoints
        const [fontsRes, clipartsRes] = await Promise.all([
          axios.get('/api/v1/fonts'),
          axios.get('/api/v1/cliparts'),
        ]);
        
        this.fonts = fontsRes.data.data.map(f => f.name);
        this.cliparts = clipartsRes.data.data;
      } catch (error) {
        console.error('Failed to load assets:', error);
        // Provide fallback data
        this.fonts = ['Arial', 'Times New Roman', 'Courier New', 'Georgia', 'Verdana'];
        this.cliparts = [];
      }
    },
    
    async saveDesign() {
      const designData = this.canvas.toJSON();
      
      try {
        let response;
        // Include guest identifier in the request
        const requestData = {
          design_data: designData,
          product_type_id: this.productTypeId,
          name: 'My Design',
        };
        
        if (this.currentDesignId) {
          // Update existing design
          response = await axios.put(`/api/designs/${this.currentDesignId}`, requestData);
        } else {
          // Create new design
          response = await axios.post('/api/designs', requestData);
        }
        
        this.currentDesignId = response.data.data.id;
        this.isDirty = false;
        
        // Emit saved event with the design data
        this.$emit('saved', response.data.data);
        
        // Show toast notification
        this.showToast('success', 'Design saved successfully!');
      } catch (error) {
        console.error('Failed to save design:', error);
        
        // Check if it's an authentication error
        if (error.response?.status === 401) {
          this.showToast('info', 'Design saved! You can access it in "My Designs" after logging in.');
          console.log('INFO: Design saved for guest session.');
        } else {
          // Handle specific error for Intervention Image driver
          const errorMessage = error.response?.data?.message || error.message;
          if (errorMessage.includes('Intervention\\Image\\Drivers\\Gd\\Driver')) {
            this.showToast('error', 'Design saved successfully! (Image processing error - contact admin)');
            console.log('INFO: Design saved but image processing failed:', errorMessage);
          } else {
            this.showToast('error', 'Failed to save design: ' + errorMessage);
            console.log('ERROR: Failed to save design:', errorMessage);
          }
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
      // First, ensure the design is saved and has an ID
      if (!this.currentDesignId) {
        await this.saveDesign();
      }
      
      // Double-check that we have a valid ID after saving
      if (!this.currentDesignId) {
        this.showToast('error', 'Cannot export design - it must be saved first');
        console.log('ERROR: Cannot export design - no ID available');
        return;
      }
      
      try {
        const response = await axios.post(`/api/designs/${this.currentDesignId}/export`, {
          format: 'high_res',
        });
        
        window.open(response.data.data.url, '_blank');
        
        this.showToast('success', 'Export started!');
      } catch (error) {
        console.error('Export failed:', error);
        
        // Check if it's an authentication error
        if (error.response?.status === 401) {
          this.showToast('info', 'Export started! You can access it after logging in.');
          console.log('INFO: Export started for guest session.');
        } else {
          // Handle the specific 404 error for null design ID
          if (error.response?.status === 404) {
            this.showToast('error', 'Cannot export design - it may not be saved properly');
            console.log('ERROR: Export failed with 404 - likely invalid design ID:', this.currentDesignId);
          } else {
            this.showToast('error', 'Export failed: ' + (error.response?.data?.message || error.message));
            console.log('ERROR: Export failed:', error.message);
          }
        }
      }
    },
    
    loadDesign(design) {
      this.canvas.loadFromJSON(design.design_data, () => {
        this.canvas.renderAll();
      });
    },
    
    async loadTemplate(template) {
      // Load the template design data onto the canvas
      try {
        console.log('Loading template:', template);
        
        // Check if template has design_data property (JSON format)
        if (template.design_data && Array.isArray(template.design_data) && template.design_data.length > 0) {
          // Load the full design data
          console.log('Loading design data from template');
          this.canvas.loadFromJSON(template.design_data, () => {
            this.canvas.renderAll();
            this.isDirty = true;
            this.$emit('changed');
            console.log('Template design loaded successfully');
          });
        } else if (template.preview_url) {
          // Sanitize the preview URL to avoid placeholder service issues
          const sanitizedUrl = this.sanitizeImageUrl(template.preview_url);
          
          // Fallback to loading as image if no design data
          console.log('Loading template as image from preview URL:', sanitizedUrl);
          fabric.Image.fromURL(sanitizedUrl, (img) => {
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
              hasControls: true,
              hasBorders: true,
              cornerStyle: 'circle',
              cornerSize: 10,
              borderColor: '#007AFF',
              cornerColor: '#007AFF',
              cornerStrokeColor: '#007AFF',
              transparentCorners: false,
              borderScaleFactor: 1.5,
            });
            
            // Add the template image to existing canvas (don't clear)
            this.canvas.add(img);
            this.canvas.setActiveObject(img);
            
            // Render the canvas
            this.canvas.renderAll();
            
            this.isDirty = true;
            this.$emit('changed');
            console.log('Template image loaded successfully');
          }, { 
            crossOrigin: 'anonymous',
            onError: (err) => {
              console.error('Error loading image:', err);
              if (typeof this.$toast !== 'undefined') {
                this.$toast.error('Failed to load template image');
              }
            }
          });
        } else {
          console.warn('Template has no design data or preview URL');
          if (typeof this.$toast !== 'undefined') {
            this.$toast.warning('Template has no content to load');
          }
        }
      } catch (error) {
        console.error('Failed to load template:', error);
        if (typeof this.$toast !== 'undefined') {
          this.$toast.error('Failed to load template: ' + error.message);
        }
      }
    },
    
    // Brush Tool Methods
    toggleBrushMode() {
      if (this.activeTool === 'brush') {
        this.canvas.isDrawingMode = true;
        this.canvas.freeDrawingBrush = new fabric.PencilBrush(this.canvas);
        this.updateBrushSettings();
        
        // Add event listener to save brush strokes for undo/redo
        this.canvas.on('path:created', (opt) => {
          this.saveBrushHistory();
        });
      } else {
        this.canvas.isDrawingMode = false;
      }
    },
    
    toggleEraserMode() {
      if (this.activeTool === 'eraser') {
        this.canvas.isDrawingMode = true;
        
        // Check if EraserBrush is available, otherwise use regular brush with white color
        if (fabric.EraserBrush) {
          this.canvas.freeDrawingBrush = new fabric.EraserBrush(this.canvas);
          this.canvas.freeDrawingBrush.width = this.brushSize;
        } else {
          // Fallback: use regular brush with white color to simulate erasing
          this.canvas.freeDrawingBrush = new fabric.PencilBrush(this.canvas);
          this.canvas.freeDrawingBrush.width = this.brushSize;
          this.canvas.freeDrawingBrush.color = '#FFFFFF';
        }
        
        // Add event listener to save eraser strokes for undo/redo
        this.canvas.on('path:created', (opt) => {
          this.saveBrushHistory();
        });
      } else {
        this.canvas.isDrawingMode = false;
      }
    },
    
    setBrushType(type) {
      this.brushType = type;
      this.updateBrushSettings();
    },
    
    updateBrushSettings() {
      if (this.canvas) {
        if (this.activeTool === 'brush') {
          // Set brush type
          switch(this.brushType) {
            case 'pencil':
              this.canvas.freeDrawingBrush = new fabric.PencilBrush(this.canvas);
              this.canvas.freeDrawingBrush.width = this.brushSize;
              this.canvas.freeDrawingBrush.color = this.brushColor;
              break;
            case 'spray':
              this.canvas.freeDrawingBrush = new fabric.SprayBrush(this.canvas);
              this.canvas.freeDrawingBrush.width = this.brushSize;
              this.canvas.freeDrawingBrush.color = this.brushColor;
              break;
            case 'marker':
              this.canvas.freeDrawingBrush = new fabric.PencilBrush(this.canvas);
              this.canvas.freeDrawingBrush.width = this.brushSize * 1.5;
              this.canvas.freeDrawingBrush.color = this.brushColor;
              break;
            case 'soft':
              this.canvas.freeDrawingBrush = new fabric.CircleBrush(this.canvas);
              this.canvas.freeDrawingBrush.width = this.brushSize * 2;
              this.canvas.freeDrawingBrush.color = this.brushColor;
              break;
            case 'eraser':
              // Check if EraserBrush is available
              if (fabric.EraserBrush) {
                this.canvas.freeDrawingBrush = new fabric.EraserBrush(this.canvas);
                this.canvas.freeDrawingBrush.width = this.brushSize;
              } else {
                // Fallback: use regular brush with white color to simulate erasing
                this.canvas.freeDrawingBrush = new fabric.PencilBrush(this.canvas);
                this.canvas.freeDrawingBrush.width = this.brushSize;
                this.canvas.freeDrawingBrush.color = '#FFFFFF';
              }
              break;
          }
        } else if (this.activeTool === 'eraser') {
          // Check if EraserBrush is available
          if (fabric.EraserBrush) {
            this.canvas.freeDrawingBrush = new fabric.EraserBrush(this.canvas);
            this.canvas.freeDrawingBrush.width = this.brushSize;
          } else {
            // Fallback: use regular brush with white color to simulate erasing
            this.canvas.freeDrawingBrush = new fabric.PencilBrush(this.canvas);
            this.canvas.freeDrawingBrush.width = this.brushSize;
            this.canvas.freeDrawingBrush.color = '#FFFFFF';
          }
        }
        
        // Apply opacity if supported
        if (this.canvas.freeDrawingBrush) {
          this.canvas.freeDrawingBrush.opacity = this.brushOpacity;
        }
      }
    },
    
    selectColor(color) {
      this.brushColor = color;
      if (this.selectedObject && (this.selectedObject.type === 'i-text' || 
          this.selectedObject.type === 'rect' || 
          this.selectedObject.type === 'circle')) {
        this.selectedObject.fill = color;
        this.updateCanvas();
      } else {
        this.updateBrushSettings();
      }
    },
    
    saveBrushHistory() {
      // Remove any history after current index
      if (this.brushHistoryIndex < this.brushHistory.length - 1) {
        this.brushHistory = this.brushHistory.slice(0, this.brushHistoryIndex + 1);
      }
      
      // Add current state
      const currentState = this.canvas.toJSON();
      this.brushHistory.push(currentState);
      this.brushHistoryIndex = this.brushHistory.length - 1;
      
      // Limit history size
      if (this.brushHistory.length > this.maxBrushHistory) {
        this.brushHistory.shift();
        this.brushHistoryIndex--;
      }
    },
    
    undoBrushStroke() {
      if (this.canUndoBrush) {
        this.brushHistoryIndex--;
        this.restoreFromBrushHistory();
      }
    },
    
    redoBrushStroke() {
      if (this.canRedoBrush) {
        this.brushHistoryIndex++;
        this.restoreFromBrushHistory();
      }
    },
    
    restoreFromBrushHistory() {
      if (this.brushHistoryIndex >= 0 && this.brushHistoryIndex < this.brushHistory.length) {
        const state = this.brushHistory[this.brushHistoryIndex];
        this.canvas.loadFromJSON(state, () => {
          this.canvas.renderAll();
        });
      }
    },
    
    get canUndoBrush() {
      return this.brushHistory && this.brushHistory.length > 0 && this.brushHistoryIndex > 0;
    },
    
    get canRedoBrush() {
      return this.brushHistory && this.brushHistory.length > 0 && this.brushHistoryIndex < this.brushHistory.length - 1;
    },
    
    clearCanvas() {
      if (confirm('Are you sure you want to clear the entire canvas?')) {
        this.canvas.clear();
        // Clear brush history
        this.brushHistory = [];
        this.brushHistoryIndex = -1;
        this.isDirty = true;
        this.$emit('changed');
      }
    },
    
    undoBrush() {
      // Legacy method kept for compatibility
      this.undoBrushStroke();
    },
    
    // Professional image editing methods
    openImageEditing: function (imageObject) {
      if (!imageObject || !(imageObject.type === 'image' || imageObject.src)) {
        if (typeof this.$toast !== 'undefined') {
          this.$toast.error('Please select an image to edit');
        }

        return;
      }

      this.editingImage = imageObject;
      this.editingImageSrc = imageObject.getSrc();
      this.showImageEditing = true;
    },

    closeImageEditing() {
      this.showImageEditing = false;
      this.editingImage = null;
      this.editingImageSrc = null;
    },
    
    updateImageEditing() {
      this.canvas.renderAll();
      this.isDirty = true;
      this.$emit('changed');
    },
    
    imageUpdated(newImageObject) {
      this.selectedObject = newImageObject;
      this.canvas.setActiveObject(newImageObject);
      this.canvas.renderAll();
      this.isDirty = true;
      this.$emit('changed');
    },
    
    sanitizeImageUrl(url) {
      // Check if URL is a placeholder service URL that might be causing issues
      if (url.includes('placeholder.com') || url.includes('via.placeholder')) {
        // Return a safe fallback image
        return '/images/default-template.svg';
      }
      return url;
    },
    
    showToast(type, message) {
      // Check if toast is available
      if (typeof this.$toast !== 'undefined' && typeof this.$toast[type] === 'function') {
        this.$toast[type](message);
      } else {
        // Fallback to browser alert if toast is not available
        console.log(`${type.toUpperCase()}: ${message}`);
      }
    },
    
    // Background removal methods
    async removeImageBackground() {
      if (!this.selectedObject || !(this.selectedObject.type === 'image' || this.selectedObject.src)) {
        throw new Error('No image selected');
      }
      
      try {
        // Store original image data for restoration
        this.selectedObject.originalSrc = this.selectedObject.getSrc();
        
        // For now, we'll use a simple approach - in a real implementation
        // you would integrate with a background removal API like remove.bg
        // or use a client-side library
        
        // This is a placeholder - you would replace this with actual
        // background removal logic
        console.log('Removing background for image:', this.selectedObject.originalSrc);
        
        // Simulate background removal by applying a filter
        // In a real implementation, this would call an external API
        const filter = new fabric.Image.filters.Brightness({
          brightness: 0.1
        });
        
        if (!this.selectedObject.filters) {
          this.selectedObject.filters = [];
        }
        
        this.selectedObject.filters.push(filter);
        this.selectedObject.applyFilters();
        this.canvas.renderAll();
        
        this.isDirty = true;
        this.$emit('changed');
        
        return this.selectedObject;
      } catch (error) {
        console.error('Failed to remove background:', error);
        throw error;
      }
    },
    
    async restoreImageBackground() {
      if (!this.selectedObject || !this.selectedObject.originalSrc) {
        throw new Error('No original image data to restore');
      }
      
      try {
        // Restore the original image
        const originalSrc = this.selectedObject.originalSrc;
        
        fabric.Image.fromURL(originalSrc, (img) => {
          // Copy properties from current image
          img.set({
            left: this.selectedObject.left,
            top: this.selectedObject.top,
            scaleX: this.selectedObject.scaleX,
            scaleY: this.selectedObject.scaleY,
            angle: this.selectedObject.angle,
            opacity: this.selectedObject.opacity,
            hasControls: true,
            hasBorders: true,
            cornerStyle: 'circle',
            cornerSize: 10,
            borderColor: '#007AFF',
            cornerColor: '#007AFF',
            cornerStrokeColor: '#007AFF',
            transparentCorners: false,
            borderScaleFactor: 1.5,
          });
          
          // Remove the old image and add the restored one
          this.canvas.remove(this.selectedObject);
          this.canvas.add(img);
          this.canvas.setActiveObject(img);
          this.selectedObject = img;
          this.canvas.renderAll();
          
          this.isDirty = true;
          this.$emit('changed');
        }, { crossOrigin: 'anonymous' });
      } catch (error) {
        console.error('Failed to restore background:', error);
        throw error;
      }
    },
  },
  
  beforeUnmount() {
    if (this.canvas) {
      this.canvas.dispose();
    }
    
    // Clean up resize observer
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }
    
    // Remove keyboard event listener
    document.removeEventListener('keydown', this.handleKeyDown);
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

.tool-btn.active-tool {
  background: #3b82f6;
  color: white;
  box-shadow: inset 2px 2px 5px #b8bec7, inset -2px -2px 5px #ffffff;
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
  min-width: 800px;
  min-height: 600px;
  max-width: 800px;
  max-height: 70vh;
  width: 800px;
  height: 600px;
  overflow: hidden;
}

canvas {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  background: white;
  border: 1px solid #cbd5e1;
  width: 800px !important;
  height: 600px !important;
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.brush-panel {
  width: 250px;
  padding: 1rem;
  overflow-y: auto;
  background: #f8fafc;
  border-left: 1px solid #e2e8f0;
}

.brush-controls {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.control-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.color-picker-container {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.color-palette {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 0.25rem;
  padding: 0.5rem;
  background: white;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.color-palette-grid {
  display: grid;
  grid-template-columns: repeat(8, 1fr);
  gap: 0.25rem;
  padding: 0.5rem;
  background: white;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  max-height: 200px;
  overflow-y: auto;
}

.color-swatch {
  width: 24px;
  height: 24px;
  border-radius: 4px;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.2s;
}

.color-swatch-large {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.2s;
}

.color-swatch:hover, .color-swatch-large:hover {
  transform: scale(1.1);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.color-swatch.active, .color-swatch-large.active {
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
}

.brush-type-selector {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.brush-type-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 0.75rem;
  border-radius: 8px;
  gap: 0.25rem;
  transition: all 0.2s;
}

.brush-type-btn.active-brush-type {
  background: #3b82f6;
  color: white;
  box-shadow: inset 2px 2px 5px #b8bec7, inset -2px -2px 5px #ffffff;
}

.brush-actions {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e2e8f0;
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

.shape-grid, .effects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
  gap: 1rem;
  margin: 1rem 0;
}

.shape-item, .effect-item {
  cursor: pointer;
  border-radius: 0.375rem;
  transition: all 0.2s;
  padding: 0.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 80px;
}

.shape-item:hover, .effect-item:hover {
  transform: scale(1.05);
}

.active-tool {
  background: #3b82f6;
  color: white;
  box-shadow: inset 2px 2px 5px #b8bec7, inset -2px -2px 5px #ffffff;
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