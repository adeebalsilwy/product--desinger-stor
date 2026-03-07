<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-neumorphic-text">Design Details</h1>
        <p class="text-neumorphic-text">{{ design.name }}</p>
      </div>

      <!-- Design Info Card -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Design Preview -->
        <div class="lg:col-span-2">
          <div class="neumorphic p-6">
            <h2 class="text-xl font-semibold mb-4 text-neumorphic-text">Design Preview</h2>
            <div class="aspect-video bg-gray-100 rounded-lg flex items-center justify-center">
              <div v-if="design.preview_image_url">
                <img :src="design.preview_image_url" :alt="design.name" class="max-w-full max-h-96 object-contain" />
              </div>
              <div v-else class="text-gray-500">
                No preview available
              </div>
            </div>
          </div>
        </div>

        <!-- Design Details -->
        <div>
          <div class="neumorphic p-6">
            <h2 class="text-xl font-semibold mb-4 text-neumorphic-text">Details</h2>
            <dl class="space-y-3">
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">ID:</dt>
                <dd class="text-neumorphic-text">{{ design.id }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Name:</dt>
                <dd class="text-neumorphic-text">{{ design.name }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Created:</dt>
                <dd class="text-neumorphic-text">{{ formatDate(design.created_at) }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Updated:</dt>
                <dd class="text-neumorphic-text">{{ formatDate(design.updated_at) }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">User:</dt>
                <dd class="text-neumorphic-text">{{ design.user?.name || 'Unknown' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Product Type:</dt>
                <dd class="text-neumorphic-text">{{ design.productType?.name || 'Unknown' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Visibility:</dt>
                <dd>
                  <span 
                    :class="{
                      'bg-green-100 text-green-800': design.is_public,
                      'bg-red-100 text-red-800': !design.is_public
                    }"
                    class="px-2 py-1 rounded-full text-xs"
                  >
                    {{ design.is_public ? 'Public' : 'Private' }}
                  </span>
                </dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Template:</dt>
                <dd>
                  <span 
                    :class="{
                      'bg-yellow-100 text-yellow-800': design.is_template,
                      'bg-gray-100 text-gray-800': !design.is_template
                    }"
                    class="px-2 py-1 rounded-full text-xs"
                  >
                    {{ design.is_template ? 'Yes' : 'No' }}
                  </span>
                </dd>
              </div>
            </dl>

            <!-- Actions -->
            <div class="mt-6 space-y-2">
              <button
                @click="toggleVisibility"
                class="w-full px-4 py-2 rounded neumorphic-btn"
                :class="design.is_public ? 'bg-red-500 text-white' : 'bg-green-500 text-white'"
              >
                {{ design.is_public ? 'Make Private' : 'Make Public' }}
              </button>
              <button
                @click="toggleTemplate"
                class="w-full px-4 py-2 rounded neumorphic-btn"
                :class="design.is_template ? 'bg-gray-500 text-white' : 'bg-yellow-500 text-white'"
              >
                {{ design.is_template ? 'Remove from Templates' : 'Make Template' }}
              </button>
              <button
                @click="deleteDesign"
                class="w-full px-4 py-2 bg-red-500 text-white rounded neumorphic-btn"
              >
                Delete Design
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Orders -->
      <div v-if="design.orderItems && design.orderItems.length > 0" class="mt-8 neumorphic p-6">
        <h2 class="text-xl font-semibold mb-4 text-neumorphic-text">Related Orders</h2>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                  Order ID
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                  Customer
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                  Date
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                  Status
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="item in design.orderItems" :key="item.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                  <router-link 
                    :to="{ name: 'admin.orders.show', params: { id: item.order.id } }"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    #{{ item.order.id }}
                  </router-link>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                  {{ item.order.customer?.name || 'Unknown' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                  {{ formatDate(item.order.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                  <span class="px-2 py-1 rounded-full text-xs"
                    :class="getStatusClass(item.order.status)">
                    {{ item.order.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/Admin.vue';
import { router } from '@inertiajs/vue3';

export default {
  name: 'AdminDesignsShow',
  
  components: {
    AdminLayout,
  },
  
  props: {
    design: Object,
  },
  
  methods: {
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },
    
    getStatusClass(status) {
      const statusClasses = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        shipped: 'bg-indigo-100 text-indigo-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
      };
      return statusClasses[status] || 'bg-gray-100 text-gray-800';
    },
    
    toggleVisibility() {
      router.patch(`/admin/designs/${this.design.id}/toggle-visibility`, {}, {
        onSuccess: () => {
          // Reload the design data
          router.reload();
        }
      });
    },
    
    toggleTemplate() {
      router.patch(`/admin/designs/${this.design.id}/make-featured`, {}, {
        onSuccess: () => {
          // Reload the design data
          router.reload();
        }
      });
    },
    
    deleteDesign() {
      if (confirm('Are you sure you want to delete this design? This action cannot be undone.')) {
        router.delete(`/admin/designs/${this.design.id}`, {
          onSuccess: () => {
            // Go back to the designs list
            router.visit('/admin/designs');
          }
        });
      }
    },
  },
};
</script>