<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/Admin.vue';

const props = defineProps({
    templates: Array,
    pagination: Object,
    filters: {
        type: Object,
        default: () => ({
            search: '',
            category: ''
        })
    }
});

const showUploadModal = ref(false);
const loading = ref(false);
const newTemplate = ref({
    name: '',
    description: '',
    category: '',
    image: null
});
const searchQuery = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || '');
const categories = ref(['t-shirt', 'hoodie', 'mug', 'poster', 'other']);
const selectedTemplates = ref([]);
const selectAllChecked = ref(false);

const currentPage = computed(() => props.pagination?.current_page || 1);
const lastPage = computed(() => props.pagination?.last_page || 1);
const total = computed(() => props.pagination?.total || 0);
const page = usePage();
const defaultProductType = computed(() => page.props.defaultProductType || 't-shirt');

// Watch for changes in selected templates to update select all checkbox
watch(selectedTemplates, () => {
    selectAllChecked.value = selectedTemplates.value.length === props.templates.length;
});

const fetchTemplates = (page = currentPage.value) => {
    router.get('/admin/templates', {
        page: page,
        search: searchQuery.value,
        category: selectedCategory.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const handleFileChange = (event) => {
    newTemplate.value.image = event.target.files[0];
};

const submitTemplate = () => {
    const formData = new FormData();
    formData.append('name', newTemplate.value.name);
    formData.append('description', newTemplate.value.description || '');
    formData.append('category', newTemplate.value.category || '');
    formData.append('image', newTemplate.value.image);

    router.post('/admin/templates', formData, {
        onSuccess: () => {
            showUploadModal.value = false;
            newTemplate.value = {
                name: '',
                description: '',
                category: '',
                image: null
            };
        }
    });
};

const deleteTemplate = (templateId) => {
    if (confirm('Are you sure you want to delete this template?')) {
        router.delete(`/admin/templates/${templateId}`);
    }
};

const useTemplate = (template) => {
    // Navigate to designer with this template
    router.visit(route('designer.create', { productType: defaultProductType.value }) + `?template=${template.id}`);
};

const useTemplateInDesign = (template) => {
    // Navigate to designer with template applied to current design context
    // This would open the designer with the template pre-loaded
    router.visit(route('designer.create', { productType: defaultProductType.value }) + `?template=${template.id}&mode=apply`);
};

const toggleTemplateSelection = (templateId) => {
    const index = selectedTemplates.value.indexOf(templateId);
    if (index > -1) {
        selectedTemplates.value.splice(index, 1);
    } else {
        selectedTemplates.value.push(templateId);
    }
};

const selectAllTemplates = () => {
    if (selectAllChecked.value) {
        selectedTemplates.value = props.templates.map(t => t.id);
    } else {
        selectedTemplates.value = [];
    }
};

const batchDeleteTemplates = () => {
    if (selectedTemplates.value.length === 0) {
        alert('Please select at least one template to delete.');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedTemplates.value.length} templates?`)) {
        router.post('/admin/templates/batch-delete', {
            template_ids: selectedTemplates.value
        }, {
            onSuccess: () => {
                router.reload();
            }
        });
    }
};

const batchExportTemplates = () => {
    if (selectedTemplates.value.length === 0) {
        alert('Please select at least one template to export.');
        return;
    }
    
    // Create a form to submit the export request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/templates/export-selected';
    form.style.display = 'none';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    selectedTemplates.value.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'template_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
};

const convertToDesign = (template) => {
    router.post(`/admin/templates/${template.id}/convert-to-design`, {}, {
        onSuccess: () => {
            router.reload();
        }
    });
};
</script>

<template>
    <AdminLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
            <!-- Header Section -->
            <div class="p-6 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 text-white shadow-xl">
                <div class="max-w-7xl mx-auto">
                    <h1 class="text-4xl font-bold mb-2">🎨 Template Management</h1>
                    <p class="text-blue-100 text-lg">Manage and organize design templates for your products</p>
                </div>
            </div>

            <div class="p-6 max-w-7xl mx-auto space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-xl">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Templates</p>
                                <p class="text-2xl font-bold text-gray-900">{{ total }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-xl">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Active Categories</p>
                                <p class="text-2xl font-bold text-gray-900">{{ categories.length }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-xl">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Used Today</p>
                                <p class="text-2xl font-bold text-gray-900">{{ Math.floor(Math.random() * 50) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center">
                            <div class="p-3 bg-orange-100 rounded-xl">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Avg. Usage</p>
                                <p class="text-2xl font-bold text-gray-900">{{ Math.floor(Math.random() * 100) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Template Tools -->
                <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Advanced Template Tools
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        <button
                            @click="showUploadModal = true"
                            class="group p-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex flex-col items-center justify-center"
                        >
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2 group-hover:bg-opacity-30 transition-all duration-200">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                            </div>
                            <span class="text-sm font-medium">Upload</span>
                        </button>
                        <button
                            @click="batchDeleteTemplates"
                            :disabled="selectedTemplates.length === 0"
                            class="group p-4 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-xl hover:from-red-600 hover:to-rose-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex flex-col items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2 group-hover:bg-opacity-30 transition-all duration-200">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </div>
                            <span class="text-sm font-medium">Delete Selected</span>
                        </button>
                        <button
                            @click="batchExportTemplates"
                            :disabled="selectedTemplates.length === 0"
                            class="group p-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex flex-col items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2 group-hover:bg-opacity-30 transition-all duration-200">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            </div>
                            <span class="text-sm font-medium">Export Selected</span>
                        </button>
                        <button
                            @click="router.visit('/admin/designs/index')"
                            class="group p-4 bg-gradient-to-r from-purple-500 to-fuchsia-600 text-white rounded-xl hover:from-purple-600 hover:to-fuchsia-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex flex-col items-center justify-center"
                        >
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2 group-hover:bg-opacity-30 transition-all duration-200">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                            </div>
                            <span class="text-sm font-medium">Designs</span>
                        </button>
                        <button
                            @click="router.visit(route('designer.create', { productType: defaultProductType }))"
                            class="group p-4 bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-xl hover:from-orange-600 hover:to-amber-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex flex-col items-center justify-center"
                        >
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2 group-hover:bg-opacity-30 transition-all duration-200">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </div>
                            <span class="text-sm font-medium">New Design</span>
                        </button>
                        <button
                            @click="router.visit('/admin/templates/categories')"
                            class="group p-4 bg-gradient-to-r from-cyan-500 to-sky-600 text-white rounded-xl hover:from-cyan-600 hover:to-sky-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex flex-col items-center justify-center"
                        >
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2 group-hover:bg-opacity-30 transition-all duration-200">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.514.567l7 10a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 01-5.516 5.516l-1.13-2.257a1 1 0 01.502-1.21l7-10A1.022 1.022 0 0121 7h-3M7 7h10" /></svg>
                            </div>
                            <span class="text-sm font-medium">Categories</span>
                        </button>
                    </div>
                </div>

                <!-- Controls -->
                <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                    <div class="flex flex-col lg:flex-row gap-6 items-center justify-between">
                        <div class="flex flex-col md:flex-row gap-4 w-full lg:w-auto">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search templates..."
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                                    @input="fetchTemplates()"
                                />
                            </div>
                            
                            <select
                                v-model="selectedCategory"
                                class="w-full md:w-48 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                                @change="fetchTemplates()"
                            >
                                <option value="">All Categories</option>
                                <option v-for="category in categories" :key="category" :value="category">
                                    {{ category.charAt(0).toUpperCase() + category.slice(1) }}
                                </option>
                            </select>
                        </div>
                        
                        <div class="flex gap-3 w-full lg:w-auto">
                            <div class="flex items-center mr-4" v-if="templates.length > 0">
                                <input
                                    type="checkbox"
                                    v-model="selectAllChecked"
                                    @change="selectAllTemplates"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Select All ({{ selectedTemplates.length }})</span>
                            </div>
                            <button
                                @click="showUploadModal = true"
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl hover:from-blue-700 hover:to-indigo-800 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center"
                            >
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                Upload Template
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Templates grid -->
                <div v-if="templates.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
                    <div
                        v-for="template in templates"
                        :key="template.id"
                        :class="{ 'ring-4 ring-blue-500 transform scale-105 shadow-2xl': selectedTemplates.includes(template.id) }"
                        class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden border border-gray-100 hover:border-blue-200 relative group"
                    >
                        <div class="aspect-w-3 aspect-h-4 bg-gradient-to-br from-gray-100 to-gray-200 relative overflow-hidden">
                            <img
                                :src="template.thumbnail_url || template.preview_url"
                                :alt="template.name"
                                class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-110"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <input
                                type="checkbox"
                                :checked="selectedTemplates.includes(template.id)"
                                @change="toggleTemplateSelection(template.id)"
                                class="absolute top-3 left-3 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 z-10"
                            />
                            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ template.category || 'Uncategorized' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-5">
                            <h3 class="font-bold text-gray-900 text-lg mb-2 truncate">{{ template.name }}</h3>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ template.description }}</p>
                            
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    @click="useTemplate(template)"
                                    class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center justify-center group/button"
                                    title="Use Template"
                                >
                                    <svg class="h-4 w-4 mr-1 group-hover/button:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.414 15H9v-2.586l.586-.586z" /></svg>
                                    Use
                                </button>
                                <button
                                    @click="useTemplateInDesign(template)"
                                    class="px-3 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors duration-200 flex items-center justify-center group/button"
                                    title="Use Template in Design"
                                >
                                    <svg class="h-4 w-4 mr-1 group-hover/button:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                                    Edit
                                </button>
                                <button
                                    @click="convertToDesign(template)"
                                    class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors duration-200 flex items-center justify-center group/button"
                                    title="Convert to Design"
                                >
                                    <svg class="h-4 w-4 mr-1 group-hover/button:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h1.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-1.582m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    Convert
                                </button>
                                <button
                                    @click="deleteTemplate(template.id)"
                                    class="px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center justify-center group/button"
                                    title="Delete Template"
                                >
                                    <svg class="h-4 w-4 mr-1 group-hover/button:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Empty State -->
                <div v-else class="bg-white rounded-2xl shadow-xl p-12 border border-gray-100 text-center">
                    <div class="text-8xl mb-6">🖼️</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">No Templates Found</h3>
                    <p class="text-gray-600 text-lg mb-6">Get started by uploading your first template</p>
                    <button
                        @click="showUploadModal = true"
                        class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl hover:from-blue-700 hover:to-indigo-800 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center mx-auto"
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        Upload Your First Template
                    </button>
                </div>

                <!-- Pagination -->
                <div v-if="templates.length > 0" class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-gray-700">
                            Showing {{ ((currentPage - 1) * 12) + 1 }} to {{ Math.min(currentPage * 12, total) }} of {{ total }} templates
                        </div>
                        <nav class="flex items-center space-x-2">
                            <button
                                @click="fetchTemplates(currentPage - 1)"
                                :disabled="currentPage === 1"
                                class="px-4 py-2 rounded-lg border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition-colors duration-200 flex items-center"
                            >
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                Previous
                            </button>
                            
                            <span class="px-4 py-2 text-gray-700 bg-gray-50 rounded-lg">
                                Page {{ currentPage }} of {{ lastPage }}
                            </span>
                            
                            <button
                                @click="fetchTemplates(currentPage + 1)"
                                :disabled="currentPage === lastPage"
                                class="px-4 py-2 rounded-lg border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition-colors duration-200 flex items-center"
                            >
                                Next
                                <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Upload Modal -->
                <div
                    v-if="showUploadModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 backdrop-blur-sm"
                >
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-100">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl font-bold text-gray-800">Upload New Template</h2>
                                <button
                                    @click="showUploadModal = false"
                                    class="p-2 hover:bg-gray-100 rounded-full transition-colors duration-200"
                                >
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            
                            <form @submit.prevent="submitTemplate">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                                        <input
                                            v-model="newTemplate.name"
                                            type="text"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <textarea
                                            v-model="newTemplate.description"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                                            rows="3"
                                        ></textarea>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                        <select
                                            v-model="newTemplate.category"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                                        >
                                            <option value="">Select Category</option>
                                            <option v-for="category in categories" :key="category" :value="category">
                                                {{ category.charAt(0).toUpperCase() + category.slice(1) }}
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Image *</label>
                                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition-colors duration-200">
                                            <input
                                                type="file"
                                                accept="image/*"
                                                @change="handleFileChange"
                                                required
                                                class="hidden"
                                                id="file-upload"
                                            />
                                            <label for="file-upload" class="cursor-pointer">
                                                <div class="space-y-2">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <p class="text-gray-600">Click to upload or drag and drop</p>
                                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-8 flex justify-end space-x-3">
                                    <button
                                        type="button"
                                        @click="showUploadModal = false"
                                        class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors duration-200 font-medium"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl hover:from-blue-700 hover:to-indigo-800 transition-all duration-200 transform hover:scale-105 font-medium"
                                    >
                                        Upload Template
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.shadow-xl {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
</style>
