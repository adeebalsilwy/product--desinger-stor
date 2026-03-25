<template>
  <CustomerLayout :showNav="false" :showFooter="false" :showCart="false" contentClass="designer-content">
    <div class="app-container">
      <div class="sidebar">
        <div class="nav-item" v-for="item in navItems" :key="item.label">
          <a :href="item.href || '#'" class="nav-icon" :class="{ active: item.active }">
            <i :class="item.icon"></i>
          </a>
          <span class="nav-label">{{ item.label }}</span>
        </div>
      </div>

      <div class="design-area">
        <div class="user-panel">
          <div class="user-avatar neumorphic-btn">
            <i class="fas fa-user"></i>
          </div>
        </div>

        <div class="top-toolbar neumorphic">
          <div class="toolbar-group">
            <button class="toolbar-btn neumorphic-btn" @click="undo"><i class="fas fa-undo"></i></button>
            <button class="toolbar-btn neumorphic-btn" @click="redo"><i class="fas fa-redo"></i></button>
            <button class="toolbar-btn neumorphic-btn" @click="duplicateSelected" :disabled="!selectedElementId"><i class="fas fa-clone"></i></button>
            <button class="toolbar-btn neumorphic-btn" @click="deleteSelected" :disabled="!selectedElementId"><i class="fas fa-trash"></i></button>
          </div>

          <div class="toolbar-group">
            <button class="hero-action save-btn" @click="saveDesign">
              <i class="fas fa-save"></i>
              <span>حفظ التصميم</span>
            </button>
            <button class="hero-action preview-btn" @click="showMockup = !showMockup">
              <i class="fas fa-female"></i>
              <span>{{ showMockup ? 'إخفاء المجسم' : 'عرض التصميم على المجسم' }}</span>
            </button>
          </div>
        </div>

        <div class="canvas-host">
          <div class="design-stage">
            <div class="stage-background" :style="{ background: activeTemplate.background }"></div>
            <canvas ref="templateCanvas" class="dress-template" :width="stageWidth" :height="stageHeight"></canvas>
            <canvas
              ref="drawingCanvas"
              class="drawing-layer"
              :width="stageWidth"
              :height="stageHeight"
              @mousedown="onStageMouseDown"
              @mousemove="onStageMouseMove"
              @mouseup="onStageMouseUp"
              @mouseleave="onStageMouseUp"
            ></canvas>

            <div class="elements-layer">
              <div
                v-for="element in orderedElements"
                :key="element.id"
                class="design-element"
                :class="{ selected: selectedElementId === element.id }"
                :style="elementStyle(element)"
                @mousedown.stop="startElementDrag($event, element)"
                @click.stop="selectElement(element.id)"
              >
                <template v-if="element.type === 'text'">
                  <div class="text-node" :style="textStyle(element)">{{ element.text }}</div>
                </template>
                <template v-else-if="element.type === 'image'">
                  <img :src="element.src" class="image-node" alt="design image" draggable="false" />
                </template>
                <template v-else-if="element.type === 'rect'">
                  <div class="shape-node rect-node" :style="shapeStyle(element)"></div>
                </template>
                <template v-else-if="element.type === 'circle'">
                  <div class="shape-node circle-node" :style="shapeStyle(element)"></div>
                </template>
                <template v-else-if="element.type === 'triangle'">
                  <div class="triangle-node" :style="triangleStyle(element)"></div>
                </template>

                <button v-if="selectedElementId === element.id" type="button" class="resize-handle" @mousedown.stop="startResize(element)"></button>
                <button v-if="selectedElementId === element.id" type="button" class="rotate-handle" @mousedown.stop="startRotate(element)">
                  <i class="fas fa-sync-alt"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="bottom-toolbar neumorphic">
          <button class="control-btn delete-btn" @click="clearDrawing"><i class="fas fa-eraser"></i><span>مسح الرسم</span></button>
          <button class="control-btn reset-btn" @click="resetDesign"><i class="fas fa-redo"></i><span>إعادة ضبط</span></button>
          <button class="control-btn save-btn" @click="saveDesign"><i class="fas fa-download"></i><span>تنزيل</span></button>
        </div>
      </div>

      <div class="tools-panel">
        <div class="tool-item" v-for="tool in tools" :key="tool.key">
          <button class="tool-icon" :class="{ active: activeTool === tool.key }" @click="activateTool(tool.key)">
            <i :class="tool.icon"></i>
          </button>
          <span class="tool-label">{{ tool.label }}</span>
        </div>
      </div>

      <div class="palette color-palette" v-if="openPalette === 'color'">
        <div class="palette-title">اختر لونًا</div>
        <div class="color-grid">
          <button v-for="color in colors" :key="color" class="color-option" :class="{ selected: currentColor === color }" :style="{ background: color }" @click="selectColor(color)"></button>
        </div>
      </div>

      <div class="palette size-palette" v-if="openPalette === 'size'">
        <div class="palette-title">اختر مقاس الأداة</div>
        <button v-for="size in brushSizes" :key="size" class="size-option" :class="{ selected: brushSize === size }" @click="brushSize = size">{{ size }}px</button>
      </div>

      <div class="palette sizes-palette" v-if="openPalette === 'dress-sizes'">
        <div class="palette-title">المقاس</div>
        <button v-for="size in availableSizes" :key="size" class="size-option" :class="{ selected: dressSize === size }" @click="dressSize = size">{{ size }}</button>
      </div>

      <div class="palette dress-colors-palette" v-if="openPalette === 'dress-color'">
        <div class="palette-title">لون القطعة</div>
        <div class="color-grid">
          <button v-for="color in garmentColors" :key="color" class="color-option" :class="{ selected: garmentColor === color }" :style="{ background: color }" @click="setGarmentColor(color)"></button>
        </div>
      </div>

      <div class="palette template-palette" v-if="openPalette === 'templates'">
        <div class="palette-title">القوالب</div>
        <div class="template-grid">
          <button v-for="template in templates" :key="template.id" class="template-card" :class="{ active: activeTemplate.id === template.id }" @click="applyTemplate(template)">
            <span class="template-preview" :style="{ background: template.background }"></span>
            <span>{{ template.name }}</span>
          </button>
        </div>
      </div>

      <div class="palette text-palette" v-if="openPalette === 'text'">
        <div class="palette-title">إضافة نص</div>
        <textarea v-model="draftText" class="palette-input" rows="3"></textarea>
        <button class="action-btn" @click="addTextElement">إضافة النص</button>
      </div>

      <div class="palette image-palette" v-if="openPalette === 'image'">
        <div class="palette-title">إضافة صورة</div>
        <button class="action-btn" @click="$refs.imageInput.click()">اختيار صورة</button>
      </div>

      <div class="palette properties-palette" v-if="selectedElement">
        <div class="palette-title">خصائص العنصر</div>

        <template v-if="selectedElement.type === 'text'">
          <label class="property-field">
            <span>النص</span>
            <textarea v-model="selectedElement.text" class="palette-input" rows="3" @input="commitChanges"></textarea>
          </label>
          <label class="property-field">
            <span>حجم الخط</span>
            <input type="range" min="12" max="120" v-model.number="selectedElement.fontSize" @input="commitChanges" />
          </label>
          <label class="property-field">
            <span>وزن الخط</span>
            <select v-model="selectedElement.fontWeight" @change="commitChanges">
              <option value="400">عادي</option>
              <option value="600">متوسط</option>
              <option value="700">عريض</option>
              <option value="900">ثقيل</option>
            </select>
          </label>
        </template>

        <template v-else>
          <label class="property-field">
            <span>العرض</span>
            <input type="range" min="40" max="800" v-model.number="selectedElement.width" @input="commitChanges" />
          </label>
          <label class="property-field">
            <span>الارتفاع</span>
            <input type="range" min="40" max="800" v-model.number="selectedElement.height" @input="commitChanges" />
          </label>
        </template>

        <label class="property-field">
          <span>الشفافية</span>
          <input type="range" min="0.1" max="1" step="0.05" v-model.number="selectedElement.opacity" @input="commitChanges" />
        </label>

        <label class="property-field">
          <span>الدوران</span>
          <input type="range" min="-180" max="180" v-model.number="selectedElement.rotation" @input="commitChanges" />
        </label>

        <label class="property-field" v-if="selectedElement.type !== 'image'">
          <span>اللون</span>
          <input type="color" v-model="selectedElement.color" @input="commitChanges" />
        </label>

        <div class="properties-actions">
          <button class="action-btn" @click="bringForward">للأمام</button>
          <button class="action-btn" @click="sendBackward">للخلف</button>
        </div>
      </div>

      <div class="wardrobe-container-3d" :class="{ open: wardrobeOpen }">
        <div class="wardrobe-3d" :class="{ open: wardrobeOpen }">
          <div class="wardrobe-body-3d"></div>
          <div class="wardrobe-door-3d door-left-3d"><div class="door-handle-3d"></div></div>
          <div class="wardrobe-door-3d door-right-3d"><div class="door-handle-3d"></div></div>

          <div class="wardrobe-inside-3d">
            <div class="clothes-rail-3d">
              <div class="hanger-3d" style="left: 15%"></div>
              <div class="hanger-3d" style="left: 35%; transform: rotate(6deg)"></div>
              <div class="hanger-3d" style="left: 55%; transform: rotate(-4deg)"></div>
              <div class="hanger-3d" style="left: 75%"></div>
            </div>

            <button v-for="item in wardrobeItems" :key="item.id" class="clothing-item-3d" @click="selectWardrobeItem(item)">
              <div class="clothing-img-container-3d">
                <div class="wardrobe-fake-image" :style="{ background: item.background }"></div>
              </div>
              <div class="clothing-name-3d">{{ item.name }}</div>
            </button>
          </div>
        </div>

        <button class="wardrobe-control-3d" @click="wardrobeOpen = !wardrobeOpen">
          <i :class="wardrobeOpen ? 'fas fa-times' : 'fas fa-tshirt'"></i>
        </button>
      </div>

      <transition name="preview-slide">
        <div class="preview-panel" v-if="showMockup">
          <div class="preview-header">
            <div>
              <h3>عرض التصميم على المجسم</h3>
              <p>مجسم نسائي احترافي بأسلوب CSS 3D يعرض التصميم والقالب المختار بشكل مباشر</p>
            </div>
            <div class="preview-actions">
              <button class="action-btn" @click="spinMockup = !spinMockup">{{ spinMockup ? 'إيقاف الدوران' : 'تدوير المجسم' }}</button>
            </div>
          </div>

          <div class="mockup-stage">
            <div class="mannequin-3d" :class="{ spinning: spinMockup }">
              <div class="mannequin-head"></div>
              <div class="mannequin-neck"></div>
              <div class="mannequin-body">
                <div class="garment-surface" :style="{ background: garmentColor }">
                  <img :src="mockupImage" class="garment-texture" alt="mockup texture" />
                </div>
              </div>
              <div class="arm arm-left"></div>
              <div class="arm arm-right"></div>
              <div class="forearm forearm-left"></div>
              <div class="forearm forearm-right"></div>
              <div class="hip"></div>
              <div class="leg leg-left"></div>
              <div class="leg leg-right"></div>
              <div class="shin shin-left"></div>
              <div class="shin shin-right"></div>
              <div class="floor-shadow"></div>
            </div>
          </div>
        </div>
      </transition>

      <input ref="imageInput" type="file" class="hidden-file" accept="image/*" @change="onImageSelected" />
    </div>
  </CustomerLayout>
</template>

<script>
export default {
  name: 'CreateCanvasProfessional',
  data() {
    return {
      navItems: [
        { label: 'الرئيسية', icon: 'fas fa-home', active: true },
        { label: 'تصاميمي', icon: 'fas fa-palette', active: false },
        { label: 'المنتجات', icon: 'fas fa-tshirt', active: false },
        { label: 'لوحة التحكم', icon: 'fas fa-file-invoice', active: false },
        { label: 'الملف الشخصي', icon: 'fas fa-user', active: false }
      ],
      tools: [
        { key: 'brush', label: 'فرشاة', icon: 'fas fa-paint-brush' },
        { key: 'pen', label: 'قلم', icon: 'fas fa-pen' },
        { key: 'eraser', label: 'ممحاة', icon: 'fas fa-eraser' },
        { key: 'color', label: 'ألوان', icon: 'fas fa-palette' },
        { key: 'size', label: 'مقاس', icon: 'fas fa-ruler' },
        { key: 'dress-sizes', label: 'المقاسات', icon: 'fas fa-ruler-combined' },
        { key: 'dress-color', label: 'لون القطعة', icon: 'fas fa-fill-drip' },
        { key: 'templates', label: 'القوالب', icon: 'fas fa-object-group' },
        { key: 'text', label: 'نص', icon: 'fas fa-font' },
        { key: 'shape', label: 'أشكال', icon: 'fas fa-shapes' },
        { key: 'image', label: 'صور', icon: 'fas fa-image' },
        { key: 'select', label: 'تحديد', icon: 'fas fa-mouse-pointer' }
      ],
      colors: ['#000000','#ff0000','#00c853','#2962ff','#ffeb3b','#ff00ff','#00ffff','#ff9800','#7c4dff','#e91e63','#03a9f4','#8bc34a','#ffffff','#bdbdbd','#616161'],
      garmentColors: ['#ffffff','#f8bbd0','#e1bee7','#d1c4e9','#c5cae9','#b3e5fc','#b2ebf2','#b2dfdb','#dcedc8','#f0f4c3','#ffecb3','#ffe0b2','#ffccbc','#d7ccc8','#cfd8dc'],
      brushSizes: [1,3,5,8,10,15,20,30,50],
      availableSizes: ['XS','S','M','L','XL','XXL'],
      templates: [
        { id: 'soft', name: 'هادئ', background: 'linear-gradient(180deg,#ffffff 0%,#f5f7ff 100%)' },
        { id: 'violet', name: 'نيون موف', background: 'linear-gradient(180deg,#ede9fe 0%,#ddd6fe 45%,#f8fafc 100%)' },
        { id: 'mesh', name: 'شبكي', background: 'linear-gradient(180deg,#ffffff 0%,#ecfeff 100%), repeating-linear-gradient(45deg, rgba(156,39,176,0.05) 0 10px, transparent 10px 20px)' },
        { id: 'luxury', name: 'فاخر', background: 'radial-gradient(circle at top,#f3e8ff,#e9d5ff 35%,#f8fafc 75%)' }
      ],
      wardrobeItems: [
        { id: 'dress', name: 'فستان حديث', background: 'linear-gradient(180deg,#f8bbd0,#fce4ec)' },
        { id: 'shirt', name: 'قميص رسمي', background: 'linear-gradient(180deg,#bbdefb,#e3f2fd)' },
        { id: 'jacket', name: 'جاكيت', background: 'linear-gradient(180deg,#6d4c41,#3e2723)' },
        { id: 'hoodie', name: 'هودي', background: 'linear-gradient(180deg,#d1c4e9,#ede7f6)' }
      ],
      stageWidth: 980,
      stageHeight: 680,
      activeTool: 'brush',
      openPalette: null,
      currentColor: '#000000',
      brushSize: 5,
      garmentColor: '#ffffff',
      dressSize: 'M',
      activeTemplate: { id: 'soft', name: 'هادئ', background: 'linear-gradient(180deg,#ffffff 0%,#f5f7ff 100%)' },
      draftText: 'نص جديد',
      wardrobeOpen: false,
      showMockup: false,
      spinMockup: true,
      mockupImage: '',
      isDrawing: false,
      selectedElementId: null,
      dragState: null,
      resizeState: null,
      rotationState: null,
      elements: [],
      history: [],
      historyIndex: -1,
      drawingContext: null,
      templateContext: null
    }
  },
  computed: {
    orderedElements() {
      return [...this.elements].sort((a, b) => (a.zIndex || 0) - (b.zIndex || 0))
    },
    selectedElement() {
      return this.elements.find(item => item.id === this.selectedElementId) || null
    }
  },
  mounted() {
    this.templateContext = this.$refs.templateCanvas.getContext('2d')
    this.drawingContext = this.$refs.drawingCanvas.getContext('2d')
    this.drawingContext.lineCap = 'round'
    this.drawingContext.lineJoin = 'round'
    this.drawTemplate()
    this.pushHistory(true)
    this.updateMockupImage()
    document.addEventListener('click', this.handleOutsideClick)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleOutsideClick)
  },
  methods: {
    handleOutsideClick(event) {
      if (!event.target.closest('.palette') && !event.target.closest('.tool-icon') && !event.target.closest('.design-element')) {
        this.openPalette = null
      }
    },
    activateTool(tool) {
      this.activeTool = tool
      if (['color','size','dress-sizes','dress-color','templates','text','image'].includes(tool)) {
        this.openPalette = tool
      } else if (tool === 'shape') {
        this.openPalette = null
        this.addShape('rect')
      } else {
        this.openPalette = null
      }
    },
    selectColor(color) {
      this.currentColor = color
      if (this.selectedElement && this.selectedElement.type !== 'image') {
        this.selectedElement.color = color
        this.commitChanges()
      }
    },
    setGarmentColor(color) {
      this.garmentColor = color
      this.drawTemplate()
      this.updateMockupImage()
    },
    applyTemplate(template) {
      this.activeTemplate = template
      this.openPalette = null
      this.commitChanges()
    },
    selectWardrobeItem() {
      this.wardrobeOpen = false
      this.updateMockupImage()
    },
    drawTemplate() {
      const ctx = this.templateContext
      ctx.clearRect(0, 0, this.stageWidth, this.stageHeight)
      ctx.save()
      ctx.fillStyle = this.garmentColor
      const cx = this.stageWidth / 2
      const top = 45
      const bodyHeight = this.stageHeight - 95
      ctx.beginPath()
      ctx.moveTo(cx - 90, top + 20)
      ctx.quadraticCurveTo(cx - 145, top + 55, cx - 185, top + 160)
      ctx.quadraticCurveTo(cx - 210, top + 235, cx - 235, top + 470)
      ctx.quadraticCurveTo(cx - 130, top + bodyHeight, cx, top + bodyHeight - 10)
      ctx.quadraticCurveTo(cx + 130, top + bodyHeight, cx + 235, top + 470)
      ctx.quadraticCurveTo(cx + 210, top + 235, cx + 185, top + 160)
      ctx.quadraticCurveTo(cx + 145, top + 55, cx + 90, top + 20)
      ctx.quadraticCurveTo(cx + 40, top - 5, cx + 12, top + 4)
      ctx.lineTo(cx + 32, top + 52)
      ctx.quadraticCurveTo(cx + 42, top + 92, cx + 22, top + 155)
      ctx.lineTo(cx - 22, top + 155)
      ctx.quadraticCurveTo(cx - 42, top + 92, cx - 32, top + 52)
      ctx.lineTo(cx - 12, top + 4)
      ctx.quadraticCurveTo(cx - 40, top - 5, cx - 90, top + 20)
      ctx.closePath()
      ctx.fill()
      ctx.strokeStyle = 'rgba(74,20,140,0.15)'
      ctx.lineWidth = 3
      ctx.stroke()
      ctx.restore()
    },
    getPoint(event) {
      const rect = this.$refs.drawingCanvas.getBoundingClientRect()
      return {
        x: ((event.clientX - rect.left) / rect.width) * this.stageWidth,
        y: ((event.clientY - rect.top) / rect.height) * this.stageHeight
      }
    },
    onStageMouseDown(event) {
      if (!['brush','pen','eraser'].includes(this.activeTool)) return
      const point = this.getPoint(event)
      this.isDrawing = true
      this.drawingContext.beginPath()
      this.drawingContext.moveTo(point.x, point.y)
      this.drawingContext.lineWidth = this.activeTool === 'pen' ? Math.max(1, this.brushSize - 2) : this.brushSize
      if (this.activeTool === 'eraser') {
        this.drawingContext.globalCompositeOperation = 'destination-out'
      } else {
        this.drawingContext.globalCompositeOperation = 'source-over'
        this.drawingContext.strokeStyle = this.currentColor
      }
    },
    onStageMouseMove(event) {
      if (this.isDrawing) {
        const point = this.getPoint(event)
        this.drawingContext.lineTo(point.x, point.y)
        this.drawingContext.stroke()
        this.updateMockupImage()
      }
      if (this.dragState && this.selectedElement) {
        const point = this.getPoint(event)
        this.selectedElement.x = this.clamp(point.x - this.dragState.offsetX, 0, this.stageWidth - 20)
        this.selectedElement.y = this.clamp(point.y - this.dragState.offsetY, 0, this.stageHeight - 20)
        this.updateMockupImage()
      }
      if (this.resizeState && this.selectedElement) {
        const point = this.getPoint(event)
        this.selectedElement.width = this.clamp(Math.round(point.x - this.selectedElement.x), 40, this.stageWidth)
        this.selectedElement.height = this.clamp(Math.round(point.y - this.selectedElement.y), 40, this.stageHeight)
        this.updateMockupImage()
      }
      if (this.rotationState && this.selectedElement) {
        const point = this.getPoint(event)
        const cx = this.selectedElement.x + this.selectedElement.width / 2
        const cy = this.selectedElement.y + this.selectedElement.height / 2
        this.selectedElement.rotation = Math.round((Math.atan2(point.y - cy, point.x - cx) * 180 / Math.PI) + 90)
        this.updateMockupImage()
      }
    },
    onStageMouseUp() {
      if (this.isDrawing) {
        this.isDrawing = false
        this.drawingContext.globalCompositeOperation = 'source-over'
        this.pushHistory()
      }
      if (this.dragState || this.resizeState || this.rotationState) {
        this.dragState = null
        this.resizeState = null
        this.rotationState = null
        this.pushHistory()
      }
    },
    addTextElement() {
      const id = this.uid()
      this.elements.push({
        id, type: 'text', text: this.draftText || 'نص جديد', x: 330, y: 180, width: 260, height: 120,
        rotation: 0, opacity: 1, color: this.currentColor, fontSize: 42, fontWeight: '700', zIndex: this.nextZ()
      })
      this.selectedElementId = id
      this.openPalette = null
      this.commitChanges()
    },
    addShape(type = 'rect') {
      const id = this.uid()
      this.elements.push({
        id, type, x: 320, y: 220, width: 180, height: 180, rotation: 0, opacity: 0.9, color: this.currentColor, zIndex: this.nextZ()
      })
      this.selectedElementId = id
      this.commitChanges()
    },
    onImageSelected(event) {
      const file = event.target.files?.[0]
      if (!file) return
      const reader = new FileReader()
      reader.onload = () => {
        const id = this.uid()
        this.elements.push({
          id, type: 'image', src: reader.result, x: 300, y: 160, width: 250, height: 250, rotation: 0, opacity: 1, zIndex: this.nextZ()
        })
        this.selectedElementId = id
        this.openPalette = null
        this.commitChanges()
      }
      reader.readAsDataURL(file)
      event.target.value = ''
    },
    selectElement(id) {
      this.selectedElementId = id
      this.activeTool = 'select'
    },
    startElementDrag(event, element) {
      if (['brush','pen','eraser'].includes(this.activeTool)) return
      const point = this.getPoint(event)
      this.selectedElementId = element.id
      this.dragState = { offsetX: point.x - element.x, offsetY: point.y - element.y }
    },
    startResize(element) {
      this.selectedElementId = element.id
      this.resizeState = true
    },
    startRotate(element) {
      this.selectedElementId = element.id
      this.rotationState = true
    },
    duplicateSelected() {
      if (!this.selectedElement) return
      const copy = JSON.parse(JSON.stringify(this.selectedElement))
      copy.id = this.uid()
      copy.x += 24
      copy.y += 24
      copy.zIndex = this.nextZ()
      this.elements.push(copy)
      this.selectedElementId = copy.id
      this.commitChanges()
    },
    deleteSelected() {
      if (!this.selectedElementId) return
      this.elements = this.elements.filter(item => item.id !== this.selectedElementId)
      this.selectedElementId = null
      this.commitChanges()
    },
    clearDrawing() {
      this.drawingContext.clearRect(0, 0, this.stageWidth, this.stageHeight)
      this.commitChanges()
    },
    resetDesign() {
      this.elements = []
      this.selectedElementId = null
      this.currentColor = '#000000'
      this.brushSize = 5
      this.garmentColor = '#ffffff'
      this.activeTemplate = this.templates[0]
      this.drawTemplate()
      this.drawingContext.clearRect(0, 0, this.stageWidth, this.stageHeight)
      this.commitChanges()
    },
    bringForward() {
      if (!this.selectedElement) return
      this.selectedElement.zIndex += 1
      this.reindexLayers()
      this.commitChanges()
    },
    sendBackward() {
      if (!this.selectedElement) return
      this.selectedElement.zIndex -= 1
      this.reindexLayers()
      this.commitChanges()
    },
    reindexLayers() {
      this.elements.sort((a, b) => a.zIndex - b.zIndex).forEach((item, index) => item.zIndex = index + 1)
    },
    commitChanges() {
      this.pushHistory()
      this.updateMockupImage()
    },
    pushHistory(initial = false) {
      const snapshot = JSON.stringify({
        activeTemplate: this.activeTemplate,
        garmentColor: this.garmentColor,
        dressSize: this.dressSize,
        drawing: this.$refs.drawingCanvas?.toDataURL('image/png') || '',
        elements: this.elements
      })
      if (initial) {
        this.history = [snapshot]
        this.historyIndex = 0
        return
      }
      if (this.history[this.historyIndex] === snapshot) return
      this.history = this.history.slice(0, this.historyIndex + 1)
      this.history.push(snapshot)
      this.historyIndex = this.history.length - 1
    },
    restoreSnapshot(snapshot) {
      const data = JSON.parse(snapshot)
      this.activeTemplate = data.activeTemplate
      this.garmentColor = data.garmentColor
      this.dressSize = data.dressSize
      this.elements = data.elements || []
      this.selectedElementId = null
      this.drawTemplate()
      const img = new Image()
      img.onload = () => {
        this.drawingContext.clearRect(0, 0, this.stageWidth, this.stageHeight)
        this.drawingContext.drawImage(img, 0, 0)
        this.updateMockupImage()
      }
      img.src = data.drawing
    },
    undo() {
      if (this.historyIndex <= 0) return
      this.historyIndex -= 1
      this.restoreSnapshot(this.history[this.historyIndex])
    },
    redo() {
      if (this.historyIndex >= this.history.length - 1) return
      this.historyIndex += 1
      this.restoreSnapshot(this.history[this.historyIndex])
    },
    elementStyle(element) {
      return {
        left: `${element.x}px`,
        top: `${element.y}px`,
        width: `${element.width}px`,
        height: `${element.height}px`,
        transform: `rotate(${element.rotation || 0}deg)`,
        opacity: element.opacity ?? 1,
        zIndex: element.zIndex || 1
      }
    },
    textStyle(element) {
      return { color: element.color, fontSize: `${element.fontSize}px`, fontWeight: element.fontWeight }
    },
    shapeStyle(element) {
      return { background: element.color }
    },
    triangleStyle(element) {
      return {
        borderLeft: `${element.width / 2}px solid transparent`,
        borderRight: `${element.width / 2}px solid transparent`,
        borderBottom: `${element.height}px solid ${element.color}`,
        width: '0',
        height: '0'
      }
    },
    uid() {
      return `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`
    },
    nextZ() {
      return this.elements.length ? Math.max(...this.elements.map(item => item.zIndex || 1)) + 1 : 1
    },
    clamp(value, min, max) {
      return Math.min(Math.max(value, min), max)
    },
    async saveDesign() {
      const canvas = document.createElement('canvas')
      canvas.width = this.stageWidth
      canvas.height = this.stageHeight
      const ctx = canvas.getContext('2d')
      const bg = await this.gradientToImage(this.activeTemplate.background)
      ctx.drawImage(bg, 0, 0, this.stageWidth, this.stageHeight)
      ctx.drawImage(this.$refs.templateCanvas, 0, 0)
      ctx.drawImage(this.$refs.drawingCanvas, 0, 0)
      for (const element of this.orderedElements) {
        await this.drawElementOnCanvas(ctx, element)
      }
      const link = document.createElement('a')
      link.href = canvas.toDataURL('image/png')
      link.download = 'design-canvas-professional.png'
      link.click()
    },
    gradientToImage(background) {
      return new Promise(resolve => {
        const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${this.stageWidth}" height="${this.stageHeight}"><foreignObject x="0" y="0" width="100%" height="100%"><div xmlns="http://www.w3.org/1999/xhtml" style="width:${this.stageWidth}px;height:${this.stageHeight}px;background:${background};"></div></foreignObject></svg>`
        const img = new Image()
        img.onload = () => resolve(img)
        img.src = `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}`
      })
    },
    async updateMockupImage() {
      const canvas = document.createElement('canvas')
      canvas.width = this.stageWidth
      canvas.height = this.stageHeight
      const ctx = canvas.getContext('2d')
      const bg = await this.gradientToImage(this.activeTemplate.background)
      ctx.drawImage(bg, 0, 0, this.stageWidth, this.stageHeight)
      ctx.drawImage(this.$refs.templateCanvas, 0, 0)
      ctx.drawImage(this.$refs.drawingCanvas, 0, 0)
      for (const element of this.orderedElements) {
        await this.drawElementOnCanvas(ctx, element)
      }
      this.mockupImage = canvas.toDataURL('image/png')
    },
    async drawElementOnCanvas(ctx, element) {
      ctx.save()
      ctx.translate(element.x + element.width / 2, element.y + element.height / 2)
      ctx.rotate((element.rotation || 0) * Math.PI / 180)
      ctx.globalAlpha = element.opacity ?? 1
      if (element.type === 'text') {
        ctx.fillStyle = element.color
        ctx.font = `${element.fontWeight} ${element.fontSize}px Arial`
        ctx.textAlign = 'center'
        ctx.textBaseline = 'middle'
        const lines = String(element.text).split('\n')
        const lineHeight = element.fontSize * 1.2
        lines.forEach((line, index) => ctx.fillText(line, 0, (index - (lines.length - 1) / 2) * lineHeight))
      } else if (element.type === 'image') {
        await new Promise(resolve => {
          const img = new Image()
          img.onload = () => {
            ctx.drawImage(img, -element.width / 2, -element.height / 2, element.width, element.height)
            resolve()
          }
          img.src = element.src
        })
      } else if (element.type === 'rect') {
        ctx.fillStyle = element.color
        this.roundRect(ctx, -element.width / 2, -element.height / 2, element.width, element.height, 18)
        ctx.fill()
      } else if (element.type === 'circle') {
        ctx.fillStyle = element.color
        ctx.beginPath()
        ctx.ellipse(0, 0, element.width / 2, element.height / 2, 0, 0, Math.PI * 2)
        ctx.fill()
      } else if (element.type === 'triangle') {
        ctx.fillStyle = element.color
        ctx.beginPath()
        ctx.moveTo(0, -element.height / 2)
        ctx.lineTo(element.width / 2, element.height / 2)
        ctx.lineTo(-element.width / 2, element.height / 2)
        ctx.closePath()
        ctx.fill()
      }
      ctx.restore()
    },
    roundRect(ctx, x, y, width, height, radius) {
      ctx.beginPath()
      ctx.moveTo(x + radius, y)
      ctx.arcTo(x + width, y, x + width, y + height, radius)
      ctx.arcTo(x + width, y + height, x, y + height, radius)
      ctx.arcTo(x, y + height, x, y, radius)
      ctx.arcTo(x, y, x + width, y, radius)
      ctx.closePath()
    }
  }
}
</script>

<style >
:root {
  --primary: #9c27b0;
  --secondary: #673ab7;
  --light: #f0f0f0;
  --dark: #4a148c;
  --text: #333;
  --bg-color: #e0e5ec;
  --shadow-light: #ffffff;
  --shadow-dark: #a3b1c6;
  --tools-width: 80px;
  --sidebar-width: 80px;
  --wardrobe-width: 350px;
  --wardrobe-height: 500px;
  --wood-color: #8b4513;
  --wood-light: #a0522d;
  --wood-dark: #5d4037;
  --metal-color: #b0b0b0;
  --clothes-hanger: #c0c0c0;
}
* { box-sizing: border-box; }
.designer-content { min-height: 100vh; background-color: var(--bg-color); }
.app-container { display: grid; grid-template-columns: var(--sidebar-width) 1fr var(--tools-width); min-height: 100vh; overflow: hidden; position: relative; }
.sidebar, .tools-panel { background: var(--bg-color); display: flex; flex-direction: column; align-items: center; padding-top: 22px; z-index: 3; }
.nav-item, .tool-item { display: flex; flex-direction: column; align-items: center; margin-bottom: 22px; width: 100%; text-align: center; }
.nav-icon, .tool-icon {
  width: 56px; height: 56px; border-radius: 18px; display: flex; align-items: center; justify-content: center; text-decoration: none;
  color: var(--primary); font-size: 21px; border: none; cursor: pointer; background: var(--bg-color);
  box-shadow: 5px 5px 10px var(--shadow-dark), -5px -5px 10px var(--shadow-light); transition: all .3s ease;
}
.nav-icon.active, .tool-icon.active { color: #fff; background: linear-gradient(135deg,var(--primary),var(--secondary)); box-shadow: inset 2px 2px 5px rgba(0,0,0,.16); }
.nav-label, .tool-label { font-size: 12px; color: var(--text); margin-top: 8px; }
.design-area { position: relative; padding: 24px; overflow: hidden; min-width: 0; }
.user-panel { position: absolute; top: 28px; left: 28px; z-index: 5; }
.user-avatar, .toolbar-btn, .hero-action, .action-btn, .bottom-toolbar, .palette, .design-stage, .preview-panel { border-radius: 18px; }
.neumorphic { background: var(--bg-color); box-shadow: 9px 9px 16px var(--shadow-dark), -9px -9px 16px var(--shadow-light); }
.neumorphic-btn { border: none; background: var(--bg-color); box-shadow: 5px 5px 10px var(--shadow-dark), -5px -5px 10px var(--shadow-light); }
.user-avatar { width: 52px; height: 52px; display:flex; align-items:center; justify-content:center; color: var(--primary); font-size: 20px; }
.top-toolbar { height: 74px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; margin: 0 70px 18px; gap: 16px; }
.toolbar-group, .preview-actions, .properties-actions { display:flex; gap:10px; align-items:center; }
.toolbar-btn, .hero-action, .action-btn, .control-btn, .template-card, .size-option { border:none; cursor:pointer; transition:.25s ease; font-weight:700; }
.toolbar-btn { width: 46px; height:46px; color: var(--primary); }
.hero-action, .action-btn, .control-btn { padding: 12px 16px; }
.hero-action, .action-btn, .save-btn, .preview-btn { background: linear-gradient(135deg,var(--primary),var(--secondary)); color:#fff; }
.canvas-host { height: calc(100vh - 230px); display:flex; align-items:center; justify-content:center; }
.design-stage { position:relative; width:min(980px,calc(100% - 40px)); height:680px; overflow:hidden; box-shadow: 12px 12px 24px rgba(163,177,198,.55), -10px -10px 20px rgba(255,255,255,.85); background:#fff; }
.stage-background, .dress-template, .drawing-layer, .elements-layer { position:absolute; inset:0; width:100%; height:100%; }
.dress-template { pointer-events:none; z-index:1; }
.drawing-layer { z-index:2; }
.elements-layer { z-index:3; }
.design-element { position:absolute; transform-origin:center center; border:1px solid transparent; }
.design-element.selected { border:1px dashed rgba(156,39,176,.9); box-shadow:0 0 0 2px rgba(156,39,176,.08); }
.text-node,.shape-node,.image-node { width:100%; height:100%; }
.text-node { display:flex; align-items:center; justify-content:center; text-align:center; white-space:pre-wrap; word-break:break-word; padding:8px; }
.image-node { object-fit: contain; pointer-events:none; }
.rect-node { border-radius: 18px; }
.circle-node { border-radius: 50%; }
.triangle-node { margin:auto; }
.resize-handle,.rotate-handle { position:absolute; border:none; cursor:pointer; border-radius:50%; }
.resize-handle { width:18px; height:18px; right:-9px; bottom:-9px; background:var(--primary); box-shadow:0 0 0 3px #fff; }
.rotate-handle { width:30px; height:30px; top:-15px; left:calc(50% - 15px); background:#fff; color:var(--primary); box-shadow:0 8px 18px rgba(0,0,0,.15); }
.bottom-toolbar { position:absolute; left:50%; bottom:22px; transform:translateX(-50%); z-index:4; display:flex; gap:14px; padding:15px 22px; background:var(--bg-color); box-shadow:9px 9px 16px var(--shadow-dark), -9px -9px 16px var(--shadow-light); }
.control-btn.delete-btn { background:#f44336; color:#fff; }
.control-btn.reset-btn { background:#607d8b; color:#fff; }
.control-btn.save-btn { color:#fff; }
.palette { position: fixed; z-index: 20; background: var(--bg-color); box-shadow: 8px 8px 15px var(--shadow-dark), -8px -8px 15px var(--shadow-light); padding:20px; width:230px; }
.color-palette,.size-palette,.sizes-palette,.dress-colors-palette,.template-palette,.text-palette,.image-palette,.properties-palette { right: 110px; }
.color-palette,.size-palette { top:42%; transform:translateY(-50%); }
.sizes-palette { top:26%; } .dress-colors-palette { top:54%; } .template-palette { top:20%; width:270px; } .text-palette { top:22%; width:260px; } .image-palette { top:22%; width:220px; }
.properties-palette { top:12%; left:118px; right:auto; width:260px; }
.palette-title { font-weight:700; text-align:center; margin-bottom:14px; color:var(--primary); }
.color-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; }
.color-option { width:32px; height:32px; border-radius:50%; border:2px solid transparent; box-shadow: 3px 3px 6px var(--shadow-dark), -3px -3px 6px var(--shadow-light); }
.color-option.selected { border-color:var(--primary); box-shadow:0 0 0 3px var(--bg-color), 0 0 0 5px var(--primary); }
.size-option,.template-card,.action-btn,.palette-input,.property-field select,.property-field input[type='color'] { width:100%; }
.size-option,.template-card,.action-btn {
  margin-top:10px; border:none; border-radius:12px; padding:10px 12px; background:var(--bg-color);
  box-shadow: inset 3px 3px 5px var(--shadow-dark), inset -3px -3px 5px var(--shadow-light);
}
.size-option.selected,.template-card.active { background: linear-gradient(135deg,var(--primary),var(--secondary)); color:#fff; box-shadow:none; }
.template-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; }
.template-card { display:flex; flex-direction:column; gap:8px; }
.template-preview { display:block; height:56px; border-radius:10px; }
.palette-input,.property-field select { border:none; border-radius:12px; padding:10px 12px; background:rgba(255,255,255,.75); margin-top:8px; }
.property-field { display:block; margin-bottom:12px; color:var(--text); font-size:13px; }
.wardrobe-container-3d { position: fixed; right: 116px; bottom: 36px; width: var(--wardrobe-width); height: var(--wardrobe-height); perspective:1500px; z-index:11; }
.wardrobe-3d { width:100%; height:100%; position:relative; transform-style:preserve-3d; transition: transform .8s cubic-bezier(.68,-.55,.265,1.55); }
.wardrobe-body-3d { position:absolute; inset:0; background:linear-gradient(to right,var(--wood-light),var(--wood-color)); border-radius:8px; box-shadow:0 20px 40px rgba(0,0,0,.25); }
.wardrobe-door-3d { position:absolute; width:49%; height:100%; background:linear-gradient(to right,var(--wood-dark),var(--wood-color)); transition:transform 1s ease-in-out; }
.door-left-3d { left:0; transform-origin:left center; border-radius:8px 0 0 8px; }
.door-right-3d { right:0; transform-origin:right center; border-radius:0 8px 8px 0; }
.wardrobe-3d.open .door-left-3d { transform: rotateY(-140deg); }
.wardrobe-3d.open .door-right-3d { transform: rotateY(140deg); }
.door-handle-3d { width:20px; height:60px; border-radius:10px; background:linear-gradient(to bottom,#d3d3d3,#a9a9a9); position:absolute; top:50%; transform:translateY(-50%); }
.door-left-3d .door-handle-3d { right:15px; } .door-right-3d .door-handle-3d { left:15px; }
.wardrobe-inside-3d { position:absolute; width:calc(100% - 60px); height:calc(100% - 60px); top:30px; left:30px; background:#f8f8f8; display:none; flex-wrap:wrap; align-content:flex-start; gap:15px; padding:18px; overflow-y:auto; border-radius:4px; }
.wardrobe-3d.open .wardrobe-inside-3d { display:flex; }
.clothes-rail-3d { width:100%; height:30px; background:var(--metal-color); border-radius:5px; position:relative; margin-bottom:18px; }
.hanger-3d { position:absolute; width:25px; height:40px; top:30px; background:var(--clothes-hanger); border-radius:0 0 10px 10px; }
.hanger-3d::before { content:''; position:absolute; width:15px; height:15px; top:-7px; left:5px; border-radius:50%; background:var(--clothes-hanger); }
.clothing-item-3d { width:86px; border:none; background:transparent; cursor:pointer; text-align:center; }
.clothing-img-container-3d { height:110px; border-radius:6px; overflow:hidden; box-shadow:0 6px 16px rgba(0,0,0,.12); }
.wardrobe-fake-image { width:100%; height:100%; } .clothing-name-3d { margin-top:8px; font-size:12px; font-weight:600; }
.wardrobe-control-3d { position:absolute; bottom:-25px; left:50%; transform:translateX(-50%); width:60px; height:60px; border-radius:50%; border:2px solid var(--wood-dark); background:linear-gradient(to bottom,#8b4513,#a0522d); color:#fff; font-size:24px; cursor:pointer; box-shadow:0 5px 20px rgba(0,0,0,.25); }
.preview-panel { position:fixed; top:26px; right:136px; width:470px; height:calc(100vh - 52px); background:rgba(224,229,236,.96); box-shadow:-12px 0 30px rgba(0,0,0,.12); z-index:25; padding:18px; display:flex; flex-direction:column; }
.preview-header { display:flex; justify-content:space-between; gap:12px; margin-bottom:14px; }
.preview-header h3 { margin:0 0 6px; color:var(--dark); } .preview-header p { margin:0; color:#5c6270; font-size:13px; line-height:1.7; }
.mockup-stage { flex:1; border-radius:18px; overflow:hidden; background:linear-gradient(180deg,#edf2f7,#dde4ea); box-shadow: inset 5px 5px 10px rgba(163,177,198,.45), inset -5px -5px 10px rgba(255,255,255,.75); position:relative; perspective:1600px; }
.mannequin-3d { position:absolute; inset:0; margin:auto; width:300px; height:620px; transform-style:preserve-3d; transform:rotateX(4deg) rotateY(-14deg); }
.mannequin-3d.spinning { animation:spinModel 10s linear infinite; }
.mannequin-head,.mannequin-neck,.mannequin-body,.arm,.forearm,.hip,.leg,.shin { position:absolute; background:linear-gradient(180deg,#f6ded1,#efcdb9); box-shadow:0 10px 18px rgba(0,0,0,.12); }
.mannequin-head { width:80px; height:96px; border-radius:45px; left:110px; top:28px; }
.mannequin-neck { width:28px; height:36px; border-radius:18px; left:136px; top:108px; }
.mannequin-body { width:180px; height:300px; left:60px; top:130px; border-radius:80px 80px 45px 45px / 90px 90px 40px 40px; overflow:hidden; background:linear-gradient(180deg,#fff,#f1f5f9); }
.garment-surface { position:absolute; inset:0; border-radius:inherit; overflow:hidden; }
.garment-texture { width:100%; height:100%; object-fit:cover; }
.arm { width:38px; height:190px; top:155px; border-radius:30px; }
.arm-left { left:24px; transform:rotate(16deg); } .arm-right { right:24px; transform:rotate(-16deg); }
.forearm { width:30px; height:152px; top:315px; border-radius:24px; }
.forearm-left { left:8px; transform:rotate(10deg); } .forearm-right { right:8px; transform:rotate(-10deg); }
.hip { width:190px; height:96px; left:55px; top:390px; border-radius:50%; }
.leg,.shin { width:52px; border-radius:30px; }
.leg { height:180px; top:452px; } .shin { height:160px; top:580px; }
.leg-left,.shin-left { left:92px; } .leg-right,.shin-right { right:92px; }
.floor-shadow { position:absolute; width:220px; height:48px; border-radius:50%; background:radial-gradient(circle,rgba(0,0,0,.18),transparent 70%); left:40px; bottom:-10px; transform:rotateX(88deg) translateZ(-30px); }
.preview-slide-enter-active,.preview-slide-leave-active { transition:all .25s ease; }
.preview-slide-enter-from,.preview-slide-leave-to { opacity:0; transform:translateX(24px); }
.hidden-file { display:none; }
@keyframes spinModel { from { transform:rotateX(4deg) rotateY(-14deg); } to { transform:rotateX(4deg) rotateY(346deg); } }
@media (max-width: 1500px) { .preview-panel{width:400px;} .wardrobe-container-3d{transform:scale(.86); transform-origin:bottom right;} }
</style>
