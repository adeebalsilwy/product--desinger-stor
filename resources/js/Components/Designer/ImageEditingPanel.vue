<template>
  <div class="image-editing-panel bg-white rounded-lg shadow-md p-4">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-semibold text-gray-800">Image Editing</h3>
      <button 
        @click="closePanel" 
        class="text-gray-500 hover:text-gray-700"
      >
       ✕      </button>
    </div>

    <!-- Basic Adjustments -->
    <div class="mb-6">
      <h4 class="font-medium text-gray-700 mb-3">Basic Adjustments</h4>
      <div class="space-y-4">
        <div>
          <div class="flex justify-between mb-1">
            <label class="text-sm text-gray-600">Brightness</label>
            <span class="text-sm text-gray-500">{{ filters.brightness }}%</span>
          </div>
          <input 
            type="range" 
            v-model.number="filters.brightness" 
            min="-100" 
            max="100"
            @input="applyFilters"
            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
          >
        </div>

        <div>
          <div class="flex justify-between mb-1">
            <label class="text-sm text-gray-600">Contrast</label>
            <span class="text-sm text-gray-500">{{ filters.contrast }}%</span>
          </div>
          <input 
            type="range" 
            v-model.number="filters.contrast" 
            min="-100" 
            max="100"
            @input="applyFilters"
            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
          >
        </div>

        <div>
          <div class="flex justify-between mb-1">
            <label class="text-sm text-gray-600">Saturation</label>
            <span class="text-sm text-gray-500">{{ filters.saturation }}%</span>
          </div>
          <input 
            type="range" 
            v-model.number="filters.saturation" 
            min="-100" 
            max="100"
            @input="applyFilters"
            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
          >
        </div>

        <div>
          <div class="flex justify-between mb-1">
            <label class="text-sm text-gray-600">Hue</label>
            <span class="text-sm text-gray-500">{{ filters.hue }}°</span>
          </div>
          <input 
            type="range" 
            v-model.number="filters.hue" 
            min="0" 
            max="360"
            @input="applyFilters"
            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
          >
        </div>
      </div>
    </div>

    <!-- Advanced Effects -->
    <div class="mb-6">
      <h4 class="font-medium text-gray-700 mb-3">Effects</h4>
      <div class="space-y-4">
        <div>
          <div class="flex justify-between mb-1">
            <label class="text-sm text-gray-600">Blur</label>
            <span class="text-sm text-gray-500">{{ filters.blur }}</span>
          </div>
          <input 
            type="range" 
            v-model.number="filters.blur" 
            min="0" 
            max="20"
            @input="applyFilters"
            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
          >
        </div>

        <div>
          <div class="flex justify-between mb-1">
            <label class="text-sm text-gray-600">Sharpen</label>
            <span class="text-sm text-gray-500">{{ filters.sharpen }}</span>
          </div>
          <input 
            type="range" 
            v-model.number="filters.sharpen" 
            min="0" 
            max="20"
            @input="applyFilters"
            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
          >
        </div>

        <div class="grid grid-cols-2 gap-2">
          <button 
            @click="toggleFilter('sepia')"
            :class="filters.sepia > 0 ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-700'"
            class="py-2 px-3 rounded text-sm font-medium hover:bg-amber-600 transition-colors"
          >
            Sepia
          </button>
          <button 
            @click="toggleFilter('grayscale')"
            :class="filters.grayscale > 0 ? 'bg-gray-600 text-white' : 'bg-gray-200 text-gray-700'"
            class="py-2 px-3 rounded text-sm font-medium hover:bg-gray-700 transition-colors"
          >
            B&W
          </button>
          <button 
            @click="toggleFilter('invert')"
            :class="filters.invert > 0 ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-700'"
            class="py-2 px-3 rounded text-sm font-medium hover:bg-purple-600 transition-colors"
          >
            Invert
          </button>
          <button 
            @click="resetAllFilters"
            class="py-2 px-3 bg-red-100 text-red-700 rounded text-sm font-medium hover:bg-red-200 transition-colors"
          >
            Reset
          </button>
        </div>
      </div>
    </div>

    <!-- Artistic Effects -->
    <div class="mb-6">
      <h4 class="font-medium text-gray-700 mb-3">Artistic Effects</h4>
      <div class="grid grid-cols-2 gap-2">
        <button 
          @click="applyArtisticEffect('vintage')"
          class="py-2 px-3 bg-amber-100 text-amber-800 rounded text-sm font-medium hover:bg-amber-200 transition-colors"
        >
          Vintage
        </button>
        <button 
          @click="applyArtisticEffect('lomo')"
          class="py-2 px-3 bg-pink-100 text-pink-800 rounded text-sm font-medium hover:bg-pink-200 transition-colors"
        >
          Lomo
        </button>
        <button 
          @click="applyArtisticEffect('clarity')"
          class="py-2 px-3 bg-blue-100 text-blue-800 rounded text-sm font-medium hover:bg-blue-200 transition-colors"
        >
          Clarity
        </button>
        <button 
          @click="applyArtisticEffect('sinCity')"
          class="py-2 px-3 bg-gray-100 text-gray-800 rounded text-sm font-medium hover:bg-gray-200 transition-colors"
        >
          Sin City
        </button>
        <button 
          @click="applyArtisticEffect('sunrise')"
          class="py-2 px-3 bg-orange-100 text-orange-800 rounded text-sm font-medium hover:bg-orange-200 transition-colors"
        >
          Sunrise
        </button>
        <button 
          @click="applyArtisticEffect('crossProcess')"
          class="py-2 px-3 bg-green-100 text-green-800 rounded text-sm font-medium hover:bg-green-200 transition-colors"
        >
          Cross Process
        </button>
      </div>
    </div>

    <!-- Transform Tools -->
    <div class="mb-6">
      <h4 class="font-medium text-gray-700 mb-3">Transform</h4>
      <div class="grid grid-cols-3 gap-2">
        <button 
          @click="rotateImage(-90)"
          class="py-2 px-3 bg-gray-200 text-gray-700 rounded text-sm font-medium hover:bg-gray-300 transition-colors"
        >
         ↺90°
        </button>
        <button 
          @click="rotateImage(90)"
          class="py-2 px-3 bg-gray-200 text-gray-700 rounded text-sm font-medium hover:bg-gray-300 transition-colors"
        >
         ↻90°
        </button>
        <button 
          @click="flipHorizontal"
          class="py-2 px-3 bg-gray-200 text-gray-700 rounded text-sm font-medium hover:bg-gray-300 transition-colors"
        >
          ↔ Flip
        </button>
        <button 
          @click="flipVertical"
          class="py-2 px-3 bg-gray-200 text-gray-700 rounded text-sm font-medium hover:bg-gray-300 transition-colors"
        >
         ↕
        </button>
        <button 
          @click="openCropper"
          class="py-2 px-3 bg-blue-500 text-white rounded text-sm font-medium hover:bg-blue-600 transition-colors col-span-2"
        >
          Crop Image
        </button>
      </div>
    </div>

    <!-- Opacity Control -->
    <div class="mb-4">
      <div class="flex justify-between mb-1">
        <label class="text-sm text-gray-600">Opacity</label>
        <span class="text-sm text-gray-500">{{ filters.opacity }}%</span>
      </div>
      <input 
        type="range" 
        v-model.number="filters.opacity" 
        min="0" 
        max="100"
        @input="updateOpacity"
        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
      >
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-2 pt-4 border-t">
      <button 
        @click="applyChanges"
        class="flex-1 py-2 px-4 bg-green-500 text-white rounded font-medium hover:bg-green-600 transition-colors"
      >
        Apply
      </button>
      <button 
        @click="cancelChanges"
        class="flex-1 py-2 px-4 bg-gray-200 text-gray-700 rounded font-medium hover:bg-gray-300 transition-colors"
      >
        Cancel
      </button>
    </div>

    <!-- Cropper Modal -->
    <div v-if="showCropper" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] m-4 overflow-hidden">
        <div class="p-4 border-b">
          <h3 class="text-xl font-semibold">Crop Image</h3>
        </div>
        <div class="p-4">
          <div class="relative">
            <img 
              ref="cropperImage" 
              :src="imageSrc" 
              alt="Image to crop"
              class="max-w-full max-h-[60vh]"
            />
          </div>
        </div>
        <div class="p-4 border-t flex justify-end gap-2">
          <button 
            @click="cancelCrop"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
          >
            Cancel
          </button>
          <button 
            @click="applyCrop"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Apply Crop
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ImageEditingService from '@/Services/ImageEditingService';

export default {
  name: 'ImageEditingPanel',
  
  props: {
    imageObject: {
      type: Object,
      required: true
    },
    imageSrc: {
      type: String,
      required: true
    }
  },
  
  data() {
    return {
      filters: {
        brightness: 0,
        contrast: 0,
        saturation: 0,
        hue: 0,
        blur: 0,
        sharpen: 0,
        sepia: 0,
        grayscale: 0,
        invert: 0,
        opacity: 100
      },
      showCropper: false,
      originalFilters: {}
    };
  },
  
  mounted() {
    // Store original filter state
    this.originalFilters = { ...ImageEditingService.getFilters() };
    
    // Initialize with current image filters
    this.filters = { ...ImageEditingService.getFilters() };
  },
  
  methods: {
    applyFilters() {
      ImageEditingService.applyFilters(this.imageObject, this.filters);
      this.$emit('update');
    },
    
    toggleFilter(filterName) {
      this.filters[filterName] = this.filters[filterName] > 0 ? 0 : 100;
      this.applyFilters();
    },
    
    resetAllFilters() {
      ImageEditingService.resetFilters(this.imageObject);
      this.filters = {
        brightness: 0,
        contrast: 0,
        saturation: 0,
        hue: 0,
        blur: 0,
        sharpen: 0,
        sepia: 0,
        grayscale: 0,
        invert: 0,
        opacity: 100
      };
      this.$emit('update');
    },
    
    applyArtisticEffect(effect) {
      ImageEditingService.applyArtisticEffect(this.imageObject, effect);
      this.$emit('update');
    },
    
    rotateImage(angle) {
      ImageEditingService.rotateImage(this.imageObject, angle);
      this.$emit('update');
    },
    
    flipHorizontal() {
      ImageEditingService.flipHorizontal(this.imageObject);
      this.$emit('update');
    },
    
    flipVertical() {
      ImageEditingService.flipVertical(this.imageObject);
      this.$emit('update');
    },
    
    updateOpacity() {
      ImageEditingService.setOpacity(this.imageObject, this.filters.opacity);
      this.$emit('update');
    },
    
    openCropper() {
      this.showCropper = true;
      this.$nextTick(() => {
        if (this.$refs.cropperImage) {
          ImageEditingService.initCropper(this.$refs.cropperImage, {
            aspectRatio: NaN,
            viewMode: 1
          });
        }
      });
    },
    
    async applyCrop() {
      try {
        const croppedImageData = await ImageEditingService.getCroppedImageData();
        
        // Update the fabric image with cropped data
        fabric.Image.fromURL(croppedImageData, (img) => {
          // Copy properties from original image
          img.set({
            left: this.imageObject.left,
            top: this.imageObject.top,
            scaleX: this.imageObject.scaleX,
            scaleY: this.imageObject.scaleY,
            angle: this.imageObject.angle,
            opacity: this.imageObject.opacity
          });
          
          // Replace the image on canvas
          const canvas = this.imageObject.canvas;
          const index = canvas.getObjects().indexOf(this.imageObject);
          
          canvas.remove(this.imageObject);
          canvas.insertAt(img, index);
          canvas.setActiveObject(img);
          canvas.renderAll();
          
          // Update the reference
          this.$emit('image-updated', img);
          
          this.closeCropper();
        });
      } catch (error) {
        console.error('Crop failed:', error);
        if (this.$toast && typeof this.$toast.error === 'function') {
          this.$toast.error('Failed to crop image: ' + error.message);
        } else {
          alert('Failed to crop image: ' + error.message);
        }
      }
    },
    
    cancelCrop() {
      this.closeCropper();
    },
    
    closeCropper() {
      ImageEditingService.destroy();
      this.showCropper = false;
    },
    
    applyChanges() {
      this.$emit('close');
    },
    
    cancelChanges() {
      // Restore original filters
      ImageEditingService.applyFilters(this.imageObject, this.originalFilters);
      this.$emit('close');
    },
    
    closePanel() {
      this.$emit('close');
    }
  },
  
  beforeUnmount() {
    ImageEditingService.destroy();
  }
};
</script>

<style scoped>
/* Custom range slider styles */
input[type="range"]::-webkit-slider-thumb {
  appearance: none;
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: #3b82f6;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

input[type="range"]::-moz-range-thumb {
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: #3b82f6;
  cursor: pointer;
  border: none;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
</style>