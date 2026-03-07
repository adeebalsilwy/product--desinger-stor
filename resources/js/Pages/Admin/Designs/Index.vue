<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-neumorphic-text">Design Management</h1>
        <p class="text-neumorphic-text">Manage all user designs and templates</p>
      </div>

      <!-- Filters -->
      <div class="neumorphic p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-neumorphic-text mb-1">Search</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Search designs..."
              class="w-full p-2 border rounded neumorphic-btn"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-neumorphic-text mb-1">Product Type</label>
            <select
              v-model="filters.product_type"
              class="w-full p-2 border rounded neumorphic-btn"
            >
              <option value="">All Types</option>
              <option v-for="type in productTypes" :key="type.id" :value="type.id">
                {{ type.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-neumorphic-text mb-1">User</label>
            <select
              v-model="filters.user_id"
              class="w-full p-2 border rounded neumorphic-btn"
            >
              <option value="">All Users</option>
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-neumorphic-text mb-1">Visibility</label>
            <select
              v-model="filters.is_public"
              class="w-full p-2 border rounded neumorphic-btn"
            >
              <option value="">All</option>
              <option value="1">Public</option>
              <option value="0">Private</option>
            </select>
          </div>
        </div>
        <div class="mt-4 flex space-x-2">
          <button
            @click="applyFilters"
            class="px-4 py-2 bg-blue-600 text-white rounded neumorphic-btn"
          >
            Apply Filters
          </button>
          <button
            @click="clearFilters"
            class="px-4 py-2 bg-gray-600 text-white rounded neumorphic-btn"
          >
            Clear Filters
          </button>
        </div>
      </div>

      <!-- Designs Table -->
      <div class="neumorphic overflow-hidden rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-neumorphic">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                ID
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                User
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                Type
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                Visibility
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-neumorphic divide-y divide-gray-200">
            <tr v-for="design in designs.data" :key="design.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                {{ design.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                {{ design.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                {{ design.user?.name || 'Unknown' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                {{ design.productType?.name || 'Unknown' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                <span 
                  :class="{
                    'bg-green-100 text-green-800': design.is_public,
                    'bg-red-100 text-red-800': !design.is_public
                  }"
                  class="px-2 py-1 rounded-full text-xs"
                >
                  {{ design.is_public ? 'Public' : 'Private' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                <div class="flex space-x-2">
                  <button
                    @click="toggleVisibility(design)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    {{ design.is_public ? 'Make Private' : 'Make Public' }}
                  </button>
                  <button
                    @click="makeFeatured(design)"
                    class="text-yellow-600 hover:text-yellow-900"
                  >
                    Make Template
                  </button>
                  <button
                    @click="deleteDesign(design)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-3 bg-neumorphic border-t border-neumorphic">
          <div class="flex justify-between items-center">
            <div class="text-sm text-neumorphic-text">
              Showing {{ designs.from }} to {{ designs.to }} of {{ designs.total }} results
            </div>
            <div class="flex space-x-2">
              <button
                v-for="link in designs.links.slice(1, designs.links.length - 1)"
                :key="link.label"
                :disabled="!link.url || link.active"
                @click="changePage(link.url)"
                :class="{
                  'bg-blue-600 text-white': link.active,
                  'bg-gray-200 text-gray-800 hover:bg-gray-300': !link.active && link.url,
                  'bg-gray-100 text-gray-400 cursor-not-allowed': !link.url
                }"
                class="px-3 py-1 rounded neumorphic-btn disabled:opacity-50"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/Admin.vue';
import { router } from '@inertiajs/vue3';

export default {
  name: 'AdminDesignsIndex',
  
  components: {
    AdminLayout,
  },
  
  props: {
    designs: Object,
    filters: Object,
    productTypes: Array,
    users: Array,
  },
  
  data() {
    return {
      // Local filters to allow live updates before submission
      localFilters: { ...this.filters },
    };
  },
  
  methods: {
    applyFilters() {
      router.get('/admin/designs', this.localFilters, {
        preserveScroll: true,
        replace: true,
      });
    },
    
    clearFilters() {
      router.get('/admin/designs', {}, {
        preserveScroll: true,
        replace: true,
      });
    },
    
    changePage(url) {
      if (url) {
        router.get(url, {}, { preserveScroll: true });
      }
    },
    
    toggleVisibility(design) {
      router.patch(`/admin/designs/${design.id}/toggle-visibility`, {}, {
        onSuccess: () => {
          // Refresh the page to update the design list
          router.reload();
        }
      });
    },
    
    makeFeatured(design) {
      router.patch(`/admin/designs/${design.id}/make-featured`, {}, {
        onSuccess: () => {
          // Refresh the page to update the design list
          router.reload();
        }
      });
    },
    
    deleteDesign(design) {
      if (confirm('Are you sure you want to delete this design?')) {
        router.delete(`/admin/designs/${design.id}`, {
          onSuccess: () => {
            // Refresh the page to update the design list
            router.reload();
          }
        });
      }
    },
  },
};
</script>