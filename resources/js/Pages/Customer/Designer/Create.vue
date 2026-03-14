<template>
  <CustomerLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <!-- Professional Header Section -->
      <div class="bg-gradient-to-r from-purple-600 to-indigo-700 shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between h-20">
            <div class="flex items-center space-x-4">
              <div class="text-white">
                <h1 class="text-3xl font-bold">Design Studio</h1>
                <p class="text-purple-200 mt-1 text-lg">
                  {{ product?.name || productType.name }} Designer
                </p>
              </div>
            </div>
            
            <div class="flex items-center gap-4">
              <!-- Undo/Redo Group -->
              <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-xl p-2">
                <button 
                  @click="undo"
                  :disabled="!canUndo"
                  class="w-12 h-12 flex items-center justify-center rounded-lg bg-white/30 hover:bg-white/50 text-white font-bold text-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                  title="Undo (Ctrl+Z)"
                >
                  ↶
                </button>
                <button 
                  @click="redo"
                  :disabled="!canRedo"
                  class="w-12 h-12 flex items-center justify-center rounded-lg bg-white/30 hover:bg-white/50 text-white font-bold text-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                  title="Redo (Ctrl+Y)"
                >
                  ↷
                </button>
              </div>
              
              <!-- Action Buttons -->
              <div class="flex items-center gap-3">
                <button 
                  @click="saveDesign"
                  :disabled="!hasChanges"
                  class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold flex items-center gap-2 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105"
                >
                  <span class="text-lg">💾</span>
                  Save Design
                </button>
                <button 
                  @click="exportDesign"
                  class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold flex items-center gap-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
                >
                  <span class="text-lg">📤</span>
                  Export
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Professional Toolbar Header -->
      <div class="bg-gradient-to-r from-purple-600 to-indigo-700 shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between h-16">
            <!-- Tool Selection -->
            <div class="flex items-center space-x-2">
              <button 
                @click="setActiveTool('select')"
                :class="{ 'active': activeTool === 'select' }"
                class="tool-btn"
                title="Selection"
              >
                <svg class="tool-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" /></svg>
              </button>
              
              <button 
                @click="setActiveTool('text')"
                :class="{ 'active': activeTool === 'text' }"
                class="tool-btn"
                title="Text"
              >
                <svg class="tool-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>
              </button>
              
              <button 
                @click="setActiveTool('image')"
                :class="{ 'active': activeTool === 'image' }"
                class="tool-btn"
                title="Image"
              >
                <svg class="tool-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
              </button>
              
              <button 
                @click="setActiveTool('brush'); toggleBrushMode()"
                :class="{ 'active': activeTool === 'brush' }"
                class="tool-btn"
                title="Brush"
              >
                <svg class="tool-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
              </button>
              
              <button 
                @click="setActiveTool('eraser'); toggleEraserMode()"
                :class="{ 'active': activeTool === 'eraser' }"
                class="tool-btn"
                title="Eraser"
              >
                <svg class="tool-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
              </button>
            </div>
            
            <!-- Shape Tools -->
            <div class="flex items-center space-x-2">
              <button @click="showShapes = true" class="tool-btn" title="Shapes">
                <svg class="tool-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
              </button>
              
              <button @click="setActiveTool('clipart'); showCliparts = true" :class="{ 'active': activeTool === 'clipart' }" class="tool-btn" title="Clipart">
                <svg class="tool-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
              </button>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center space-x-2">
              <button @click="saveDesign" class="tool-btn primary" title="Save">
                <svg class="tool-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
              </button>
              
              <button @click="exportDesign" class="tool-btn success" title="Export">
                <svg class="tool-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Designer Area with Proper Container -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-6 h-[calc(100vh-11rem)]">

          <!-- Main Canvas Area - Full width -->
          <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col h-full">
            <!-- Canvas Container - Full width -->
            <div class="flex-1 p-4 bg-gradient-to-br from-gray-50 to-gray-100 overflow-auto">
              <div class="bg-white rounded-xl shadow-inner border-2 border-dashed border-gray-300 w-full h-full mx-auto flex items-center justify-center">
                <div class="w-full h-full flex items-center justify-center p-4">
                  <ProductDesigner 
                    ref="designer"
                    :product-type-id="productType.id"
                    @saved="onDesignSaved"
                    @changed="onDesignChanged"
                    @zoom="updateZoom"
                  />
                </div>
              </div>
            </div>
            
            <!-- Canvas Controls -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-t">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <button @click="zoomOut" class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold transition-colors">
                    −
                  </button>
                  <span class="text-sm font-medium min-w-[60px] text-center">{{ Math.round(zoomLevel * 100) }}%</span>
                  <button @click="zoomIn" class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold transition-colors">
                    +
                  </button>
                </div>
                
                <div class="flex items-center gap-2">
                  <button @click="centerCanvas" class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors">
                    Center View
                  </button>
                  <button @click="toggleGrid" class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors" :class="{ 'bg-blue-200': showGrid }">
                    Grid {{ showGrid ? 'On' : 'Off' }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Panel - Properties, Tools & Editing -->
          <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col h-full">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
              <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="text-purple-600">⚙️</span>
                Properties & Tools
              </h3>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-6">
              <!-- Brush/Eraser Tools -->
              <div v-if="activeTool === 'brush' || activeTool === 'eraser'" class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-4 border border-purple-200">
                <h4 class="font-semibold text-purple-800 mb-3 flex items-center gap-2">
                  <span>{{ activeTool === 'eraser' ? '🧽' : '🖌️' }}</span>
                  {{ activeTool === 'eraser' ? 'Eraser' : 'Brush' }} Tools
                </h4>
                
                <div class="space-y-4">
                  <!-- Brush Size -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Size: {{ brushSize }}px</label>
                    <input 
                      type="range" 
                      v-model.number="brushSize" 
                      min="1" 
                      max="50"
                      @input="updateBrushSettings"
                      class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                    >
                  </div>
                  
                  <!-- Brush Color (only for brush, not eraser) -->
                  <div v-if="activeTool !== 'eraser'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                    <input 
                      type="color" 
                      v-model="brushColor" 
                      @change="updateBrushSettings"
                      class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer"
                    >
                  </div>
                  
                  <!-- Brush Opacity -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Opacity: {{ Math.round(brushOpacity * 100) }}%</label>
                    <input 
                      type="range" 
                      v-model.number="brushOpacity" 
                      min="0.1" 
                      max="1"
                      step="0.1"
                      @input="updateBrushSettings"
                      class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                    >
                  </div>
                  
                  <!-- Brush Types -->
                  <div v-if="activeTool === 'brush'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brush Type</label>
                    <div class="grid grid-cols-2 gap-2">
                      <button 
                        v-for="brush in brushTypes" 
                        :key="brush.value"
                        @click="setBrushType(brush.value)"
                        :class="{ 'active': brushType === brush.value }"
                        class="p-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium transition-colors"
                      >
                        {{ brush.name }}
                      </button>
                    </div>
                  </div>
                  
                  <!-- Actions -->
                  <div class="pt-4 border-t border-gray-200">
                    <button @click="clearCanvas" class="w-full p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium transition-colors mb-2">
                      Clear Canvas
                    </button>
                    <div class="flex gap-2">
                      <button @click="undoBrushStroke" :disabled="!canUndoBrush()" class="flex-1 p-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
                        Undo
                      </button>
                      <button @click="redoBrushStroke" :disabled="!canRedoBrush()" class="flex-1 p-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
                        Redo
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Object Properties -->
              <div v-if="selectedObject" class="bg-blue-50 rounded-xl p-4">
                <h4 class="font-semibold text-blue-800 mb-3">Selected: {{ selectedObject.type || 'Object' }}</h4>
                
                <!-- Text Properties -->
                <div v-if="selectedObject.type === 'i-text'" class="space-y-3 mb-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Text Content</label>
                    <textarea 
                      v-model="selectedObject.text" 
                      @input="updateCanvas" 
                      class="w-full p-2 border border-gray-300 rounded-lg text-sm"
                      placeholder="Enter text..."
                      rows="3"
                    ></textarea>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Font Size: {{ selectedObject.fontSize }}px</label>
                    <input 
                      type="range" 
                      v-model.number="selectedObject.fontSize" 
                      min="8" 
                      max="72"
                      @input="updateCanvas"
                      class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                    >
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input 
                      type="color" 
                      v-model="selectedObject.fill" 
                      @change="updateCanvas"
                      class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer"
                    >
                  </div>
                </div>
                
                <!-- Common Properties -->
                <div class="space-y-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Opacity: {{ Math.round((selectedObject.opacity || 1) * 100) }}%</label>
                    <input 
                      type="range" 
                      v-model.number="selectedObject.opacity" 
                      min="0" 
                      max="1" 
                      step="0.1"
                      @input="updateCanvas"
                      class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                    >
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rotation: {{ Math.round(selectedObject.angle || 0) }}°</label>
                    <input 
                      type="range" 
                      v-model.number="selectedObject.angle" 
                      min="0" 
                      max="360"
                      @input="updateCanvas"
                      class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                    >
                  </div>
                </div>
                
                <!-- Position Controls -->
                <div class="grid grid-cols-2 gap-3 mt-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">X Position</label>
                    <input 
                      type="number"
                      v-model.number="selectedObject.left" 
                      @input="updateCanvas"
                      class="w-full p-2 border border-gray-300 rounded-lg text-sm"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Y Position</label>
                    <input 
                      type="number"
                      v-model.number="selectedObject.top" 
                      @input="updateCanvas"
                      class="w-full p-2 border border-gray-300 rounded-lg text-sm"
                    >
                  </div>
                </div>
                
                <!-- Layer Controls -->
                <div class="mt-4 pt-4 border-t border-blue-200">
                  <div class="grid grid-cols-2 gap-2">
                    <button @click="bringToFront" class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm font-medium transition-colors">
                      Bring Front
                    </button>
                    <button @click="sendToBack" class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm font-medium transition-colors">
                      Send Back
                    </button>
                    <button @click="bringForward" class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm font-medium transition-colors">
                      Forward
                    </button>
                    <button @click="sendBackwards" class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm font-medium transition-colors">
                      Backward
                    </button>
                  </div>
                  
                  <button @click="deleteObject" class="w-full mt-2 p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium transition-colors">
                    Delete Object
                  </button>
                </div>
                
                <!-- Text Formatting -->
                <div v-if="selectedObject.type === 'i-text'" class="mt-4 pt-4 border-t border-blue-200">
                  <h5 class="font-medium text-blue-700 mb-2">Text Formatting</h5>
                  <div class="grid grid-cols-3 gap-2">
                    <button @click="toggleBold" :class="{'bg-blue-200': isBold()}" class="p-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium transition-colors">
                      Bold
                    </button>
                    <button @click="toggleItalic" :class="{'bg-blue-200': isItalic()}" class="p-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium transition-colors">
                      Italic
                    </button>
                    <button @click="toggleUnderline" :class="{'bg-blue-200': isUnderline()}" class="p-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-sm font-medium transition-colors">
                      Underline
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- No Selection State -->
              <div v-else class="text-center py-8 text-gray-500">
                <div class="text-4xl mb-2">🖱️</div>
                <p>Select an element to view properties</p>
              </div>
              
              <!-- Quick Actions -->
              <div class="bg-gray-50 rounded-xl p-4">
                <h4 class="font-semibold text-gray-700 mb-3">Quick Actions</h4>
                <div class="space-y-2">
                  <button @click="clearCanvas" class="w-full p-3 bg-gradient-to-r from-red-50 to-red-100 hover:from-red-100 hover:to-red-200 text-red-700 rounded-xl font-medium transition-all duration-200 flex items-center gap-2">
                    <span>🗑️</span> Clear Canvas
                  </button>
                  <button @click="showTemplates = true" class="w-full p-3 bg-gradient-to-r from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 text-purple-700 rounded-xl font-medium transition-all duration-200 flex items-center gap-2">
                    <span>🎨</span> Browse Templates
                  </button>
                  <button @click="showCliparts = true" class="w-full p-3 bg-gradient-to-r from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 text-green-700 rounded-xl font-medium transition-all duration-200 flex items-center gap-2">
                    <span>🖼️</span> Browse Clipart
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Templates Modal -->
      <div v-if="showTemplates" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden shadow-2xl">
          <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-purple-600 to-indigo-700 text-white">
            <div>
              <h3 class="text-2xl font-bold flex items-center gap-2">
                <span>🎨</span>
                Design Templates
              </h3>
              <p class="text-purple-200 mt-1">Choose from professionally designed templates</p>
            </div>
            <button @click="showTemplates = false" class="text-white hover:text-gray-200 text-3xl font-light">×</button>
          </div>
          <div class="p-6 max-h-[70vh] overflow-y-auto">
            <div class="mb-6 flex gap-4">
              <input
                v-model="templateSearch"
                type="text"
                placeholder="Search templates..."
                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              />
              <select
                v-model="templateCategory"
                class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              >
                <option value="">All Categories</option>
                <option v-for="category in templateCategories" :key="category" :value="category">
                  {{ category.charAt(0).toUpperCase() + category.slice(1) }}
                </option>
              </select>
            </div>
            
            <div v-if="loadingTemplates" class="flex justify-center items-center h-64">
              <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500"></div>
            </div>
            
            <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
              <div 
                v-for="template in templates" 
                :key="template.id"
                class="border rounded-xl overflow-hidden cursor-pointer hover:ring-2 hover:ring-purple-500 transition-all hover:shadow-lg"
                @click="useTemplate(template)"
              >
                <div class="aspect-square bg-gray-100 relative">
                  <img :src="template.thumbnail_url || template.preview_url" :alt="template.name" class="w-full h-full object-cover" />
                  <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-10 transition-all"></div>
                </div>
                <div class="p-3">
                  <div class="font-medium text-sm truncate">{{ template.name }}</div>
                  <div class="text-xs text-gray-500 mt-1">{{ template.category || 'Uncategorized' }}</div>
                </div>
              </div>
            </div>
            
            <!-- Pagination -->
            <div v-if="!loadingTemplates && templates.length > 0" class="mt-8 flex justify-center">
              <nav class="flex items-center space-x-2">
                <button
                  @click="fetchTemplates(currentTemplatePage - 1)"
                  :disabled="currentTemplatePage === 1"
                  class="px-4 py-2 rounded-lg border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                >
                  Previous
                </button>
                
                <span class="mx-4 text-gray-600">
                  Page {{ currentTemplatePage }} of {{ totalTemplatePages }}
                </span>
                
                <button
                  @click="fetchTemplates(currentTemplatePage + 1)"
                  :disabled="currentTemplatePage === totalTemplatePages"
                  class="px-4 py-2 rounded-lg border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                >
                  Next
                </button>
              </nav>
            </div>
          </div>
          <div class="p-6 border-t border-gray-200 text-center bg-gray-50">
            <button @click="showTemplates = false" class="px-6 py-3 bg-gradient-to-r from-gray-200 to-gray-300 hover:from-gray-300 hover:to-gray-400 rounded-lg font-medium transition-all duration-200">
              Close
            </button>
          </div>
        </div>
      </div>

      <!-- Clipart Modal -->
      <div v-if="showCliparts" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-96 overflow-hidden shadow-2xl">
          <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-purple-600 to-indigo-700 text-white">
            <div>
              <h3 class="text-2xl font-bold flex items-center gap-2">
                <span>🖼️</span>
                Clipart Library
              </h3>
              <p class="text-purple-200 mt-1">Browse our collection of illustrations</p>
            </div>
            <button @click="showCliparts = false" class="text-white hover:text-gray-200 text-3xl font-light">×</button>
          </div>
          <div class="p-6 max-h-80 overflow-y-auto">
            <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
              <div 
                v-for="clipart in cliparts" 
                :key="clipart.id"
                class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:ring-2 hover:ring-purple-500 hover:shadow-lg transition-all"
                @click="addClipart(clipart)"
              >
                <img :src="clipart.image_url" :alt="clipart.title" class="w-full h-full object-cover" />
              </div>
            </div>
          </div>
          <div class="p-6 border-t border-gray-200 text-center bg-gray-50">
            <button @click="showCliparts = false" class="px-6 py-3 bg-gradient-to-r from-gray-200 to-gray-300 hover:from-gray-300 hover:to-gray-400 rounded-lg font-medium transition-all duration-200">
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
import { router } from '@inertiajs/vue3';

export default {
  name: 'DesignerCreate',
  
  components: {
    CustomerLayout,
    ProductDesigner,
  },
  
  props: {
    productType: Object,
    product: Object,
    printAreas: Array,
    initialTemplate: Object,
  },
  
  data() {
    return {
      showCliparts: false,
      showTemplates: false,
      showProperties: true,
      showShapes: false,
      cliparts: [],
      fonts: [],
      userAssets: [],
      recentDesigns: [],
      // selectedObject is now a computed property
      previewUrl: null,
      hasChanges: false,
      templates: [],
      loadingTemplates: false,
      templateSearch: '',
      templateCategory: '',
      currentTemplatePage: 1,
      totalTemplatePages: 1,
      templateCategories: ['t-shirt', 'hoodie', 'mug', 'poster', 'other'],
      imageBackgroundRemoved: false,
      zoomLevel: 1,
      showGrid: false,
      history: [],
      historyIndex: -1,
      maxHistory: 50,
      
      // Tool state
      activeTool: 'select',
      
      // Brush properties
      brushSize: 5,
      brushColor: '#000000',
      brushOpacity: 1,
      brushType: 'pencil',
      brushTypes: [
        { value: 'pencil', name: 'Pencil' },
        { value: 'spray', name: 'Spray' },
        { value: 'marker', name: 'Marker' },
        { value: 'soft', name: 'Soft Brush' },
        { value: 'eraser', name: 'Eraser' }
      ],
    };
  },
  
  async created() {
    await this.loadAssets();
    await this.loadUserAssets();
    await this.loadRecentDesigns();
    await this.fetchTemplates(1);
    
    // Watch for template filters
    this.$watch('templateSearch', this.onTemplateFilterChange);
    this.$watch('templateCategory', this.onTemplateFilterChange);
  },
  
  async mounted() {
    console.log('Create.vue mounted');
    
    // Load template if provided
    if (this.initialTemplate) {
      console.log('Loading initial template:', this.initialTemplate);
      await this.$nextTick();
      try {
        await this.useTemplate(this.initialTemplate);
      } catch (error) {
        console.error('Failed to load initial template:', error);
      }
    }
    
    // Ensure proper initialization of designer
    await this.$nextTick();
    if (this.$refs.designer) {
      console.log('Designer component reference available');
      // Make sure canvas is properly initialized
      if (this.$refs.designer.initializeCanvas && typeof this.$refs.designer.initializeCanvas === 'function') {
        this.$refs.designer.initializeCanvas();
      }
    } else {
      console.error('Designer component reference not available');
    }
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
        // Provide fallback data
        this.fonts = [];
        this.cliparts = [];
      }
    },
    
    clearCanvas() {
      if (this.$refs.designer && typeof this.$refs.designer.clearCanvas === 'function') {
        this.$refs.designer.clearCanvas();
      }
    },
    
    async loadUserAssets() {
      try {
        const response = await axios.get('/api/assets');
        this.userAssets = response.data.data || [];
      } catch (error) {
        // User not authenticated, no need to load user assets
        if (error.response && error.response.status === 401) {
          console.log('User not authenticated, skipping user assets');
          this.userAssets = [];
        } else {
          console.error('Failed to load user assets:', error);
          this.userAssets = [];
        }
      }
    },
    
    async loadRecentDesigns() {
      try {
        const response = await axios.get('/api/designs');
        this.recentDesigns = response.data.data || [];
      } catch (error) {
        // User not authenticated, no need to load recent designs
        if (error.response && error.response.status === 401) {
          console.log('User not authenticated, skipping recent designs');
          this.recentDesigns = [];
        } else {
          console.error('Failed to load recent designs:', error);
          this.recentDesigns = [];
        }
      }
    },
    
    async fetchTemplates(page = 1) {
      this.loadingTemplates = true;
      try {
        const response = await axios.get('/api/v1/templates', {
          params: {
            page,
            search: this.templateSearch,
            category: this.templateCategory
          }
        });
        
        // Handle response format properly - check if data exists
        if (response.data && response.data.data) {
          this.templates = response.data.data;
          
          // Check if meta exists
          if (response.data.meta) {
            this.currentTemplatePage = response.data.meta.current_page || 1;
            this.totalTemplatePages = response.data.meta.last_page || 1;
          } else {
            // Fallback if meta doesn't exist
            this.currentTemplatePage = 1;
            this.totalTemplatePages = 1;
          }
        } else {
          // No data returned
          this.templates = [];
          this.currentTemplatePage = 1;
          this.totalTemplatePages = 1;
        }
      } catch (error) {
        console.error('Failed to load templates:', error);
        // Set defaults on error
        this.templates = [];
        this.currentTemplatePage = 1;
        this.totalTemplatePages = 1;
      } finally {
        this.loadingTemplates = false;
      }
    },
    
    onTemplateFilterChange() {
      this.fetchTemplates(1);
    },
    
    async useTemplate(template) {
      console.log('Using template:', template);
      // Load the template into the designer
      this.showTemplates = false;
      
      // Call the designer component method to load template
      if (this.$refs.designer && typeof this.$refs.designer.loadTemplate === 'function') {
        await this.$refs.designer.loadTemplate(template);
      } else {
        console.error('Designer component not ready or loadTemplate method not available');
        if (typeof this.$toast !== 'undefined') {
          this.$toast.error('Designer not ready. Please try again.');
        }
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
    
    exportDesign() {
      this.$refs.designer.exportDesign();
    },
    
    onDesignSaved(design) {
      this.hasChanges = false;
      // Use Inertia router to navigate
      this.$inertia.visit(route('designer.my-designs'));
    },
    
    onDesignChanged() {
      this.hasChanges = true;
    },
    
    loadDesign(design) {
      this.$refs.designer.loadDesign(design);
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
    
    bringToFront() {
      if (this.$refs.designer && typeof this.$refs.designer.bringToFront === 'function') {
        this.$refs.designer.bringToFront();
        this.hasChanges = true;
      }
    },
    
    sendToBack() {
      if (this.$refs.designer && typeof this.$refs.designer.sendToBack === 'function') {
        this.$refs.designer.sendToBack();
        this.hasChanges = true;
      }
    },
    
    toggleBold() {
      if (this.$refs.designer && this.$refs.designer.selectedObject) {
        this.$refs.designer.toggleBold();
      }
    },
    
    toggleItalic() {
      if (this.$refs.designer && this.$refs.designer.selectedObject) {
        this.$refs.designer.toggleItalic();
      }
    },
    
    adjustImageFilters() {
      this.$refs.designer.adjustImageFilters();
      this.hasChanges = true;
      this.saveToHistory();
    },
    
    toggleUnderline() {
      if (this.$refs.designer && this.$refs.designer.selectedObject) {
        this.$refs.designer.toggleUnderline();
      }
    },
    
    togglePropertiesPanel() {
      this.showProperties = !this.showProperties;
    },
    
    async toggleImageBackground() {
      if (!this.$refs.designer || !this.$refs.designer.selectedObject) return;
      
      this.imageBackgroundRemoved = !this.imageBackgroundRemoved;
      
      try {
        if (this.imageBackgroundRemoved) {
          // Remove background
          await this.$refs.designer.removeImageBackground();
        } else {
          // Restore original image
          await this.$refs.designer.restoreImageBackground();
        }
        this.hasChanges = true;
        this.saveToHistory();
      } catch (error) {
        console.error('Failed to toggle image background:', error);
        this.imageBackgroundRemoved = !this.imageBackgroundRemoved; // Revert state
        if (typeof this.$toast !== 'undefined') {
          this.$toast.error('Failed to process image background');
        }
      }
    },
    
    // Zoom Controls
    zoomIn() {
      this.zoomLevel = Math.min(this.zoomLevel + 0.1, 3);
      this.updateCanvasZoom();
    },
    
    zoomOut() {
      this.zoomLevel = Math.max(this.zoomLevel - 0.1, 0.1);
      this.updateCanvasZoom();
    },
    
    resetZoom() {
      this.zoomLevel = 1;
      this.updateCanvasZoom();
    },
    
    updateZoom(newZoom) {
      this.zoomLevel = newZoom;
    },
    
    updateCanvasZoom() {
      if (this.$refs.designer && this.$refs.designer.canvas) {
        this.$refs.designer.canvas.setZoom(this.zoomLevel);
        this.$refs.designer.canvas.renderAll();
      }
    },
    
    centerCanvas() {
      if (this.$refs.designer && this.$refs.designer.canvas) {
        const canvas = this.$refs.designer.canvas;
        const viewportCenter = {
          x: canvas.width / 2,
          y: canvas.height / 2
        };
        
        // Center the viewport
        canvas.setViewportTransform([1, 0, 0, 1, 0, 0]);
        canvas.renderAll();
      }
    },
    
    toggleGrid() {
      this.showGrid = !this.showGrid;
      // This would need to be implemented in the ProductDesigner component
      if (this.$refs.designer && typeof this.$refs.designer.toggleGrid === 'function') {
        this.$refs.designer.toggleGrid();
      }
    },
    
    // History/Undo-Redo
    saveToHistory() {
      // Remove any history after current index
      if (this.historyIndex < this.history.length - 1) {
        this.history = this.history.slice(0, this.historyIndex + 1);
      }
      
      // Add current state
      const currentState = this.$refs.designer.canvas.toJSON();
      this.history.push(currentState);
      this.historyIndex = this.history.length - 1;
      
      // Limit history size
      if (this.history.length > this.maxHistory) {
        this.history.shift();
        this.historyIndex--;
      }
    },
    
    undo() {
      if (this.canUndo) {
        this.historyIndex--;
        this.restoreFromHistory();
      }
    },
    
    redo() {
      if (this.canRedo) {
        this.historyIndex++;
        this.restoreFromHistory();
      }
    },
    
    restoreFromHistory() {
      if (this.historyIndex >= 0 && this.historyIndex < this.history.length) {
        const state = this.history[this.historyIndex];
        this.$refs.designer.canvas.loadFromJSON(state, () => {
          this.$refs.designer.canvas.renderAll();
        });
      }
    },
    
    get canUndo() {
      return this.history && this.history.length > 0 && this.historyIndex > 0;
    },
    
    get canRedo() {
      return this.history && this.history.length > 0 && this.historyIndex < this.history.length - 1;
    },
    
    // Tool Methods
    setActiveTool(tool) {
      console.log('Create.vue: Setting active tool to', tool);
      this.activeTool = tool;
      
      // Notify designer component of tool change
      if (this.$refs.designer && typeof this.$refs.designer.setActiveTool === 'function') {
        this.$refs.designer.setActiveTool(tool);
      } else {
        console.error('Designer component not ready or setActiveTool method not available');
      }
    },
    
    toggleBrushMode() {
      if (this.$refs.designer && typeof this.$refs.designer.toggleBrushMode === 'function') {
        this.$refs.designer.toggleBrushMode();
      }
    },
    
    toggleEraserMode() {
      if (this.$refs.designer && typeof this.$refs.designer.toggleEraserMode === 'function') {
        this.$refs.designer.toggleEraserMode();
      }
    },
    
    updateBrushSettings() {
      if (this.$refs.designer && typeof this.$refs.designer.updateBrushSettings === 'function') {
        this.$refs.designer.updateBrushSettings();
      }
    },
    
    setBrushType(type) {
      this.brushType = type;
      if (this.$refs.designer && typeof this.$refs.designer.setBrushType === 'function') {
        this.$refs.designer.setBrushType(type);
      }
    },
    
    undoBrushStroke() {
      if (this.$refs.designer && typeof this.$refs.designer.undoBrushStroke === 'function') {
        this.$refs.designer.undoBrushStroke();
      }
    },
    
    redoBrushStroke() {
      if (this.$refs.designer && typeof this.$refs.designer.redoBrushStroke === 'function') {
        this.$refs.designer.redoBrushStroke();
      }
    },
    
    canUndoBrush() {
      return this.$refs.designer && typeof this.$refs.designer.canUndoBrush === 'function' 
        ? this.$refs.designer.canUndoBrush() 
        : false;
    },
    
    canRedoBrush() {
      return this.$refs.designer && typeof this.$refs.designer.canRedoBrush === 'function' 
        ? this.$refs.designer.canRedoBrush() 
        : false;
    },
    
    // Text formatting helpers
    isBold() {
      return this.$refs.designer && this.$refs.designer.selectedObject 
        ? this.$refs.designer.selectedObject.fontWeight === 'bold'
        : false;
    },
    
    isItalic() {
      return this.$refs.designer && this.$refs.designer.selectedObject 
        ? this.$refs.designer.selectedObject.fontStyle === 'italic'
        : false;
    },
    
    isUnderline() {
      return this.$refs.designer && this.$refs.designer.selectedObject 
        ? this.$refs.designer.selectedObject.underline === true
        : false;
    },
    
    // Undo/Redo methods for template access
    canUndo() {
      return this.$refs.designer && typeof this.$refs.designer.canUndo === 'function' 
        ? this.$refs.designer.canUndo() 
        : false;
    },
    
    canRedo() {
      return this.$refs.designer && typeof this.$refs.designer.canRedo === 'function' 
        ? this.$refs.designer.canRedo() 
        : false;
    },
    
    canUndoBrush() {
      return this.$refs.designer && typeof this.$refs.designer.canUndoBrush === 'function' 
        ? this.$refs.designer.canUndoBrush() 
        : false;
    },
    
    canRedoBrush() {
      return this.$refs.designer && typeof this.$refs.designer.canRedoBrush === 'function' 
        ? this.$refs.designer.canRedoBrush() 
        : false;
    },
    
    // Computed properties for safe template access
    designerRef() {
      return this.$refs.designer || null;
    },
    
    selectedObject() {
      return this.designerRef ? this.designerRef.selectedObject : null;
    },
    
    // Utility Methods
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString();
    },
    
    formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
  },
};
</script>

<style scoped>
/* Toolbar Button Styles */
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
</style>