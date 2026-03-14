<template>
  <div class="designer-container">


    <!-- Main Design Area -->
    <div class="design-area">
      <!-- Main Canvas -->
      <div class="canvas-container">
        <div class="canvas-wrapper">
          <canvas ref="canvas"></canvas>
        </div>
      </div>
    </div>



    <!-- Modals -->
    <div v-if="showCliparts" class="modal-overlay" @click.self="showCliparts = false">
      <div class="modal">
        <div class="modal-header">
          <h3>Choose Clipart</h3>
          <button @click="showCliparts = false" class="close-btn">×</button>
        </div>
        <div class="modal-content">
          <div class="clipart-grid">
            <div 
              v-for="clipart in cliparts" 
              :key="clipart.id"
              class="clipart-item"
              @click="addClipart(clipart)"
            >
              <img :src="clipart.image_url" :alt="clipart.title">
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div v-if="showShapes" class="modal-overlay" @click.self="showShapes = false">
      <div class="modal">
        <div class="modal-header">
          <h3>Choose Shape</h3>
          <button @click="showShapes = false" class="close-btn">×</button>
        </div>
        <div class="modal-content">
          <div class="shape-grid">
            <div 
              v-for="shape in shapes" 
              :key="shape.type"
              class="shape-item"
              @click="addShape(shape.type)"
            >
              <span class="shape-icon">{{ shape.icon }}</span>
              <span class="shape-name">{{ shape.name || shape.type }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Effects Modal -->
    <div v-if="showEffects" class="modal-overlay" @click.self="showEffects = false">
      <div class="modal">
        <div class="modal-header">
          <h3>Apply Effect</h3>
          <button @click="showEffects = false" class="close-btn">×</button>
        </div>
        <div class="modal-content">
          <div class="effects-grid">
            <div 
              v-for="effect in effects" 
              :key="effect.type"
              class="effect-item"
              @click="applyEffect(effect.type)"
            >
              <span class="effect-icon">{{ effect.icon }}</span>
              <span class="effect-name">{{ effect.name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Professional Image Editing Panel -->
    <div v-if="showImageEditing" class="modal-overlay" @click.self="closeImageEditing">
      <div class="modal modal-large">
        <div class="modal-header">
          <h3>Edit Image</h3>
          <button @click="closeImageEditing" class="close-btn">×</button>
        </div>
        <div class="modal-content">
          <ImageEditingPanel
            :image-object="editingImage"
            :image-src="editingImageSrc"
            @update="updateImageEditing"
            @image-updated="imageUpdated"
            @close="closeImageEditing"
          />
        </div>
      </div>
    </div>
    
    <input 
      ref="imageInput" 
      type="file" 
      accept="image/*" 
      style="display: none"
      @change="handleImageUpload"
    />
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
      // Removed previewCanvas since preview panel is removed
      selectedObject: null,
      fonts: [],
      cliparts: [],
      shapes: [
        { type: 'rectangle', icon: '⬜', name: 'Rectangle' },
        { type: 'circle', icon: '⭕', name: 'Circle' },
        { type: 'triangle', icon: '🔺', name: 'Triangle' },
        { type: 'polygon', icon: '🔷', name: 'Polygon' },
        { type: 'line', icon: '📏', name: 'Line' },
      ],
      effects: [
        { type: 'blur', name: 'Blur', icon: '🌫️' },
        { type: 'shadow', name: 'Shadow', icon: '🌑' },
        { type: 'glow', name: 'Glow', icon: '✨' },
        { type: 'outline', name: 'Outline', icon: '⭕' },
      ],
      // Tool state management
      isDirty: false,
      currentDesignId: null,
      saveTimeout: null,
      showImageEditing: false,
      editingImage: null,
      editingImageSrc: null,
      
      // Color Palette Tabs
      activeColorTab: 'basic',
      colorPaletteTabs: [
        { id: 'basic', name: 'Basic' },
        { id: 'advanced', name: 'Advanced' },
        { id: 'gradient', name: 'Gradients' },
        { id: 'metallic', name: 'Metallic' }
      ],
      
      // Basic Colors - Simplified palette
      basicColors: [
        '#000000', '#FFFFFF', '#FF0000', '#00FF00', '#0000FF',
        '#FFFF00', '#FF00FF', '#00FFFF', '#FFA500', '#800080',
        '#FFC0CB', '#A52A2A', '#808080', '#C0C0C0', '#800000',
        '#008000', '#000080', '#808000', '#008080'
      ],
      
      // Advanced Colors - Extended palette
      advancedColors: [
        '#FFB6C1', '#FFD700', '#FF6347', '#40E0D0', '#9370DB',
        '#2F4F4F', '#000080', '#4B0082', '#8B0000', '#006400',
        '#DC143C', '#008B8B', '#483D8B', '#FF8C00', '#FF1493',
        '#00BFFF', '#1E90FF', '#9ACD32', '#FFA07A', '#20B2AA',
        '#87CEEB', '#DDA0DD', '#F0E68C', '#98FB98', '#F5DEB3',
        '#DEB887', '#FA8072', '#E6E6FA', '#FFFACD', '#FFE4C4'
      ],
      
      // Gradient Colors
      gradientColors: [
        'linear-gradient(45deg, #ff0000, #ffff00)',
        'linear-gradient(45deg, #ff00ff, #00ffff)',
        'linear-gradient(45deg, #00ff00, #0000ff)',
        'linear-gradient(45deg, #ff6b6b, #feca57)',
        'linear-gradient(45deg, #5f27cd, #00d2d3)',
        'linear-gradient(45deg, #ee5a24, #f368e0)',
        'linear-gradient(45deg, #10ac84, #1dd1a1)',
        'linear-gradient(45deg, #54a0ff, #5f27cd)',
        'linear-gradient(to bottom, #ff0000, #0000ff)',
        'linear-gradient(to bottom, #ffd700, #ff8c00)'
      ],
      
      // Metallic Colors
      metallicColors: [
        '#D4AF37', // Gold
        '#C0C0C0', // Silver
        '#CD7F32', // Bronze
        '#E5E4E2', // Platinum
        '#B5A642', // Old Gold
        '#D3D3D3', // Light Silver
        '#CFB53B', // Old Brass
        '#DA8A67'  // Copper
      ],
      
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
      ],
      
      // General history for all canvas operations
      generalHistory: [],
      generalHistoryIndex: -1,
      maxGeneralHistory: 50,
      
      // Reactive properties for parent component access
      canUndoValue: false,
      canRedoValue: false,
      canUndoBrushValue: false,
      canRedoBrushValue: false,
      
      // Tool state management
      activeTool: 'select',
      showCliparts: false,
      showShapes: false,
      showEffects: false
    };
  },
  
  async mounted() {
    console.log('ProductDesigner mounted');
    this.initializeCanvas();
    // Removed preview canvas initialization since preview panel is removed
    await this.loadAssets();
    
    if (this.initialDesign) {
      this.currentDesignId = this.initialDesign.id;
      this.loadDesign(this.initialDesign);
    } else {
      // Initialize history for empty canvas
      this.$nextTick(() => {
        this.saveToGeneralHistory();
      });
    }
    
    // Initialize reactive properties
    this.updateReactiveProperties();
    
    // Set up interval to periodically update reactive properties
    setInterval(() => {
      this.updateReactiveProperties();
    }, 100); // Update every 100ms
    
    // Add event listener for window resize to handle responsiveness
    window.addEventListener('resize', this.handleResize);
    
    console.log('ProductDesigner initialized with canvas:', this.canvas);
  },
  
  methods: {
    // Public API methods for parent component access
    getCanUndo() {
      return this.canUndoGeneral();
    },
    
    getCanRedo() {
      return this.canRedoGeneral();
    },
    
    getCanUndoBrush() {
      return this.canUndoBrush;
    },
    
    getCanRedoBrush() {
      return this.canRedoBrush;
    },
    
    // Debug method to expose canvas
    getCanvas() {
      return this.canvas;
    },
    
    getActiveTool() {
      return this.activeTool;
    },
    
    // Wrapper methods for parent component access
    handleUndo() {
      this.undo();
    },
    
    handleRedo() {
      this.redo();
    },
    
    handleUndoBrush() {
      this.undoBrushStroke();
    },
    
    handleRedoBrush() {
      this.redoBrushStroke();
    },
    
    // Direct method implementations for parent component access
    canUndo() {
      return this.canUndoGeneral();
    },
    
    canRedo() {
      return this.canRedoGeneral();
    },
    
    canUndoBrush() {
      return this.brushHistory && this.brushHistory.length > 0 && this.brushHistoryIndex > 0;
    },
    
    canRedoBrush() {
      return this.brushHistory && this.brushHistory.length > 0 && this.brushHistoryIndex < this.brushHistory.length - 1;
    },
    
    updateReactiveProperties() {
      this.canUndoValue = this.canUndoGeneral();
      this.canRedoValue = this.canRedoGeneral();
      this.canUndoBrushValue = this.brushHistory && this.brushHistory.length > 0 && this.brushHistoryIndex > 0;
      this.canRedoBrushValue = this.brushHistory && this.brushHistory.length > 0 && this.brushHistoryIndex < this.brushHistory.length - 1;
    },
    
    updatePreviewCanvas() {
      // Preview functionality removed - no preview panel
      // This method is kept for compatibility but does nothing
    },
    
    initializePreviewCanvas() {
      // Preview functionality removed - no preview panel
      // This method is kept for compatibility but does nothing
    },
    
    initializeCanvas() {
      // Use responsive canvas sizing
      const container = this.$refs.canvas?.parentElement;
      let width = 800;
      let height = 600;
      
      // If container exists, try to get responsive dimensions
      if (container) {
        const rect = container.getBoundingClientRect();
        width = Math.min(rect.width || 800, 800);
        height = Math.min(rect.height || 600, 600);
      }
      
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
        // Only change to select tool if not in brush/eraser mode
        if (this.activeTool !== 'brush' && this.activeTool !== 'eraser') {
          this.activeTool = 'select';
        }
      });
      
      // Initialize default tool state
      this.canvas.isDrawingMode = false;
      this.activeTool = 'select';
      this.updateCanvasCursor();
      this.canvas.on('object:modified', (e) => {
        this.isDirty = true;
        // Save to general history for any object modification
        this.saveToGeneralHistory();
        // Update selected object properties after modification
        if (this.selectedObject && e.target) {
          // Sync the selected object reference with the modified object
          this.selectedObject = e.target;
          this.$forceUpdate();
        }
        this.updateReactiveProperties(); // Update reactive properties
        this.updatePreviewCanvas(); // Update preview
      });
      this.canvas.on('object:added', (e) => {
        this.isDirty = true;
        this.saveToGeneralHistory();
        this.updateReactiveProperties(); // Update reactive properties
        this.updatePreviewCanvas(); // Update preview
      });
      this.canvas.on('object:removed', (e) => {
        this.isDirty = true;
        this.saveToGeneralHistory();
        this.updateReactiveProperties(); // Update reactive properties
        this.updatePreviewCanvas(); // Update preview
      });
      
      // Update properties panel when object changes
      this.canvas.on('object:modified', () => {
        if (this.selectedObject) {
          this.selectedObject.set({ dirty: true });
          this.canvas.requestRenderAll();
        }
      });
      
      // Listen for path creation events to save brush history
      this.canvas.on('path:created', (opt) => {
        this.saveBrushHistory();
        this.saveToGeneralHistory(); // Also save to general history
        this.updateReactiveProperties(); // Update reactive properties
      });
      
      // Listen for mouse up to save state after drawing
      this.canvas.on('mouse:up', () => {
        if (this.canvas.isDrawingMode) {
          this.saveToGeneralHistory();
          this.updateReactiveProperties(); // Update reactive properties
        }
      });
      
      // Keyboard shortcuts
      document.addEventListener('keydown', this.handleKeyDown);
      
      // Enable drawing mode for freehand
      this.canvas.isDrawingMode = false;
      
      // Initialize history with the initial canvas state
      this.$nextTick(() => {
        this.saveToGeneralHistory();
      });
    },
    
    handleSelection(e) {
      if (e.selected && e.selected.length > 0) {
        const selected = e.selected[0];
        this.selectedObject = selected;
        
        // Ensure proper initialization of properties and controls
        const commonProperties = {
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
          hasRotatingPoint: true
        };
        
        selected.set(commonProperties);
        
        // Type-specific initialization
        if (selected.type === 'i-text') {
          // Text properties
          if (!selected.fontWeight) selected.fontWeight = 'normal';
          if (!selected.fontSize) selected.fontSize = 32;
          if (!selected.lineHeight) selected.lineHeight = 1.2;
          selected.editable = true;
          
          // Force text to be editable immediately
          this.$nextTick(() => {
            selected.enterEditing();
          });
        } else if (selected.type === 'image' || selected.src) {
          // Image properties
          if (!selected.scaleX) selected.scaleX = 1;
          if (!selected.scaleY) selected.scaleY = 1;
          if (!selected.opacity) selected.opacity = 1;
        } else if (['rect', 'circle', 'triangle', 'polygon'].includes(selected.type)) {
          // Shape properties
          if (!selected.fill) selected.fill = '#FF0000';
          if (!selected.stroke) selected.stroke = '#000000';
          if (!selected.strokeWidth) selected.strokeWidth = 2;
        }
        
        // Update object coordinates and render
        selected.setCoords();
        this.canvas.requestRenderAll();
        this.isDirty = true;
        this.$emit('changed');
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
        selectable: true,
        editable: true,
        evented: true,
        cornerStyle: 'circle',
        cornerSize: 10,
        borderColor: '#007AFF',
        cornerColor: '#007AFF',
        cornerStrokeColor: '#007AFF',
        transparentCorners: false,
        borderScaleFactor: 1.5,
        hasRotatingPoint: true,
      });
      
      this.canvas.add(text);
      this.canvas.setActiveObject(text);
      this.isDirty = true;
      this.$emit('changed');
      
      // Force immediate edit mode
      this.$nextTick(() => {
        if (this.canvas.getActiveObject() === text) {
          text.enterEditing();
          text.selectAll();
        }
      });
    },
    
    setActiveTool(tool) {
      console.log('Setting active tool:', tool, 'current tool:', this.activeTool);
      
      // Save current state to general history before switching tools
      this.saveToGeneralHistory();
      
      // Disable current drawing mode
      this.canvas.isDrawingMode = false;
      
      this.activeTool = tool;
      
      // Update canvas cursor
      this.updateCanvasCursor();
      
      // Enable appropriate mode based on tool
      if (tool === 'brush') {
        this.canvas.isDrawingMode = true;
        this.updateBrushSettings();
        this.canvas.freeDrawingBrush = new fabric.PencilBrush(this.canvas);
        this.canvas.freeDrawingBrush.width = this.brushSize;
        this.canvas.freeDrawingBrush.color = this.brushColor;
        this.canvas.freeDrawingBrush.opacity = this.brushOpacity;
        console.log('Brush mode activated with size:', this.brushSize, 'color:', this.brushColor);
      } else if (tool === 'eraser') {
        this.canvas.isDrawingMode = true;
        // Check if EraserBrush is available
        if (fabric.EraserBrush) {
          this.canvas.freeDrawingBrush = new fabric.EraserBrush(this.canvas);
          this.canvas.freeDrawingBrush.width = this.brushSize;
        } else {
          // Fallback: use regular brush with white color
          this.canvas.freeDrawingBrush = new fabric.PencilBrush(this.canvas);
          this.canvas.freeDrawingBrush.width = this.brushSize;
          this.canvas.freeDrawingBrush.color = '#FFFFFF';
        }
        this.canvas.freeDrawingBrush.opacity = this.brushOpacity;
        console.log('Eraser mode activated with size:', this.brushSize);
      } else if (tool === 'text') {
        // Text tool mode - add text immediately
        console.log('Text tool activated - adding text to canvas');
        this.addText();
      } else if (tool === 'image') {
        // Image tool mode - trigger image upload
        console.log('Image tool activated - triggering image upload');
        this.triggerImageUpload();
      } else {
        console.log('Selection mode activated');
      }
      
      this.$emit('changed');
      this.updateReactiveProperties();
    },
    
    updateCanvasCursor() {
      const canvasElement = this.$refs.canvas;
      if (canvasElement) {
        if (this.canvas.isDrawingMode) {
          canvasElement.classList.add('drawing-mode');
        } else {
          canvasElement.classList.remove('drawing-mode');
        }
      }
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
      // Ensure we have the current state in history
      this.saveToGeneralHistory();
      
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
      // Clear any existing history before loading new design
      this.generalHistory = [];
      this.generalHistoryIndex = -1;
      
      this.canvas.loadFromJSON(design.design_data, () => {
        this.canvas.renderAll();
        // Initialize history after loading the design
        this.saveToGeneralHistory();
      });
    },
    
    async loadTemplate(template) {
      // Load the template design data onto the canvas
      try {
        console.log('Loading template:', template);
        
        // Check if template has design_data property (JSON format)
        if (template.design_data && Array.isArray(template.design_data) && template.design_data.length > 0) {
          // Clear any existing history before loading new template
          this.generalHistory = [];
          this.generalHistoryIndex = -1;
          
          // Load the full design data
          console.log('Loading design data from template');
          this.canvas.loadFromJSON(template.design_data, () => {
            this.canvas.renderAll();
            this.isDirty = true;
            this.$emit('changed');
            // Initialize history after loading the template
            this.saveToGeneralHistory();
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
            // Initialize history after adding template image
            this.saveToGeneralHistory();
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
        this.updateBrushSettings();
      } else {
        this.canvas.isDrawingMode = false;
      }
    },
    
    toggleEraserMode() {
      if (this.activeTool === 'eraser') {
        this.canvas.isDrawingMode = true;
        this.updateBrushSettings();
      } else {
        this.canvas.isDrawingMode = false;
      }
    },
    
    setBrushType(type) {
      this.brushType = type;
      this.updateBrushSettings();
    },
    
    updateBrushSettings() {
      if (this.canvas && this.canvas.freeDrawingBrush) {
        // Update brush properties
        this.canvas.freeDrawingBrush.width = this.brushSize;
        this.canvas.freeDrawingBrush.color = this.brushColor;
        this.canvas.freeDrawingBrush.opacity = this.brushOpacity;
        
        // Set specific brush type
        if (this.activeTool === 'brush') {
          switch(this.brushType) {
            case 'pencil':
              this.canvas.freeDrawingBrush = new fabric.PencilBrush(this.canvas);
              break;
            case 'spray':
              this.canvas.freeDrawingBrush = new fabric.SprayBrush(this.canvas);
              break;
            case 'marker':
              this.canvas.freeDrawingBrush = new fabric.PencilBrush(this.canvas);
              this.canvas.freeDrawingBrush.width = this.brushSize * 1.5;
              break;
            case 'soft':
              this.canvas.freeDrawingBrush = new fabric.CircleBrush(this.canvas);
              this.canvas.freeDrawingBrush.width = this.brushSize * 2;
              break;
          }
          
          // Apply current settings to the new brush
          this.canvas.freeDrawingBrush.width = this.brushSize;
          this.canvas.freeDrawingBrush.color = this.brushColor;
          this.canvas.freeDrawingBrush.opacity = this.brushOpacity;
        }
        
        this.isDirty = true;
        this.$emit('changed');
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
    
    selectGradientColor(gradient) {
      // Extract first color from gradient for brush
      const colorMatch = gradient.match(/#[0-9a-fA-F]{3,6}/);
      if (colorMatch) {
        this.brushColor = colorMatch[0];
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
      
      // Update reactive properties
      this.updateReactiveProperties();
    },
    
    undoBrushStroke() {
      if (this.canUndoBrush()) {
        this.brushHistoryIndex--;
        this.restoreFromBrushHistory();
        this.updateReactiveProperties();
      }
    },
    
    redoBrushStroke() {
      if (this.canRedoBrush()) {
        this.brushHistoryIndex++;
        this.restoreFromBrushHistory();
        this.updateReactiveProperties();
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
    

    
    // General undo/redo methods that handle all canvas operations
    canUndoGeneral() {
      // Check if we have any general history entries
      return this.generalHistory && this.generalHistory.length > 0 && this.generalHistoryIndex > 0;
    },
    
    canRedoGeneral() {
      return this.generalHistory && this.generalHistory.length > 0 && this.generalHistoryIndex < this.generalHistory.length - 1;
    },
    
    saveToGeneralHistory() {
      // Remove any history after current index
      if (this.generalHistoryIndex < this.generalHistory.length - 1) {
        this.generalHistory = this.generalHistory.slice(0, this.generalHistoryIndex + 1);
      }
      
      // Add current state
      const currentState = this.canvas.toJSON();
      this.generalHistory.push(currentState);
      this.generalHistoryIndex = this.generalHistory.length - 1;
      
      // Limit history size
      if (this.generalHistory.length > this.maxGeneralHistory) {
        this.generalHistory.shift();
        this.generalHistoryIndex--;
      }
      
      // Update reactive properties
      this.updateReactiveProperties();
    },
    
    undoGeneral() {
      if (this.canUndoGeneral()) {
        this.generalHistoryIndex--;
        this.restoreFromGeneralHistory();
        this.updateReactiveProperties();
      }
    },
    
    redoGeneral() {
      if (this.canRedoGeneral()) {
        this.generalHistoryIndex++;
        this.restoreFromGeneralHistory();
        this.updateReactiveProperties();
      }
    },
    
    restoreFromGeneralHistory() {
      if (this.generalHistoryIndex >= 0 && this.generalHistoryIndex < this.generalHistory.length) {
        const state = this.generalHistory[this.generalHistoryIndex];
        this.canvas.loadFromJSON(state, () => {
          this.canvas.renderAll();
        });
      }
    },
    
    // Unified undo/redo methods that handle both brush and general operations
    undo() {
      // Try general history first (more comprehensive)
      if (this.canUndoGeneral()) {
        this.undoGeneral();
      } else if (this.canUndoBrush()) {
        this.undoBrushStroke();
      }
    },
    
    redo() {
      // Try general history first (more comprehensive)
      if (this.canRedoGeneral()) {
        this.redoGeneral();
      } else if (this.canRedoBrush()) {
        this.redoBrushStroke();
      }
    },
    
    clearCanvas() {
      if (confirm('Are you sure you want to clear the entire canvas?')) {
        this.canvas.clear();
        // Clear brush history
        this.brushHistory = [];
        this.brushHistoryIndex = -1;
        // Also clear general history since we're starting fresh
        this.generalHistory = [];
        this.generalHistoryIndex = -1;
        this.isDirty = true;
        this.$emit('changed');
        
        // Initialize history for empty canvas
        this.saveToGeneralHistory();
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
    
    // Expose unified undo/redo methods for external access via refs
    undo() {
      this.undoGeneral();
    },
    
    redo() {
      this.redoGeneral();
    },
    
    // Properly expose methods for parent component access
    canUndo() {
      return this.canUndoGeneral();
    },
    
    canRedo() {
      return this.canRedoGeneral();
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
    
    // Handle window resize for responsiveness
    handleResize() {
      // Only resize canvas if it exists
      if (this.canvas) {
        // Get the container dimensions
        const container = this.$refs.canvas?.parentElement;
        if (container) {
          const rect = container.getBoundingClientRect();
          
          // Update canvas dimensions if they've changed significantly
          if (Math.abs(this.canvas.width - rect.width) > 10 || Math.abs(this.canvas.height - rect.height) > 10) {
            // Preserve the aspect ratio
            const canvasRatio = this.canvas.width / this.canvas.height;
            const containerRatio = rect.width / rect.height;
            
            let newWidth, newHeight;
            if (containerRatio > canvasRatio) {
              // Container is wider than canvas - limit by height
              newHeight = Math.min(rect.height, 600);
              newWidth = newHeight * canvasRatio;
            } else {
              // Container is taller than canvas - limit by width
              newWidth = Math.min(rect.width, 800);
              newHeight = newWidth / canvasRatio;
            }
            
            // Update canvas dimensions
            this.canvas.setWidth(newWidth);
            this.canvas.setHeight(newHeight);
            
            // Update the canvas element itself
            const canvasEl = this.$refs.canvas;
            canvasEl.width = newWidth;
            canvasEl.height = newHeight;
            
            // Preview canvas functionality removed - no preview panel
            
            // Save to history after resize
            this.$nextTick(() => {
              this.saveToGeneralHistory();
            });
          }
        }
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
    
    // Remove window resize listener
    window.removeEventListener('resize', this.handleResize);
  },
};
</script>

<style scoped>
.designer-container {
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 0;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  position: relative;
  overflow: hidden;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Modern Toolbar Header */
.toolbar-header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  backdrop-filter: blur(10px);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  padding: 0.75rem 1.5rem;
  flex-shrink: 0;
}

.toolbar-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 100%;
  margin: 0 auto;
  gap: 1rem;
}

.tool-group {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.tool-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  border: none;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.15);
  color: white;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: hidden;
}

.tool-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 100%);
  opacity: 0;
  transition: opacity 0.3s;
}

.tool-btn:hover::before {
  opacity: 1;
}

.tool-btn:hover {
  transform: translateY(-2px);
  background: rgba(255, 255, 255, 0.25);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.tool-btn.active {
  background: white;
  color: #667eea;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  transform: translateY(0);
}

.tool-btn .tool-icon {
  width: 22px;
  height: 22px;
  z-index: 1;
}

.tool-btn.primary {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.tool-btn.primary:hover {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.tool-btn.success {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.tool-btn.success:hover {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.actions-group {
  display: flex;
  gap: 0.75rem;
  padding-left: 1rem;
  border-left: 1px solid rgba(255, 255, 255, 0.2);
}

/* Main Design Area - Full width canvas */
.design-area {
  display: flex;
  padding: 1.5rem;
  flex: 1;
  overflow: hidden;
  min-height: 0;
  position: relative;
  height: calc(100vh - 40px); /* Account for reduced header */
}

/* Canvas Container - Full width */
.canvas-container {
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  flex: 1;
  min-height: 0;
  width: 100%;
}

.canvas-wrapper {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.5);
  transition: all 0.3s ease;
  width: 100%;
  height: 100%;
  max-width: 100%;
  max-height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.canvas-wrapper:hover {
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.18);
  transform: translateY(-2px);
}

canvas {
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  background: white;
  cursor: crosshair;
  max-width: 100%;
  max-height: 100%;
  display: block;
  width: 100%;
  height: 100%;
  object-fit: contain;
}

canvas.drawing-mode {
  cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" fill="black" opacity="0.7"/></svg>') 12 12, crosshair;
}



@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.brush-panel::-webkit-scrollbar {
  width: 6px;
}

.brush-panel::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.brush-panel::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 10px;
}

.brush-control {
  margin-bottom: 1.25rem;
}

.brush-control label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: #4a5568;
  margin-bottom: 0.5rem;
}

.brush-slider {
  width: 100%;
  height: 6px;
  border-radius: 3px;
  background: #e2e8f0;
  outline: none;
  -webkit-appearance: none;
}

.brush-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.brush-types {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.5rem;
  margin: 1rem 0;
}

.brush-type-btn {
  padding: 0.75rem;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.875rem;
  font-weight: 500;
}

.brush-type-btn:hover {
  border-color: #667eea;
  transform: translateY(-2px);
}

.brush-type-btn.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-color: #667eea;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.brush-actions {
  margin-top: 1.5rem;
  padding-top: 1.25rem;
  border-top: 2px solid #f0f0f0;
}

.undo-redo {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.75rem;
}

.action-btn {
  flex: 1;
  padding: 0.625rem 1rem;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
  color: #4a5568;
}

.action-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.action-btn.danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  width: 100%;
  margin-bottom: 0.75rem;
}

.action-btn.danger:hover {
  background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.brush-panel.enhanced {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-left: none;
  box-shadow: -4px 0 15px rgba(0,0,0,0.1);
}

.brush-panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid rgba(255,255,255,0.2);
}

.brush-panel-header h4 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: bold;
  display: flex;
  align-items: center;
}

.close-panel-btn {
  background: rgba(255,255,255,0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.close-panel-btn:hover {
  background: rgba(255,255,255,0.3);
  transform: rotate(90deg);
}

.control-group.enhanced {
  background: rgba(255,255,255,0.1);
  padding: 1rem;
  border-radius: 12px;
  backdrop-filter: blur(10px);
  margin-bottom: 1rem;
}

.control-label {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.value-badge {
  background: rgba(255,255,255,0.2);
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: bold;
}

.slider-container {
  position: relative;
  margin-bottom: 0.5rem;
}

.enhanced-slider {
  width: 100%;
  height: 8px;
  border-radius: 4px;
  background: rgba(255,255,255,0.2);
  outline: none;
  -webkit-appearance: none;
}

.enhanced-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: white;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

.enhanced-slider::-moz-range-thumb {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: white;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  border: none;
}

.slider-indicator {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  pointer-events: none;
}

.size-preview {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 60px;
  background: rgba(255,255,255,0.1);
  border-radius: 8px;
  margin-top: 0.5rem;
}

.size-dot {
  border-radius: 50%;
  transition: all 0.3s;
}

.opacity-preview {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 60px;
  background: rgba(255,255,255,0.1);
  border-radius: 8px;
  margin-top: 0.5rem;
}

.opacity-dot {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  transition: all 0.3s;
}

.color-section .control-label {
  flex-direction: column;
  align-items: flex-start;
  gap: 0.5rem;
}

.current-color-display {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: rgba(255,255,255,0.1);
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  width: 100%;
}

.color-preview-circle {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.color-hex-code {
  font-family: 'Courier New', monospace;
  font-size: 0.875rem;
  font-weight: bold;
  text-transform: uppercase;
}

.enhanced-color-picker {
  width: 100%;
  height: 50px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  background: transparent;
}

.color-picker-label {
  font-size: 0.875rem;
  opacity: 0.8;
  text-align: center;
  display: block;
  margin-top: 0.5rem;
}

.palette-tabs {
  display: flex;
  gap: 0.25rem;
  margin-bottom: 1rem;
  background: rgba(255,255,255,0.1);
  padding: 0.5rem;
  border-radius: 10px;
}

.palette-tab {
  flex: 1;
  padding: 0.5rem;
  border: none;
  background: rgba(255,255,255,0.1);
  color: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.75rem;
  font-weight: 600;
  transition: all 0.2s;
}

.palette-tab.active {
  background: white;
  color: #667eea;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.color-palette-content {
  background: rgba(255,255,255,0.1);
  padding: 0.75rem;
  border-radius: 10px;
}

.color-palette-grid.basic,
.color-palette-grid.advanced {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 0.5rem;
}

.color-palette-grid.gradient {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 0.5rem;
}

.color-palette-grid.metallic {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.75rem;
}

.color-swatch-gradient {
  width: 100%;
  aspect-ratio: 1;
  border-radius: 8px;
  cursor: pointer;
  border: 2px solid rgba(255,255,255,0.3);
  transition: all 0.2s;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.color-swatch-gradient:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.color-swatch-large.metallic {
  box-shadow: inset 0 0 10px rgba(255,255,255,0.5);
  position: relative;
  overflow: hidden;
}

.color-swatch-large.metallic::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(
    45deg,
    transparent,
    rgba(255,255,255,0.3),
    transparent
  );
  animation: shimmer 3s infinite;
}

@keyframes shimmer {
  0% { transform: translateX(-100%) rotate(45deg); }
  100% { transform: translateX(100%) rotate(45deg); }
}

.checkmark {
  color: white;
  font-size: 1rem;
  font-weight: bold;
  text-shadow: 0 1px 3px rgba(0,0,0,0.5);
}

.brush-type-selector.enhanced {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 0.5rem;
  background: rgba(255,255,255,0.1);
  padding: 0.75rem;
  border-radius: 12px;
}

.brush-type-btn.enhanced {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 0.5rem;
  border: 2px solid rgba(255,255,255,0.2);
  background: rgba(255,255,255,0.1);
  color: white;
  border-radius: 10px;
  gap: 0.25rem;
  transition: all 0.2s;
  cursor: pointer;
}

.brush-type-btn.enhanced:hover {
  background: rgba(255,255,255,0.2);
  transform: translateY(-2px);
}

.brush-type-btn.enhanced.active-brush-type {
  background: white;
  color: #667eea;
  border-color: white;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  transform: translateY(-2px);
}

.brush-actions.enhanced {
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 2px solid rgba(255,255,255,0.2);
}

.action-row {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.action-row:last-child {
  margin-bottom: 0;
}

.tool-btn.enhanced {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 0.75rem;
  border: 2px solid rgba(255,255,255,0.3);
  background: rgba(255,255,255,0.15);
  color: white;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s;
  font-weight: 600;
  gap: 0.25rem;
}

.tool-btn.enhanced .icon {
  font-size: 1.25rem;
}

.tool-btn.enhanced:hover:not(:disabled) {
  background: rgba(255,255,255,0.25);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.tool-btn.enhanced:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.tool-btn.danger.enhanced {
  background: rgba(239, 68, 68, 0.8);
  border-color: rgba(239, 68, 68, 0.5);
}

.tool-btn.danger.enhanced:hover {
  background: rgba(239, 68, 68, 1);
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

/* Properties Panel - Modern Design */
.properties-panel {
  position: relative;
  align-self: stretch;
  background: white;
  border-radius: 16px;
  padding: 1.25rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.5);
  max-width: 300px;
  transition: all 0.3s ease;
  animation: slideInRight 0.3s ease-out;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.properties-panel:hover {
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
}

.properties-panel::-webkit-scrollbar {
  width: 6px;
}

.properties-panel::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.properties-panel::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 10px;
}

.properties-panel::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

.property-section {
  margin-bottom: 1.5rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid #f0f0f0;
}

.property-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
}

.property-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: #4a5568;
  margin-bottom: 0.5rem;
}

.property-input,
.property-slider {
  width: 100%;
  margin-bottom: 0.75rem;
}

.property-input {
  padding: 0.625rem;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: all 0.2s;
  background: white;
}

.property-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.property-slider {
  -webkit-appearance: none;
  appearance: none;
  width: 100%;
  height: 6px;
  border-radius: 3px;
  background: #e2e8f0;
  outline: none;
  margin-bottom: 0.5rem;
}

.property-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
  transition: all 0.2s;
}

.property-slider::-webkit-slider-thumb:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.slider-value {
  display: inline-block;
  font-size: 0.75rem;
  font-weight: 600;
  color: #667eea;
  background: #f0f0ff;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  margin-top: 0.25rem;
}

.color-picker {
  width: 100%;
  height: 42px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  cursor: pointer;
  background: white;
  transition: all 0.2s;
}

.color-picker:hover {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.position-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}

.position-input label {
  display: block;
  font-size: 0.75rem;
  font-weight: 600;
  color: #718096;
  margin-bottom: 0.25rem;
}

.layer-controls {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.layer-btn {
  padding: 0.625rem 1rem;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
  color: #4a5568;
}

.layer-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.layer-btn.danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
}

.layer-btn.danger:hover {
  background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

/* Modal Styles - Modern Overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.modal {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  max-width: 90vw;
  max-height: 85vh;
  overflow-y: auto;
  width: 700px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: modalSlideIn 0.3s ease-out;
}

.modal-large {
  width: 900px;
  max-width: 95vw;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #f0f0f0;
}

.modal-header h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a202c;
  margin: 0;
}

.modal-content {
  margin-top: 1rem;
}

.clipart-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 1rem;
  margin: 1rem 0;
}

.clipart-item {
  cursor: pointer;
  border-radius: 12px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  padding: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8fafc;
  border: 2px solid transparent;
}

.clipart-item:hover {
  transform: translateY(-4px) scale(1.05);
  background: white;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  border-color: #667eea;
}

.clipart-item img {
  width: 100%;
  height: 100px;
  object-fit: contain;
  transition: all 0.3s;
}

.clipart-item:hover img {
  transform: scale(1.1);
}

.shape-grid,
.effects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: 1rem;
  margin: 1rem 0;
}

.shape-item,
.effect-item {
  cursor: pointer;
  border-radius: 12px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  padding: 1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100px;
  background: linear-gradient(135deg, #f8fafc 0%, #f0f0f0 100%);
  border: 2px solid transparent;
}

.shape-item:hover,
.effect-item:hover {
  transform: translateY(-4px) scale(1.05);
  background: white;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  border-color: #667eea;
}

.shape-icon,
.effect-icon {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
  transition: all 0.3s;
}

.shape-item:hover .shape-icon,
.effect-item:hover .effect-icon {
  transform: scale(1.2) rotate(10deg);
}

.shape-name,
.effect-name {
  font-size: 0.75rem;
  font-weight: 600;
  color: #4a5568;
  text-align: center;
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

/* Responsive Design */
@media (max-width: 1200px) {
  .design-area {
    grid-template-columns: 2fr 260px;
    gap: 1rem;
    padding: 1rem;
  }
  
  .properties-panel {
    max-width: 260px;
    padding: 1rem;
  }
  
  .brush-panel {
    right: 260px;
    width: 260px;
  }
  
  .modal {
    max-width: 95vw;
    width: 800px;
  }
}

@media (max-width: 992px) {
  .design-area {
    grid-template-columns: 1fr;
    grid-template-rows: 1fr auto;
    padding: 1rem;
    height: auto;
    min-height: 70vh;
  }
  
  .canvas-container {
    order: 1;
  }
  
  .properties-panel {
    position: static;
    max-width: 100%;
    width: 100%;
    grid-column: 1;
    align-self: auto;
    order: 2;
    margin-top: 1rem;
  }
  
  .brush-panel {
    position: relative;
    top: auto;
    right: auto;
    width: 100%;
    max-width: 100%;
    max-height: none;
    margin-bottom: 1rem;
  }
  
  .toolbar-content {
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.5rem;
  }
  
  .tool-group {
    margin-bottom: 0.5rem;
  }
  
  .modal {
    width: 90vw;
    max-width: 90vw;
  }
}

@media (max-width: 768px) {
  .design-area {
    grid-template-columns: 1fr;
    padding: 0.75rem;
    gap: 1rem;
  }
  
  .toolbar-header {
    padding: 0.5rem;
  }
  
  .tool-btn {
    width: 36px;
    height: 36px;
  }
  
  .tool-btn .tool-icon {
    width: 18px;
    height: 18px;
  }
  
  .canvas-wrapper {
    padding: 1rem;
    border-radius: 12px;
  }
  
  .modal {
    max-width: 95vw;
    padding: 1.5rem;
  }
  
  .clipart-grid,
  .shape-grid,
  .effects-grid {
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
  }
}

@media (max-width: 480px) {
  .design-area {
    padding: 0.5rem;
    gap: 0.75rem;
  }
  
  .tool-group {
    gap: 0.25rem;
  }
  
  .tool-btn {
    width: 32px;
    height: 32px;
  }
  
  .actions-group {
    padding-left: 0.5rem;
    border-left: none;
    margin-top: 0.5rem;
  }
  
  .canvas-wrapper {
    border-radius: 12px;
    padding: 0.75rem;
  }
  
  .modal {
    width: 95vw;
    padding: 1rem;
  }
  
  .modal-large {
    width: 95vw;
  }
  
  .property-input {
    font-size: 0.75rem;
    padding: 0.5rem;
  }
}
</style>