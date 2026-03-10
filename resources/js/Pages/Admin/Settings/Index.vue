<script setup>
import { ref, watch } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/Admin.vue';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    settings: Object,
});

const toast = useToast();

const form = useForm({
    site_name: props.settings?.site_name || '',
    site_logo: null,
    site_logo_preview: props.settings?.site_logo || null,
    site_favicon: null,
    site_favicon_preview: props.settings?.site_favicon || null,
    site_description: props.settings?.site_description || '',
    site_email: props.settings?.site_email || '',
    site_phone: props.settings?.site_phone || '',
    site_address: props.settings?.site_address || '',
    site_currency: props.settings?.site_currency || 'USD',
    tax_rate: props.settings?.tax_rate || 0,
    maintenance_mode: props.settings?.maintenance_mode || false,
    maintenance_message: props.settings?.maintenance_message || '',
    facebook_url: props.settings?.facebook_url || '',
    twitter_url: props.settings?.twitter_url || '',
    instagram_url: props.settings?.instagram_url || '',
    linkedin_url: props.settings?.linkedin_url || '',
    meta_keywords: props.settings?.meta_keywords || '',
    meta_description: props.settings?.meta_description || '',
    analytics_id: props.settings?.analytics_id || '',
    enable_registration: props.settings?.enable_registration || true,
    enable_reviews: props.settings?.enable_reviews || true,
    products_per_page: props.settings?.products_per_page || 12,
    // Brand Colors
    brand_primary_color: props.settings?.brand_primary_color || '#1a1a2e',
    brand_secondary_color: props.settings?.brand_secondary_color || '#16213e',
    brand_accent_color: props.settings?.brand_accent_color || '#e94560',
    brand_bg_color: props.settings?.brand_bg_color || '#0f3460',
    brand_text_color: props.settings?.brand_text_color || '#ffffff',
    brand_tagline: props.settings?.brand_tagline || 'Elegance, Sewn to Perfection',
    brand_logo_woman: null,
    brand_logo_woman_preview: props.settings?.brand_logo_woman || null,
    brand_script_font: props.settings?.brand_script_font || 'brand-script',
    brand_regular_font: props.settings?.brand_regular_font || 'brand-elegant',
});

// Settings categories for tab navigation
const activeTab = ref('general');
const tabs = [
    { id: 'general', name: 'General', icon: 'pi pi-home' },
    { id: 'branding', name: 'Branding', icon: 'pi pi-palette' },
    { id: 'contact', name: 'Contact', icon: 'pi pi-phone' },
    { id: 'social', name: 'Social Media', icon: 'pi pi-share-alt' },
    { id: 'seo', name: 'SEO', icon: 'pi pi-search' },
    { id: 'store', name: 'Store', icon: 'pi pi-shopping-cart' },
    { id: 'maintenance', name: 'Maintenance', icon: 'pi pi-cog' }
];

const brandLogoFile = ref(null);
const siteLogoFile = ref(null);
const siteFaviconFile = ref(null);

const handleBrandLogoUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        brandLogoFile.value = file;
        form.brand_logo_woman = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            // We'll use the file object for upload, but keep preview data
            form.brand_logo_woman_preview = e.target.result;
        };
        reader.readAsDataURL(file);
        
        toast.add({
            severity: 'info',
            summary: 'Brand Logo Selected',
            detail: 'Brand logo selected for upload. Save settings to apply changes.',
            life: 3000
        });
    }
};

const handleSiteLogoUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        siteLogoFile.value = file;
        form.site_logo = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            form.site_logo_preview = e.target.result;
        };
        reader.readAsDataURL(file);
        
        toast.add({
            severity: 'info',
            summary: 'Site Logo Selected',
            detail: 'Site logo selected for upload. Save settings to apply changes.',
            life: 3000
        });
    }
};

const handleSiteFaviconUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        siteFaviconFile.value = file;
        form.site_favicon = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            form.site_favicon_preview = e.target.result;
        };
        reader.readAsDataURL(file);
        
        toast.add({
            severity: 'info',
            summary: 'Favicon Selected',
            detail: 'Favicon selected for upload. Save settings to apply changes.',
            life: 3000
        });
    }
};

const submit = () => {
    // Create FormData for file uploads
    const formData = new FormData();
    
    // Add all form fields
    Object.keys(form.data()).forEach(key => {
        // Skip preview fields
        if (!key.endsWith('_preview')) {
            const value = form[key];
            if (value !== null && value !== undefined) {
                if ((key === 'brand_logo_woman' || key === 'site_logo' || key === 'site_favicon') && value instanceof File) {
                    // Handle file uploads
                    formData.append(key, value);
                } else if (typeof value === 'boolean') {
                    formData.append(key, value ? '1' : '0');
                } else {
                    formData.append(key, value);
                }
            }
        }
    });
    
    form.post(route('admin.settings.update'), {
        data: formData,
        forceFormData: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Settings updated successfully',
                life: 3000
            });
        },
        onError: (errors) => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Failed to update settings',
                life: 3000
            });
        }
    });
};
</script>

<template>
    <Head title="Ahlam's Girls Settings" />
    <AdminLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="admin-card shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-brand-gold border-opacity-30">
                        <h2 class="font-brand-elegant text-3xl text-brand-gold mb-2">Site Settings</h2>
                        <p class="text-brand-rose text-sm">Manage your Ahlam's Girls boutique configuration</p>
                    </div>
                    
                    <form @submit.prevent="submit" class="p-6">
                        <!-- Tab Navigation -->
                        <div class="mb-8 border-b border-brand-gold border-opacity-20">
                            <nav class="flex space-x-8 overflow-x-auto pb-2">
                                <button
                                    v-for="tab in tabs"
                                    :key="tab.id"
                                    type="button"
                                    @click="activeTab = tab.id"
                                    :class="[
                                        'flex items-center px-1 py-4 text-sm font-medium whitespace-nowrap transition-colors',
                                        activeTab === tab.id
                                            ? 'text-brand-accent border-b-2 border-brand-accent'
                                            : 'text-brand-secondary hover:text-brand-primary hover:border-b-2 hover:border-brand-gold'
                                    ]"
                                >
                                    <i :class="tab.icon + ' mr-2'"></i>
                                    {{ tab.name }}
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="space-y-8">
                            <!-- General Tab -->
                            <div v-show="activeTab === 'general'" class="space-y-6">
                                <h3 class="font-brand-elegant text-2xl text-brand-primary border-b border-brand-gold border-opacity-20 pb-2">General Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Site Name <span class="text-red-500">*</span></label>
                                        <input
                                            v-model="form.site_name"
                                            type="text"
                                            placeholder="Enter your site name"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                            required
                                        />
                                        <span v-if="form.errors.site_name" class="text-red-500 text-sm mt-1">{{ form.errors.site_name }}</span>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Site Currency</label>
                                        <select
                                            v-model="form.site_currency"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        >
                                            <option value="USD">USD - US Dollar</option>
                                            <option value="EUR">EUR - Euro</option>
                                            <option value="GBP">GBP - British Pound</option>
                                            <option value="SAR">SAR - Saudi Riyal</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Tax Rate (%)</label>
                                        <input
                                            v-model="form.tax_rate"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="100"
                                            placeholder="0.00"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        />
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Site Description</label>
                                        <textarea
                                            v-model="form.site_description"
                                            rows="4"
                                            placeholder="Describe your boutique and services"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Branding Tab -->
                            <div v-show="activeTab === 'branding'" class="space-y-6">
                                <h3 class="font-brand-elegant text-2xl text-brand-primary border-b border-brand-gold border-opacity-20 pb-2">Branding & Identity</h3>
                                
                                <!-- Brand Colors -->
                                <div class="bg-brand-secondary bg-opacity-5 rounded-lg p-6 mb-6">
                                    <h4 class="font-brand-elegant text-lg text-brand-primary mb-4">Color Scheme</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-brand-primary mb-2">Primary</label>
                                            <input
                                                v-model="form.brand_primary_color"
                                                type="color"
                                                class="w-16 h-16 border-2 border-brand-gold border-opacity-30 rounded-lg cursor-pointer mx-auto block"
                                            />
                                            <span class="block text-xs text-brand-secondary mt-2 font-mono">{{ form.brand_primary_color }}</span>
                                        </div>
                                        
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-brand-primary mb-2">Secondary</label>
                                            <input
                                                v-model="form.brand_secondary_color"
                                                type="color"
                                                class="w-16 h-16 border-2 border-brand-gold border-opacity-30 rounded-lg cursor-pointer mx-auto block"
                                            />
                                            <span class="block text-xs text-brand-secondary mt-2 font-mono">{{ form.brand_secondary_color }}</span>
                                        </div>
                                        
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-brand-primary mb-2">Accent</label>
                                            <input
                                                v-model="form.brand_accent_color"
                                                type="color"
                                                class="w-16 h-16 border-2 border-brand-gold border-opacity-30 rounded-lg cursor-pointer mx-auto block"
                                            />
                                            <span class="block text-xs text-brand-secondary mt-2 font-mono">{{ form.brand_accent_color }}</span>
                                        </div>
                                        
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-brand-primary mb-2">Background</label>
                                            <input
                                                v-model="form.brand_bg_color"
                                                type="color"
                                                class="w-16 h-16 border-2 border-brand-gold border-opacity-30 rounded-lg cursor-pointer mx-auto block"
                                            />
                                            <span class="block text-xs text-brand-secondary mt-2 font-mono">{{ form.brand_bg_color }}</span>
                                        </div>
                                        
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-brand-primary mb-2">Text</label>
                                            <input
                                                v-model="form.brand_text_color"
                                                type="color"
                                                class="w-16 h-16 border-2 border-brand-gold border-opacity-30 rounded-lg cursor-pointer mx-auto block"
                                            />
                                            <span class="block text-xs text-brand-secondary mt-2 font-mono">{{ form.brand_text_color }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Brand Identity -->
                                <div class="bg-brand-secondary bg-opacity-5 rounded-lg p-6">
                                    <h4 class="font-brand-elegant text-lg text-brand-primary mb-4">Brand Identity</h4>
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-medium text-brand-primary mb-2">Brand Tagline</label>
                                            <input
                                                v-model="form.brand_tagline"
                                                type="text"
                                                placeholder="Elegance, Sewn to Perfection"
                                                class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                            />
                                        </div>
                                        
                                        <!-- Logo Uploads -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Site Logo -->
                                            <div>
                                                <label class="block text-sm font-medium text-brand-primary mb-2">Site Logo</label>
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex-1">
                                                        <input
                                                            type="file"
                                                            accept="image/*"
                                                            @change="handleSiteLogoUpload"
                                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                                        />
                                                        <p class="text-xs text-brand-secondary mt-1">Upload your main site logo (PNG, JPG, SVG)</p>
                                                    </div>
                                                    <div v-if="form.site_logo_preview" class="w-24 h-24 border-2 border-brand-gold border-opacity-30 rounded-lg overflow-hidden flex items-center justify-center bg-brand-secondary bg-opacity-5">
                                                        <img :src="form.site_logo_preview" :alt="'Site Logo'" class="w-16 h-16 object-contain" />
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Brand Logo -->
                                            <div>
                                                <label class="block text-sm font-medium text-brand-primary mb-2">Brand Logo</label>
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex-1">
                                                        <input
                                                            type="file"
                                                            accept="image/*"
                                                            @change="handleBrandLogoUpload"
                                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                                        />
                                                        <p class="text-xs text-brand-secondary mt-1">Upload your Ahlam's Girls logo (PNG, JPG, SVG)</p>
                                                    </div>
                                                    <div v-if="form.brand_logo_woman_preview" class="w-24 h-24 border-2 border-brand-gold border-opacity-30 rounded-lg overflow-hidden flex items-center justify-center bg-brand-secondary bg-opacity-5">
                                                        <img :src="form.brand_logo_woman_preview" :alt="'Ahlam\'s Girls Logo'" class="w-16 h-16 object-contain" />
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Favicon -->
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-brand-primary mb-2">Favicon</label>
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex-1">
                                                        <input
                                                            type="file"
                                                            accept="image/*,.ico"
                                                            @change="handleSiteFaviconUpload"
                                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                                        />
                                                        <p class="text-xs text-brand-secondary mt-1">Upload favicon (PNG, JPG, ICO - 32x32px recommended)</p>
                                                    </div>
                                                    <div v-if="form.site_favicon_preview" class="w-16 h-16 border-2 border-brand-gold border-opacity-30 rounded-lg overflow-hidden flex items-center justify-center bg-brand-secondary bg-opacity-5">
                                                        <img :src="form.site_favicon_preview" :alt="'Favicon'" class="w-8 h-8 object-contain" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Elegant Woman Silhouette Preview -->
                                        <div class="mt-6 p-4 bg-brand-secondary bg-opacity-5 rounded-lg border border-brand-gold border-opacity-20">
                                            <h5 class="font-brand-elegant text-md text-brand-primary mb-3">Ahlam's Girls Brand Preview</h5>
                                            <div class="flex items-center justify-center space-x-4">
                                                <div class="text-center">
                                                    <img src="/images/ahlams-girls-logo.svg" alt="Ahlam's Girls Logo" class="w-24 h-24 mx-auto" />
                                                    <p class="font-brand-elegant text-lg text-brand-primary mt-2">Ahlam's</p>
                                                    <p class="font-bold text-brand-secondary">GIRLS</p>
                                                    <p class="text-xs text-brand-accent italic">{{ form.brand_tagline }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Tab -->
                            <div v-show="activeTab === 'contact'" class="space-y-6">
                                <h3 class="font-brand-elegant text-2xl text-brand-primary border-b border-brand-gold border-opacity-20 pb-2">Contact Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Email Address</label>
                                        <input
                                            v-model="form.site_email"
                                            type="email"
                                            placeholder="info@yourboutique.com"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Phone Number</label>
                                        <input
                                            v-model="form.site_phone"
                                            type="text"
                                            placeholder="+1 (555) 123-4567"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        />
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Physical Address</label>
                                        <textarea
                                            v-model="form.site_address"
                                            rows="3"
                                            placeholder="123 Fashion Street, Style City, SC 12345"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media Tab -->
                            <div v-show="activeTab === 'social'" class="space-y-6">
                                <h3 class="font-brand-elegant text-2xl text-brand-primary border-b border-brand-gold border-opacity-20 pb-2">Social Media Profiles</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Facebook URL</label>
                                        <input
                                            v-model="form.facebook_url"
                                            type="url"
                                            placeholder="https://facebook.com/yourboutique"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Twitter URL</label>
                                        <input
                                            v-model="form.twitter_url"
                                            type="url"
                                            placeholder="https://twitter.com/yourboutique"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Instagram URL</label>
                                        <input
                                            v-model="form.instagram_url"
                                            type="url"
                                            placeholder="https://instagram.com/yourboutique"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-brand-primary mb-2">LinkedIn URL</label>
                                        <input
                                            v-model="form.linkedin_url"
                                            type="url"
                                            placeholder="https://linkedin.com/company/yourboutique"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Tab -->
                            <div v-show="activeTab === 'seo'" class="space-y-6">
                                <h3 class="font-brand-elegant text-2xl text-brand-primary border-b border-brand-gold border-opacity-20 pb-2">Search Engine Optimization</h3>
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Meta Keywords</label>
                                        <input
                                            v-model="form.meta_keywords"
                                            type="text"
                                            placeholder="fashion, women, clothing, boutique, elegant"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Meta Description</label>
                                        <textarea
                                            v-model="form.meta_description"
                                            rows="4"
                                            placeholder="Brief description of your boutique for search engines"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        ></textarea>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Google Analytics ID</label>
                                        <input
                                            v-model="form.analytics_id"
                                            type="text"
                                            placeholder="GA-XXXXXXXXX or G-XXXXXXXXXX"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Store Tab -->
                            <div v-show="activeTab === 'store'" class="space-y-6">
                                <h3 class="font-brand-elegant text-2xl text-brand-primary border-b border-brand-gold border-opacity-20 pb-2">Store Configuration</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Products Per Page</label>
                                        <input
                                            v-model="form.products_per_page"
                                            type="number"
                                            min="1"
                                            max="100"
                                            placeholder="12"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        />
                                    </div>
                                    
                                    <div class="flex items-center pt-6">
                                        <input
                                            v-model="form.enable_registration"
                                            type="checkbox"
                                            class="h-5 w-5 text-brand-accent focus:ring-brand-accent border-brand-gold border-opacity-30 rounded"
                                        />
                                        <label class="ml-3 block text-sm text-brand-primary">Enable Customer Registration</label>
                                    </div>
                                    
                                    <div class="flex items-center pt-6">
                                        <input
                                            v-model="form.enable_reviews"
                                            type="checkbox"
                                            class="h-5 w-5 text-brand-accent focus:ring-brand-accent border-brand-gold border-opacity-30 rounded"
                                        />
                                        <label class="ml-3 block text-sm text-brand-primary">Enable Product Reviews</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Maintenance Tab -->
                            <div v-show="activeTab === 'maintenance'" class="space-y-6">
                                <h3 class="font-brand-elegant text-2xl text-brand-primary border-b border-brand-gold border-opacity-20 pb-2">Maintenance Mode</h3>
                                <div class="space-y-6">
                                    <div class="flex items-center">
                                        <input
                                            v-model="form.maintenance_mode"
                                            type="checkbox"
                                            class="h-5 w-5 text-brand-accent focus:ring-brand-accent border-brand-gold border-opacity-30 rounded"
                                        />
                                        <label class="ml-3 block text-sm text-brand-primary">Enable Maintenance Mode</label>
                                    </div>
                                    
                                    <div v-if="form.maintenance_mode">
                                        <label class="block text-sm font-medium text-brand-primary mb-2">Maintenance Message</label>
                                        <textarea
                                            v-model="form.maintenance_message"
                                            rows="4"
                                            placeholder="We are currently updating our site. Please check back soon!"
                                            class="w-full px-4 py-3 border border-brand-gold border-opacity-30 rounded-lg focus:ring-2 focus:ring-brand-accent focus:border-transparent bg-neumorphic text-brand-primary transition-all"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-8 border-t border-brand-gold border-opacity-20 mt-8">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-8 py-3 btn-luxury font-medium rounded-lg hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-brand-accent focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center"
                            >
                                <span v-if="form.processing" class="flex items-center">
                                    <i class="pi pi-spin pi-spinner mr-2"></i>
                                    Saving...
                                </span>
                                <span v-else class="flex items-center">
                                    <i class="pi pi-save mr-2"></i>
                                    Save Settings
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
