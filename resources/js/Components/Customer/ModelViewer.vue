<template>
  <div class="model-viewer-container">
    <div ref="container" class="model-container"></div>
    
    <!-- Loading indicator -->
    <div v-if="loading" class="loading-overlay">
      <div class="loading-spinner">
        <div class="spinner"></div>
        <p>Loading 3D Model...</p>
      </div>
    </div>
    
    <!-- Controls -->
    <div class="model-controls" v-if="!loading">
      <button @click="toggleRotation" class="control-btn">
        <i :class="isRotating ? 'pi pi-pause' : 'pi pi-play'"></i>
      </button>
      <button @click="resetView" class="control-btn">
        <i class="pi pi-refresh"></i>
      </button>
      <button @click="toggleFullscreen" class="control-btn">
        <i class="pi pi-expand"></i>
      </button>
    </div>
    
    <!-- Error message -->
    <div v-if="error" class="error-overlay">
      <div class="error-content">
        <i class="pi pi-exclamation-triangle text-4xl mb-3"></i>
        <p class="error-text">{{ error }}</p>
        <button @click="retryLoad" class="retry-btn">Retry</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import * as THREE from 'three'
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js'
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js'

const props = defineProps({
  modelPath: {
    type: String,
    default: '/model/xander-avaturn-model.glb'
  },
  autoRotate: {
    type: Boolean,
    default: true
  },
  backgroundColor: {
    type: String,
    default: '#f0f0f0'
  }
})

const container = ref(null)
const loading = ref(true)
const error = ref(null)
const isRotating = ref(true)

let scene, camera, renderer, model, controls, animationId
let targetRotation = 0

onMounted(() => {
  init()
  loadModel()
  animate()
  
  // Handle window resize
  window.addEventListener('resize', onWindowResize)
})

onUnmounted(() => {
  cleanup()
  window.removeEventListener('resize', onWindowResize)
})

watch(() => props.autoRotate, (newVal) => {
  isRotating.value = newVal
})

function init() {
  // Create scene
  scene = new THREE.Scene()
  scene.background = new THREE.Color(props.backgroundColor)
  
  // Create camera
  camera = new THREE.PerspectiveCamera(45, 1, 0.1, 1000)
  camera.position.set(0, 1.5, 3)
  
  // Create renderer
  renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true })
  renderer.setSize(400, 600)
  renderer.setPixelRatio(window.devicePixelRatio)
  renderer.shadowMap.enabled = true
  renderer.shadowMap.type = THREE.PCFSoftShadowMap
  
  if (container.value) {
    container.value.appendChild(renderer.domElement)
  }
  
  // Add lighting
  const ambientLight = new THREE.AmbientLight(0xffffff, 0.6)
  scene.add(ambientLight)
  
  const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8)
  directionalLight.position.set(5, 10, 7)
  directionalLight.castShadow = true
  directionalLight.shadow.mapSize.width = 1024
  directionalLight.shadow.mapSize.height = 1024
  scene.add(directionalLight)
  
  // Add orbit controls
  controls = new OrbitControls(camera, renderer.domElement)
  controls.enableDamping = true
  controls.dampingFactor = 0.05
  controls.screenSpacePanning = false
  controls.minDistance = 2
  controls.maxDistance = 10
  controls.maxPolarAngle = Math.PI / 2
  
  // Handle resize
  onWindowResize()
}

function loadModel() {
  loading.value = true
  error.value = null
  
  const loader = new GLTFLoader()
  
  loader.load(
    props.modelPath,
    (gltf) => {
      model = gltf.scene
      scene.add(model)
      
      // Center the model
      const box = new THREE.Box3().setFromObject(model)
      const center = box.getCenter(new THREE.Vector3())
      const size = box.getSize(new THREE.Vector3())
      
      // Center the model
      model.position.x = -center.x
      model.position.y = -center.y
      model.position.z = -center.z
      
      // Adjust camera position based on model size
      const maxDim = Math.max(size.x, size.y, size.z)
      camera.position.set(0, maxDim * 0.8, maxDim * 1.5)
      camera.lookAt(0, 0, 0)
      
      // Enable shadows
      model.traverse((child) => {
        if (child.isMesh) {
          child.castShadow = true
          child.receiveShadow = true
        }
      })
      
      loading.value = false
    },
    (progress) => {
      // Progress callback
      console.log('Loading progress:', (progress.loaded / progress.total) * 100 + '%')
    },
    (err) => {
      console.error('Error loading model:', err)
      error.value = 'Failed to load 3D model. Please try again.'
      loading.value = false
    }
  )
}

function animate() {
  animationId = requestAnimationFrame(animate)
  
  if (controls) {
    controls.update()
  }
  
  if (model && isRotating.value) {
    model.rotation.y += 0.005
  }
  
  if (renderer && scene && camera) {
    renderer.render(scene, camera)
  }
}

function onWindowResize() {
  if (!container.value || !camera || !renderer) return
  
  const width = container.value.clientWidth
  const height = container.value.clientHeight
  
  camera.aspect = width / height
  camera.updateProjectionMatrix()
  
  renderer.setSize(width, height)
}

function toggleRotation() {
  isRotating.value = !isRotating.value
}

function resetView() {
  if (controls && camera) {
    controls.reset()
    camera.position.set(0, 1.5, 3)
    camera.lookAt(0, 0, 0)
  }
  if (model) {
    model.rotation.set(0, 0, 0)
  }
}

function toggleFullscreen() {
  if (!container.value) return
  
  if (!document.fullscreenElement) {
    container.value.requestFullscreen().catch(err => {
      console.error('Error attempting to enable fullscreen:', err)
    })
  } else {
    document.exitFullscreen()
  }
}

function retryLoad() {
  if (model) {
    scene.remove(model)
    model = null
  }
  loadModel()
}

function cleanup() {
  if (animationId) {
    cancelAnimationFrame(animationId)
  }
  
  if (renderer) {
    renderer.dispose()
  }
  
  if (controls) {
    controls.dispose()
  }
  
  // Remove event listeners
  if (container.value && renderer) {
    container.value.removeChild(renderer.domElement)
  }
}
</script>

<style scoped>
.model-viewer-container {
  position: relative;
  width: 100%;
  height: 600px;
  border-radius: 20px;
  overflow: hidden;
  background: linear-gradient(135deg, #e0e5ec 0%, #c9d6ff 100%);
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.1),
    inset 8px 8px 16px #a3b1c6,
    inset -8px -8px 16px #ffffff;
}

body.night-mode .model-viewer-container {
  background: linear-gradient(135deg, #1e1e1e 0%, #3a3a5a 100%);
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.3),
    inset 8px 8px 16px #1a202c,
    inset -8px -8px 16px #4a5568;
}

.model-container {
  width: 100%;
  height: 100%;
  position: relative;
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(224, 229, 236, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
  backdrop-filter: blur(10px);
}

body.night-mode .loading-overlay {
  background: rgba(30, 30, 30, 0.8);
}

.loading-spinner {
  text-align: center;
  color: #4a7eff;
}

body.night-mode .loading-spinner {
  color: #6ea8ff;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e0e5ec;
  border-top: 4px solid #4a7eff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 15px;
}

body.night-mode .spinner {
  border: 4px solid #2d3748;
  border-top: 4px solid #6ea8ff;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.model-controls {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 10px;
  z-index: 5;
  background: rgba(224, 229, 236, 0.8);
  padding: 10px 15px;
  border-radius: 30px;
  backdrop-filter: blur(10px);
}

body.night-mode .model-controls {
  background: rgba(45, 55, 72, 0.8);
}

.control-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
}

.control-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(102, 126, 234, 0.4);
}

.control-btn:active {
  transform: translateY(0);
}

.error-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(224, 229, 236, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
  backdrop-filter: blur(10px);
}

body.night-mode .error-overlay {
  background: rgba(30, 30, 30, 0.9);
}

.error-content {
  text-align: center;
  padding: 30px;
  background: #ffffff;
  border-radius: 20px;
  box-shadow: 
    0 10px 30px rgba(0, 0, 0, 0.1),
    inset 5px 5px 10px #a3b1c6,
    inset -5px -5px 10px #ffffff;
  max-width: 300px;
}

body.night-mode .error-content {
  background: #2d3748;
  box-shadow: 
    0 10px 30px rgba(0, 0, 0, 0.3),
    inset 5px 5px 10px #1a202c,
    inset -5px -5px 10px #4a5568;
}

.error-text {
  color: #e53e3e;
  margin-bottom: 20px;
  font-weight: 500;
}

body.night-mode .error-text {
  color: #fc8181;
}

.retry-btn {
  padding: 10px 25px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 25px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.retry-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* Responsive Design */
@media (max-width: 768px) {
  .model-viewer-container {
    height: 450px;
  }
  
  .model-controls {
    bottom: 15px;
    padding: 8px 12px;
  }
  
  .control-btn {
    width: 35px;
    height: 35px;
    font-size: 14px;
  }
}
</style>