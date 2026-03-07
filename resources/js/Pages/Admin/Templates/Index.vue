<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Template Management</h1>
            <p class="text-gray-600 mt-2">Manage design templates for your products</p>
        </div>

        <!-- Controls -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search templates..."
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        @input="fetchTemplates()"
                    />
                    
                    <select
                        v-model="selectedCategory"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        @change="fetchTemplates()"
                    >
                        <option value="">All Categories</option>
                        <option v-for="category in categories" :key="category" :value="category">
                            {{ category.charAt(0).toUpperCase() + category.slice(1) }}
                        </option>
                    </select>
                </div>
                
                <button
                    @click="showUploadModal = true"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center"
                >
                    <i class="fas fa-plus mr-2"></i>
                    Upload Template
                </button>
            </div>
        </div>

        <!-- Loading indicator -->
        <div v-if="loading" class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
        </div>

        <!-- Templates grid -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <div
                v-for="template in templates"
                :key="template.id"
                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200"
            >
                <div class="aspect-w-3 aspect-h-4 bg-gray-200">
                    <img
                        :src="template.thumbnail_url || template.preview_url"
                        :alt="template.name"
                        class="w-full h-48 object-cover"
                    />
                </div>
                
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 truncate">{{ template.name }}</h3>
                    <p class="text-sm text-gray-600 mt-1 truncate">{{ template.description }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ template.category || 'Uncategorized' }}</p>
                    
                    <div class="mt-4 flex justify-between">
                        <button
                            @click="useTemplate(template)"
                            class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors duration-200"
                        >
                            Use
                        </button>
                        <button
                            @click="deleteTemplate(template.id)"
                            class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors duration-200"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="!loading && templates.length > 0" class="mt-8 flex justify-center">
            <nav class="flex items-center space-x-2">
                <button
                    @click="fetchTemplates(currentPage - 1)"
                    :disabled="currentPage === 1"
                    class="px-3 py-1 rounded border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Previous
                </button>
                
                <span class="mx-2">
                    {{ currentPage }} of {{ lastPage }}
                </span>
                
                <button
                    @click="fetchTemplates(currentPage + 1)"
                    :disabled="currentPage === lastPage"
                    class="px-3 py-1 rounded border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
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
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-4">Upload New Template</h2>
                    
                    <form @submit.prevent="submitTemplate">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input
                                    v-model="newTemplate.name"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea
                                    v-model="newTemplate.description"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                ></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select
                                    v-model="newTemplate.category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                />
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="showUploadModal = false"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                            >
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const templates = ref([]);
const loading = ref(false);
const showUploadModal = ref(false);
const newTemplate = ref({
    name: '',
    description: '',
    category: '',
    image: null
});
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);
const searchQuery = ref('');
const selectedCategory = ref('');
const categories = ref(['t-shirt', 'hoodie', 'mug', 'poster', 'other']);

const fetchTemplates = async (page = 1) => {
    loading.value = true;
    try {
        const response = await fetch(`/admin/templates?page=${page}&search=${searchQuery.value}&category=${selectedCategory.value}`);
        const data = await response.json();
        
        templates.value = data.templates;
        currentPage.value = data.pagination.current_page;
        lastPage.value = data.pagination.last_page;
        total.value = data.pagination.total;
    } catch (error) {
        console.error('Error fetching templates:', error);
    } finally {
        loading.value = false;
    }
};

const handleFileChange = (event) => {
    newTemplate.value.image = event.target.files[0];
};

const submitTemplate = async () => {
    const formData = new FormData();
    formData.append('name', newTemplate.value.name);
    formData.append('description', newTemplate.value.description || '');
    formData.append('category', newTemplate.value.category || '');
    formData.append('image', newTemplate.value.image);

    try {
        const response = await fetch('/admin/templates', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        });

        const result = await response.json();
        
        if (result.success) {
            showUploadModal.value = false;
            newTemplate.value = {
                name: '',
                description: '',
                category: '',
                image: null
            };
            fetchTemplates(); // Refresh the list
        }
    } catch (error) {
        console.error('Error uploading template:', error);
    }
};

const deleteTemplate = async (templateId) => {
    if (confirm('Are you sure you want to delete this template?')) {
        try {
            const response = await fetch(`/admin/templates/${templateId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            });

            const result = await response.json();
            
            if (result.success) {
                fetchTemplates(); // Refresh the list
            }
        } catch (error) {
            console.error('Error deleting template:', error);
        }
    }
};

const useTemplate = (template) => {
    // Navigate to designer with this template
    router.get(`/designer/t-shirt?template=${template.id}`);
};

onMounted(() => {
    fetchTemplates();
});
</script>