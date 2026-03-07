<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-neumorphic-text">Permissions Management</h1>
            <p class="text-neumorphic-text">Manage system permissions</p>
          </div>
          <button
            @click="openCreateModal"
            class="px-4 py-2 bg-green-600 text-white rounded neumorphic-btn"
          >
            Add Permission
          </button>
        </div>
      </div>

      <!-- Permissions Table -->
      <div class="neumorphic overflow-hidden rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-neumorphic">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                Slug
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                Resource
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                Action
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-neumorphic divide-y divide-gray-200">
            <tr v-for="permission in permissions.data" :key="permission.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text font-medium">
                {{ permission.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                {{ permission.slug }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                {{ permission.resource }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                {{ permission.action }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                <span 
                  :class="{
                    'bg-green-100 text-green-800': permission.is_active,
                    'bg-red-100 text-red-800': !permission.is_active
                  }"
                  class="px-2 py-1 rounded-full text-xs"
                >
                  {{ permission.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                <div class="flex space-x-2">
                  <button
                    @click="openEditModal(permission)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    Edit
                  </button>
                  <button
                    @click="toggleStatus(permission)"
                    class="text-yellow-600 hover:text-yellow-900"
                  >
                    {{ permission.is_active ? 'Deactivate' : 'Activate' }}
                  </button>
                  <button
                    @click="deletePermission(permission)"
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
              Showing {{ permissions.from }} to {{ permissions.to }} of {{ permissions.total }} results
            </div>
            <div class="flex space-x-2">
              <button
                v-for="link in permissions.links.slice(1, permissions.links.length - 1)"
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

      <!-- Permission Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
          <h3 class="text-lg font-semibold mb-4 text-neumorphic-text">
            {{ editingPermission ? 'Edit Permission' : 'Create Permission' }}
          </h3>
          
          <form @submit.prevent="savePermission">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-neumorphic-text mb-1">Name</label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full p-2 border rounded neumorphic-btn"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-neumorphic-text mb-1">Slug</label>
                <input
                  v-model="form.slug"
                  type="text"
                  required
                  class="w-full p-2 border rounded neumorphic-btn"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-neumorphic-text mb-1">Resource</label>
                <input
                  v-model="form.resource"
                  type="text"
                  required
                  class="w-full p-2 border rounded neumorphic-btn"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-neumorphic-text mb-1">Action</label>
                <input
                  v-model="form.action"
                  type="text"
                  required
                  class="w-full p-2 border rounded neumorphic-btn"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-neumorphic-text mb-1">Description</label>
                <textarea
                  v-model="form.description"
                  class="w-full p-2 border rounded neumorphic-btn"
                ></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium text-neumorphic-text mb-1">Status</label>
                <select
                  v-model="form.is_active"
                  class="w-full p-2 border rounded neumorphic-btn"
                >
                  <option :value="true">Active</option>
                  <option :value="false">Inactive</option>
                </select>
              </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-2">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 bg-gray-600 text-white rounded neumorphic-btn"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded neumorphic-btn"
              >
                {{ editingPermission ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/Admin.vue';
import { router } from '@inertiajs/vue3';

export default {
  name: 'AdminPermissionsIndex',
  
  components: {
    AdminLayout,
  },
  
  props: {
    permissions: Object,
  },
  
  data() {
    return {
      showModal: false,
      editingPermission: null,
      form: {
        name: '',
        slug: '',
        resource: '',
        action: '',
        description: '',
        is_active: true,
      },
    };
  },
  
  methods: {
    openCreateModal() {
      this.editingPermission = null;
      this.form = {
        name: '',
        slug: '',
        resource: '',
        action: '',
        description: '',
        is_active: true,
      };
      this.showModal = true;
    },
    
    openEditModal(permission) {
      this.editingPermission = permission;
      this.form = {
        name: permission.name,
        slug: permission.slug,
        resource: permission.resource,
        action: permission.action,
        description: permission.description,
        is_active: permission.is_active,
      };
      this.showModal = true;
    },
    
    closeModal() {
      this.showModal = false;
      this.editingPermission = null;
    },
    
    savePermission() {
      if (this.editingPermission) {
        // Update existing permission
        router.put(`/admin/permissions/${this.editingPermission.id}`, this.form, {
          onSuccess: () => {
            this.closeModal();
            router.reload();
          },
          onError: (errors) => {
            console.error('Validation errors:', errors);
          }
        });
      } else {
        // Create new permission
        router.post('/admin/permissions', this.form, {
          onSuccess: () => {
            this.closeModal();
            router.reload();
          },
          onError: (errors) => {
            console.error('Validation errors:', errors);
          }
        });
      }
    },
    
    toggleStatus(permission) {
      router.patch(`/admin/permissions/${permission.id}/toggle-status`, {
        is_active: !permission.is_active
      }, {
        onSuccess: () => {
          router.reload();
        }
      });
    },
    
    deletePermission(permission) {
      if (confirm(`Are you sure you want to delete the permission "${permission.name}"?`)) {
        router.delete(`/admin/permissions/${permission.id}`, {
          onSuccess: () => {
            router.reload();
          }
        });
      }
    },
    
    changePage(url) {
      if (url) {
        router.get(url, {}, { preserveScroll: true });
      }
    },
  },
};
</script>