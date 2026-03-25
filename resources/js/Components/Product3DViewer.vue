<script setup>
import { ref, shallowRef, computed, onMounted, onBeforeUnmount, watch, nextTick } from "vue";
import * as THREE from "three";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls.js";
import { GLTFLoader } from "three/examples/jsm/loaders/GLTFLoader.js";
import { FBXLoader } from "three/examples/jsm/loaders/FBXLoader.js";
import { TDSLoader } from "three/examples/jsm/loaders/TDSLoader.js";
import { Timer } from "three";

const props = defineProps({
    productName: {
        type: String,
        default: "Product",
    },
    images: {
        type: Array,
        default: () => [],
    },
    productImage: {
        type: String,
        default: "",
    },
    height: {
        type: Number,
        default: 680,
    },
    autoRotate: {
        type: Boolean,
        default: true,
    },
    showGrid: {
        type: Boolean,
        default: false,
    },
    fullscreen: {
        type: Boolean,
        default: false,
    },
});

const MODEL_OPTIONS = [
    {
        id: "eve",
        nameKey: "viewer.models.eve",
        shortKey: "viewer.models_short.eve",
        url: "/model/eve_-_character_model.glb",
        type: "glb",
        fit: {
            targetHeight: 2.15,
            cameraZ: 2.9,
            targetY: 0.85,
            projection: {
                x: 0,
                y: 0.44,
                z: 0.18,
                scaleX: 0.84,
                scaleY: 1.04,
                rotateX: 4,
                rotateY: 0,
                rotateZ: 0,
                curve: 0.10,
                opacity: 1,
            },
        },
    },
    {
        id: "fmel_fbx",
        nameKey: "viewer.models.female_fbx",
        shortKey: "viewer.models_short.female_fbx",
        url: "/model/fmel_girl_4.0_.fbx",
        type: "fbx",
        fit: {
            targetHeight: 2.20,
            cameraZ: 3.0,
            targetY: 0.82,
            projection: {
                x: 0,
                y: 0.42,
                z: 0.17,
                scaleX: 0.82,
                scaleY: 1.02,
                rotateX: 5,
                rotateY: 0,
                rotateZ: 0,
                curve: 0.11,
                opacity: 1,
            },
        },
    },
    {
        id: "kangana",
        nameKey: "viewer.models.kangana",
        shortKey: "viewer.models_short.kangana",
        url: "/model/kangana ranaout.glb",
        type: "glb",
        fit: {
            targetHeight: 2.18,
            cameraZ: 2.85,
            targetY: 0.82,
            projection: {
                x: 0,
                y: 0.40,
                z: 0.18,
                scaleX: 0.82,
                scaleY: 1.00,
                rotateX: 4,
                rotateY: 0,
                rotateZ: 0,
                curve: 0.10,
                opacity: 1,
            },
        },
    },
    {
        id: "rihanna_3ds",
        nameKey: "viewer.models.rihanna",
        shortKey: "viewer.models_short.rihanna",
        url: "/model/Rihanna 3DS.3DS",
        type: "3ds",
        fit: {
            targetHeight: 2.12,
            cameraZ: 3.1,
            targetY: 0.84,
            projection: {
                x: 0,
                y: 0.43,
                z: 0.20,
                scaleX: 0.80,
                scaleY: 1.00,
                rotateX: 4,
                rotateY: 0,
                rotateZ: 0,
                curve: 0.09,
                opacity: 1,
            },
        },
    },
    {
        id: "xander",
        nameKey: "viewer.models.xander",
        shortKey: "viewer.models_short.xander",
        url: "/model/xander-avaturn-model.glb",
        type: "glb",
        fit: {
            targetHeight: 2.18,
            cameraZ: 2.95,
            targetY: 0.84,
            projection: {
                x: 0,
                y: 0.41,
                z: 0.16,
                scaleX: 0.80,
                scaleY: 0.98,
                rotateX: 4,
                rotateY: 0,
                rotateZ: 0,
                curve: 0.10,
                opacity: 1,
            },
        },
    },
];

const containerRef = ref(null);

const isLoading = ref(true);
const hasError = ref(false);
const errorMessage = ref("");
const usingFallback = ref(false);

const showControlsPanel = ref(false);
const projectionVisible = ref(true);
const showPropertyOverlays = ref(false);
const isBackgroundRemovalActive = ref(false);
const showImageEditorModal = ref(false);
const showSidebar = ref(false);
const showOrderForm = ref(false);

// Order customization properties
const orderDetails = ref({
    color: '#ffffff',
    size: 'M',
    quantity: 1,
    price: 0,
    notes: '',
});

const availableSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
const availableColors = ['#ffffff', '#000000', '#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff', '#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57', '#ff9ff3'];

// Expanded sections for control panel - all collapsed by default
const expandedSections = ref({
    imageSelector: false,
    quickControls: false,
    fineControls: false,
});

// Color management and eraser functionality
const isColorModeActive = ref(false);
const selectedColor = ref('#ffffff');
const isEraserActive = ref(false);
const eraserSize = ref(20); // in pixels
const colorIntensity = ref(0.7); // 0-1 range for color blending

const selectedModelId = ref("kangana");
const selectedModel = computed(() => {
    return MODEL_OPTIONS.find((m) => m.id === selectedModelId.value) || MODEL_OPTIONS[0];
});

const selectedModelIndex = computed(() => {
    return MODEL_OPTIONS.findIndex((m) => m.id === selectedModelId.value);
});

const sceneRef = shallowRef(null);
const cameraRef = shallowRef(null);
const rendererRef = shallowRef(null);
const controlsRef = shallowRef(null);
const modelRef = shallowRef(null);
const animationFrameRef = shallowRef(null);
const resizeObserverRef = shallowRef(null);
const projectionGroupRef = shallowRef(null);
const projectionMeshRef = shallowRef(null);
const projectionTextureRef = shallowRef(null);
const mixerRef = shallowRef(null);

const timer = new Timer();

const projection = ref({
    x: 0,
    y: 0.42,
    z: 0.18,
    scaleX: 0.82,
    scaleY: 1.02,
    rotateX: 4,
    rotateY: 0,
    rotateZ: 0,
    curve: 0.10,
    opacity: 1,
});

const activeImage = ref(props.productImage || props.images?.[0] || "");

const availableImages = computed(() => {
    const list = [];
    if (props.productImage) list.push(props.productImage);
    if (Array.isArray(props.images)) {
        props.images.forEach((img) => {
            if (img && !list.includes(img)) list.push(img);
        });
    }
    return list;
});

function degToRad(v) {
    return THREE.MathUtils.degToRad(Number(v));
}



function getViewerHeight() {
    return props.height || (props.fullscreen ? 720 : 680);
}

function applyModelPreset() {
    const preset = selectedModel.value?.fit?.projection;
    if (!preset) return;

    projection.value = {
        x: preset.x,
        y: preset.y,
        z: preset.z,
        scaleX: preset.scaleX,
        scaleY: preset.scaleY,
        rotateX: preset.rotateX,
        rotateY: preset.rotateY,
        rotateZ: preset.rotateZ,
        curve: preset.curve,
        opacity: preset.opacity,
    };
}

function disposeMaterial(material) {
    if (!material) return;

    if (Array.isArray(material)) {
        material.forEach(disposeMaterial);
        return;
    }

    if (material.map) material.map.dispose?.();
    if (material.alphaMap) material.alphaMap.dispose?.();
    if (material.normalMap) material.normalMap.dispose?.();
    if (material.roughnessMap) material.roughnessMap.dispose?.();
    if (material.metalnessMap) material.metalnessMap.dispose?.();
    if (material.aoMap) material.aoMap.dispose?.();
    material.dispose?.();
}

function cleanupScene() {
    if (animationFrameRef.value) {
        cancelAnimationFrame(animationFrameRef.value);
        animationFrameRef.value = null;
    }

    if (controlsRef.value) {
        controlsRef.value.dispose();
        controlsRef.value = null;
    }

    if (sceneRef.value) {
        sceneRef.value.traverse((child) => {
            if (child.geometry) child.geometry.dispose?.();
            if (child.material) disposeMaterial(child.material);
        });
    }

    if (rendererRef.value) {
        rendererRef.value.dispose();
        rendererRef.value.forceContextLoss?.();
        if (rendererRef.value.domElement?.parentNode) {
            rendererRef.value.domElement.parentNode.removeChild(rendererRef.value.domElement);
        }
    }

    sceneRef.value = null;
    cameraRef.value = null;
    rendererRef.value = null;
    modelRef.value = null;
    controlsRef.value = null;
    projectionGroupRef.value = null;
    projectionMeshRef.value = null;
    projectionTextureRef.value = null;
    mixerRef.value = null;
}

function createRenderer(width, height) {
    const renderer = new THREE.WebGLRenderer({
        antialias: true,
        alpha: true,
        powerPreference: "high-performance",
    });

    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setSize(width, height);
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFShadowMap;
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.08;

    return renderer;
}

function setupLights(scene) {
    // Enhanced lighting setup for optimal image projection
    const hemi = new THREE.HemisphereLight(0xffffff, 0xf3e6de, 1.2);
    scene.add(hemi);

    const ambient = new THREE.AmbientLight(0xffffff, 0.92);
    scene.add(ambient);

    // Main key light for primary illumination
    const key = new THREE.DirectionalLight(0xffffff, 2.6);
    key.position.set(2.8, 4.4, 3.2);
    key.castShadow = true;
    key.shadow.mapSize.width = 2048;
    key.shadow.mapSize.height = 2048;
    key.shadow.camera.near = 0.5;
    key.shadow.camera.far = 15;
    key.shadow.camera.left = -3;
    key.shadow.camera.right = 3;
    key.shadow.camera.top = 3;
    key.shadow.camera.bottom = -3;
    scene.add(key);

    // Fill light to soften shadows
    const fill = new THREE.DirectionalLight(0xffd8ea, 0.8);
    fill.position.set(-2.0, 2.4, 2.2);
    scene.add(fill);

    // Rim light to highlight edges
    const rim = new THREE.DirectionalLight(0xd6e2ff, 0.8);
    rim.position.set(-2.4, 3.0, -2.4);
    scene.add(rim);

    // Additional lights for better texture visibility
    const textureLight1 = new THREE.SpotLight(0xffffff, 1.2, 10, Math.PI / 6, 0.5, 2.0);
    textureLight1.position.set(0, 2.5, 2.5);
    textureLight1.castShadow = true;
    scene.add(textureLight1);
    
    const textureLight2 = new THREE.SpotLight(0xffffff, 0.8, 8, Math.PI / 4, 0.5, 2.0);
    textureLight2.position.set(0, 1.0, -2.5);
    scene.add(textureLight2);

    const glow1 = new THREE.PointLight(0xff6fa9, 0.7, 6);
    glow1.position.set(1.2, 1.8, 1.2);
    scene.add(glow1);

    const glow2 = new THREE.PointLight(0x8f7dff, 0.45, 6);
    glow2.position.set(-1.2, 1.6, 1.2);
    scene.add(glow2);
    
    // Store lights reference for dynamic adjustment
    scene.userData.lights = {
        key,
        fill,
        rim,
        textureLight1,
        textureLight2,
        glow1,
        glow2,
        ambient,
        hemi
    };
}

function setupStage(scene) {
    const base = new THREE.Mesh(
        new THREE.CylinderGeometry(1.0, 1.18, 0.16, 64),
        new THREE.MeshStandardMaterial({
            color: 0xf7f3ff,
            roughness: 0.4,
            metalness: 0.14,
        })
    );
    base.position.set(0, -1.05, 0);
    base.receiveShadow = true;
    scene.add(base);

    const shadow = new THREE.Mesh(
        new THREE.CircleGeometry(0.82, 64),
        new THREE.ShadowMaterial({ opacity: 0.18 })
    );
    shadow.rotation.x = -Math.PI / 2;
    shadow.position.set(0, -0.97, 0);
    shadow.receiveShadow = true;
    scene.add(shadow);

    if (props.showGrid) {
        const grid = new THREE.GridHelper(6, 30, 0xe4d7ff, 0xf6edff);
        grid.position.y = -1.0;
        scene.add(grid);
    }
}

function createFallbackModel(scene) {
    usingFallback.value = true;

    const group = new THREE.Group();

    const skin = new THREE.MeshStandardMaterial({
        color: 0xe9c6ae,
        roughness: 0.88,
        metalness: 0.02,
    });

    const hair = new THREE.MeshStandardMaterial({
        color: 0x261511,
        roughness: 0.96,
    });

    const body = new THREE.MeshStandardMaterial({
        color: 0xf1d8ca,
        roughness: 0.86,
        metalness: 0.02,
    });

    const head = new THREE.Mesh(new THREE.SphereGeometry(0.22, 48, 48), skin);
    head.position.set(0, 1.2, 0);
    head.scale.set(0.9, 1.08, 0.92);
    head.castShadow = true;
    group.add(head);

    const hairCap = new THREE.Mesh(new THREE.SphereGeometry(0.245, 48, 48), hair);
    hairCap.position.set(0, 1.28, -0.02);
    hairCap.scale.set(1.02, 1.05, 1);
    hairCap.castShadow = true;
    group.add(hairCap);

    const neck = new THREE.Mesh(new THREE.CylinderGeometry(0.055, 0.065, 0.12, 24), skin);
    neck.position.set(0, 0.97, 0);
    neck.castShadow = true;
    group.add(neck);

    const torso = new THREE.Mesh(new THREE.CapsuleGeometry(0.33, 0.85, 12, 24), body);
    torso.position.set(0, 0.35, 0);
    torso.scale.set(0.92, 1.08, 0.74);
    torso.castShadow = true;
    group.add(torso);

    const hips = new THREE.Mesh(new THREE.SphereGeometry(0.28, 32, 32), body);
    hips.position.set(0, -0.34, 0);
    hips.scale.set(1.28, 1.0, 0.86);
    hips.castShadow = true;
    group.add(hips);

    const armL = new THREE.Mesh(new THREE.CapsuleGeometry(0.08, 0.72, 8, 16), skin);
    armL.position.set(-0.42, 0.46, 0);
    armL.rotation.z = -0.2;
    armL.castShadow = true;
    group.add(armL);

    const armR = new THREE.Mesh(new THREE.CapsuleGeometry(0.08, 0.72, 8, 16), skin);
    armR.position.set(0.42, 0.46, 0);
    armR.rotation.z = 0.2;
    armR.castShadow = true;
    group.add(armR);

    const legL = new THREE.Mesh(new THREE.CapsuleGeometry(0.1, 0.95, 8, 16), skin);
    legL.position.set(-0.16, -0.98, 0);
    legL.castShadow = true;
    group.add(legL);

    const legR = new THREE.Mesh(new THREE.CapsuleGeometry(0.1, 0.95, 8, 16), skin);
    legR.position.set(0.16, -0.98, 0);
    legR.castShadow = true;
    group.add(legR);

    scene.add(group);
    modelRef.value = group;
}

function fitModel(model, scene) {
    model.traverse((child) => {
        if (child.isMesh) {
            child.castShadow = true;
            child.receiveShadow = true;

            if (child.material) {
                child.material.side = THREE.FrontSide;
                child.material.needsUpdate = true;
            }
        }
    });

    const box = new THREE.Box3().setFromObject(model);
    const size = new THREE.Vector3();
    const center = new THREE.Vector3();
    box.getSize(size);
    box.getCenter(center);

    const targetHeight = selectedModel.value?.fit?.targetHeight || 2.2;
    const scaleFactor = targetHeight / Math.max(size.y, 0.001);
    model.scale.setScalar(scaleFactor);

    const scaledBox = new THREE.Box3().setFromObject(model);
    const scaledCenter = new THREE.Vector3();
    scaledBox.getCenter(scaledCenter);

    model.position.x -= scaledCenter.x;
    model.position.z -= scaledCenter.z;
    model.position.y -= scaledBox.min.y + 1.0;

    scene.add(model);
    modelRef.value = model;
    usingFallback.value = false;
}

async function loadGLB(url, scene) {
    const loader = new GLTFLoader();

    const gltf = await new Promise((resolve, reject) => {
        loader.load(url, resolve, undefined, reject);
    });

    if (gltf.animations?.length) {
        const mixer = new THREE.AnimationMixer(gltf.scene);
        const action = mixer.clipAction(gltf.animations[0]);
        action.play();
        mixerRef.value = mixer;
    }

    fitModel(gltf.scene, scene);
}

async function loadFBX(url, scene) {
    const loader = new FBXLoader();

    const fbx = await new Promise((resolve, reject) => {
        loader.load(url, resolve, undefined, reject);
    });

    if (fbx.animations?.length) {
        const mixer = new THREE.AnimationMixer(fbx);
        const action = mixer.clipAction(fbx.animations[0]);
        action.play();
        mixerRef.value = mixer;
    }

    fitModel(fbx, scene);
}

async function loadTDS(url, scene) {
    const loader = new TDSLoader();

    const object3d = await new Promise((resolve, reject) => {
        loader.load(url, resolve, undefined, reject);
    });

    fitModel(object3d, scene);
}

async function loadSelectedModel(scene) {
    const model = selectedModel.value;

    if (model.type === "glb") {
        await loadGLB(model.url, scene);
        return;
    }

    if (model.type === "fbx") {
        await loadFBX(model.url, scene);
        return;
    }

    if (model.type === "3ds") {
        await loadTDS(model.url, scene);
        return;
    }

    throw new Error(`Unsupported model type: ${model.type}`);
}

function adjustLightingForModel() {
    if (!sceneRef.value || !sceneRef.value.userData.lights) return;
    
    const lights = sceneRef.value.userData.lights;
    const model = selectedModel.value;
    
    // Adjust lighting intensity based on model type for optimal image projection
    if (model.id === 'kangana' || model.id === 'eve') {
        // For more detailed models, increase fill light
        lights.fill.intensity = 1.0;
        lights.rim.intensity = 1.0;
    } else if (model.id === 'rihanna_3ds') {
        // For 3DS models, adjust lighting for better texture visibility
        lights.textureLight1.intensity = 1.5;
        lights.textureLight2.intensity = 1.0;
    } else if (model.id === 'fmel_fbx') {
        // For FBX models, optimize for animation
        lights.key.intensity = 2.8;
        lights.fill.intensity = 0.9;
    } else {
        // Default lighting
        lights.key.intensity = 2.6;
        lights.fill.intensity = 0.8;
    }
    
    // Adjust texture lights for better image projection
    lights.textureLight1.position.set(0, 2.0, 2.0);
    lights.textureLight2.position.set(0, 1.2, -2.0);
}

function adjustMaterialForModel() {
    // Adjust material properties based on the selected model for optimal projection
    if (!projectionMeshRef.value || !projectionMeshRef.value.material) return;
    
    const material = projectionMeshRef.value.material;
    const model = selectedModel.value;
    
    // Different models may need different material properties for best projection
    if (model.id === 'kangana') {
        material.roughness = 0.70;
        material.metalness = 0.15;
    } else if (model.id === 'eve') {
        material.roughness = 0.65;
        material.metalness = 0.1;
    } else if (model.id === 'rihanna_3ds') {
        material.roughness = 0.80;
        material.metalness = 0.05;
    } else if (model.id === 'fmel_fbx') {
        material.roughness = 0.75;
        material.metalness = 0.12;
    } else if (model.id === 'xander') {
        material.roughness = 0.72;
        material.metalness = 0.08;
    } else {
        // Default values
        material.roughness = 0.75;
        material.metalness = 0.1;
    }
    
    material.needsUpdate = true;
}

function removeProjection() {
    if (projectionMeshRef.value) {
        projectionMeshRef.value.geometry?.dispose?.();
        disposeMaterial(projectionMeshRef.value.material);
        projectionMeshRef.value = null;
    }

    if (projectionGroupRef.value && modelRef.value && projectionGroupRef.value.parent === modelRef.value) {
        modelRef.value.remove(projectionGroupRef.value);
    }

    projectionGroupRef.value = null;

    if (projectionTextureRef.value) {
        projectionTextureRef.value.dispose?.();
        projectionTextureRef.value = null;
    }
}

function inferDefaultProjection(texture) {
    const aspect =
        texture?.image?.width && texture?.image?.height
            ? texture.image.width / texture.image.height
            : 0.8;

    const base = selectedModel.value?.fit?.projection || {
        x: 0,
        y: 0.42,
        z: 0.18,
        scaleX: 0.82,
        scaleY: 1.02,
        rotateX: 4,
        rotateY: 0,
        rotateZ: 0,
        curve: 0.1,
        opacity: 1,
    };

    // Professional automatic fitting based on image aspect ratio and model type
    const modelId = selectedModel.value?.id;
    
    // Custom positioning for different design types
    if (aspect > 1.2) {
        // Wide images (landscape)
        projection.value = {
            ...base,
            scaleX: base.scaleX * 1.15,
            scaleY: base.scaleY * 0.95,
            y: base.y + 0.08,
            curve: base.curve * 1.1,
        };
    } else if (aspect < 0.75) {
        // Tall images (portrait)
        projection.value = {
            ...base,
            scaleX: base.scaleX * 0.88,
            scaleY: base.scaleY * 1.12,
            y: base.y + 0.05,
            curve: base.curve * 0.95,
        };
    } else {
        // Square images
        projection.value = {
            ...base,
            scaleX: base.scaleX * 1.05,
            scaleY: base.scaleY * 1.05,
            y: base.y + 0.06,
            curve: base.curve,
        };
    }

    // Model-specific optimizations for professional fitting
    if (modelId === 'kangana' || modelId === 'eve') {
        // Female models - optimize for fitted clothing
        projection.value.y += 0.02;
        projection.value.curve += 0.02;
    } else if (modelId === 'xander') {
        // Male model - broader chest
        projection.value.scaleX += 0.08;
        projection.value.y += 0.03;
    } else if (modelId === 'fmel_fbx') {
        // FBX model - adjusted proportions
        projection.value.scaleY += 0.03;
        projection.value.curve += 0.015;
    }
}

function createCurvedPlane(width, height, curveStrength) {
    // Enhanced curved plane creation for more realistic clothing projection
    const segmentsW = 40;
    const segmentsH = 40;
    const geometry = new THREE.PlaneGeometry(width, height, segmentsW, segmentsH);
    const pos = geometry.attributes.position;

    for (let i = 0; i < pos.count; i++) {
        const x = pos.getX(i);
        const y = pos.getY(i);
        
        // Normalize coordinates to -1 to 1 range
        const nx = x / (width / 2);
        const ny = y / (height / 2);
        
        // More sophisticated 3D curvature simulation
        const chestCurve = Math.cos(nx * Math.PI * 0.5) * Math.cos(ny * Math.PI * 0.25) * curveStrength * 0.7;
        const bodyContour = Math.pow(1 - Math.abs(nx), 1.5) * curveStrength * 0.3;
        const verticalCurve = Math.sin(ny * Math.PI * 0.5) * curveStrength * 0.2;
        
        pos.setZ(i, chestCurve + bodyContour + verticalCurve);
    }

    pos.needsUpdate = true;
    geometry.computeVertexNormals();

    return geometry;
}

function updateMaterialProperties() {
    // Update material properties based on current projection settings
    if (projectionMeshRef.value && projectionMeshRef.value.material) {
        const material = projectionMeshRef.value.material;
        material.opacity = Number(projection.value.opacity);
        material.needsUpdate = true;
    }
}

function applyProjectionTransform() {
    if (!projectionMeshRef.value) return;

    projectionMeshRef.value.visible = projectionVisible.value;

    projectionMeshRef.value.position.set(
        Number(projection.value.x),
        Number(projection.value.y),
        Number(projection.value.z)
    );

    projectionMeshRef.value.rotation.set(
        degToRad(projection.value.rotateX),
        degToRad(projection.value.rotateY),
        degToRad(projection.value.rotateZ)
    );

    projectionMeshRef.value.scale.set(
        Number(projection.value.scaleX),
        Number(projection.value.scaleY),
        1
    );

    if (projectionMeshRef.value.material) {
        projectionMeshRef.value.material.opacity = projectionVisible.value
            ? Number(projection.value.opacity)
            : 0;
        projectionMeshRef.value.material.transparent = true;
        projectionMeshRef.value.material.needsUpdate = true;
    }
    
    // Update material properties based on current settings
    updateMaterialProperties();
}

function rebuildProjectionGeometry() {
    if (!projectionMeshRef.value || !projectionTextureRef.value) return;

    const texture = projectionTextureRef.value;
    const aspect =
        texture?.image?.width && texture?.image?.height
            ? texture.image.width / texture.image.height
            : 0.8;

    const width = aspect >= 1 ? 0.95 : 0.8;
    const height = aspect >= 1 ? 0.82 : 1.02;

    const newGeometry = createCurvedPlane(width, height, Number(projection.value.curve));

    projectionMeshRef.value.geometry.dispose?.();
    projectionMeshRef.value.geometry = newGeometry;
}

function preprocessImageUrl(imageUrl) {
    // Process image URL to ensure optimal loading for 3D projection
    if (!imageUrl) return null;
    
    // Ensure the URL is properly formatted
    if (imageUrl.startsWith('data:')) {
        // Data URLs are already processed
        return imageUrl;
    }
    
    // Convert relative URLs to absolute if needed
    if (imageUrl.startsWith('/')) {
        return window.location.origin + imageUrl;
    }
    
    // Add optimization parameters for better quality
    try {
        const url = new URL(imageUrl, window.location.origin);
        // Add quality parameters if not present
        if (!url.searchParams.has('quality')) {
            url.searchParams.append('quality', 'high');
        }
        return url.toString();
    } catch (e) {
        // If URL parsing fails, return original
        return imageUrl;
    }
}

async function removeImageBackground(imageUrl) {
    // Professional background removal using canvas processing
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        
        img.onload = () => {
            try {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                canvas.width = img.width;
                canvas.height = img.height;
                
                // Draw original image
                ctx.drawImage(img, 0, 0);
                
                // Get image data
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const data = imageData.data;
                
                // Advanced background removal algorithm
                // Analyze multiple areas to determine background
                const backgroundColors = detectBackgroundColors(data, canvas.width, canvas.height);
                
                // Remove pixels matching background colors
                for (let i = 0; i < data.length; i += 4) {
                    const r = data[i];
                    const g = data[i + 1];
                    const b = data[i + 2];
                    
                    // Check if pixel is similar to any detected background color
                    let isBackground = false;
                    for (const bgColor of backgroundColors) {
                        const diff = Math.sqrt(
                            Math.pow(r - bgColor.r, 2) + 
                            Math.pow(g - bgColor.g, 2) + 
                            Math.pow(b - bgColor.b, 2)
                        );
                        
                        // Euclidean distance threshold
                        if (diff < bgColor.threshold) {
                            isBackground = true;
                            break;
                        }
                    }
                    
                    if (isBackground) {
                        data[i + 3] = 0; // Set alpha to 0
                    }
                }
                
                ctx.putImageData(imageData, 0, 0);
                
                // Convert to data URL
                const processedUrl = canvas.toDataURL('image/png');
                resolve(processedUrl);
            } catch (error) {
                console.error('Background removal failed:', error);
                resolve(imageUrl); // Fallback to original
            }
        };
        
        img.onerror = () => {
            console.error('Failed to load image for background removal');
            resolve(imageUrl); // Fallback to original
        };
        
        img.src = imageUrl;
    });
}

function detectBackgroundColors(data, width, height) {
    // Advanced background color detection
    // Sample from corners and edges to determine background
    const samples = [];
    
    // Corner samples
    samples.push(getPixelColor(data, 0, 0, width));
    samples.push(getPixelColor(data, width - 1, 0, width));
    samples.push(getPixelColor(data, 0, height - 1, width));
    samples.push(getPixelColor(data, width - 1, height - 1, width));
    
    // Edge samples
    samples.push(getPixelColor(data, Math.floor(width / 2), 0, width));
    samples.push(getPixelColor(data, Math.floor(width / 2), height - 1, width));
    samples.push(getPixelColor(data, 0, Math.floor(height / 2), width));
    samples.push(getPixelColor(data, width - 1, Math.floor(height / 2), width));
    
    // Group similar colors
    const groupedColors = [];
    const tolerance = 20; // Color similarity tolerance
    
    for (const sample of samples) {
        let foundGroup = false;
        
        for (const group of groupedColors) {
            const dist = Math.sqrt(
                Math.pow(sample.r - group.color.r, 2) +
                Math.pow(sample.g - group.color.g, 2) +
                Math.pow(sample.b - group.color.b, 2)
            );
            
            if (dist <= tolerance) {
                group.count++;
                group.color = {
                    r: Math.round((group.color.r + sample.r) / 2),
                    g: Math.round((group.color.g + sample.g) / 2),
                    b: Math.round((group.color.b + sample.b) / 2)
                };
                foundGroup = true;
                break;
            }
        }
        
        if (!foundGroup) {
            groupedColors.push({ color: sample, count: 1 });
        }
    }
    
    // Return the most common background colors with thresholds
    groupedColors.sort((a, b) => b.count - a.count);
    
    // Return top 2 most common background colors with adaptive thresholds
    return groupedColors.slice(0, 2).map(group => ({
        r: group.color.r,
        g: group.color.g,
        b: group.color.b,
        threshold: 40 // Base threshold
    }));
}

function getPixelColor(data, x, y, width) {
    const index = (y * width + x) * 4;
    return {
        r: data[index],
        g: data[index + 1],
        b: data[index + 2]
    };
}

function loadProductTexture(imageUrl, resetPlacement = false) {
    // Enhanced texture loading with preprocessing and background removal
    if (!imageUrl || !modelRef.value || !rendererRef.value) return;
    
    // Preprocess the image URL to ensure it loads correctly
    const processedImageUrl = preprocessImageUrl(imageUrl);
    
    if (!processedImageUrl) {
        console.warn('Invalid image URL for texture loading');
        return;
    }
    
    const loader = new THREE.TextureLoader();
    
    // Set texture loading options for better quality
    loader.crossOrigin = 'anonymous';
    
    loader.load(
        processedImageUrl,
        (texture) => {
            texture.colorSpace = THREE.SRGBColorSpace;
            texture.anisotropy = rendererRef.value.capabilities.getMaxAnisotropy();
            texture.minFilter = THREE.LinearMipmapLinearFilter;
            texture.magFilter = THREE.LinearFilter;
            texture.generateMipmaps = true;

            removeProjection();
            projectionTextureRef.value = texture;

            if (resetPlacement) {
                inferDefaultProjection(texture);
            }

            const aspect =
                texture?.image?.width && texture?.image?.height
                    ? texture.image.width / texture.image.height
                    : 0.8;

            const baseWidth = aspect >= 1 ? 0.95 : 0.8;
            const baseHeight = aspect >= 1 ? 0.82 : 1.02;

            const geometry = createCurvedPlane(baseWidth, baseHeight, Number(projection.value.curve));

            // Enhanced material properties for realistic clothing appearance with transparency
            const material = new THREE.MeshStandardMaterial({
                map: texture,
                transparent: true,
                alphaTest: 0.01,
                side: THREE.DoubleSide,
                roughness: 0.75,
                metalness: 0.1,
                bumpScale: 0.05,
                envMapIntensity: 0.5,
                opacity: Number(projection.value.opacity),
                depthWrite: false,
                blending: THREE.NormalBlending,
            });

            const plane = new THREE.Mesh(geometry, material);

            // Add subtle shine effect
            const shine = new THREE.Mesh(
                new THREE.PlaneGeometry(baseWidth * 0.9, baseHeight * 0.92, 12, 12),
                new THREE.MeshBasicMaterial({
                    color: 0xffffff,
                    transparent: true,
                    opacity: 0.04,
                    blending: THREE.AdditiveBlending,
                    depthWrite: false,
                })
            );
            shine.position.set(0.03, 0.01, 0.012);
            plane.add(shine);

            const group = new THREE.Group();
            group.add(plane);

            modelRef.value.add(group);

            projectionGroupRef.value = group;
            projectionMeshRef.value = plane;

            applyProjectionTransform();
        },
        undefined,
        () => {
            hasError.value = true;
            errorMessage.value = "تعذر تحميل صورة المنتج أو التصميم.";
        }
    );
}

async function loadProductTextureWithBackgroundRemoval(imageUrl, resetPlacement = false) {
    // Load product texture with automatic background removal
    if (!imageUrl || !modelRef.value || !rendererRef.value) return;
    
    isLoading.value = true;
    
    try {
        // Remove background
        const processedUrl = await removeImageBackground(imageUrl);
        
        // Load the processed texture
        loadProductTexture(processedUrl, resetPlacement);
        
        isBackgroundRemovalActive.value = true;
    } catch (error) {
        console.error('Background removal failed:', error);
        // Fallback to normal loading
        loadProductTexture(imageUrl, resetPlacement);
        isBackgroundRemovalActive.value = false;
    } finally {
        isLoading.value = false;
    }
}

function toggleBackgroundRemoval() {
    // Toggle background removal on/off while preserving current projection settings
    if (activeImage.value) {
        // Store current projection settings before reloading
        const currentProjection = { ...projection.value };
        
        if (isBackgroundRemovalActive.value) {
            // Reload without background removal
            loadProductTexture(activeImage.value, false); // Don't reset placement
            isBackgroundRemovalActive.value = false;
            
            // Restore projection settings after texture loads
            setTimeout(() => {
                projection.value = currentProjection;
                applyProjectionTransform();
            }, 100);
        } else {
            // Reload with background removal
            loadProductTextureWithBackgroundRemoval(activeImage.value, false); // Don't reset placement
            
            // Restore projection settings after texture loads
            setTimeout(() => {
                projection.value = currentProjection;
                applyProjectionTransform();
            }, 100);
        }
    }
}

function toggleColorMode() {
    // Toggle color mode on/off
    isColorModeActive.value = !isColorModeActive.value;
    
    // If turning on color mode, make sure eraser is off
    if (isColorModeActive.value) {
        isEraserActive.value = false;
    }
}

function toggleEraser() {
    // Toggle eraser tool on/off
    isEraserActive.value = !isEraserActive.value;
    
    // If turning on eraser, make sure color mode is off
    if (isEraserActive.value) {
        isColorModeActive.value = false;
    }
}

function applyColorToProjection() {
    // Apply the selected color to the projection with the specified intensity
    if (!projectionMeshRef.value || !projectionMeshRef.value.material) return;
    
    const material = projectionMeshRef.value.material;
    
    // Create a colored texture to blend with the original image
    const canvas = document.createElement('canvas');
    canvas.width = 256;
    canvas.height = 256;
    const ctx = canvas.getContext('2d');
    
    // Fill with selected color
    ctx.fillStyle = selectedColor.value;
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Create texture from canvas
    const colorTexture = new THREE.CanvasTexture(canvas);
    
    // Update material to use the color blend
    if (isColorModeActive.value) {
        // Blend original texture with selected color
        material.emissive = new THREE.Color(selectedColor.value);
        material.emissiveIntensity = colorIntensity.value;
    } else {
        // Reset to original appearance
        material.emissive = new THREE.Color(0x000000);
        material.emissiveIntensity = 0;
    }
    
    material.needsUpdate = true;
}

function updateEraserEffect() {
    // Update the eraser effect on the projection
    if (!projectionMeshRef.value || !projectionMeshRef.value.material) return;
    
    const material = projectionMeshRef.value.material;
    
    if (isEraserActive.value) {
        // Reduce opacity to simulate erasing
        material.opacity = Math.max(0.1, material.opacity - 0.3);
    } else {
        // Restore original opacity
        material.opacity = projection.value.opacity;
    }
    
    material.needsUpdate = true;
}

function setColor(color) {
    // Set the selected color
    selectedColor.value = color;
    
    // Apply the color if color mode is active
    if (isColorModeActive.value) {
        applyColorToProjection();
    }
}

function setEraserSize(size) {
    // Set the eraser size
    eraserSize.value = size;
}

function setColorIntensity(intensity) {
    // Set the color intensity
    colorIntensity.value = intensity;
    
    // Apply the color if color mode is active
    if (isColorModeActive.value) {
        applyColorToProjection();
    }
}

function resetColorAndEraser() {
    // Reset color and eraser to defaults
    isColorModeActive.value = false;
    isEraserActive.value = false;
    selectedColor.value = '#ffffff';
    eraserSize.value = 20;
    colorIntensity.value = 0.7;
    
    // Reset material properties
    if (projectionMeshRef.value && projectionMeshRef.value.material) {
        const material = projectionMeshRef.value.material;
        material.emissive = new THREE.Color(0x000000);
        material.emissiveIntensity = 0;
        material.opacity = projection.value.opacity;
        material.needsUpdate = true;
    }
}

function handleResize() {
    if (!containerRef.value || !rendererRef.value || !cameraRef.value) return;

    const width = containerRef.value.clientWidth || 600;
    const height = getViewerHeight();

    cameraRef.value.aspect = width / height;
    cameraRef.value.updateProjectionMatrix();
    rendererRef.value.setSize(width, height);
}

function animate() {
    animationFrameRef.value = requestAnimationFrame(animate);

    timer.update();
    const delta = timer.getDelta();

    if (mixerRef.value) {
        mixerRef.value.update(delta);
    }

    if (usingFallback.value && modelRef.value) {
        modelRef.value.rotation.y += delta * 0.22;
    }

    if (controlsRef.value) {
        controlsRef.value.update();
    }

    if (rendererRef.value && sceneRef.value && cameraRef.value) {
        rendererRef.value.render(sceneRef.value, cameraRef.value);
    }
}

async function initViewer() {
    if (!containerRef.value) return;

    isLoading.value = true;
    hasError.value = false;
    errorMessage.value = "";
    usingFallback.value = false;

    cleanupScene();
    await nextTick();

    const width = containerRef.value.clientWidth || 600;
    const height = getViewerHeight();

    const scene = new THREE.Scene();
    sceneRef.value = scene;

    const camera = new THREE.PerspectiveCamera(35, width / height, 0.1, 100);
    const cameraZ = selectedModel.value?.fit?.cameraZ || 2.9;
    camera.position.set(0, 1.42, cameraZ);
    cameraRef.value = camera;

    const renderer = createRenderer(width, height);
    rendererRef.value = renderer;

    containerRef.value.innerHTML = "";
    containerRef.value.appendChild(renderer.domElement);

    setupLights(scene);
    setupStage(scene);

    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.enablePan = false;
    controls.autoRotate = props.autoRotate;
    controls.autoRotateSpeed = 0.9;
    controls.minDistance = 2.0;
    controls.maxDistance = 5.2;
    controls.minPolarAngle = Math.PI / 2.8;
    controls.maxPolarAngle = Math.PI / 1.76;
    controls.target.set(0, selectedModel.value?.fit?.targetY || 0.82, 0);
    controlsRef.value = controls;

    try {
        await loadSelectedModel(scene);
        // Adjust lighting for optimal image projection based on the selected model
        adjustLightingForModel();
        // Also adjust material properties based on the selected model
        adjustMaterialForModel();
    } catch (error) {
        console.error("Failed to load selected model, using fallback:", error);
        hasError.value = true;
        errorMessage.value = "تعذر تحميل المجسم المحدد، تم تشغيل نموذج احتياطي.";
        createFallbackModel(scene);
        // Even with fallback model, adjust lighting and materials
        adjustLightingForModel();
        adjustMaterialForModel();
    }

    if (activeImage.value) {
        loadProductTexture(activeImage.value, true);
    }

    timer.reset();
    animate();
    isLoading.value = false;
}

function resetView() {
    if (!cameraRef.value || !controlsRef.value) return;

    const cameraZ = selectedModel.value?.fit?.cameraZ || 2.9;
    cameraRef.value.position.set(0, 1.42, cameraZ);
    controlsRef.value.target.set(0, selectedModel.value?.fit?.targetY || 0.82, 0);
    controlsRef.value.update();
}

function toggleAutoRotate() {
    if (controlsRef.value) {
        controlsRef.value.autoRotate = !controlsRef.value.autoRotate;
    }
}

function setAutoRotate(enabled) {
    if (controlsRef.value) {
        controlsRef.value.autoRotate = enabled;
    }
}

function rotateDesignLeft() {
    projection.value.rotateZ -= 5;
}

function rotateDesignRight() {
    projection.value.rotateZ += 5;
}

function scaleDesignUp() {
    projection.value.scaleX += 0.04;
    projection.value.scaleY += 0.04;
}

function scaleDesignDown() {
    projection.value.scaleX = Math.max(0.2, Number(projection.value.scaleX) - 0.04);
    projection.value.scaleY = Math.max(0.2, Number(projection.value.scaleY) - 0.04);
}

function moveDesignUp() {
    projection.value.y += 0.02;
}

function moveDesignDown() {
    projection.value.y -= 0.02;
}

function moveDesignLeft() {
    projection.value.x -= 0.02;
}

function moveDesignRight() {
    projection.value.x += 0.02;
}

function resetDesignTransform() {
    if (!projectionTextureRef.value) return;
    inferDefaultProjection(projectionTextureRef.value);
}

function selectImage(img) {
    activeImage.value = img;
    // Always use background removal when selecting new image
    loadProductTextureWithBackgroundRemoval(img, true);
}

function selectModel(modelId) {
    if (selectedModelId.value === modelId) return;
    selectedModelId.value = modelId;
}

function selectPrevModel() {
    const index = selectedModelIndex.value;
    const prevIndex = index <= 0 ? MODEL_OPTIONS.length - 1 : index - 1;
    selectedModelId.value = MODEL_OPTIONS[prevIndex].id;
}

function selectNextModel() {
    const index = selectedModelIndex.value;
    const nextIndex = index >= MODEL_OPTIONS.length - 1 ? 0 : index + 1;
    selectedModelId.value = MODEL_OPTIONS[nextIndex].id;
}

function closeControlsPanel() {
    showControlsPanel.value = false;
}

function openControlsPanel() {
    showControlsPanel.value = true;
}

function toggleProjectionVisibility() {
    projectionVisible.value = !projectionVisible.value;
    applyProjectionTransform();
}

function toggleSection(sectionName) {
    // Toggle the visibility of a specific section
    expandedSections.value[sectionName] = !expandedSections.value[sectionName];
}

function toggleSidebar() {
    showSidebar.value = !showSidebar.value;
    // Close other panels when opening sidebar
    showImageEditorModal.value = false;
    showPropertyOverlays.value = false;
    showControlsPanel.value = false;
}

function toggleOrderForm() {
    showOrderForm.value = !showOrderForm.value;
}

function saveDesign() {
    // Prepare design data to send to backend
    const designData = {
        name: prompt('أدخل اسم التصميم', 'تصميم جديد') || 'تصميم جديد',
        product_image: activeImage.value,
        projection_settings: JSON.stringify(projection.value),
        model_type: selectedModelId.value,
        is_background_removed: isBackgroundRemovalActive.value,
        order_details: JSON.stringify(orderDetails.value),
        // Additional metadata
        created_at: new Date().toISOString(),
    };
    
    // In a real implementation, this would make an API call to save the design
    // For now, we'll simulate the save process
    console.log('Saving design:', designData);
    
    // Simulate API call
    fetch('/api/user-designs', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify(designData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تم حفظ التصميم بنجاح!');
        } else {
            alert('فشل حفظ التصميم.');
        }
    })
    .catch(error => {
        console.error('Error saving design:', error);
        alert('حدث خطأ أثناء حفظ التصميم.');
    });
}

function exportAsOrder() {
    // Calculate estimated price based on complexity
    const basePrice = 29.99;
    const complexityMultiplier = isBackgroundRemovalActive.value ? 1.2 : 1.0;
    const designPrice = basePrice * complexityMultiplier;
    
    orderDetails.value.price = designPrice;
    
    // In a real implementation, this would export the design as an order
    console.log('Design exported as order:', {
        projection: projection.value,
        image: activeImage.value,
        model: selectedModelId.value,
        orderDetails: orderDetails.value
    });
    
    alert('تم تصدير الطلب بنجاح!');
}

function openImageEditorModal() {
    showImageEditorModal.value = true;
}

function closeImageEditorModal() {
    showImageEditorModal.value = false;
}

defineExpose({
    resetView,
    toggleAutoRotate,
    setAutoRotate,
    rotateDesignLeft,
    rotateDesignRight,
    scaleDesignUp,
    scaleDesignDown,
    moveDesignUp,
    moveDesignDown,
    moveDesignLeft,
    moveDesignRight,
    resetDesignTransform,
    openControlsPanel,
    closeControlsPanel,
    toggleBackgroundRemoval,
    loadProductTextureWithBackgroundRemoval,
    togglePropertyOverlays: () => {
        showPropertyOverlays.value = !showPropertyOverlays.value;
    },
    openImageEditorModal,
    closeImageEditorModal,
    toggleSidebar,
    toggleOrderForm,
    saveDesign,
    exportAsOrder,
    toggleColorMode,
    toggleEraser,
    applyColorToProjection,
    updateEraserEffect,
    setColor,
    setEraserSize,
    setColorIntensity,
    resetColorAndEraser,
});

onMounted(async () => {
    applyModelPreset();
    await initViewer();

    const ro = new ResizeObserver(() => {
        handleResize();
    });

    resizeObserverRef.value = ro;

    if (containerRef.value) {
        ro.observe(containerRef.value);
    }
});

watch(
    () => props.productImage,
    (newValue) => {
        if (newValue) {
            activeImage.value = newValue;
            // Use background removal for product image
            loadProductTextureWithBackgroundRemoval(newValue, true);
        }
    }
);

watch(
    () => selectedModelId.value,
    async () => {
        applyModelPreset();
        await initViewer();
    }
);

watch(
    projection,
    () => {
        applyProjectionTransform();
        rebuildProjectionGeometry();
        applyProjectionTransform();
    },
    { deep: true }
);

watch(
    () => projectionVisible.value,
    () => {
        applyProjectionTransform();
    }
);

watch(
    () => isColorModeActive.value,
    () => {
        applyColorToProjection();
    }
);

watch(
    () => isEraserActive.value,
    () => {
        updateEraserEffect();
    }
);

watch(
    () => selectedColor.value,
    () => {
        if (isColorModeActive.value) {
            applyColorToProjection();
        }
    }
);

watch(
    () => colorIntensity.value,
    () => {
        if (isColorModeActive.value) {
            applyColorToProjection();
        }
    }
);

onBeforeUnmount(() => {
    if (resizeObserverRef.value && containerRef.value) {
        resizeObserverRef.value.unobserve(containerRef.value);
    }

    resizeObserverRef.value = null;
    cleanupScene();
});
</script>

<template>
    <div class="relative w-full rounded-[30px] overflow-hidden border border-white/70 shadow-[0_25px_60px_rgba(28,32,61,0.14)] bg-[radial-gradient(circle_at_top_right,rgba(109,93,252,0.16),transparent_24%),radial-gradient(circle_at_bottom_left,rgba(255,79,163,0.12),transparent_24%),linear-gradient(180deg,#f8f9ff_0%,#eef2ff_100%)]">
        <div
            ref="containerRef"
            class="w-full"
            :style="{ height: `${height}px` }"
        ></div>

        <div
            v-if="isLoading"
            class="absolute inset-0 bg-white/55 backdrop-blur-md flex flex-col items-center justify-center gap-4"
        >
            <div class="w-12 h-12 rounded-full border-4 border-brand-gold/20 border-t-brand-gold animate-spin"></div>
            <p class="text-brand-primary font-semibold">
                {{ $t('viewer.loading_model') }}
            </p>
        </div>

        <div
            v-if="hasError && errorMessage"
            class="absolute left-4 right-4 bottom-4 px-4 py-3 rounded-2xl bg-black/75 text-white text-sm shadow-xl"
        >
            {{ errorMessage }}
        </div>

        <div class="absolute top-4 right-4 px-4 py-2 rounded-full bg-gradient-to-r from-brand-primary to-brand-accent text-white text-xs font-bold shadow-lg">
            {{ usingFallback ? $t('viewer.fallback_badge') : selectedModel.type.toUpperCase() }}
        </div>

        <div class="absolute top-4 left-4 px-4 py-2 rounded-full bg-white/85 backdrop-blur-md text-brand-primary text-xs font-bold border border-brand-gold/15 shadow">
            {{ productName }}
        </div>
        
        <!-- Real-time Projection Info -->
        <div class="absolute top-12 left-4 px-3 py-1.5 rounded-full bg-black/30 backdrop-blur-sm text-white text-xs font-medium flex items-center gap-2">
            <i class="fas fa-image"></i>
            <span>{{ $t('viewer.projection_quality', 'جودة التصميم') }}: 
                <span class="font-bold">{{ Math.round(projection.opacity * 100) }}%</span>
            </span>
            <span class="mx-1">|</span>
            <i class="fas" :class="controlsRef?.autoRotate ? 'fa-spinner fa-spin' : 'fa-pause-circle'"></i>
            <span>{{ controlsRef?.autoRotate ? $t('viewer.rotating') : $t('viewer.paused') }}</span>
            <span class="mx-1" v-if="isBackgroundRemovalActive">|</span>
            <i class="fas fa-eraser text-green-400" v-if="isBackgroundRemovalActive"></i>
            <span class="text-green-400 font-semibold" v-if="isBackgroundRemovalActive">{{ $t('viewer.bg_removed', 'بدون خلفية') }}</span>
        </div>

        <!-- Properties Icon Button -->
        <button
            @click="showPropertyOverlays = !showPropertyOverlays"
            class="absolute top-12 right-4 w-10 h-10 rounded-full bg-white/90 backdrop-blur-md border border-brand-gold/20 text-brand-primary font-bold shadow-lg hover:bg-gradient-to-r hover:from-brand-primary hover:to-brand-accent hover:text-white transition-all flex items-center justify-center"
            :title="$t('viewer.toggle_properties', 'خصائص الصورة')"
        >
            <i class="fas fa-cog" :class="showPropertyOverlays ? 'fa-spin' : ''"></i>
        </button>

        <!-- Quick Tools Button -->
        <button
            @click="toggleSidebar"
            class="absolute top-[72px] right-4 w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-bold shadow-xl hover:shadow-2xl hover:-translate-y-0.5 transition-all flex items-center justify-center"
            :title="$t('viewer.tools', 'الأدوات')"
        >
            <i class="fas fa-sliders-h"></i>
        </button>

        <!-- top action bar - With sidebar toggle -->
        <div class="absolute top-16 left-4 right-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <button
                    @click="selectPrevModel"
                    class="px-4 py-2 rounded-2xl bg-white/90 backdrop-blur-md border border-white shadow text-brand-primary font-bold text-sm hover:-translate-y-0.5 transition-all flex items-center gap-2"
                    :title="$t('viewer.previous_model')"
                >
                    <i class="fas fa-chevron-left mr-1"></i>
                    {{ $t('viewer.prev') }}
                </button>

                <button
                    @click="selectNextModel"
                    class="px-4 py-2 rounded-2xl bg-white/90 backdrop-blur-md border border-white shadow text-brand-primary font-bold text-sm hover:-translate-y-0.5 transition-all flex items-center gap-2"
                    :title="$t('viewer.next_model')"
                >
                    {{ $t('viewer.next') }}
                    <i class="fas fa-chevron-right ml-1"></i>
                </button>
            </div>

            <div class="flex items-center gap-2">
                <button
                    @click="setAutoRotate(!controlsRef?.autoRotate)"
                    class="px-4 py-2 rounded-2xl font-bold text-sm shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2"
                    :class="controlsRef?.autoRotate 
                        ? 'bg-gradient-to-r from-brand-primary to-brand-accent text-white' 
                        : 'bg-white/90 backdrop-blur-md border border-white text-brand-primary'"
                    :title="controlsRef?.autoRotate ? $t('viewer.pause_rotation') : $t('viewer.start_rotation')"
                >
                    <i class="fas" :class="controlsRef?.autoRotate ? 'fa-pause' : 'fa-play'"></i>
                    <span class="hidden sm:inline">{{ controlsRef?.autoRotate ? $t('viewer.pause') : $t('viewer.rotate') }}</span>
                </button>
                
                <button
                    @click="toggleSidebar"
                    class="px-4 py-2 rounded-2xl bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-bold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-2"
                    :title="$t('viewer.customize_design')"
                >
                    <i class="fas fa-sliders-h"></i>
                    <span class="hidden sm:inline">{{ $t('viewer.tools') }}</span>
                </button>
            </div>
        </div>

        <!-- professional model switcher -->
        <div class="absolute left-4 right-4 top-[180px]">
            <div class="mx-auto max-w-[760px] rounded-3xl bg-white/70 backdrop-blur-xl border border-white/70 shadow-xl px-3 py-3">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
                    <button
                        v-for="model in MODEL_OPTIONS"
                        :key="model.id"
                        @click="selectModel(model.id)"
                        class="group rounded-2xl px-3 py-3 text-center transition-all border font-bold text-sm"
                        :class="selectedModelId === model.id
                            ? 'bg-gradient-to-r from-brand-primary to-brand-accent text-white border-transparent shadow-lg'
                            : 'bg-white/80 text-brand-primary border-brand-gold/10 hover:border-brand-gold/30 hover:-translate-y-0.5'"
                    >
                        <div class="truncate">{{ $t(model.shortKey) }}</div>
                        <div
                            class="mt-1 text-[11px]"
                            :class="selectedModelId === model.id ? 'text-white/80' : 'text-brand-secondary'"
                        >
                            {{ model.type.toUpperCase() }}
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Left Sidebar Panel -->
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform -translate-x-full opacity-0"
            enter-to-class="transform translate-x-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-x-0 opacity-100"
            leave-to-class="transform -translate-x-full opacity-0"
        >
            <div
                v-if="showSidebar"
                class="fixed top-0 left-0 h-full w-[320px] bg-white shadow-2xl z-50 flex flex-col"
                @click.stop
            >
                <!-- Sidebar Header -->
                <div class="bg-gradient-to-r from-brand-primary to-brand-accent text-white p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-sliders-h text-xl"></i>
                        <h2 class="text-lg font-bold">{{ $t('viewer.design_tools', 'أدوات التصميم') }}</h2>
                    </div>
                    <button
                        @click="toggleSidebar"
                        class="text-white hover:text-gray-200 text-xl font-bold"
                    >
                        ×
                    </button>
                </div>

                <!-- Sidebar Content -->
                <div class="flex-1 overflow-y-auto p-4 space-y-6">
                    <!-- Quick Actions Section -->
                    <div class="bg-blue-50 rounded-2xl p-4 border border-blue-200">
                        <h3 class="text-sm font-bold text-brand-primary mb-3 flex items-center gap-2">
                            <i class="fas fa-bolt text-blue-600"></i>
                            {{ $t('viewer.quick_actions', 'إجراءات سريعة') }}
                        </h3>
                        <div class="grid grid-cols-2 gap-2">
                            <button
                                @click="toggleProjectionVisibility"
                                class="px-3 py-2 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all flex flex-col items-center gap-1"
                                :class="projectionVisible 
                                    ? 'bg-white text-brand-primary border border-blue-200' 
                                    : 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white'"
                            >
                                <i class="fas" :class="projectionVisible ? 'fa-eye' : 'fa-eye-slash'"></i>
                                <span class="text-xs">{{ projectionVisible ? $t('viewer.hide') : $t('viewer.show') }}</span>
                            </button>

                            <button
                                @click="setAutoRotate(!controlsRef?.autoRotate)"
                                class="px-3 py-2 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all flex flex-col items-center gap-1"
                                :class="controlsRef?.autoRotate 
                                    ? 'bg-gradient-to-r from-brand-primary to-brand-accent text-white' 
                                    : 'bg-white text-brand-primary border border-brand-gold/20'"
                                :title="controlsRef?.autoRotate ? $t('viewer.pause_rotation') : $t('viewer.start_rotation')"
                            >
                                <i class="fas" :class="controlsRef?.autoRotate ? 'fa-pause' : 'fa-play'"></i>
                                <span class="text-xs">{{ controlsRef?.autoRotate ? $t('viewer.pause') : $t('viewer.rotate') }}</span>
                            </button>

                            <button
                                @click="resetDesignTransform"
                                class="px-3 py-2 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all flex flex-col items-center gap-1 bg-white text-brand-primary border border-brand-gold/20"
                            >
                                <i class="fas fa-undo text-base"></i>
                                <span class="text-xs">{{ $t('viewer.reset_design', 'إعادة ضبط') }}</span>
                            </button>

                            <button
                                @click="resetView"
                                class="px-3 py-2 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all flex flex-col items-center gap-1 bg-white text-brand-primary border border-brand-gold/20"
                            >
                                <i class="fas fa-camera text-base"></i>
                                <span class="text-xs">{{ $t('viewer.reset_camera', 'الكاميرا') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Image Selection Section -->
                    <div class="bg-green-50 rounded-2xl p-4 border border-green-200">
                        <h3 class="text-sm font-bold text-brand-primary mb-3 flex items-center gap-2">
                            <i class="fas fa-images text-green-600"></i>
                            {{ $t('viewer.select_design_image', 'اختر صورة التصميم') }}
                        </h3>
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                v-for="(img, index) in availableImages"
                                :key="`${img}-${index}`"
                                @click="selectImage(img)"
                                class="aspect-square overflow-hidden rounded-xl border-2 bg-white transition-all hover:shadow-lg"
                                :class="activeImage === img ? 'border-brand-gold ring-4 ring-brand-gold/10' : 'border-brand-gold/10 hover:border-brand-gold/40'"
                            >
                                <img :src="img" :alt="`design-${index}`" class="w-full h-full object-cover" />
                            </button>
                        </div>
                    </div>

                    <!-- Background Removal -->
                    <div class="bg-purple-50 rounded-2xl p-4 border border-purple-200">
                        <h3 class="text-sm font-bold text-brand-primary mb-3 flex items-center gap-2">
                            <i class="fas fa-eraser text-purple-600"></i>
                            {{ $t('viewer.background_removal', 'إزالة الخلفية') }}
                        </h3>
                        <button
                            @click="toggleBackgroundRemoval"
                            class="w-full px-3 py-2 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all"
                            :class="isBackgroundRemovalActive 
                                ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' 
                                : 'bg-white text-brand-primary border border-green-200'"
                            :title="$t('viewer.toggle_background_removal', 'تبديل إزالة الخلفية مع الحفاظ على الإعدادات')"
                        >
                            <i class="fas fa-eraser mr-2"></i>
                            {{ isBackgroundRemovalActive ? $t('viewer.bg_removed_on', 'مزال') : $t('viewer.remove_bg', 'إزالة الخلفية') }}
                        </button>
                    </div>

                    <!-- Color Management and Eraser Tools -->
                    <div class="bg-red-50 rounded-2xl p-4 border border-red-200">
                        <h3 class="text-sm font-bold text-brand-primary mb-3 flex items-center gap-2">
                            <i class="fas fa-palette text-red-600"></i>
                            {{ $t('viewer.color_management', 'إدارة الألوان') }}
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-primary mb-2">{{ $t('viewer.select_color', 'اختر اللون') }}</label>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="color in availableColors.concat(['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57', '#ff9ff3'])"
                                        :key="color"
                                        @click="setColor(color)"
                                        class="w-8 h-8 rounded-full border-2 transition-all"
                                        :class="selectedColor === color && isColorModeActive ? 'border-brand-primary ring-2 ring-offset-2 ring-brand-primary' : 'border-gray-300'"
                                        :style="{ backgroundColor: color }"
                                        :title="color"
                                    ></button>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-brand-primary mb-2">{{ $t('viewer.color_intensity', 'شدة اللون') }}: {{ (colorIntensity * 100).toFixed(0) }}%</label>
                                <input 
                                    v-model="colorIntensity" 
                                    type="range" 
                                    min="0" 
                                    max="1" 
                                    step="0.01" 
                                    class="w-full accent-red-600"
                                    @input="applyColorToProjection"
                                />
                            </div>
                            
                            <div class="flex gap-2">
                                <button
                                    @click="toggleColorMode"
                                    class="flex-1 px-3 py-2 rounded-lg text-sm font-bold shadow-md hover:shadow-lg transition-all"
                                    :class="isColorModeActive 
                                        ? 'bg-gradient-to-r from-red-500 to-red-600 text-white' 
                                        : 'bg-white text-brand-primary border border-red-200'"
                                >
                                    <i class="fas fa-fill-drip mr-1"></i>
                                    {{ isColorModeActive ? $t('viewer.color_mode_on', 'اللون مفعل') : $t('viewer.color_mode', 'وضع اللون') }}
                                </button>
                                
                                <button
                                    @click="resetColorAndEraser"
                                    class="px-3 py-2 rounded-lg text-sm font-bold shadow-md hover:shadow-lg transition-all bg-white text-brand-primary border border-red-200"
                                >
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Eraser Tool -->
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200">
                        <h3 class="text-sm font-bold text-brand-primary mb-3 flex items-center gap-2">
                            <i class="fas fa-eraser text-gray-600"></i>
                            {{ $t('viewer.eraser_tool', 'أداة الممحاة') }}
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-primary mb-2">{{ $t('viewer.eraser_size', 'حجم الممحاة') }}: {{ eraserSize }}px</label>
                                <input 
                                    v-model="eraserSize" 
                                    type="range" 
                                    min="5" 
                                    max="50" 
                                    step="1" 
                                    class="w-full accent-gray-600"
                                />
                            </div>
                            
                            <button
                                @click="toggleEraser"
                                class="w-full px-3 py-2 rounded-lg text-sm font-bold shadow-md hover:shadow-lg transition-all"
                                :class="isEraserActive 
                                    ? 'bg-gradient-to-r from-gray-500 to-gray-700 text-white' 
                                    : 'bg-white text-brand-primary border border-gray-200'"
                            >
                                <i class="fas fa-eraser mr-1"></i>
                                {{ isEraserActive ? $t('viewer.eraser_on', 'الممحاة مفعلة') : $t('viewer.use_eraser', 'استخدام الممحاة') }}
                            </button>
                        </div>
                    </div>

                    <!-- Position Controls -->
                    <div class="bg-indigo-50 rounded-2xl p-4 border border-indigo-200">
                        <h3 class="text-sm font-bold text-brand-primary mb-3 flex items-center gap-2">
                            <i class="fas fa-arrows-alt text-indigo-600"></i>
                            {{ $t('viewer.position_controls', 'التحكم بالموضع') }}
                        </h3>
                        <div class="grid grid-cols-2 gap-2">
                            <button
                                @click="moveDesignUp"
                                class="px-3 py-2 rounded-lg bg-white border border-indigo-200 text-brand-primary font-bold text-sm hover:bg-indigo-100 transition-all"
                            >
                                <i class="fas fa-arrow-up mr-1"></i>
                                {{ $t('viewer.move_up', 'أعلى') }}
                            </button>
                            <button
                                @click="moveDesignDown"
                                class="px-3 py-2 rounded-lg bg-white border border-indigo-200 text-brand-primary font-bold text-sm hover:bg-indigo-100 transition-all"
                            >
                                <i class="fas fa-arrow-down mr-1"></i>
                                {{ $t('viewer.move_down', 'أسفل') }}
                            </button>
                            <button
                                @click="moveDesignLeft"
                                class="px-3 py-2 rounded-lg bg-white border border-indigo-200 text-brand-primary font-bold text-sm hover:bg-indigo-100 transition-all"
                            >
                                <i class="fas fa-arrow-left mr-1"></i>
                                {{ $t('viewer.move_left', 'يسار') }}
                            </button>
                            <button
                                @click="moveDesignRight"
                                class="px-3 py-2 rounded-lg bg-white border border-indigo-200 text-brand-primary font-bold text-sm hover:bg-indigo-100 transition-all"
                            >
                                <i class="fas fa-arrow-right mr-1"></i>
                                {{ $t('viewer.move_right', 'يمين') }}
                            </button>
                        </div>
                    </div>

                    <!-- Scale & Rotation Controls -->
                    <div class="bg-orange-50 rounded-2xl p-4 border border-orange-200">
                        <h3 class="text-sm font-bold text-brand-primary mb-3 flex items-center gap-2">
                            <i class="fas fa-expand text-orange-600"></i>
                            {{ $t('viewer.scale_rotation', 'الحجم والدوران') }}
                        </h3>
                        <div class="grid grid-cols-2 gap-2">
                            <button
                                @click="scaleDesignUp"
                                class="px-3 py-2 rounded-lg bg-white border border-orange-200 text-brand-primary font-bold text-sm hover:bg-orange-100 transition-all"
                            >
                                <i class="fas fa-plus mr-1"></i>
                                {{ $t('viewer.scale_up', 'تكبير') }}
                            </button>
                            <button
                                @click="scaleDesignDown"
                                class="px-3 py-2 rounded-lg bg-white border border-orange-200 text-brand-primary font-bold text-sm hover:bg-orange-100 transition-all"
                            >
                                <i class="fas fa-minus mr-1"></i>
                                {{ $t('viewer.scale_down', 'تصغير') }}
                            </button>
                            <button
                                @click="rotateDesignLeft"
                                class="px-3 py-2 rounded-lg bg-white border border-orange-200 text-brand-primary font-bold text-sm hover:bg-orange-100 transition-all"
                            >
                                <i class="fas fa-undo mr-1"></i>
                                {{ $t('viewer.rotate_left', 'يسار') }}
                            </button>
                            <button
                                @click="rotateDesignRight"
                                class="px-3 py-2 rounded-lg bg-white border border-orange-200 text-brand-primary font-bold text-sm hover:bg-orange-100 transition-all"
                            >
                                <i class="fas fa-redo mr-1"></i>
                                {{ $t('viewer.rotate_right', 'يمين') }}
                            </button>
                        </div>
                    </div>

                    <!-- Fine Tuning Sliders -->
                    <div class="bg-pink-50 rounded-2xl p-4 border border-pink-200">
                        <h3 class="text-sm font-bold text-brand-primary mb-4 flex items-center gap-2">
                            <i class="fas fa-sliders-h text-pink-600"></i>
                            {{ $t('viewer.fine_tuning', 'ضبط دقيق') }}
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-brand-primary mb-1 flex items-center gap-2">
                                    <i class="fas fa-sync"></i>
                                    {{ $t('viewer.rotate_z', 'دوران Z') }}: {{ Number(projection.rotateZ).toFixed(0) }}°
                                </label>
                                <input v-model="projection.rotateZ" type="range" min="-180" max="180" step="1" class="w-full accent-pink-600" />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-brand-primary mb-1 flex items-center gap-2">
                                    <i class="fas fa-sync-alt"></i>
                                    {{ $t('viewer.rotate_x', 'دوران X') }}: {{ Number(projection.rotateX).toFixed(0) }}°
                                </label>
                                <input v-model="projection.rotateX" type="range" min="-45" max="45" step="1" class="w-full accent-pink-600" />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-brand-primary mb-1 flex items-center gap-2">
                                    <i class="fas fa-compress-arrows-alt"></i>
                                    {{ $t('viewer.curve', 'انحناء') }}: {{ (Number(projection.curve) * 100).toFixed(1) }}%
                                </label>
                                <input v-model="projection.curve" type="range" min="0" max="0.25" step="0.005" class="w-full accent-pink-600" />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-brand-primary mb-1 flex items-center gap-2">
                                    <i class="fas fa-adjust"></i>
                                    {{ $t('viewer.opacity', 'شفافية') }}: {{ (Number(projection.opacity) * 100).toFixed(0) }}%
                                </label>
                                <input v-model="projection.opacity" type="range" min="0.2" max="1" step="0.01" class="w-full accent-pink-600" />
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-brand-primary mb-1 flex items-center gap-2">
                                        <i class="fas fa-arrows-alt-h"></i>
                                        {{ $t('viewer.scale_x', 'عرض X') }}: {{ Number(projection.scaleX).toFixed(2) }}
                                    </label>
                                    <input v-model="projection.scaleX" type="range" min="0.2" max="2" step="0.01" class="w-full accent-pink-600" />
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-brand-primary mb-1 flex items-center gap-2">
                                        <i class="fas fa-arrows-alt-v"></i>
                                        {{ $t('viewer.scale_y', 'طول Y') }}: {{ Number(projection.scaleY).toFixed(2) }}
                                    </label>
                                    <input v-model="projection.scaleY" type="range" min="0.2" max="2" step="0.01" class="w-full accent-pink-600" />
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-xs font-bold text-brand-primary mb-1">
                                        {{ $t('viewer.axis_x', 'محور X') }}: {{ Number(projection.x).toFixed(2) }}
                                    </label>
                                    <input v-model="projection.x" type="range" min="-1" max="1" step="0.01" class="w-full accent-pink-600" />
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-brand-primary mb-1">
                                        {{ $t('viewer.axis_y', 'محور Y') }}: {{ Number(projection.y).toFixed(2) }}
                                    </label>
                                    <input v-model="projection.y" type="range" min="-0.5" max="1.5" step="0.01" class="w-full accent-pink-600" />
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-brand-primary mb-1">
                                        {{ $t('viewer.axis_z', 'محور Z') }}: {{ Number(projection.z).toFixed(2) }}
                                    </label>
                                    <input v-model="projection.z" type="range" min="-0.2" max="1" step="0.01" class="w-full accent-pink-600" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Customization Section -->
                    <div class="bg-yellow-50 rounded-2xl p-4 border border-yellow-200">
                        <h3 class="text-sm font-bold text-brand-primary mb-4 flex items-center gap-2">
                            <i class="fas fa-shopping-cart text-yellow-600"></i>
                            {{ $t('viewer.order_customization', 'تخصيص الطلب') }}
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-brand-primary mb-2">{{ $t('viewer.color_selection', 'اختيار اللون') }}</label>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="color in availableColors"
                                        :key="color"
                                        @click="orderDetails.color = color"
                                        class="w-8 h-8 rounded-full border-2 transition-all"
                                        :class="orderDetails.color === color ? 'border-brand-primary ring-2 ring-offset-2 ring-brand-primary' : 'border-gray-300'"
                                        :style="{ backgroundColor: color }"
                                        :title="color"
                                    ></button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-brand-primary mb-2">{{ $t('viewer.size_selection', 'اختيار الحجم') }}</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button
                                        v-for="size in availableSizes"
                                        :key="size"
                                        @click="orderDetails.size = size"
                                        class="px-3 py-2 rounded-lg text-sm font-bold border transition-all"
                                        :class="orderDetails.size === size 
                                            ? 'bg-brand-primary text-white border-brand-primary' 
                                            : 'bg-white text-brand-primary border-brand-gold/20 hover:bg-brand-primary hover:text-white'"
                                    >
                                        {{ size }}
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-brand-primary mb-2">{{ $t('viewer.quantity', 'الكمية') }}</label>
                                <input
                                    v-model="orderDetails.quantity"
                                    type="number"
                                    min="1"
                                    class="w-full px-3 py-2 rounded-lg border border-brand-gold/20 text-brand-primary"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-brand-primary mb-2">{{ $t('viewer.notes', 'ملاحظات') }}</label>
                                <textarea
                                    v-model="orderDetails.notes"
                                    class="w-full px-3 py-2 rounded-lg border border-brand-gold/20 text-brand-primary"
                                    rows="3"
                                    :placeholder="$t('viewer.notes_placeholder', 'ملاحظات إضافية...')"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <button
                            @click="saveDesign"
                            class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-save"></i>
                            {{ $t('viewer.save_design', 'حفظ التصميم') }}
                        </button>

                        <button
                            @click="exportAsOrder"
                            class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-brand-primary to-brand-accent text-white font-bold shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-file-export"></i>
                            {{ $t('viewer.export_order', 'تصدير كطلب') }}
                        </button>

                        <button
                            @click="toggleOrderForm"
                            class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-bold shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-edit"></i>
                            {{ $t('viewer.customize_order', 'تخصيص الطلب') }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Overlay for sidebar -->
        <div
            v-if="showSidebar"
            class="fixed inset-0 bg-black/50 z-40"
            @click="toggleSidebar"
        ></div>
    </div>
</template>