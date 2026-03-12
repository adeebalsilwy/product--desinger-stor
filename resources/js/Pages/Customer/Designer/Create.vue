<template>
  <CustomerLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <!-- Header Section -->
      <div class="bg-white shadow-sm border-b sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between h-16">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">
                Design Studio
              </h1>
              <p class="text-sm text-gray-500">
                {{ product?.name || productType.name }} Designer
              </p>
            </div>
            
            <div class="flex items-center gap-3">
              <!-- Undo/Redo -->
              <div class="flex border rounded-lg overflow-hidden">
                <button 
                  @click="undo"
                  class="px-3 py-2 text-gray-500 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="!canUndo"
                >
                 ↶                  </button>
                <button 
                  @click="redo"
                  class="px-3 py-2 text-gray-500 hover:bg-gray-100 border-l disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="!canRedo"
                >
                 ↷
                </button>
              </div>
              
              <!-- Action Buttons -->
              <div class="flex gap-2">
                <button 
                  @click="saveDesign"
                  :disabled="!hasChanges"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transition-colors"
                >
                  <span>💾</span>
                  Save
                </button>
                <button 
                  @click="exportDesign"
                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2 transition-colors"
                >
                  <span>📤</span>
                  Export
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Designer Area -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex gap-6 h-[calc(100vh-8rem)]">
          <!-- Left Panel - Tools & Assets -->
          <div class="w-72 bg-white rounded-xl shadow-lg overflow-hidden flex flex-col">
            <!-- Panel Header -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4">
              <h2 class="text-white font-semibold text-lg">Design Tools</h2>
              <p class="text-blue-100 text-sm">Add elements to your design</p>
            </div>
            
            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-4">
              <div class="space-y-6">
                <!-- Templates Section -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="text-blue-500">📋</span>
                    Templates
                  </h3>
                  
                  <div class="space-y-2">
                    <button 
                      @click="showTemplates = true"
                      class="w-full flex items-center gap-3 px-4 py-3 bg-white hover:bg-blue-50 text-blue-700 rounded-lg border border-blue-200 transition-all hover:shadow-sm"
                    >
                      <span class="text-xl">🎨</span>
                      <div class="text-left">
                        <div class="font-medium">Browse Templates</div>
                        <div class="text-xs text-gray-500">Choose from professional designs</div>
                      </div>
                    </button>
                  </div>
                </div>
                
                <!-- Elements Section -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="text-green-500">➕</span>
                    Add Elements
                  </h3>
                  
                  <div class="space-y-2">
                    <button 
                      @click="addText"
                      class="w-full flex items-center gap-3 px-4 py-3 bg-white hover:bg-blue-50 text-blue-700 rounded-lg border border-blue-200 transition-all hover:shadow-sm"
                    >
                      <span class="text-xl bg-blue-100 text-blue-600 w-8 h-8 rounded-lg flex items-center justify-center">T</span>
                      <div class="text-left">
                        <div class="font-medium">Add Text</div>
                        <div class="text-xs text-gray-500">Create custom text elements</div>
                      </div>
                    </button>
                    
                    <button 
                      @click="triggerImageUpload"
                      class="w-full flex items-center gap-3 px-4 py-3 bg-white hover:bg-green-50 text-green-700 rounded-lg border border-green-200 transition-all hover:shadow-sm"
                    >
                      <span class="text-xl bg-green-100 text-green-600 w-8 h-8 rounded-lg flex items-center justify-center">📷</span>
                      <div class="text-left">
                        <div class="font-medium">Upload Image</div>
                        <div class="text-xs text-gray-500">Add your own photos</div>
                      </div>
                    </button>
                    
                    <button 
                      @click="showCliparts = true"
                      class="w-full flex items-center gap-3 px-4 py-3 bg-white hover:bg-purple-50 text-purple-700 rounded-lg border border-purple-200 transition-all hover:shadow-sm"
                    >
                      <span class="text-xl bg-purple-100 text-purple-600 w-8 h-8 rounded-lg flex items-center justify-center">🎨</span>
                      <div class="text-left">
                        <div class="font-medium">Add Clipart</div>
                        <div class="text-xs text-gray-500">Browse illustration library</div>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Recent Designs -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="text-orange-500">⏱️</span>
                    Recent Designs
                  </h3>
                  <div class="space-y-2 max-h-40 overflow-y-auto">
                    <button 
                      v-for="design in recentDesigns"
                      :key="design.id"
                      @click="loadDesign(design)"
                      class="w-full text-left p-3 hover:bg-white rounded-lg border border-gray-200 hover:shadow-sm transition-all"
                    >
                      <div class="font-medium text-sm truncate">{{ design.name }}</div>
                      <div class="text-xs text-gray-500">{{ formatDate(design.updated_at) }}</div>
                    </button>
                    <div v-if="recentDesigns.length === 0" class="text-center py-4 text-gray-500 text-sm">
                      No recent designs
                    </div>
                  </div>
                </div>

                <!-- My Assets -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="text-indigo-500">📁</span>
                    My Assets
                  </h3>
                  <div class="space-y-2 max-h-40 overflow-y-auto">
                    <div 
                      v-for="asset in userAssets"
                      :key="asset.id"
                      class="flex items-center gap-3 p-3 hover:bg-white rounded-lg border border-gray-200 hover:shadow-sm cursor-pointer transition-all"
                      @click="addImageFromAsset(asset)"
                    >
                      <img :src="asset.thumbnail_url" class="w-10 h-10 object-cover rounded-lg" />
                      <div class="flex-1 min-w-0">
                        <div class="font-medium text-sm truncate">{{ asset.original_filename }}</div>
                        <div class="text-xs text-gray-500">{{ formatFileSize(asset.file_size) }}</div>
                      </div>
                    </div>
                    <div v-if="userAssets.length === 0" class="text-center py-4 text-gray-500 text-sm">
                      No assets uploaded
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Canvas Area - FIXED AND SCROLLABLE -->
          <div class="flex-1 flex flex-col bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Canvas Header -->
            <div class="bg-gray-50 border-b px-6 py-4">
              <div class="flex items-center justify-between">
                <div>
                  <h2 class="text-lg font-semibold text-gray-800">Design Canvas</h2>
                  <p class="text-sm text-gray-500">Click and drag to position elements</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                  <span>Zoom: {{ Math.round(zoomLevel * 100) }}%</span>
                  <button @click="resetZoom" class="ml-2 px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">Reset</button>
                </div>
              </div>
            </div>
            
            <!-- Canvas Container - FIXED HEIGHT WITH SCROLL -->
            <div class="flex-1 p-6 overflow-auto bg-gray-50">
              <div class="bg-white rounded-lg shadow-inner border-2 border-dashed border-gray-300 w-full h-full max-w-4xl max-h-[70vh] mx-auto">
                <ProductDesigner 
                  ref="designer"
                  :product-type-id="productType.id"
                  @saved="onDesignSaved"
                  @changed="onDesignChanged"
                  @zoom="updateZoom"
                />
              </div>
            </div>
            
            <!-- Canvas Footer/Toolbar -->
            <div class="bg-gray-50 border-t px-6 py-3">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <button @click="zoomOut" class="p-2 rounded-lg hover:bg-gray-200">−</button>
                  <span class="text-sm font-medium">{{ Math.round(zoomLevel * 100) }}%</span>
                  <button @click="zoomIn" class="p-2 rounded-lg hover:bg-gray-200">+</button>
                </div>
                
                <div class="flex items-center gap-2">
                  <button @click="centerCanvas" class="px-3 py-1 text-sm bg-gray-200 rounded-lg hover:bg-gray-300">
                    Center View
                  </button>
                  <button @click="toggleGrid" class="px-3 py-1 text-sm bg-gray-200 rounded-lg hover:bg-gray-300" :class="{ 'bg-blue-200': showGrid }">
                    Grid {{ showGrid ? 'On' : 'Off' }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Panel - FIXED PROPERTIES PANEL -->
          <div class="w-80 flex-shrink-0 flex flex-col">
            <!-- Properties Panel - COMPLETELY FIXED -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-full">
              <!-- Panel Header -->
              <div class="bg-gradient-to-r from-indigo-600 to-purple-700 p-5 flex-shrink-0">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-white font-bold text-xl">Properties</h3>
                    <p class="text-indigo-100 text-sm mt-1">Edit selected element</p>
                  </div>
                  <button 
                    @click="togglePropertiesPanel"
                    class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition-colors"
                    :title="showProperties ? 'Hide Properties' : 'Show Properties'"
                  >
                    <span class="text-xl">{{ showProperties ? '◀' : '▶' }}</span>
                  </button>
                </div>
              </div>
              
              <div v-show="showProperties" class="flex-grow overflow-y-auto p-5">
                <div v-if="$refs.designer && $refs.designer.selectedObject">
                  <div class="space-y-6">
                    <!-- Background Removal for Images -->
                    <div v-if="$refs.designer.selectedObject.type === 'image'" class="bg-blue-50 rounded-xl p-5 border border-blue-200">
                      <h4 class="font-bold text-blue-800 mb-4 flex items-center gap-2 text-lg">
                        <span class="text-2xl">🖼️</span>
                        Image Settings
                      </h4>
                      <div class="flex items-center justify-between">
                        <div>
                          <div class="font-semibold text-gray-800 text-lg">Remove Background</div>
                          <div class="text-sm text-gray-600 mt-1">AI-powered background removal</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                          <input 
                            type="checkbox" 
                            :checked="imageBackgroundRemoved"
                            @change="toggleImageBackground"
                            class="sr-only peer"
                          />
                          <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                      </div>
                    </div>
                    
                    <!-- Text Properties -->
                    <div v-if="$refs.designer.selectedObject.type === 'i-text'" class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-200">
                      <h4 class="font-bold text-blue-800 mb-4 text-lg">🔤 Text Properties</h4>
                      <div class="space-y-5">
                        <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-3">Font Family</label>
                          <select 
                            v-model="$refs.designer.selectedObject.fontFamily" 
                            @change="updateCanvas"
                            class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm"
                          >
                            <option v-for="font in fonts" :value="font">{{ font }}</option>
                          </select>
                        </div>
                        
                        <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-3">Text Content</label>
                          <textarea
                            v-model="$refs.designer.selectedObject.text"
                            @input="updateCanvas"
                            class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 h-24 resize-none shadow-sm"
                            placeholder="Enter your text..."
                          ></textarea>
                        </div>
                        
                        <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-3">Text Color</label>
                          <input 
                            type="color" 
                            v-model="$refs.designer.selectedObject.fill" 
                            @change="updateCanvas"
                            class="w-full h-12 rounded-xl border border-gray-300 cursor-pointer shadow-sm"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Font Size: {{ $refs.designer.selectedObject.fontSize }}px
                          </label>
                          <input 
                            type="range" 
                            v-model.number="$refs.designer.selectedObject.fontSize" 
                            min="8" 
                            max="200"
                            @input="updateCanvas"
                            class="w-full accent-blue-600"
                          />
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3 pt-2">
                          <label class="flex items-center justify-center p-3 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 hover:border-blue-300 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input 
                              type="checkbox" 
                              :checked="$refs.designer.selectedObject.fontWeight === 'bold'"
                              @change="toggleBold"
                              class="sr-only"
                            />
                            <span class="text-lg font-bold text-gray-700">B</span>
                          </label>
                          <label class="flex items-center justify-center p-3 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 hover:border-blue-300 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input 
                              type="checkbox" 
                              :checked="$refs.designer.selectedObject.fontStyle === 'italic'"
                              @change="toggleItalic"
                              class="sr-only"
                            />
                            <span class="text-lg italic text-gray-700">I</span>
                          </label>
                          <label class="flex items-center justify-center p-3 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 hover:border-blue-300 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input 
                              type="checkbox" 
                              :checked="$refs.designer.selectedObject.underline"
                              @change="toggleUnderline"
                              class="sr-only"
                            />
                            <span class="text-lg underline text-gray-700">U</span>
                          </label>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Transform Properties -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-200">
                      <h4 class="font-bold text-green-800 mb-4 text-lg">📐 Transform</h4>
                      <div class="space-y-5">
                        <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Opacity: {{ Math.round($refs.designer.selectedObject.opacity * 100) }}%
                          </label>
                          <input 
                            type="range" 
                            v-model.number="$refs.designer.selectedObject.opacity" 
                            min="0" 
                            max="1"
                            step="0.01"
                            @input="updateCanvas"
                            class="w-full accent-green-600"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Rotation: {{ Math.round($refs.designer.selectedObject.angle || 0) }}°
                          </label>
                          <input 
                            type="range" 
                            v-model.number="$refs.designer.selectedObject.angle" 
                            min="0" 
                            max="360"
                            @input="updateCanvas"
                            class="w-full accent-green-600"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Width Scale: {{ Math.round(($refs.designer.selectedObject.scaleX || 1) * 100) }}%
                          </label>
                          <input 
                            type="range" 
                            v-model.number="$refs.designer.selectedObject.scaleX" 
                            min="0.1" 
                            max="3"
                            step="0.1"
                            @input="updateCanvas"
                            class="w-full accent-green-600"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Height Scale: {{ Math.round(($refs.designer.selectedObject.scaleY || 1) * 100) }}%
                          </label>
                          <input 
                            type="range" 
                            v-model.number="$refs.designer.selectedObject.scaleY" 
                            min="0.1" 
                            max="3"
                            step="0.1"
                            @input="updateCanvas"
                            class="w-full accent-green-600"
                          />
                        </div>
                      </div>
                    </div>
                    
                    <!-- Position Properties -->
                    <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-5 border border-yellow-200">
                      <h4 class="font-bold text-yellow-800 mb-4 text-lg">📍 Position</h4>
                      <div class="space-y-4">
                        <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-3">Position X: {{ Math.round($refs.designer.selectedObject.left || 0) }}</label>
                          <input 
                            type="number"
                            v-model.number="$refs.designer.selectedObject.left" 
                            @input="updateCanvas"
                            class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 shadow-sm"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-3">Position Y: {{ Math.round($refs.designer.selectedObject.top || 0) }}</label>
                          <input 
                            type="number"
                            v-model.number="$refs.designer.selectedObject.top" 
                            @input="updateCanvas"
                            class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 shadow-sm"
                          />
                        </div>
                      </div>
                    </div>
                    
                    <!-- Image Filters (for images) -->
                    <div v-if="$refs.designer.selectedObject.type === 'image' || $refs.designer.selectedObject.src" class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl p-5 border border-purple-200">
                      <h4 class="font-bold text-purple-800 mb-4 text-lg">✨ Filters</h4>
                      <div class="space-y-5">
                        <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Brightness: {{ Math.round((($refs.designer.selectedObject.brightness || 0) + 0.5) * 100) }}%
                          </label>
                          <input 
                            type="range" 
                            v-model.number="$refs.designer.selectedObject.brightness" 
                            min="-0.5" 
                            max="0.5" 
                            step="0.05"
                            @input="adjustImageFilters"
                            class="w-full accent-purple-600"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Contrast: {{ Math.round((($refs.designer.selectedObject.contrast || 0) + 0.5) * 100) }}%
                          </label>
                          <input 
                            type="range" 
                            v-model.number="$refs.designer.selectedObject.contrast" 
                            min="-0.5" 
                            max="0.5" 
                            step="0.05"
                            @input="adjustImageFilters"
                            class="w-full accent-purple-600"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Saturation: {{ Math.round((($refs.designer.selectedObject.saturation || 0) + 0.5) * 100) }}%
                          </label>
                          <input 
                            type="range" 
                            v-model.number="$refs.designer.selectedObject.saturation" 
                            min="-0.5" 
                            max="0.5" 
                            step="0.05"
                            @input="adjustImageFilters"
                            class="w-full accent-purple-600"
                          />
                        </div>
                      </div>
                    </div>
                    
                    <!-- Layer Controls -->
                    <div class="bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl p-5 border border-gray-200">
                      <h4 class="font-bold text-gray-800 mb-4 text-lg">📊 Layer Position</h4>
                      <div class="grid grid-cols-2 gap-3">
                        <button @click="bringForward" class="px-4 py-3 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 text-sm font-medium transition-all">
                          ↑ Forward
                        </button>
                        <button @click="sendBackwards" class="px-4 py-3 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 text-sm font-medium transition-all">
                          ↓ Backward
                        </button>
                        <button @click="bringToFront" class="px-4 py-3 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 text-sm font-medium transition-all col-span-2">
                         ⇈ Bring to Front
                        </button>
                        <button @click="sendToBack" class="px-4 py-3 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 text-sm font-medium transition-all col-span-2">
                         ⇊ Send to Back
                        </button>
                      </div>
                    </div>
                    
                    <!-- Delete Button -->
                    <button @click="deleteObject" class="w-full py-4 bg-red-500 hover:bg-red-600 text-white rounded-xl flex items-center justify-center gap-3 font-semibold transition-colors shadow-lg hover:shadow-xl">
                      <span class="text-xl">🗑️</span>
                      Delete Element
                    </button>
                  </div>
                </div>
                <div v-else class="text-center py-16">
                  <div class="text-6xl mb-4">🎨</div>
                  <div class="text-gray-600 font-bold text-xl">No Element Selected</div>
                  <div class="text-gray-500 mt-2">Click on an element to edit its properties</div>
                </div>
              </div>
            </div>

            <!-- Preview Panel -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6 flex-shrink-0">
              <div class="bg-gradient-to-r from-green-600 to-teal-700 p-5">
                <h3 class="text-white font-bold text-xl">Preview</h3>
                <p class="text-green-100 text-sm mt-1">Real-time design preview</p>
              </div>
              <div class="p-5">
                <div class="aspect-square bg-gray-100 rounded-xl flex items-center justify-center border-2 border-dashed border-gray-300">
                  <img 
                    v-if="previewUrl" 
                    :src="previewUrl" 
                    alt="Design Preview" 
                    class="max-w-full max-h-full object-contain rounded-lg"
                  />
                  <div v-else class="text-center">
                    <div class="text-4xl mb-3">👁️</div>
                    <div class="text-gray-500 font-medium">No preview available</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Templates Modal -->
      <div v-if="showTemplates" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden shadow-2xl">
          <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <div>
              <h3 class="text-2xl font-bold text-gray-900">Design Templates</h3>
              <p class="text-gray-600 mt-1">Choose from professionally designed templates</p>
            </div>
            <button @click="showTemplates = false" class="text-gray-400 hover:text-gray-600 text-3xl">×</button>
          </div>
          <div class="p-6 max-h-[70vh] overflow-y-auto">
            <div class="mb-6 flex gap-4">
              <input
                v-model="templateSearch"
                type="text"
                placeholder="Search templates..."
                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              <select
                v-model="templateCategory"
                class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">All Categories</option>
                <option v-for="category in templateCategories" :key="category" :value="category">
                  {{ category.charAt(0).toUpperCase() + category.slice(1) }}
                </option>
              </select>
            </div>
            
            <div v-if="loadingTemplates" class="flex justify-center items-center h-64">
              <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
            </div>
            
            <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
              <div 
                v-for="template in templates" 
                :key="template.id"
                class="border rounded-xl overflow-hidden cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all hover:shadow-lg"
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
          <div class="p-6 border-t border-gray-200 text-center">
            <button @click="showTemplates = false" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">
              Close
            </button>
          </div>
        </div>
      </div>

      <!-- Clipart Modal -->
      <div v-if="showCliparts" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-96 overflow-hidden shadow-2xl">
          <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <div>
              <h3 class="text-2xl font-bold text-gray-900">Clipart Library</h3>
              <p class="text-gray-600 mt-1">Browse our collection of illustrations</p>
            </div>
            <button @click="showCliparts = false" class="text-gray-400 hover:text-gray-600 text-3xl">×</button>
          </div>
          <div class="p-6 max-h-80 overflow-y-auto">
            <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
              <div 
                v-for="clipart in cliparts" 
                :key="clipart.id"
                class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:ring-2 hover:ring-blue-500 hover:shadow-lg transition-all"
                @click="addClipart(clipart)"
              >
                <img :src="clipart.image_url" :alt="clipart.title" class="w-full h-full object-cover" />
              </div>
            </div>
          </div>
          <div class="p-6 border-t border-gray-200 text-center">
            <button @click="showCliparts = false" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">
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
      cliparts: [],
      fonts: [],
      userAssets: [],
      recentDesigns: [],
      selectedObject: null,
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
    
    bringToFront() {
      this.$refs.designer.bringToFront();
      this.hasChanges = true;
      this.saveToHistory();
    },
    
    sendToBack() {
      this.$refs.designer.sendToBack();
      this.hasChanges = true;
      this.saveToHistory();
    },
  },
};
</script>