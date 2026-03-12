<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
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
    router.visit(route('designer.create', { productType: 't-shirt' }) + `?template=${template.id}`);
};

const useTemplateInDesign = (template) => {
    // Navigate to designer with template applied to current design context
    // This would open the designer with the template pre-loaded
    router.visit(route('designer.create', { productType: 't-shirt' }) + `?template=${template.id}&mode=apply`);
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
    
    const csrfToken = document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content');
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
        <div class="p-6 bg-gray-50 min-h-screen">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Template Management</h1>
                <p class="text-gray-600 mt-2">Manage design templates for your products</p>
            </div>

            <!-- Advanced Template Tools -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 neumorphic">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Advanced Template Tools</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <button
                        @click="showUploadModal = true"
                        class="px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 neumorphic-btn flex flex-col items-center justify-center"
                    >
                        <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                        <span class="text-sm">Upload</span>
                    </button>
                    <button
                        @click="batchDeleteTemplates"
                        :disabled="selectedTemplates.length === 0"
                        class="px-4 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-lg hover:from-red-600 hover:to-rose-700 transition-all duration-200 neumorphic-btn flex flex-col items-center justify-center disabled:opacity-50"
                    >
                        <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        <span class="text-sm">Delete Selected</span>
                    </button>
                    <button
                        @click="batchExportTemplates"
                        :disabled="selectedTemplates.length === 0"
                        class="px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-200 neumorphic-btn flex flex-col items-center justify-center disabled:opacity-50"
                    >
                        <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        <span class="text-sm">Export Selected</span>
                    </button>
                    <button
                        @click="router.visit('/admin/designs/index')"
                        class="px-4 py-3 bg-gradient-to-r from-purple-500 to-fuchsia-600 text-white rounded-lg hover:from-purple-600 hover:to-fuchsia-700 transition-all duration-200 neumorphic-btn flex flex-col items-center justify-center"
                    >
                        <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                        <span class="text-sm">Designs</span>
                    </button>
                    <button
                        @click="router.visit(route('designer.create', { productType: 't-shirt' }))"
                        class="px-4 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-lg hover:from-orange-600 hover:to-amber-700 transition-all duration-200 neumorphic-btn flex flex-col items-center justify-center"
                    >
                        <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        <span class="text-sm">New Design</span>
                    </button>
                    <button
                        @click="router.visit('/admin/templates/categories')"
                        class="px-4 py-3 bg-gradient-to-r from-cyan-500 to-sky-600 text-white rounded-lg hover:from-cyan-600 hover:to-sky-700 transition-all duration-200 neumorphic-btn flex flex-col items-center justify-center"
                    >
                        <svg class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.514.567l7 10a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 01-5.516 5.516l-1.13-2.257a1 1 0 01.502-1.21l7-10A1.022 1.022 0 0121 7h-3M7 7h10" /></svg>
                        <span class="text-sm">Categories</span>
                    </button>
                </div>
            </div>

            <!-- Controls -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 neumorphic">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search templates..."
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent neumorphic-btn"
                            @input="fetchTemplates()"
                        />
                        
                        <select
                            v-model="selectedCategory"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent neumorphic-btn"
                            @change="fetchTemplates()"
                        >
                            <option value="">All Categories</option>
                            <option v-for="category in categories" :key="category" :value="category">
                                {{ category.charAt(0).toUpperCase() + category.slice(1) }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="flex gap-2 w-full md:w-auto">
                        <div class="flex items-center mr-4" v-if="templates.length > 0">
                            <input
                                type="checkbox"
                                v-model="selectAllChecked"
                                @change="selectAllTemplates"
                                class="mr-2 rounded text-blue-600"
                            />
                            <span class="text-sm text-gray-700">Select All ({{ selectedTemplates.length }})</span>
                        </div>
                        <button
                            @click="showUploadModal = true"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center neumorphic-btn"
                        >
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            Upload Template
                        </button>
                    </div>
                </div>
            </div>

            <!-- Templates grid -->
            <div v-if="templates.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                <div
                    v-for="template in templates"
                    :key="template.id"
                    :class="{ 'ring-2 ring-blue-500': selectedTemplates.includes(template.id) }"
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200 neumorphic relative"
                >
                    <div class="aspect-w-3 aspect-h-4 bg-gray-200 relative">
                        <img
                            :src="template.thumbnail_url || template.preview_url"
                            :alt="template.name"
                            class="w-full h-48 object-cover"
                        />
                        <input
                            type="checkbox"
                            :checked="selectedTemplates.includes(template.id)"
                            @change="toggleTemplateSelection(template.id)"
                            class="absolute top-2 left-2 rounded text-blue-600"
                        />
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800 truncate">{{ template.name }}</h3>
                        <p class="text-sm text-gray-600 mt-1 truncate">{{ template.description }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ template.category || 'Uncategorized' }}</p>
                        
                        <div class="mt-4 flex justify-between flex-wrap gap-2">
                            <button
                                @click="useTemplate(template)"
                                class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors duration-200 neumorphic-btn flex-1 min-w-[50px]"
                                title="Use Template"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.414 15H9v-2.586l.586-.586z" /></svg>
                            </button>
                            <button
                                @click="useTemplateInDesign(template)"
                                class="px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 transition-colors duration-200 neumorphic-btn flex-1 min-w-[50px]"
                                title="Use Template in Design"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                            </button>
                            <button
                                @click="convertToDesign(template)"
                                class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 transition-colors duration-200 neumorphic-btn flex-1 min-w-[50px]"
                                title="Convert to Design"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h1.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-1.582m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </button>
                            <button
                                @click="deleteTemplate(template.id)"
                                class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors duration-200 neumorphic-btn flex-1 min-w-[50px]"
                                title="Delete Template"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Empty State -->
            <div v-else class="text-center py-12 bg-white rounded-lg shadow-md neumorphic">
                <div class="text-5xl mb-4">🖼️</div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Templates Found</h3>
                <p class="text-gray-500 mb-4">Get started by uploading your first template</p>
                <button
                    @click="showUploadModal = true"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 neumorphic-btn"
                >
                    Upload Your First Template
                </button>
            </div>

            <!-- Pagination -->
            <div v-if="templates.length > 0" class="mt-8 flex justify-center neumorphic p-4 rounded-lg">
                <nav class="flex items-center space-x-2">
                    <button
                        @click="fetchTemplates(currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="px-3 py-1 rounded border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed neumorphic-btn"
                    >
                        Previous
                    </button>
                    
                    <span class="mx-2 text-gray-700">
                        Page {{ currentPage }} of {{ lastPage }} ({{ total }} total)
                    </span>
                    
                    <button
                        @click="fetchTemplates(currentPage + 1)"
                        :disabled="currentPage === lastPage"
                        class="px-3 py-1 rounded border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed neumorphic-btn"
                    >
                        Next
                    </button>
                </nav>
            </div>

            <!-- Upload Modal -->
            <div
                v-if="showUploadModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            >
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md neumorphic">
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-4 text-gray-800">Upload New Template</h2>
                        
                        <form @submit.prevent="submitTemplate">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                    <input
                                        v-model="newTemplate.name"
                                        type="text"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent neumorphic-btn"
                                    />
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea
                                        v-model="newTemplate.description"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent neumorphic-btn"
                                    ></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <select
                                        v-model="newTemplate.category"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent neumorphic-btn"
                                    >
                                        <option value="">Select Category</option>
                                        <option v-for="category in categories" :key="category" :value="category">
                                            {{ category.charAt(0).toUpperCase() + category.slice(1) }}
                                        </option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Image *</label>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="handleFileChange"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent neumorphic-btn"
                                    />
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-end space-x-3">
                                <button
                                    type="button"
                                    @click="showUploadModal = false"
                                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 neumorphic-btn"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 neumorphic-btn"
                                >
                                    Upload
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>