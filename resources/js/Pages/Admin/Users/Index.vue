<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Users</h1>
          <p class="text-gray-600">Manage your platform users</p>
        </div>
        <Link 
          :href="route('admin.users.create')"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
        >
          Add User
        </Link>
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
            <option v-for="role in roles" :key="role.id" :value="role.id">
              {{ role.name }}
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

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form @submit.prevent="applyFilters" class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input 
              v-model="filters.search" 
              type="text" 
              placeholder="Search users..."
              class="w-full border rounded px-3 py-2"
            />
          </div>
          <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select 
              v-model="filters.role" 
              class="w-full border rounded px-3 py-2"
            >
              <option value="">All Roles</option>
              <option v-for="role in roles" :key="role.id" :value="role.name">
                {{ role.name }}
              </option>
            </select>
          </div>
          <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select 
              v-model="filters.status" 
              class="w-full border rounded px-3 py-2"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="flex items-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
              Apply Filters
            </button>
          </div>
        </form>
      </div>

      <!-- Users Table -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                <input 
                  type="checkbox" 
                  @change="selectAll"
                  :checked="selectedUsers.length === users.data.length && users.data.length > 0"
                  class="rounded"
                />
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in users.data" :key="user.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <input 
                  type="checkbox" 
                  :value="user.id"
                  v-model="selectedUsers"
                  class="rounded"
                />
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <div class="bg-gray-200 border-2 border-dashed rounded-xl w-10 h-10" />
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ user.role }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <StatusBadge :status="user.is_active ? 'active' : 'inactive'" />
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.total_orders || 0 }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                  <Link 
                    :href="route('admin.users.show', user.id)"
                    class="text-indigo-600 hover:text-indigo-900"
                  >
                    View
                  </Link>
                  <Link 
                    :href="route('admin.users.edit', user.id)"
                    class="text-green-600 hover:text-green-900"
                  >
                    Edit
                  </Link>
                  <button 
                    @click="toggleUserStatus(user)"
                    class="text-yellow-600 hover:text-yellow-900"
                    :disabled="user.id === $page.props.auth.user.id"
                  >
                    {{ user.is_active ? 'Deactivate' : 'Activate' }}
                  </button>
                  <button 
                    @click="deleteUser(user)"
                    class="text-red-600 hover:text-red-900"
                    :disabled="user.id === $page.props.auth.user.id"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
          <div class="flex-1 flex justify-between sm:hidden">
            <Link 
              :href="users.prev_page_url" 
              :class="{'opacity-50 cursor-not-allowed': !users.prev_page_url}"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Previous
            </Link>
            <Link 
              :href="users.next_page_url" 
              :class="{'opacity-50 cursor-not-allowed': !users.next_page_url}"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Next
            </Link>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Showing {{ users.from }} to {{ users.to }} of {{ users.total }} results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <Link
                  v-for="(link, index) in users.links.slice(1, -1)"
                  :key="index"
                  :href="link.url || '#'"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                    link.active 
                      ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                  ]"
                  v-html="link.label"
                />
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/Admin.vue';
import StatusBadge from '@/Components/Status.vue';
import { Link, router } from '@inertiajs/vue3';

export default {
  name: 'AdminUsersIndex',
  
  components: {
    AdminLayout,
    StatusBadge,
    Link,
  },
  
  props: {
    users: Object,
    filters: Object,
    roles: Array,
  },
  
  data() {
    return {
      filters: { ...this.filters },
      selectedUsers: [],
      selectedRoleForBulk: '',
    };
  },
  
  methods: {
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
      });
    },
    
    applyFilters() {
      // Submit form to apply filters
      this.$inertia.get('/admin/users', this.filters, {
        preserveState: true,
        preserveScroll: true,
      });
    },
    
    selectAll(event) {
      if (event.target.checked) {
        this.selectedUsers = this.users.data.map(user => user.id);
      } else {
        this.selectedUsers = [];
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
    
    toggleUserStatus(user) {
      if (user.id === this.$page.props.auth.user.id) {
        alert('You cannot change your own status');
        return;
      }
      
      if (confirm(`Are you sure you want to ${user.is_active ? 'deactivate' : 'activate'} this user?`)) {
        this.$inertia.patch(`/admin/users/${user.id}/toggle-status`, {}, {
          onSuccess: () => {
            // Success message is handled by Laravel
          },
          onError: (errors) => {
            console.error(errors);
          },
        });
      }
    },
    
    deleteUser(user) {
      if (user.id === this.$page.props.auth.user.id) {
        alert('You cannot delete your own account');
        return;
      }
      
      if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        this.$inertia.delete(`/admin/users/${user.id}`, {
          onSuccess: () => {
            // Success message is handled by Laravel
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