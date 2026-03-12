<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-neumorphic-text">User Details</h1>
            <p class="text-neumorphic-text">{{ user.name }}</p>
          </div>
          <Link :href="route('admin.users.index')" class="px-4 py-2 bg-gray-600 text-white rounded neumorphic-btn">
            Back to Users
          </Link>
        </div>
      </div>

      <!-- User Info Card -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- User Details -->
        <div class="lg:col-span-2">
          <div class="neumorphic p-6">
            <h2 class="text-xl font-semibold mb-4 text-neumorphic-text">User Information</h2>
            <dl class="space-y-3">
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">ID:</dt>
                <dd class="text-neumorphic-text">{{ user.id }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Name:</dt>
                <dd class="text-neumorphic-text">{{ user.name }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Email:</dt>
                <dd class="text-neumorphic-text">{{ user.email }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Phone:</dt>
                <dd class="text-neumorphic-text">{{ user.phone || 'Not provided' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Address:</dt>
                <dd class="text-neumorphic-text">{{ user.address || 'Not provided' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">City:</dt>
                <dd class="text-neumorphic-text">{{ user.city || 'Not provided' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Country:</dt>
                <dd class="text-neumorphic-text">{{ user.country || 'Not provided' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">ZIP Code:</dt>
                <dd class="text-neumorphic-text">{{ user.zip_code || 'Not provided' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Status:</dt>
                <dd>
                  <span 
                    :class="{
                      'bg-green-100 text-green-800': user.is_active,
                      'bg-red-100 text-red-800': !user.is_active
                    }"
                    class="px-2 py-1 rounded-full text-xs"
                  >
                    {{ user.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Email Verified:</dt>
                <dd>
                  <span 
                    :class="{
                      'bg-green-100 text-green-800': user.email_verified_at,
                      'bg-yellow-100 text-yellow-800': !user.email_verified_at
                    }"
                    class="px-2 py-1 rounded-full text-xs"
                  >
                    {{ user.email_verified_at ? 'Yes' : 'No' }}
                  </span>
                </dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Created:</dt>
                <dd class="text-neumorphic-text">{{ formatDate(user.created_at) }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="font-medium text-neumorphic-text">Updated:</dt>
                <dd class="text-neumorphic-text">{{ formatDate(user.updated_at) }}</dd>
              </div>
            </dl>
          </div>
        </div>

        <!-- User Actions -->
        <div>
          <div class="neumorphic p-6">
            <h2 class="text-xl font-semibold mb-4 text-neumorphic-text">Actions</h2>
            <div class="space-y-2">
              <Link
                :href="route('admin.users.edit', user.id)"
                class="w-full px-4 py-2 bg-blue-600 text-white rounded neumorphic-btn block text-center"
              >
                Edit User
              </Link>
              <button
                @click="toggleStatus"
                class="w-full px-4 py-2 rounded neumorphic-btn"
                :class="user.is_active ? 'bg-red-500 text-white' : 'bg-green-500 text-white'"
                :disabled="!user.id"
              >
                {{ user.is_active ? 'Deactivate User' : 'Activate User' }}
              </button>
              <button
                @click="deleteUser"
                class="w-full px-4 py-2 bg-red-500 text-white rounded neumorphic-btn"
                :disabled="!user.id || user.id === authUser.id"
              >
                Delete User
              </button>
            </div>
          </div>

          <!-- Roles and Permissions -->
          <div class="neumorphic p-6 mt-6">
            <h2 class="text-xl font-semibold mb-4 text-neumorphic-text">Roles & Permissions</h2>
            <div class="space-y-4">
              <div>
                <h3 class="font-medium text-neumorphic-text mb-2">Assigned Roles:</h3>
                <div v-if="user.roles && user.roles.length > 0" class="space-y-1">
                  <div
                    v-for="role in user.roles"
                    :key="role.id"
                    class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs inline-block mr-2 mb-2"
                  >
                    {{ role.name }}
                  </div>
                </div>
                <div v-else class="text-gray-500 text-sm">
                  No roles assigned
                </div>
              </div>
              <div>
                <h3 class="font-medium text-neumorphic-text mb-2">Direct Permissions:</h3>
                <div v-if="user.permissions && user.permissions.length > 0" class="space-y-1">
                  <div
                    v-for="permission in user.permissions"
                    :key="permission.id"
                    class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs inline-block mr-2 mb-2"
                  >
                    {{ permission.name }}
                  </div>
                </div>
                <div v-else class="text-gray-500 text-sm">
                  No direct permissions
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Data Tabs -->
      <div class="mt-8 neumorphic rounded-lg">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8 px-6">
            <button
              @click="activeTab = 'orders'"
              :class="{
                'border-neumorphic-accent text-neumorphic-text border-b-2': activeTab === 'orders',
                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'orders'
              }"
              class="whitespace-nowrap py-4 px-1 text-sm font-medium"
            >
              Orders ({{ user.orders_count || 0 }})
            </button>
            <button
              @click="activeTab = 'designs'"
              :class="{
                'border-neumorphic-accent text-neumorphic-text border-b-2': activeTab === 'designs',
                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'designs'
              }"
              class="whitespace-nowrap py-4 px-1 text-sm font-medium"
            >
              Designs ({{ user.designs_count || 0 }})
            </button>
          </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
          <!-- Orders Tab -->
          <div v-show="activeTab === 'orders'" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-neumorphic">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                    ID
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                    Total Amount
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                    Date
                  </th>
                </tr>
              </thead>
              <tbody class="bg-neumorphic divide-y divide-gray-200">
                <tr v-for="order in orders?.data || []" :key="order.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                    {{ order.id }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                    {{ formatCurrency(order.total_amount) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                    <span class="px-2 py-1 rounded-full text-xs"
                      :class="getStatusClass(order.status)">
                      {{ order.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                    {{ formatDate(order.created_at) }}
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="!orders || !orders.data || orders.data.length === 0" class="text-center py-4 text-gray-500">
              No orders found
            </div>
          </div>

          <!-- Designs Tab -->
          <div v-show="activeTab === 'designs'" class="overflow-x-auto">
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
                    Type
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                    Visibility
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-neumorphic-text uppercase tracking-wider">
                    Date
                  </th>
                </tr>
              </thead>
              <tbody class="bg-neumorphic divide-y divide-gray-200">
                <tr v-for="design in designs?.data || []" :key="design.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                    {{ design.id }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-neumorphic-text">
                    {{ design.name }}
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
                    {{ formatDate(design.created_at) }}
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="!designs || !designs.data || designs.data.length === 0" class="text-center py-4 text-gray-500">
              No designs found
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/Admin.vue';
import { router, Link } from '@inertiajs/vue3';

export default {
  name: 'AdminUsersShow',
  
  components: {
    AdminLayout,
    Link,
  },
  
  props: {
    user: Object,
    orders: Object,
    designs: Object,
    authUser: Object,
  },
  
  data() {
    return {
      activeTab: 'orders',
    };
  },
  
  mounted() {
    // Ensure user data exists
    if (!this.user) {
      console.error('User data not loaded properly');
      return;
    }
    
    // Initialize user data if needed
    if (!this.user.orders) {
      this.user.orders = { data: [] };
    }
    if (!this.user.designs) {
      this.user.designs = { data: [] };
    }
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
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
      }).format(amount);
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
    
    toggleStatus() {
      if (!this.user || !this.user.id) {
        alert('User data not available');
        return;
      }
      
      router.patch(`/admin/users/${this.user.id}/toggle-status`, {}, {
        onSuccess: () => {
          // Reload the user data
          router.reload();
        },
        onError: (errors) => {
          console.error('Status update error:', errors);
          alert('Error updating user status');
        }
      });
    },
    
    deleteUser() {
      // Check if user exists and prevent self-deletion
      if (!this.user || !this.user.id) {
        alert('User data not available');
        return;
      }
      
      if (this.user.id === this.authUser?.id) {
        alert('You cannot delete your own account');
        return;
      }
      
      if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        router.delete(`/admin/users/${this.user.id}`, {
          onSuccess: () => {
            // Go back to the users list
            router.visit('/admin/users');
          },
          onError: (errors) => {
            console.error('Delete error:', errors);
            alert('Error deleting user');
          }
        });
      }
    },
  },
};
</script>