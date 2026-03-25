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

const showControlsPanel = ref(true);
const projectionVisible = ref(true);

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
    const hemi = new THREE.HemisphereLight(0xffffff, 0xf3e6de, 1.2);
    scene.add(hemi);

    const ambient = new THREE.AmbientLight(0xffffff, 0.92);
    scene.add(ambient);

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

    const fill = new THREE.DirectionalLight(0xffd8ea, 0.8);
    fill.position.set(-2.0, 2.4, 2.2);
    scene.add(fill);

    const rim = new THREE.DirectionalLight(0xd6e2ff, 0.8);
    rim.position.set(-2.4, 3.0, -2.4);
    scene.add(rim);

    const glow1 = new THREE.PointLight(0xff6fa9, 0.7, 6);
    glow1.position.set(1.2, 1.8, 1.2);
    scene.add(glow1);

    const glow2 = new THREE.PointLight(0x8f7dff, 0.45, 6);
    glow2.position.set(-1.2, 1.6, 1.2);
    scene.add(glow2);
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

    projection.value = {
        ...base,
    };

    if (aspect > 1.05) {
        projection.value.scaleX += 0.07;
        projection.value.scaleY -= 0.12;
    } else if (aspect < 0.7) {
        projection.value.scaleX -= 0.06;
        projection.value.scaleY += 0.08;
    }
}

function createCurvedPlane(width, height, curveStrength) {
    const geometry = new THREE.PlaneGeometry(width, height, 28, 36);
    const pos = geometry.attributes.position;

    for (let i = 0; i < pos.count; i++) {
        const x = pos.getX(i);
        const y = pos.getY(i);

        const nx = x / width;
        const chestCurve = Math.cos(nx * Math.PI) * curveStrength;
        const sidePull = Math.abs(nx) * -(curveStrength * 0.35);
        const upperLift = y > height * 0.08 ? curveStrength * 0.12 : 0;

        pos.setZ(i, chestCurve + sidePull + upperLift);
    }

    pos.needsUpdate = true;
    geometry.computeVertexNormals();

    return geometry;
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
    }
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

function loadProductTexture(imageUrl, resetPlacement = false) {
    if (!imageUrl || !modelRef.value || !rendererRef.value) return;

    const loader = new THREE.TextureLoader();

    loader.load(
        imageUrl,
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

            const material = new THREE.MeshStandardMaterial({
                map: texture,
                transparent: true,
                alphaTest: 0.05,
                side: THREE.DoubleSide,
                roughness: 0.84,
                metalness: 0.02,
                opacity: Number(projection.value.opacity),
            });

            const plane = new THREE.Mesh(geometry, material);

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
    } catch (error) {
        console.error("Failed to load selected model, using fallback:", error);
        hasError.value = true;
        errorMessage.value = "تعذر تحميل المجسم المحدد، تم تشغيل نموذج احتياطي.";
        createFallbackModel(scene);
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
    loadProductTexture(img, true);
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

defineExpose({
    resetView,
    toggleAutoRotate,
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
            loadProductTexture(newValue, true);
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

        <!-- top action bar -->
        <div class="absolute top-16 left-4 right-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <button
                    @click="selectPrevModel"
                    class="px-4 py-2 rounded-2xl bg-white/90 backdrop-blur-md border border-white shadow text-brand-primary font-bold text-sm hover:-translate-y-0.5 transition-all"
                >
                    {{ $t('viewer.models_prev') }}
                </button>

                <button
                    @click="selectNextModel"
                    class="px-4 py-2 rounded-2xl bg-white/90 backdrop-blur-md border border-white shadow text-brand-primary font-bold text-sm hover:-translate-y-0.5 transition-all"
                >
                    {{ $t('viewer.models_next') }}
                </button>
            </div>

            <div class="flex items-center gap-2">
                <button
                    v-if="!showControlsPanel"
                    @click="openControlsPanel"
                    class="px-4 py-2 rounded-2xl bg-gradient-to-r from-brand-primary to-brand-accent text-white font-bold text-sm shadow-lg hover:-translate-y-0.5 transition-all"
                >
                    {{ $t('viewer.open_controls') }}
                </button>

                <button
                    @click="toggleProjectionVisibility"
                    class="px-4 py-2 rounded-2xl bg-white/90 backdrop-blur-md border border-white shadow text-brand-primary font-bold text-sm hover:-translate-y-0.5 transition-all"
                >
                    {{ projectionVisible ? $t('viewer.hide_design') : $t('viewer.show_design') }}
                </button>
            </div>
        </div>

        <!-- professional model switcher -->
        <div class="absolute left-4 right-4 top-32">
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

        <!-- control panel -->
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-3"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-3"
        >
            <div
                v-if="showControlsPanel"
                class="absolute right-4 bottom-4 w-[380px] max-w-[calc(100%-2rem)] rounded-3xl bg-white/88 backdrop-blur-xl border border-white/80 shadow-2xl p-4 space-y-4"
            >
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-brand-primary">
                            {{ $t('viewer.controls_title') }}
                        </h3>
                        <p class="text-xs text-brand-secondary mt-1">
                            {{ $t(selectedModel.nameKey) }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            @click="toggleAutoRotate"
                            class="px-3 py-1.5 rounded-full bg-brand-primary text-white text-xs font-bold"
                        >
                            {{ $t('viewer.auto_rotate') }}
                        </button>

                        <button
                            @click="closeControlsPanel"
                            class="w-9 h-9 rounded-full bg-white border border-brand-gold/20 text-brand-primary font-bold shadow hover:bg-brand-primary hover:text-white transition-all"
                            :title="$t('viewer.close_controls')"
                        >
                            ×
                        </button>
                    </div>
                </div>

                <!-- image selector -->
                <div v-if="availableImages.length" class="space-y-2">
                    <p class="text-xs font-bold text-brand-primary">
                        {{ $t('viewer.select_design_image') }}
                    </p>
                    <div class="grid grid-cols-4 gap-2">
                        <button
                            v-for="(img, index) in availableImages"
                            :key="`${img}-${index}`"
                            @click="selectImage(img)"
                            class="aspect-square overflow-hidden rounded-2xl border-2 bg-white transition-all"
                            :class="activeImage === img ? 'border-brand-gold ring-4 ring-brand-gold/10' : 'border-brand-gold/10 hover:border-brand-gold/40'"
                        >
                            <img :src="img" :alt="`design-${index}`" class="w-full h-full object-cover" />
                        </button>
                    </div>
                </div>

                <!-- quick controls -->
                <div class="grid grid-cols-2 gap-2">
                    <button @click="rotateDesignLeft" class="px-3 py-2 rounded-2xl bg-brand-primary/8 text-brand-primary font-bold text-sm">
                        {{ $t('viewer.rotate_left') }}
                    </button>
                    <button @click="rotateDesignRight" class="px-3 py-2 rounded-2xl bg-brand-primary/8 text-brand-primary font-bold text-sm">
                        {{ $t('viewer.rotate_right') }}
                    </button>
                    <button @click="scaleDesignUp" class="px-3 py-2 rounded-2xl bg-brand-gold/10 text-brand-gold font-bold text-sm">
                        {{ $t('viewer.scale_up') }}
                    </button>
                    <button @click="scaleDesignDown" class="px-3 py-2 rounded-2xl bg-brand-gold/10 text-brand-gold font-bold text-sm">
                        {{ $t('viewer.scale_down') }}
                    </button>
                    <button @click="moveDesignUp" class="px-3 py-2 rounded-2xl bg-brand-accent/10 text-brand-accent font-bold text-sm">
                        {{ $t('viewer.move_up') }}
                    </button>
                    <button @click="moveDesignDown" class="px-3 py-2 rounded-2xl bg-brand-accent/10 text-brand-accent font-bold text-sm">
                        {{ $t('viewer.move_down') }}
                    </button>
                    <button @click="moveDesignLeft" class="px-3 py-2 rounded-2xl bg-brand-secondary/10 text-brand-primary font-bold text-sm">
                        {{ $t('viewer.move_left') }}
                    </button>
                    <button @click="moveDesignRight" class="px-3 py-2 rounded-2xl bg-brand-secondary/10 text-brand-primary font-bold text-sm">
                        {{ $t('viewer.move_right') }}
                    </button>
                </div>

                <!-- fine controls -->
                <div class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-bold text-brand-primary mb-1">
                            {{ $t('viewer.rotate_z') }}
                        </label>
                        <input v-model="projection.rotateZ" type="range" min="-180" max="180" step="1" class="w-full" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-brand-primary mb-1">
                            {{ $t('viewer.rotate_x') }}
                        </label>
                        <input v-model="projection.rotateX" type="range" min="-45" max="45" step="1" class="w-full" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-brand-primary mb-1">
                            {{ $t('viewer.rotate_y') }}
                        </label>
                        <input v-model="projection.rotateY" type="range" min="-45" max="45" step="1" class="w-full" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-brand-primary mb-1">
                            {{ $t('viewer.curve') }}
                        </label>
                        <input v-model="projection.curve" type="range" min="0" max="0.25" step="0.005" class="w-full" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-brand-primary mb-1">
                            {{ $t('viewer.opacity') }}
                        </label>
                        <input v-model="projection.opacity" type="range" min="0.2" max="1" step="0.01" class="w-full" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-brand-primary mb-1">
                                {{ $t('viewer.scale_x') }}
                            </label>
                            <input v-model="projection.scaleX" type="range" min="0.2" max="2" step="0.01" class="w-full" />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-brand-primary mb-1">
                                {{ $t('viewer.scale_y') }}
                            </label>
                            <input v-model="projection.scaleY" type="range" min="0.2" max="2" step="0.01" class="w-full" />
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-brand-primary mb-1">
                                {{ $t('viewer.axis_x') }}
                            </label>
                            <input v-model="projection.x" type="range" min="-1" max="1" step="0.01" class="w-full" />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-brand-primary mb-1">
                                {{ $t('viewer.axis_y') }}
                            </label>
                            <input v-model="projection.y" type="range" min="-0.5" max="1.5" step="0.01" class="w-full" />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-brand-primary mb-1">
                                {{ $t('viewer.axis_z') }}
                            </label>
                            <input v-model="projection.z" type="range" min="-0.2" max="1" step="0.01" class="w-full" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <button
                        @click="resetDesignTransform"
                        class="px-4 py-3 rounded-2xl bg-gradient-to-r from-brand-primary to-brand-accent text-white font-bold text-sm shadow-lg"
                    >
                        {{ $t('viewer.reset_design') }}
                    </button>

                    <button
                        @click="resetView"
                        class="px-4 py-3 rounded-2xl bg-white border border-brand-gold/20 text-brand-primary font-bold text-sm shadow"
                    >
                        {{ $t('viewer.reset_camera') }}
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>