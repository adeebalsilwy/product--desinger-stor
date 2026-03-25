<template>
  <Customer :showNav="false" :showFooter="false" :showCart="false" contentClass="designer-content">
    <div class="app-container">
      <aside class="sidebar">
        <div class="nav-item" v-for="item in navItems" :key="item.key">
          <button type="button" class="nav-icon" :class="{ active: item.active }" @click="handleNavItemClick(item)" :title="item.label">
            <i :class="item.icon"></i>
          </button>
          <span class="nav-label">{{ item.label }}</span>
        </div>
      </aside>

      <main class="design-area">
        <div class="user-panel">
          <div class="user-avatar neumorphic-btn">
            <i class="fas fa-user"></i>
          </div>
        </div>

        <div class="top-toolbar neumorphic">
          <div class="toolbar-group">
            <button class="toolbar-btn neumorphic-btn" @click="undo" :disabled="historyIndex <= 0"><i
                class="fas fa-undo"></i></button>
            <button class="toolbar-btn neumorphic-btn" @click="redo" :disabled="historyIndex >= history.length - 1"><i
                class="fas fa-redo"></i></button>
            <button class="toolbar-btn neumorphic-btn" @click="duplicateSelected" :disabled="!selectedElementId"><i
                class="fas fa-clone"></i></button>
            <button class="toolbar-btn neumorphic-btn" @click="deleteSelected" :disabled="!selectedElementId"><i
                class="fas fa-trash"></i></button>
          </div>

          <div class="toolbar-group">
            <button class="hero-action save-btn" @click="saveDesignToDatabase" :disabled="pendingSave"
              :title="t('designer.save_to_db_tooltip', 'حفظ التصميم في قاعدة البيانات')">
              <i class="fas fa-database"></i>
              <span>{{ t('designer.save_to_db', 'حفظ في قاعدة البيانات') }}</span>
            </button>
            <button class="hero-action save-btn" @click="saveDesignToLibrary" :disabled="pendingSave"
              :title="t('designer.save_local_tooltip', 'حفظ التصميم محليًا')">
              <i class="fas fa-save"></i>
              <span>{{ t('designer.save_design', 'حفظ التصميم') }}</span>
            </button>
            <button class="hero-action preview-btn" @click="showMockup = !showMockup"
              :title="t('designer.toggle_mockup_tooltip', 'تبديل عرض المجسم')">
              <i class="fas fa-female"></i>
              <span>{{ showMockup ? t('designer.hide_preview', 'إخفاء المجسم') : t('designer.show_preview', 'عرض التصميم على المجسم') }}</span>
            </button>
          </div>
        </div>

        <div class="canvas-wrap">
          <div class="design-stage neumorphic-inset">
            <div class="stage-background"
              :style="{ background: activeTemplate.background || defaultTemplate.background }"></div>
            <canvas ref="templateCanvas" class="dress-template" :width="stageWidth" :height="stageHeight"></canvas>
            <canvas ref="drawingCanvas" class="drawing-layer" :class="{ 'draw-cursor': isDrawTool }" :width="stageWidth"
              :height="stageHeight" @mousedown="onStageMouseDown" @mousemove="onStageMouseMove"
              @mouseup="onStageMouseUp" @mouseleave="onStageMouseUp"></canvas>

            <div class="elements-layer">
              <div v-for="element in orderedElements" :key="element.id" class="design-element"
                :class="{ selected: selectedElementId === element.id }" :style="elementStyle(element)"
                @mousedown.stop="startElementDrag($event, element)" @click.stop="selectElement(element.id)">
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

                <button v-if="selectedElementId === element.id" class="resize-handle"
                  @mousedown.stop="startResize(element)"></button>
                <button v-if="selectedElementId === element.id" class="rotate-handle"
                  @mousedown.stop="startRotate(element)">
                  <i class="fas fa-sync-alt"></i>
                </button>
                <button v-if="selectedElementId === element.id" class="delete-handle"
                  @click.stop="deleteElement(element.id)">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="bottom-toolbar neumorphic">
          <button class="control-btn delete-btn" @click="clearDrawing"><i class="fas fa-eraser"></i><span>{{
            t('designer.clear',
              'مسح الرسم') }}</span></button>
          <button class="control-btn reset-btn" @click="resetDesign"><i class="fas fa-rotate-left"></i><span>{{
            t('designer.reset', 'إعادة ضبط') }}</span></button>
          <button class="control-btn save-btn" @click="downloadDesign"><i class="fas fa-download"></i><span>{{
            t('designer.download', 'تنزيل') }}</span></button>
        </div>
      </main>

      <aside class="tools-panel">
        <div class="tool-item" v-for="tool in tools" :key="tool.key">
          <button class="tool-icon" :class="{ active: activeTool === tool.key }" @click="activateTool(tool.key)">
            <i :class="tool.icon"></i>
          </button>
          <span class="tool-label">{{ tool.label }}</span>
        </div>
      </aside>

      <div class="palette color-palette" v-if="openPalette === 'color'">
        <div class="palette-header">
          <div class="palette-title">{{ t('designer.colors', 'الألوان') }}</div>
          <button class="close-btn" @click="closePalette"><i class="fas fa-times"></i></button>
        </div>
        <div class="color-grid">
          <button v-for="color in colors" :key="color" class="color-option"
            :class="{ selected: currentColor === color }" :style="{ background: color }"
            @click="selectColor(color)"></button>
        </div>
      </div>

      <div class="palette size-palette" v-if="openPalette === 'size'">
        <div class="palette-header">
          <div class="palette-title">{{ t('designer.brush_size', 'مقاس الأداة') }}</div>
          <button class="close-btn" @click="closePalette"><i class="fas fa-times"></i></button>
        </div>
        <button v-for="size in brushSizes" :key="size" class="size-option" :class="{ selected: brushSize === size }"
          @click="brushSize = size">{{ size }}px</button>
      </div>

      <div class="palette template-palette wide-palette" v-if="openPalette === 'templates'">
        <div class="palette-header">
          <div class="palette-title">{{ t('designer.templates', 'القوالب') }}</div>
          <button class="close-btn" @click="closePalette"><i class="fas fa-times"></i></button>
        </div>

        <div class="loading-box" v-if="templatesLoading">{{ t('designer.loading_templates', 'جاري تحميل القوالب...') }}
        </div>
        <div class="error-box" v-else-if="templatesError">{{ templatesError }}</div>
        <div class="empty-box" v-else-if="!templates.length">{{ t('designer.no_templates', 'لا توجد قوالب متاحة') }}
        </div>

        <div v-else class="template-grid">
          <button v-for="template in templates" :key="template.id" class="template-card"
            :class="{ active: activeTemplate.id === template.id }" @click="applyTemplate(template)">
            <span class="template-preview template-image-preview" :style="templateCardStyle(template)">
              <img v-if="template.preview" :src="template.preview" :alt="template.name" />
            </span>
            <span class="template-name">{{ template.name }}</span>
          </button>
        </div>
      </div>

      <div class="palette text-palette" v-if="openPalette === 'text'">
        <div class="palette-header">
          <div class="palette-title">{{ t('designer.add_text', 'إضافة نص') }}</div>
          <button class="close-btn" @click="closePalette"><i class="fas fa-times"></i></button>
        </div>
        <textarea v-model="draftText" class="palette-input" rows="3"></textarea>
        <button class="action-btn" @click="addTextElement">{{ t('designer.add_text', 'إضافة النص') }}</button>
      </div>

      <div class="palette image-palette" v-if="openPalette === 'image'">
        <div class="palette-header">
          <div class="palette-title">{{ t('designer.add_image', 'إضافة صورة') }}</div>
          <button class="close-btn" @click="closePalette"><i class="fas fa-times"></i></button>
        </div>
        <button class="action-btn" @click="$refs.imageInput.click()">{{ t('designer.choose_image', 'اختيار صورة')
          }}</button>
      </div>

      <div class="palette layers-palette" v-if="openPalette === 'layers'">
        <div class="palette-header">
          <div class="palette-title">{{ t('designer.layers', 'الطبقات') }}</div>
          <button class="close-btn" @click="closePalette"><i class="fas fa-times"></i></button>
        </div>
        <div class="layers-list">
          <button v-for="element in reversedLayers" :key="element.id" class="layer-item"
            :class="{ active: selectedElementId === element.id }" @click="selectElement(element.id)">
            <span>{{ layerLabel(element) }}</span>
            <i class="fas fa-layer-group"></i>
          </button>
        </div>
      </div>

      <div class="palette properties-palette" v-if="selectedElement">
        <div class="palette-header">
          <div class="palette-title">{{ t('designer.properties', 'خصائص العنصر') }}</div>
          <button class="close-btn" @click="selectedElementId = null"><i class="fas fa-times"></i></button>
        </div>

        <template v-if="selectedElement.type === 'text'">
          <label class="property-field">
            <span>{{ t('designer.text', 'النص') }}</span>
            <textarea v-model="selectedElement.text" class="palette-input" rows="3" @input="commitChanges"></textarea>
          </label>
          <label class="property-field">
            <span>{{ t('designer.font_size', 'حجم الخط') }}</span>
            <input type="range" min="12" max="120" v-model.number="selectedElement.fontSize" @input="commitChanges" />
          </label>
        </template>

        <template v-else>
          <label class="property-field">
            <span>{{ t('designer.width', 'العرض') }}</span>
            <input type="range" min="40" max="900" v-model.number="selectedElement.width" @input="commitChanges" />
          </label>
          <label class="property-field">
            <span>{{ t('designer.height', 'الارتفاع') }}</span>
            <input type="range" min="40" max="900" v-model.number="selectedElement.height" @input="commitChanges" />
          </label>
        </template>

        <label class="property-field">
          <span>{{ t('designer.opacity', 'الشفافية') }}</span>
          <input type="range" min="0.1" max="1" step="0.05" v-model.number="selectedElement.opacity"
            @input="commitChanges" />
        </label>

        <label class="property-field">
          <span>{{ t('designer.rotation', 'الدوران') }}</span>
          <input type="range" min="-180" max="180" v-model.number="selectedElement.rotation" @input="commitChanges" />
        </label>

        <label class="property-field" v-if="selectedElement.type !== 'image'">
          <span>{{ t('designer.color', 'اللون') }}</span>
          <input type="color" v-model="selectedElement.color" @input="commitChanges" />
        </label>
      </div>

      <div class="wardrobe-container-3d" :class="{ 'wardrobe-minimized': wardrobeMinimized }">
        <div class="wardrobe-3d" :class="{ open: wardrobeOpen, minimized: wardrobeMinimized }">
          <div class="wardrobe-body-3d"></div>
          <div class="wardrobe-door-3d door-left-3d">
            <div class="door-handle-3d"></div>
          </div>
          <div class="wardrobe-door-3d door-right-3d">
            <div class="door-handle-3d"></div>
          </div>

          <div class="wardrobe-inside-3d">
            <div class="wardrobe-header">
              <span>{{ t('designer.templates', 'القوالب') }}</span>
              <div class="wardrobe-controls">
                <button class="wardrobe-minimize-btn" @click="toggleWardrobeMinimized"
                  :title="wardrobeMinimized ? t('designer.expand', 'توسيع') : t('designer.minimize', 'تصغير')">
                  <i :class="wardrobeMinimized ? 'fas fa-expand' : 'fas fa-compress'"></i>
                </button>
                <button class="close-btn small" @click="wardrobeOpen = false; wardrobeMinimized = false"
                  :title="t('designer.close', 'إغلاق')">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>

            <div class="clothes-rail-3d">
              <div class="hanger-3d" style="left: 14%"></div>
              <div class="hanger-3d" style="left: 34%"></div>
              <div class="hanger-3d" style="left: 54%"></div>
              <div class="hanger-3d" style="left: 74%"></div>
            </div>

            <div class="loading-box wardrobe-state" v-if="templatesLoading">{{ t('designer.loading_templates', 'جاري تحميل القوالب...') }}</div>
            <div class="error-box wardrobe-state" v-else-if="templatesError">{{ templatesError }}</div>
            <div class="empty-box wardrobe-state" v-else-if="!templates.length">{{ t('designer.no_templates', 'لا توجد قوالب متاحة') }}</div>

            <button v-else v-for="template in templates" :key="`wardrobe-${template.id}`" class="clothing-item-3d"
              :class="{ active: activeTemplate.id === template.id }"
              @click="applyTemplate(template); wardrobeMinimized = false">
              <div class="clothing-img-container-3d">
                <img v-if="template.preview" :src="template.preview" class="clothing-img-3d" :alt="template.name" />
                <div v-else class="clothing-fallback" :style="templateCardStyle(template)"></div>
              </div>
              <div class="clothing-name-3d">{{ template.name }}</div>
            </button>
          </div>
        </div>

        <button class="wardrobe-control-3d" @click="toggleWardrobe">
          <i :class="wardrobeOpen ? 'fas fa-times' : 'fas fa-tshirt'"></i>
        </button>
      </div>

      <transition name="preview-slide">
        <div class="preview-panel" v-if="showMockup">
          <div class="preview-header">
            <div>
              <h3>{{ t('designer.preview', 'عرض التصميم على المجسم') }}</h3>
              <p>{{ t('designer.preview_desc', 'مجسم بنت شابة احترافي مع عرض حي للتصميم والقالب المحدد.') }}</p>
            </div>
            <div class="preview-actions">
              <button class="action-btn compact" @click="spinMockup = !spinMockup">{{ spinMockup ? t('designer.stop', 'إيقاف الدوران') : t('designer.rotate', 'تدوير المجسم') }}</button>
              <button class="close-btn on-panel" @click="showMockup = false"><i class="fas fa-times"></i></button>
            </div>
          </div>

          <div class="mockup-stage">
            <div class="mannequin-3d" :class="{ spinning: spinMockup }">
              <div class="hair-back"></div>
              <div class="mannequin-head"></div>
              <div class="hair-front"></div>
              <div class="mannequin-neck"></div>
              <div class="mannequin-body">
                <div class="garment-surface" :style="{ background: garmentColor }">
                  <img :src="mockupImage" class="garment-texture" alt="mockup" />
                </div>
                <div class="body-highlight"></div>
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

      <input ref="imageInput" class="hidden-file" type="file" accept="image/*" @change="onImageSelected" />
    </div>
  </Customer>
</template>

<script>
import Customer from '@/Layouts/Customer.vue'

export default {
  name: 'CreateWardrobeTemplatesFixed',
  components: { Customer },
  props: {
    templatesFromServer: { type: Array, default: () => [] },
    designTemplates: { type: Array, default: () => [] },
    templates: { type: Array, default: () => [] },
    editMode: { type: Boolean, default: false },
    initialDesign: { type: Object, default: null }
  },
  data() {
    return {
      navItems: [
        { key: 'home', label: 'الرئيسية', icon: 'fas fa-home', active: true, route: '/' },
        { key: 'designs', label: 'تصاميمي', icon: 'fas fa-palette', active: false, route: '/designer/my-designs' },
        { key: 'products', label: 'المنتجات', icon: 'fas fa-tshirt', active: false, route: '/products' },
        { key: 'panel', label: 'لوحة التحكم', icon: 'fas fa-file-invoice', active: false, route: '/customer/dashboard' },
        { key: 'profile', label: 'الملف الشخصي', icon: 'fas fa-user', active: false, route: '/customer/profile' }
      ],
      tools: [
        { key: 'brush', label: 'فرشاة', icon: 'fas fa-paint-brush' },
        { key: 'pen', label: 'قلم', icon: 'fas fa-pen' },
        { key: 'eraser', label: 'ممحاة', icon: 'fas fa-eraser' },
        { key: 'color', label: 'ألوان', icon: 'fas fa-palette' },
        { key: 'size', label: 'مقاس', icon: 'fas fa-ruler' },
        { key: 'templates', label: 'القوالب', icon: 'fas fa-object-group' },
        { key: 'text', label: 'نص', icon: 'fas fa-font' },
        { key: 'shape', label: 'مربع', icon: 'fas fa-square' },
        { key: 'circle', label: 'دائرة', icon: 'fas fa-circle' },
        { key: 'triangle', label: 'مثلث', icon: 'fas fa-play' },
        { key: 'image', label: 'صور', icon: 'fas fa-image' },
        { key: 'layers', label: 'الطبقات', icon: 'fas fa-layer-group' },
        { key: 'wardrobe', label: 'دولاب', icon: 'fas fa-wardrobe' },
        { key: 'select', label: 'تحديد', icon: 'fas fa-mouse-pointer' }
      ],
      colors: ['#000000', '#ff0000', '#00c853', '#2962ff', '#ffeb3b', '#ff00ff', '#00ffff', '#ff9800', '#7c4dff', '#e91e63', '#03a9f4', '#8bc34a', '#ffffff', '#bdbdbd', '#616161'],
      brushSizes: [1, 3, 5, 8, 10, 15, 20, 30, 50],
      defaultTemplate: { id: 'default', name: 'افتراضي', background: 'linear-gradient(180deg,#ffffff 0%,#f5f7ff 100%)', preview: null, baseColor: '#ffffff' },
      templatesList: [],
      templatesLoading: false,
      templatesError: '',
      stageWidth: 980,
      stageHeight: 680,
      activeTool: 'brush',
      openPalette: null,
      currentColor: '#000000',
      brushSize: 5,
      garmentColor: '#ffffff',
      activeTemplate: { id: 'default', name: 'افتراضي', background: 'linear-gradient(180deg,#ffffff 0%,#f5f7ff 100%)', preview: null, baseColor: '#ffffff' },
      draftText: 'نص جديد',
      showMockup: false,
      spinMockup: true,
      wardrobeOpen: false,
      wardrobeMinimized: false,
      mockupImage: '',
      isDrawing: false,
      selectedElementId: null,
      dragState: null,
      resizeState: false,
      rotationState: false,
      elements: [],
      history: [],
      historyIndex: -1,
      drawingContext: null,
      templateContext: null,
      pendingSave: false
    }
  },
  computed: {
    isDrawTool() {
      return ['brush', 'pen', 'eraser'].includes(this.activeTool)
    },
    orderedElements() {
      return [...this.elements].sort((a, b) => (a.zIndex || 0) - (b.zIndex || 0))
    },
    reversedLayers() {
      return [...this.orderedElements].reverse()
    },
    selectedElement() {
      return this.elements.find(item => item.id === this.selectedElementId) || null
    },
    templatesResolved() {
      return this.templatesList.length ? this.templatesList : [this.defaultTemplate]
    },
    templates() {
      return this.templatesResolved
    }
  },
  async mounted() {
    this.templateContext = this.$refs.templateCanvas.getContext('2d')
    this.drawingContext = this.$refs.drawingCanvas.getContext('2d')
    this.drawingContext.lineCap = 'round'
    this.drawingContext.lineJoin = 'round'
    this.drawTemplate()
    this.pushHistory(true)
    await this.loadTemplates()
    this.restoreInitialDesign()
    await this.updateMockupImage()
    document.addEventListener('click', this.handleOutsideClick)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleOutsideClick)
  },
  methods: {
    t(key, fallback) {
      try {
        if (typeof window !== 'undefined' && typeof window.__ === 'function') return window.__(key)
      } catch (e) { }
      return fallback
    },
    handleOutsideClick(event) {
      if (!event.target.closest('.palette') && !event.target.closest('.tool-icon') && !event.target.closest('.design-element') && !event.target.closest('.wardrobe-container-3d')) {
        this.openPalette = null
      }
    },
    closePalette() { this.openPalette = null },
    activateTool(tool) {
      this.activeTool = tool
      if (['color', 'size', 'templates', 'text', 'image', 'layers', 'wardrobe'].includes(tool)) {
        if (tool === 'wardrobe') {
          this.openWardrobe()
          return
        }
        this.openPalette = tool
        return
      }
      if (tool === 'shape') return this.addShape('rect')
      if (tool === 'circle') return this.addShape('circle')
      if (tool === 'triangle') return this.addShape('triangle')
      this.openPalette = null
    },
    normalizeTemplate(row, index = 0) {
      const preview = row.preview || row.image || row.thumbnail || row.photo || row.template_image || row.cover || row.url || null
      const background = row.background || row.gradient || row.bg || 'linear-gradient(180deg,#ffffff 0%,#f5f7ff 100%)'
      return {
        id: row.id ?? row.uuid ?? `template-${index}`,
        name: row.name || row.title || row.template_name || `Template ${index + 1}`,
        preview,
        background,
        baseColor: row.baseColor || row.base_color || '#ffffff'
      }
    },
    async loadTemplates() {
      this.templatesLoading = true
      this.templatesError = ''
      try {
        const fromProps = [
          ...(Array.isArray(this.templatesFromServer) ? this.templatesFromServer : []),
          ...(Array.isArray(this.designTemplates) ? this.designTemplates : []),
          ...(Array.isArray(this.$props.templates) ? this.$props.templates : []),
          ...(Array.isArray(this.$page?.props?.templates) ? this.$page.props.templates : []),
          ...(Array.isArray(this.$page?.props?.designTemplates) ? this.$page.props.designTemplates : []),
          ...(Array.isArray(this.$page?.props?.templatesFromServer) ? this.$page.props.templatesFromServer : [])
        ]

        let rows = fromProps.filter(Boolean)

        if (!rows.length) {
          const endpoints = [
            '/designer/templates',
            '/customer/designer/templates',
            '/api/designer/templates',
            '/api/templates',
            '/templates'
          ]
          for (const endpoint of endpoints) {
            try {
              const response = await fetch(endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
              if (!response.ok) continue
              const json = await response.json()
              const list = Array.isArray(json) ? json : (json.data || json.templates || [])
              if (Array.isArray(list) && list.length) {
                rows = list
                break
              }
            } catch (_) { }
          }
        }

        this.templatesList = rows.map((row, index) => this.normalizeTemplate(row, index))
        if (!this.templatesList.length) this.templatesList = [this.defaultTemplate]

        const keepSelected = this.templatesList.find(item => item.id === this.activeTemplate.id)
        if (!keepSelected) this.applyTemplate(this.templatesList[0], false)
      } catch (e) {
        this.templatesError = this.t('designer.templates_load_failed', 'تعذر جلب القوالب من قاعدة البيانات')
        this.templatesList = [this.defaultTemplate]
        this.applyTemplate(this.defaultTemplate, false)
      } finally {
        this.templatesLoading = false
      }
    },
    restoreInitialDesign() {
      const design = this.initialDesign || this.$page?.props?.initialDesign || null
      if (!design) return
      const selectedTemplateId = design.template_id || design.templateId || design.template?.id
      if (selectedTemplateId) {
        const found = this.templatesList.find(t => String(t.id) === String(selectedTemplateId))
        if (found) this.applyTemplate(found, false)
      }
      if (Array.isArray(design.elements)) this.elements = design.elements
      if (design.drawing) {
        const img = new Image()
        img.onload = () => {
          this.drawingContext.clearRect(0, 0, this.stageWidth, this.stageHeight)
          this.drawingContext.drawImage(img, 0, 0)
          this.updateMockupImage()
        }
        img.src = design.drawing
      }
    },
    templateCardStyle(template) {
      return { background: template.background || this.defaultTemplate.background }
    },
    selectColor(color) {
      this.currentColor = color
      if (this.selectedElement && this.selectedElement.type !== 'image') {
        this.selectedElement.color = color
        this.commitChanges()
      }
    },
    applyTemplate(template, push = true) {
      this.activeTemplate = { ...this.defaultTemplate, ...template }
      this.garmentColor = this.activeTemplate.baseColor || '#ffffff'
      if (push) this.commitChanges()
      else this.drawTemplate()
      this.wardrobeOpen = false
      this.openPalette = null
      this.updateMockupImage()
    },
    drawTemplate() {
      const ctx = this.templateContext
      ctx.clearRect(0, 0, this.stageWidth, this.stageHeight)
      ctx.save()
      ctx.fillStyle = this.garmentColor
      const cx = this.stageWidth / 2
      const top = 44
      const bodyHeight = this.stageHeight - 95
      ctx.beginPath()
      ctx.moveTo(cx - 88, top + 18)
      ctx.quadraticCurveTo(cx - 145, top + 55, cx - 188, top + 165)
      ctx.quadraticCurveTo(cx - 215, top + 248, cx - 235, top + 468)
      ctx.quadraticCurveTo(cx - 130, top + bodyHeight, cx, top + bodyHeight - 10)
      ctx.quadraticCurveTo(cx + 130, top + bodyHeight, cx + 235, top + 468)
      ctx.quadraticCurveTo(cx + 215, top + 248, cx + 188, top + 165)
      ctx.quadraticCurveTo(cx + 145, top + 55, cx + 88, top + 18)
      ctx.quadraticCurveTo(cx + 42, top - 4, cx + 10, top + 6)
      ctx.lineTo(cx + 34, top + 58)
      ctx.quadraticCurveTo(cx + 44, top + 95, cx + 20, top + 154)
      ctx.lineTo(cx - 20, top + 154)
      ctx.quadraticCurveTo(cx - 44, top + 95, cx - 34, top + 58)
      ctx.lineTo(cx - 10, top + 6)
      ctx.quadraticCurveTo(cx - 42, top - 4, cx - 88, top + 18)
      ctx.closePath()
      ctx.fill()
      ctx.strokeStyle = 'rgba(74,20,140,0.16)'
      ctx.lineWidth = 3
      ctx.stroke()
      ctx.restore()
    },
    getPoint(event) {
      const rect = this.$refs.drawingCanvas.getBoundingClientRect()
      return { x: ((event.clientX - rect.left) / rect.width) * this.stageWidth, y: ((event.clientY - rect.top) / rect.height) * this.stageHeight }
    },
    onStageMouseDown(event) {
      if (!this.isDrawTool) return
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
        this.resizeState = false
        this.rotationState = false
        this.pushHistory()
      }
    },
    addTextElement() {
      const id = this.uid()
      this.elements.push({ id, type: 'text', text: this.draftText || 'نص جديد', x: 330, y: 180, width: 260, height: 120, rotation: 0, opacity: 1, color: this.currentColor, fontSize: 42, fontWeight: '700', zIndex: this.nextZ() })
      this.selectedElementId = id
      this.openPalette = null
      this.commitChanges()
    },
    addShape(type = 'rect') {
      const id = this.uid()
      this.elements.push({ id, type, x: 320, y: 220, width: 180, height: 180, rotation: 0, opacity: 0.9, color: this.currentColor, zIndex: this.nextZ() })
      this.selectedElementId = id
      this.commitChanges()
    },
    onImageSelected(event) {
      const file = event.target.files?.[0]
      if (!file) return
      const reader = new FileReader()
      reader.onload = () => {
        const id = this.uid()
        this.elements.push({ id, type: 'image', src: reader.result, x: 300, y: 160, width: 250, height: 250, rotation: 0, opacity: 1, zIndex: this.nextZ() })
        this.selectedElementId = id
        this.openPalette = null
        this.commitChanges()
      }
      reader.readAsDataURL(file)
      event.target.value = ''
    },
    selectElement(id) { this.selectedElementId = id; this.activeTool = 'select' },
    startElementDrag(event, element) {
      if (this.isDrawTool) return
      const point = this.getPoint(event)
      this.selectedElementId = element.id
      this.dragState = { offsetX: point.x - element.x, offsetY: point.y - element.y }
    },
    startResize(element) { this.selectedElementId = element.id; this.resizeState = true },
    startRotate(element) { this.selectedElementId = element.id; this.rotationState = true },
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
    deleteElement(id) {
      this.elements = this.elements.filter(item => item.id !== id)
      if (this.selectedElementId === id) {
        this.selectedElementId = null
      }
      this.commitChanges()
    },
    clearDrawing() { this.drawingContext.clearRect(0, 0, this.stageWidth, this.stageHeight); this.commitChanges() },
    resetDesign() {
      this.elements = []
      this.selectedElementId = null
      this.currentColor = '#000000'
      this.brushSize = 5
      this.activeTemplate = this.templates[0] || this.defaultTemplate
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
      this.elements.sort((a, b) => a.zIndex - b.zIndex).forEach((item, index) => { item.zIndex = index + 1 })
    },
    layerLabel(element) {
      const labels = { text: 'نص', image: 'صورة', rect: 'مربع', circle: 'دائرة', triangle: 'مثلث' }
      return `${labels[element.type] || 'عنصر'} #${element.zIndex || 1}`
    },
    commitChanges() { this.pushHistory(); this.updateMockupImage() },
    pushHistory(initial = false) {
      const snapshot = JSON.stringify({
        activeTemplate: this.activeTemplate,
        drawing: this.$refs.drawingCanvas?.toDataURL('image/png') || '',
        elements: this.elements
      })
      if (initial) { this.history = [snapshot]; this.historyIndex = 0; return }
      if (this.history[this.historyIndex] === snapshot) return
      this.history = this.history.slice(0, this.historyIndex + 1)
      this.history.push(snapshot)
      this.historyIndex = this.history.length - 1
    },
    restoreSnapshot(snapshot) {
      const data = JSON.parse(snapshot)
      this.activeTemplate = data.activeTemplate
      this.elements = data.elements || []
      this.selectedElementId = null
      const img = new Image()
      img.onload = () => {
        this.drawingContext.clearRect(0, 0, this.stageWidth, this.stageHeight)
        this.drawingContext.drawImage(img, 0, 0)
        this.updateMockupImage()
      }
      img.src = data.drawing
    },
    undo() { if (this.historyIndex <= 0) return; this.historyIndex -= 1; this.restoreSnapshot(this.history[this.historyIndex]) },
    redo() { if (this.historyIndex >= this.history.length - 1) return; this.historyIndex += 1; this.restoreSnapshot(this.history[this.historyIndex]) },
    elementStyle(element) {
      return { left: `${element.x}px`, top: `${element.y}px`, width: `${element.width}px`, height: `${element.height}px`, transform: `rotate(${element.rotation || 0}deg)`, opacity: element.opacity ?? 1, zIndex: element.zIndex || 1 }
    },
    textStyle(element) { return { color: element.color, fontSize: `${element.fontSize}px`, fontWeight: element.fontWeight } },
    shapeStyle(element) { return { background: element.color } },
    triangleStyle(element) {
      return { borderLeft: `${element.width / 2}px solid transparent`, borderRight: `${element.width / 2}px solid transparent`, borderBottom: `${element.height}px solid ${element.color}`, width: '0', height: '0' }
    },
    uid() { return `${Date.now()}-${Math.random().toString(36).slice(2, 8)}` },
    nextZ() { return this.elements.length ? Math.max(...this.elements.map(item => item.zIndex || 1)) + 1 : 1 },
    clamp(value, min, max) { return Math.min(Math.max(value, min), max) },
    async gradientToImage(background) {
      return new Promise(resolve => {
        const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${this.stageWidth}" height="${this.stageHeight}"><foreignObject x="0" y="0" width="100%" height="100%"><div xmlns="http://www.w3.org/1999/xhtml" style="width:${this.stageWidth}px;height:${this.stageHeight}px;background:${background};"></div></foreignObject></svg>`
        const img = new Image()
        img.onload = () => resolve(img)
        img.src = `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}`
      })
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
          img.onload = () => { ctx.drawImage(img, -element.width / 2, -element.height / 2, element.width, element.height); resolve() }
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
    },
    async buildExportCanvas() {
      this.drawTemplate()
      const canvas = document.createElement('canvas')
      canvas.width = this.stageWidth
      canvas.height = this.stageHeight
      const ctx = canvas.getContext('2d')
      const bg = await this.gradientToImage(this.activeTemplate.background || this.defaultTemplate.background)
      ctx.drawImage(bg, 0, 0, this.stageWidth, this.stageHeight)
      ctx.drawImage(this.$refs.templateCanvas, 0, 0)
      ctx.drawImage(this.$refs.drawingCanvas, 0, 0)
      for (const element of this.orderedElements) await this.drawElementOnCanvas(ctx, element)
      return canvas
    },
    async downloadDesign() {
      const canvas = await this.buildExportCanvas()
      const link = document.createElement('a')
      link.href = canvas.toDataURL('image/png')
      link.download = 'design.png'
      link.click()
    },
    async saveDesignToLibrary() {
      if (this.pendingSave) return
      this.pendingSave = true
      try {
        const canvas = await this.buildExportCanvas()
        const dataUrl = canvas.toDataURL('image/png')
        const payload = {
          id: this.uid(),
          template_id: this.activeTemplate.id,
          name: `design-${new Date().toISOString()}`,
          preview: dataUrl,
          state: {
            activeTemplate: this.activeTemplate,
            elements: this.elements,
            drawing: this.$refs.drawingCanvas.toDataURL('image/png')
          },
          created_at: new Date().toISOString()
        }
        const current = JSON.parse(localStorage.getItem('designer_designs') || '[]')
        current.unshift(payload)
        localStorage.setItem('designer_designs', JSON.stringify(current))
        alert(this.t('designer.saved_successfully', 'تم حفظ التصميم محليًا بنجاح'))
      } catch (e) {
        alert(this.t('designer.save_failed', 'تعذر حفظ التصميم'))
      } finally {
        this.pendingSave = false
      }
    },
    openWardrobe() {
      this.wardrobeOpen = true
      this.wardrobeMinimized = false
      this.openPalette = null
    },
    toggleWardrobe() {
      this.wardrobeOpen = !this.wardrobeOpen
      if (!this.wardrobeOpen) {
        this.wardrobeMinimized = false
      }
    },
    toggleWardrobeMinimized() {
      this.wardrobeMinimized = !this.wardrobeMinimized
    },
    navigateTo(route) {
      // Use Inertia.js to navigate to the route
      if (this.$inertia) {
        this.$inertia.visit(route)
      } else {
        // Fallback to window.location if Inertia is not available
        window.location.href = route
      }
    },
    handleNavItemClick(item) {
      // Update active state
      this.navItems.forEach(navItem => {
        navItem.active = false
      })
      item.active = true
      
      // Navigate to the route
      if (item.route) {
        this.navigateTo(item.route)
      }
    },
    async saveDesignToDatabase() {
      if (this.pendingSave) return
      this.pendingSave = true

      try {
        const canvas = await this.buildExportCanvas()
        const dataUrl = canvas.toDataURL('image/png')

        // Create a basic design data object
        const designData = {
          elements: this.elements,
          activeTemplate: this.activeTemplate,
          drawing: this.$refs.drawingCanvas.toDataURL('image/png'),
          garment_color: this.garmentColor,
          dress_size: this.selectedSize || 'M'  // Default size if none selected
        }

        const payload = {
          name: `تصميم ${new Date().toLocaleString('ar-SA')} - ${this.activeTemplate.name || 'مخصص'}`,
          design_data: designData,
          preview_url: dataUrl,
          product_type_id: this.activeTemplate.product_type_id || null,  // Use template's product type if available
          product_id: null,  // Let backend handle product creation
          template_id: this.activeTemplate.id || null,
          is_public: false,
          session_id: this.getSessionId()
        }

        // Add user ID if authenticated
        if (this.auth?.user?.id) {
          payload.user_id = this.auth.user.id
        }

        const response = await fetch('/api/designs', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(payload)
        })

        const result = await response.json()

        if (!response.ok) {
          throw new Error(result.message || result.error || 'فشل الحفظ في قاعدة البيانات')
        }

        alert(this.t('designer.saved_to_db_success', 'تم حفظ التصميم في قاعدة البيانات بنجاح!'))

        // Optionally redirect to my designs
        setTimeout(() => {
          if (this.auth?.user) {
            window.location.href = '/designer/my-designs'
          }
        }, 1500)

      } catch (error) {
        console.error('Save to database error:', error)
        alert(this.t('designer.save_to_db_error', 'خطأ في حفظ التصميم: ') + error.message)
      } finally {
        this.pendingSave = false
      }
    },
    getSessionId() {
      // Get or create session ID
      let sessionId = sessionStorage.getItem('designer_session_id')
      if (!sessionId) {
        sessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9)
        sessionStorage.setItem('designer_session_id', sessionId)
      }
      return sessionId
    },
    async updateMockupImage() {
      const canvas = await this.buildExportCanvas()
      this.mockupImage = canvas.toDataURL('image/png')
    }
  }
}
</script>

<style>
:root {
  --primary: #9c27b0;
  --secondary: #673ab7;
  --text: #333;
  --bg-color: #e0e5ec;
  --shadow-light: #ffffff;
  --shadow-dark: #a3b1c6;
  --tools-width: 80px;
  --sidebar-width: 80px;
  --wardrobe-width: 360px;
  --wardrobe-height: 520px;
  --wood-color: #8B4513;
  --wood-light: #A0522D;
  --wood-dark: #5D4037;
  --metal-color: #B0B0B0;
  --clothes-hanger: #C0C0C0
}

* {
  box-sizing: border-box
}

.designer-content {
  min-height: 100vh;
  background-color: var(--bg-color)
}

.app-container {
  display: grid;
  grid-template-columns: var(--sidebar-width) 1fr var(--tools-width);
  min-height: 100vh;
  overflow: hidden;
  position: relative
}

.neumorphic {
  border-radius: 15px;
  background: var(--bg-color);
  box-shadow: 9px 9px 16px var(--shadow-dark), -9px -9px 16px var(--shadow-light)
}

.neumorphic-inset {
  border-radius: 18px;
  background: var(--bg-color);
  box-shadow: inset 3px 3px 5px var(--shadow-dark), inset -3px -3px 5px var(--shadow-light)
}

.neumorphic-btn {
  border: none;
  border-radius: 10px;
  background: var(--bg-color);
  box-shadow: 5px 5px 10px var(--shadow-dark), -5px -5px 10px var(--shadow-light);
  cursor: pointer
}

.sidebar,
.tools-panel {
  background: var(--bg-color);
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 20px
}

.nav-item,
.tool-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 25px;
  width: 100%;
  text-align: center
}

.nav-icon,
.tool-icon {
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  color: var(--primary);
  margin-bottom: 5px;
  cursor: pointer;
  border-radius: 15px;
  background: var(--bg-color);
  box-shadow: 5px 5px 10px var(--shadow-dark), -5px -5px 10px var(--shadow-light);
  transition: all .3s;
  border: none;
  text-decoration: none
}

.nav-icon.active,
.tool-icon.active {
  color: #fff;
  background: var(--primary)
}

.nav-label,
.tool-label {
  font-size: 12px;
  color: var(--text)
}

.design-area {
  position: relative;
  padding: 20px;
  overflow: hidden
}

.user-panel {
  position: absolute;
  top: 25px;
  left: 25px;
  z-index: 10
}

.user-avatar {
  width: 50px;
  height: 50px;
  color: var(--primary);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px
}

.top-toolbar {
  position: absolute;
  top: 20px;
  left: 90px;
  right: 90px;
  height: 70px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 18px;
  z-index: 8
}

.toolbar-group,
.preview-actions,
.palette-header,
.properties-actions,
.wardrobe-header {
  display: flex;
  align-items: center;
  gap: 10px
}

.toolbar-btn,
.hero-action,
.action-btn,
.control-btn,
.template-card,
.size-option,
.layer-item,
.clothing-item-3d {
  border: none;
  cursor: pointer;
  transition: .25s ease;
  font-weight: 700
}

.toolbar-btn {
  width: 44px;
  height: 44px;
  color: var(--primary)
}

.hero-action,
.action-btn,
.control-btn {
  padding: 12px 16px;
  border-radius: 12px
}

.hero-action,
.action-btn {
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  color: #fff
}

.hero-action {
  display: inline-flex;
  align-items: center;
  gap: 8px
}

.canvas-wrap {
  height: calc(100vh - 170px);
  display: flex;
  justify-content: center;
  align-items: center;
  padding-top: 55px
}

.design-stage {
  position: relative;
  width: min(980px, calc(100% - 40px));
  height: 680px;
  overflow: hidden
}

.stage-background,
.dress-template,
.drawing-layer,
.elements-layer {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%
}

.dress-template {
  z-index: 1;
  pointer-events: none
}

.drawing-layer {
  z-index: 2
}

.drawing-layer.draw-cursor {
  cursor: crosshair
}

.elements-layer {
  z-index: 3;
  pointer-events: none
}

.design-element {
  position: absolute;
  transform-origin: center center;
  border: 1px solid transparent;
  pointer-events: auto
}

.design-element.selected {
  border: 1px dashed rgba(156, 39, 176, .9);
  box-shadow: 0 0 0 2px rgba(156, 39, 176, .08)
}

.text-node,
.shape-node,
.image-node {
  width: 100%;
  height: 100%
}

.text-node {
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  white-space: pre-wrap;
  word-break: break-word;
  padding: 8px
}

.image-node {
  object-fit: contain;
  pointer-events: none
}

.rect-node {
  border-radius: 18px
}

.circle-node {
  border-radius: 50%
}

.triangle-node {
  margin: auto
}

.resize-handle,
.rotate-handle,
.delete-handle,
.close-btn {
  border: none;
  cursor: pointer
}

.resize-handle,
.rotate-handle,
.delete-handle {
  position: absolute;
  border-radius: 50%
}

.resize-handle {
  width: 18px;
  height: 18px;
  right: -9px;
  bottom: -9px;
  background: var(--primary);
  box-shadow: 0 0 0 3px white
}

.rotate-handle {
  width: 30px;
  height: 30px;
  top: -15px;
  left: calc(50% - 15px);
  background: #fff;
  color: var(--primary);
  box-shadow: 0 8px 18px rgba(0, 0, 0, .15)
}

.delete-handle {
  width: 24px;
  height: 24px;
  top: -12px;
  right: -12px;
  background: #f44336;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 6px rgba(0, 0, 0, .3);
  z-index: 10;
}

.bottom-toolbar {
  position: absolute;
  left: 50%;
  bottom: 22px;
  transform: translateX(-50%);
  z-index: 8;
  display: flex;
  gap: 14px;
  padding: 14px 20px
}

.control-btn.delete-btn {
  background: #f44336;
  color: #fff
}

.control-btn.reset-btn {
  background: #607d8b;
  color: #fff
}

.control-btn.save-btn {
  color: #fff
}

.palette {
  position: fixed;
  z-index: 20;
  background: var(--bg-color);
  border-radius: 20px;
  box-shadow: 8px 8px 15px var(--shadow-dark), -8px -8px 15px var(--shadow-light);
  padding: 18px;
  width: 240px
}

.wide-palette {
  width: 420px
}

.palette-header {
  justify-content: space-between;
  margin-bottom: 12px
}

.palette-title {
  font-weight: 700;
  color: var(--primary)
}

.close-btn {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: var(--bg-color);
  box-shadow: 3px 3px 6px var(--shadow-dark), -3px -3px 6px var(--shadow-light);
  color: var(--primary)
}

.close-btn.small {
  width: 28px;
  height: 28px;
  font-size: 12px
}

.close-btn.on-panel {
  background: rgba(255, 255, 255, .7)
}

.color-palette,
.size-palette,
.template-palette,
.text-palette,
.image-palette,
.layers-palette {
  right: 100px
}

.color-palette,
.size-palette {
  top: 40%;
  transform: translateY(-50%)
}

.template-palette {
  top: 14%;
  max-height: 76vh;
  overflow: auto
}

.text-palette {
  top: 20%;
  width: 270px
}

.image-palette {
  top: 20%;
  width: 220px
}

.layers-palette {
  top: 24%;
  width: 260px
}

.properties-palette {
  top: 12%;
  left: 105px;
  width: 280px
}

.color-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px
}

.color-option {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  border: 2px solid transparent;
  box-shadow: 3px 3px 6px var(--shadow-dark), -3px -3px 6px var(--shadow-light)
}

.color-option.selected {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px var(--bg-color), 0 0 0 5px var(--primary)
}

.size-option,
.template-card,
.action-btn,
.palette-input,
.property-field select,
.property-field input[type='color'],
.layer-item {
  width: 100%
}

.size-option,
.template-card,
.action-btn,
.layer-item {
  margin-top: 10px;
  border-radius: 12px;
  padding: 10px 12px;
  background: var(--bg-color);
  box-shadow: inset 3px 3px 5px var(--shadow-dark), inset -3px -3px 5px var(--shadow-light)
}

.size-option.selected,
.template-card.active,
.layer-item.active,
.clothing-item-3d.active {
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  color: #fff;
  box-shadow: none
}

.template-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px
}

.template-card {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 0
}

.template-image-preview {
  height: 110px;
  overflow: hidden;
  border-radius: 12px;
  display: block
}

.template-image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block
}

.template-name {
  font-size: 13px
}

.palette-input,
.property-field select {
  border: none;
  border-radius: 12px;
  padding: 10px 12px;
  background: rgba(255, 255, 255, .75);
  margin-top: 8px
}

.property-field {
  display: block;
  margin-bottom: 12px;
  color: var(--text);
  font-size: 13px
}

.layers-list {
  max-height: 280px;
  overflow: auto
}

.layer-item {
  display: flex;
  align-items: center;
  justify-content: space-between
}

.loading-box,
.error-box,
.empty-box {
  padding: 14px;
  border-radius: 12px;
  text-align: center;
  margin-top: 10px
}

.loading-box {
  background: #eef2ff;
  color: #4338ca
}

.error-box {
  background: #fee2e2;
  color: #b91c1c
}

.empty-box {
  background: #f8fafc;
  color: #475569
}

.wardrobe-container-3d {
  position: fixed;
  bottom: 50px;
  right: 100px;
  width: var(--wardrobe-width);
  height: var(--wardrobe-height);
  perspective: 1500px;
  z-index: 90
}

.wardrobe-container-3d.wardrobe-minimized {
  height: 60px
}

.wardrobe-3d {
  width: 100%;
  height: 100%;
  position: relative;
  transform-style: preserve-3d;
  transition: transform .8s cubic-bezier(.68, -.55, .265, 1.55)
}

.wardrobe-3d.minimized {
  height: 50px;
  transform: translateY(0) !important
}

.wardrobe-3d.minimized .wardrobe-inside-3d {
  display: none
}

.wardrobe-3d.minimized .wardrobe-body-3d {
  display: flex;
  align-items: center;
  justify-content: center
}

.wardrobe-body-3d {
  position: absolute;
  width: 100%;
  height: 100%;
  background: linear-gradient(to right, var(--wood-light), var(--wood-color));
  border-radius: 8px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, .3);
  overflow: hidden
}

.wardrobe-door-3d {
  position: absolute;
  width: 49%;
  height: 100%;
  background: linear-gradient(to right, var(--wood-dark), var(--wood-color));
  transition: transform 1s ease-in-out;
  box-shadow: 5px 0 15px rgba(0, 0, 0, .2);
  display: flex;
  align-items: center;
  justify-content: center
}

.door-left-3d {
  left: 0;
  border-radius: 8px 0 0 8px;
  transform-origin: left center
}

.door-right-3d {
  right: 0;
  border-radius: 0 8px 8px 0;
  transform-origin: right center
}

.door-handle-3d {
  width: 20px;
  height: 60px;
  background: linear-gradient(to bottom, #D3D3D3, #A9A9A9);
  border-radius: 10px;
  position: absolute
}

.door-left-3d .door-handle-3d {
  right: 15px
}

.door-right-3d .door-handle-3d {
  left: 15px
}

.wardrobe-inside-3d {
  position: absolute;
  width: calc(100% - 60px);
  height: calc(100% - 60px);
  top: 30px;
  left: 30px;
  background: #F8F8F8;
  border-radius: 4px;
  display: none;
  flex-wrap: wrap;
  align-content: flex-start;
  padding: 20px;
  gap: 15px;
  overflow-y: auto;
  box-shadow: inset 0 0 20px rgba(0, 0, 0, .1)
}

.wardrobe-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px
}

.wardrobe-controls {
  display: flex;
  gap: 5px
}

.wardrobe-minimize-btn {
  background: var(--bg-color);
  border-radius: 50%;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 3px 3px 6px var(--shadow-dark), -3px -3px 6px var(--shadow-light);
  border: none;
  margin-left: 10px
}

.wardrobe-minimize-btn:hover {
  background: var(--shadow-light)
}

.clothes-rail-3d {
  width: 100%;
  height: 30px;
  background: var(--metal-color);
  border-radius: 5px;
  position: relative;
  margin-bottom: 20px
}

.hanger-3d {
  position: absolute;
  width: 25px;
  height: 40px;
  top: 30px;
  background: var(--clothes-hanger);
  border-radius: 0 0 10px 10px
}

.hanger-3d:before {
  content: "";
  position: absolute;
  width: 15px;
  height: 15px;
  background: var(--clothes-hanger);
  border-radius: 50%;
  top: -7px;
  left: 5px
}

.clothing-item-3d {
  width: 85px;
  position: relative;
  background: transparent;
  padding: 0
}

.clothing-img-container-3d {
  width: 100%;
  height: 110px;
  position: relative;
  box-shadow: 0 5px 15px rgba(0, 0, 0, .1);
  border-radius: 6px;
  overflow: hidden;
  background: #fff
}

.clothing-img-3d,
.clothing-fallback {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block
}

.clothing-name-3d {
  font-size: 12px;
  text-align: center;
  margin-top: 8px;
  color: #333;
  font-weight: 600
}

.wardrobe-3d.open {
  transform: translateY(-20px)
}

.wardrobe-3d.open .door-left-3d {
  transform: rotateY(-140deg)
}

.wardrobe-3d.open .door-right-3d {
  transform: rotateY(140deg)
}

.wardrobe-3d.open .wardrobe-inside-3d {
  display: flex
}

.wardrobe-control-3d {
  position: absolute;
  bottom: -25px;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(to bottom, #8B4513, #A0522D);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  cursor: pointer;
  box-shadow: 0 5px 20px rgba(0, 0, 0, .3);
  border: 2px solid #5D4037;
  z-index: 110
}

.wardrobe-state {
  width: 100%
}

.preview-panel {
  position: fixed;
  top: 24px;
  right: 128px;
  width: 470px;
  height: calc(100vh - 48px);
  background: rgba(224, 229, 236, .96);
  border-radius: 24px;
  box-shadow: -12px 0 30px rgba(0, 0, 0, .12);
  z-index: 25;
  padding: 18px;
  display: flex;
  flex-direction: column
}

.preview-header {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 14px
}

.preview-header h3 {
  margin: 0 0 6px;
  color: #4a148c
}

.preview-header p {
  margin: 0;
  color: #5c6270;
  font-size: 13px;
  line-height: 1.7
}

.action-btn.compact {
  padding: 10px 12px;
  font-size: 12px
}

.mockup-stage {
  flex: 1;
  border-radius: 18px;
  overflow: hidden;
  background: linear-gradient(180deg, #edf2f7, #dde4ea);
  box-shadow: inset 5px 5px 10px rgba(163, 177, 198, .45), inset -5px -5px 10px rgba(255, 255, 255, .75);
  position: relative;
  perspective: 1800px
}

.mannequin-3d {
  position: absolute;
  inset: 0;
  margin: auto;
  width: 320px;
  height: 660px;
  transform-style: preserve-3d;
  transform: rotateX(3deg) rotateY(-10deg)
}

.mannequin-3d.spinning {
  animation: spinModel 10s linear infinite
}

.hair-back,
.hair-front,
.mannequin-head,
.mannequin-neck,
.mannequin-body,
.arm,
.forearm,
.hip,
.leg,
.shin {
  position: absolute;
  box-shadow: 0 10px 18px rgba(0, 0, 0, .12)
}

.hair-back {
  width: 122px;
  height: 145px;
  border-radius: 60px;
  left: 99px;
  top: 24px;
  background: linear-gradient(180deg, #402313, #110804)
}

.hair-front {
  width: 92px;
  height: 65px;
  border-radius: 45px 45px 30px 30px;
  left: 114px;
  top: 20px;
  background: linear-gradient(180deg, #3b2114, #140a06)
}

.mannequin-head {
  width: 82px;
  height: 104px;
  border-radius: 45px;
  left: 119px;
  top: 34px;
  background: linear-gradient(180deg, #f7dfd2, #efcab8)
}

.mannequin-neck {
  width: 30px;
  height: 42px;
  border-radius: 18px;
  left: 145px;
  top: 130px;
  background: linear-gradient(180deg, #f5d7c8, #edc4b0)
}

.mannequin-body {
  width: 192px;
  height: 316px;
  left: 64px;
  top: 152px;
  border-radius: 86px 86px 50px 50px / 95px 95px 48px 48px;
  overflow: hidden;
  background: linear-gradient(180deg, #fff, #f1f5f9)
}

.body-highlight {
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 50% 22%, rgba(255, 255, 255, .35), transparent 30%)
}

.garment-surface {
  position: absolute;
  inset: 0;
  border-radius: inherit;
  overflow: hidden
}

.garment-texture {
  width: 100%;
  height: 100%;
  object-fit: cover
}

.arm {
  width: 38px;
  height: 198px;
  top: 176px;
  border-radius: 30px;
  background: linear-gradient(180deg, #f5d7c8, #edc4b0)
}

.arm-left {
  left: 20px;
  transform: rotate(17deg)
}

.arm-right {
  right: 20px;
  transform: rotate(-17deg)
}

.forearm {
  width: 30px;
  height: 155px;
  top: 338px;
  border-radius: 24px;
  background: linear-gradient(180deg, #f5d7c8, #edc4b0)
}

.forearm-left {
  left: 7px;
  transform: rotate(10deg)
}

.forearm-right {
  right: 7px;
  transform: rotate(-10deg)
}

.hip {
  width: 206px;
  height: 108px;
  left: 57px;
  top: 420px;
  border-radius: 50%;
  background: linear-gradient(180deg, #f3d5c7, #ebc1ae)
}

.leg,
.shin {
  width: 54px;
  border-radius: 30px;
  background: linear-gradient(180deg, #f5d7c8, #edc4b0)
}

.leg {
  height: 184px;
  top: 474px
}

.shin {
  height: 168px;
  top: 606px
}

.leg-left,
.shin-left {
  left: 96px
}

.leg-right,
.shin-right {
  right: 96px
}

.floor-shadow {
  position: absolute;
  width: 220px;
  height: 48px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(0, 0, 0, .18), transparent 70%);
  left: 48px;
  bottom: -10px;
  transform: rotateX(88deg) translateZ(-30px)
}

.preview-slide-enter-active,
.preview-slide-leave-active {
  transition: all .25s ease
}

.preview-slide-enter-from,
.preview-slide-leave-to {
  opacity: 0;
  transform: translateX(24px)
}

.hidden-file {
  display: none
}

@keyframes spinModel {
  from {
    transform: rotateX(3deg) rotateY(-10deg)
  }

  to {
    transform: rotateX(3deg) rotateY(350deg)
  }
}
</style>
