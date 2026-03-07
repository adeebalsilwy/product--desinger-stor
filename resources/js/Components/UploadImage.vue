<script setup>
import { ref, computed } from "vue";
import Remove from "@/Icons/Remove.vue";

const props = defineProps({
    id: { type: String, default: "image" },
    label: { type: String, default: "Label" },
    width: { type: String, default: "w-24" },
    height: { type: String, default: "h-24" },
    maxFileSize: { type: Number, default: 5 * 1024 * 1024 }, // 5MB
    acceptedFileTypes: { type: String, default: "image/*" },
    defaultImage: { type: String, default: null },
});

const emit = defineEmits(["update:modelValue"]);

const imagePreview = ref(null);
const imageFile = ref(null);
const errorMessage = ref("");
const defaultImage = ref(props.defaultImage);

const computedAcceptedTypes = computed(() =>
    props.acceptedFileTypes.split(",")
);

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    const validType = computedAcceptedTypes.value.some((type) =>
        file.type.startsWith(type.replace("*", ""))
    );
    if (!validType) {
        errorMessage.value = `Please select a valid file type (${props.acceptedFileTypes}).`;
        return;
    }

    // Validate file size
    if (file.size > props.maxFileSize) {
        errorMessage.value = `File size must be less than ${
            props.maxFileSize / (1024 * 1024)
        }MB.`;
        return;
    }

    imageFile.value = file;
    emit("update:modelValue", file); // This emits the file itself

    // Create a preview
    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.value = e.target.result;
    };
    reader.onloadend = () => reader.abort();
    reader.readAsDataURL(file);
}

function clearImage() {
    imagePreview.value = null;
    imageFile.value = null;
    errorMessage.value = "";
    defaultImage.value = null;
    emit("update:modelValue", null);
}
</script>

<template>
    <div
        class="flex flex-col justify-center items-start relative "
        :class="[width, height]"
    >
        <label
            :for="id"
            class="flex items-center gap-2 text-sm ms-1 "
            :style="{
                maxWidth: '100%',
                maxHeight: '40px',
            }"
        >
            {{ label }}
        </label>

        <div class="relative my-1 w-full h-full  overflow-hidden">
            <img
                v-if="imagePreview"
                :src="imagePreview"
                alt="Image Preview"
                class="shadow-md rounded-xl w-full h-full object-cover bg-white border-2 border-slate-600 transition-all duration-200"
                style="filter: grayscale(10%)"
            />

            <img
                v-else-if="defaultImage"
                :src="defaultImage"
                alt="Image Preview"
                class="shadow-md rounded-xl w-full h-full object-cover bg-white border-2 border-slate-600 transition-all duration-200"
                style="filter: grayscale(10%)"
            />
            <label
                :for="id"
                class="absolute inset-0 flex items-center justify-center w-full h-full bg-black bg-opacity-50 text-white rounded-xl border-1 border-slate-500 cursor-pointer opacity-20 hover:opacity-100 transition-opacity duration-200"
                aria-label="Upload image"
            >
                <span class="text-2xl">+</span>
            </label>
        </div>
        <input
            :id="id"
            type="file"
            class="hidden"
            :accept="acceptedFileTypes"
            @change="handleFileSelect"
        />
        <button
            v-if="imagePreview || defaultImage"
            class="text-red-500 hover:text-red-700 mt-2 absolute top-1 -left-3 p-1"
            @click.prevent="clearImage"
            aria-label="Remove image"
        >
            <Remove class="w-4 h-4 text-red-500" />
        </button>
        <p v-if="errorMessage" class="text-sm text-red-500">
            {{ errorMessage }}
        </p>
    </div>
</template>
