<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-neumorphic-text">Roles Management</h1>
            <p class="text-neumorphic-text">Manage user roles and their permissions</p>
          </div>
          <button
            @click="openCreateModal"
            class="px-4 py-2 bg-green-600 text-white rounded neumorphic-btn"
          >
            Add Role
          </button>
        </div>
      </div>

      <!-- Bulk Actions -->
      <div v-if="selectedUsers.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex flex-wrap items-center justify-between">
        <div class="mb-2 sm:mb-0">
          <span class="text-blue-800 font-medium">{{ selectedUsers.length }} user(s) selected</span>
        </div>
        <div class="flex flex-wrap gap-2">
          <select 
            v-model="selectedRoleForBulk"
            class="border rounded px-3 py-2 mr-2"
          >
            <option value="">Select Role</option>
            <option v-for="roleOption in roles.data" :key="roleOption.id" :value="roleOption.id">
              {{ roleOption.name }}
            </option>
          </select>
          <button 
            @click="bulkAssignRole"
            :disabled="!selectedRoleForBulk"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 disabled:opacity-50"
          >
            Assign Role
          </button>
          <button 
            @click="bulkRemoveRole"
            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 ml-2"
          >
            Remove Role
          </button>
          <button 
            @click="clearSelection"
            class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 ml-2"
          >
            Clear Selection
          </button>
        </div>
      </div>

      <!-- Roles Table -->
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
                Description
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                Users Count
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-neumorphic divide-y divide-gray-200">
            <tr v-for="role in roles.data" :key="role.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text font-medium">
                {{ role.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                {{ role.slug }}
              </td>
              <td class="px-6 py-4 text-sm text-neumorphic-text">
                {{ role.description }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                <span 
                  :class="{
                    'bg-green-100 text-green-800': role.is_active,
                    'bg-red-100 text-red-800': !role.is_active
                  }"
                  class="px-2 py-1 rounded-full text-xs"
                >
                  {{ role.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                {{ role.users_count || 0 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                <div class="flex space-x-2">
                  <button
                    @click="openEditModal(role)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    Edit
                  </button>
                  <button
                    @click="toggleStatus(role)"
                    class="text-yellow-600 hover:text-yellow-900"
                  >
                    {{ role.is_active ? 'Deactivate' : 'Activate' }}
                  </button>
                  <button
                    @click="deleteRole(role)"
                    class="text-red-600 hover:text-red-900"
                    :disabled="role.users_count > 0"
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
              Showing {{ roles.from }} to {{ roles.to }} of {{ roles.total }} results
            </div>
            <div class="flex space-x-2">
              <button
                v-for="link in roles.links.slice(1, roles.links.length - 1)"
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

      <!-- Role Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
          <h3 class="text-lg font-semibold mb-4 text-neumorphic-text">
            {{ editingRole ? 'Edit Role' : 'Create Role' }}
          </h3>
          
          <form @submit.prevent="saveRole">
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
                {{ editingRole ? 'Update' : 'Create' }}
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
  name: 'AdminRolesIndex',
  
  components: {
    AdminLayout,
  },
  
  props: {
    roles: Object,
  },
  
  data() {
    return {
      showModal: false,
      editingRole: null,
      form: {
        name: '',
        slug: '',
        description: '',
        is_active: true,
      },
      selectedUsers: [],
      selectedRoleForBulk: '',
    };
  },
  
  methods: {
    openCreateModal() {
      this.editingRole = null;
      this.form = {
        name: '',
        slug: '',
        description: '',
        is_active: true,
      };
      this.showModal = true;
    },
    
    openEditModal(role) {
      this.editingRole = role;
      this.form = {
        name: role.name,
        slug: role.slug,
        description: role.description,
        is_active: role.is_active,
      };
      this.showModal = true;
    },
    
    closeModal() {
      this.showModal = false;
      this.editingRole = null;
    },
    
    saveRole() {
      if (this.editingRole) {
        // Update existing role
        router.put(`/admin/roles/${this.editingRole.id}`, this.form, {
          onSuccess: () => {
            this.closeModal();
            router.reload();
          },
          onError: (errors) => {
            console.error('Validation errors:', errors);
          }
        });
      } else {
        // Create new role
        router.post('/admin/roles', this.form, {
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
    
    toggleStatus(role) {
      router.patch(`/admin/roles/${role.id}/toggle-status`, {
        is_active: !role.is_active
      }, {
        onSuccess: () => {
          router.reload();
        }
      });
    },
    
    deleteRole(role) {
      if (role.users_count > 0) {
        alert('Cannot delete role with assigned users');
        return;
      }
      
      if (confirm(`Are you sure you want to delete the role "${role.name}"?`)) {
        router.delete(`/admin/roles/${role.id}`, {
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
    
    clearSelection() {
      this.selectedUsers = [];
      this.selectedRoleForBulk = '';
    },
    
    async bulkAssignRole() {
      if (!this.selectedRoleForBulk) {
        alert('Please select a role to assign');
        return;
      }
      
      if (this.selectedUsers.length === 0) {
        alert('Please select at least one user');
        return;
      }
      
      if (confirm(`Are you sure you want to assign the selected role to ${this.selectedUsers.length} user(s)?`)) {
        router.post('/admin/users/bulk-assign-role', {
          user_ids: this.selectedUsers,
          role_id: this.selectedRoleForBulk,
        }, {
          onSuccess: () => {
            this.clearSelection();
            // Message is handled by Laravel
          },
          onError: (errors) => {
            console.error(errors);
          },
        });
      }
    },
    
    async bulkRemoveRole() {
      if (this.selectedUsers.length === 0) {
        alert('Please select at least one user');
        return;
      }
      
      if (confirm(`Are you sure you want to remove roles from ${this.selectedUsers.length} user(s)?`)) {
        router.post('/admin/users/bulk-remove-role', {
          user_ids: this.selectedUsers,
        }, {
          onSuccess: () => {
            this.clearSelection();
            // Message is handled by Laravel
          },
          onError: (errors) => {
            console.error(errors);
          },
        });
      }
    },
  },
};
</script>