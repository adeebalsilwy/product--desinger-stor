<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="max-w-3xl mx-auto">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
          <p class="text-gray-600">Update user information</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
          <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  required
                  class="w-full border rounded px-3 py-2"
                  :class="{ 'border-red-500': errors.name }"
                />
                <div v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name[0] }}</div>
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input 
                  v-model="form.email" 
                  type="email" 
                  required
                  class="w-full border rounded px-3 py-2"
                  :class="{ 'border-red-500': errors.email }"
                />
                <div v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email[0] }}</div>
              </div>

              <!-- Password (Optional) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password (Optional)</label>
                <input 
                  v-model="form.password" 
                  type="password" 
                  minlength="8"
                  class="w-full border rounded px-3 py-2"
                  :class="{ 'border-red-500': errors.password }"
                />
                <div v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password[0] }}</div>
                <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
              </div>

              <!-- Password Confirmation -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input 
                  v-model="form.password_confirmation" 
                  type="password" 
                  class="w-full border rounded px-3 py-2"
                />
              </div>

              <!-- Role -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select 
                  v-model="form.role" 
                  required
                  class="w-full border rounded px-3 py-2"
                  :class="{ 'border-red-500': errors.role }"
                >
                  <option value="">Select Role</option>
                  <option v-for="role in roles" :key="role.id" :value="role.name">
                    {{ role.name }}
                  </option>
                </select>
                <div v-if="errors.role" class="text-red-500 text-sm mt-1">{{ errors.role[0] }}</div>
              </div>

              <!-- Status -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select 
                  v-model="form.is_active" 
                  class="w-full border rounded px-3 py-2"
                >
                  <option :value="true">Active</option>
                  <option :value="false">Inactive</option>
                </select>
              </div>

              <!-- Phone -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input 
                  v-model="form.phone" 
                  type="text" 
                  class="w-full border rounded px-3 py-2"
                />
              </div>

              <!-- Address -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input 
                  v-model="form.address" 
                  type="text" 
                  class="w-full border rounded px-3 py-2"
                />
              </div>

              <!-- City -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <input 
                  v-model="form.city" 
                  type="text" 
                  class="w-full border rounded px-3 py-2"
                />
              </div>

              <!-- Country -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                <input 
                  v-model="form.country" 
                  type="text" 
                  class="w-full border rounded px-3 py-2"
                />
              </div>

              <!-- Zip Code -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Zip Code</label>
                <input 
                  v-model="form.zip_code" 
                  type="text" 
                  class="w-full border rounded px-3 py-2"
                />
              </div>
            </div>

            <div class="mt-6 flex justify-between">
              <button 
                @click="deleteUser"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                :disabled="processing"
              >
                Delete User
              </button>
              <div class="flex space-x-4">
                <Link 
                  :href="route('admin.users.index')"
                  class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                >
                  Cancel
                </Link>
                <button 
                  type="submit" 
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                  :disabled="processing"
                >
                  <span v-if="processing">Updating...</span>
                  <span v-else>Update User</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/Admin.vue';
import { Link } from '@inertiajs/vue3';

export default {
  name: 'AdminUsersEdit',
  
  components: {
    AdminLayout,
    Link,
  },
  
  props: {
    user: Object,
    roles: Array,
  },
  
  data() {
    return {
      form: {
        name: this.user.name,
        email: this.user.email,
        password: '',
        password_confirmation: '',
        role: this.user.role,
        is_active: this.user.is_active,
        phone: this.user.phone || '',
        address: this.user.address || '',
        city: this.user.city || '',
        country: this.user.country || '',
        zip_code: this.user.zip_code || '',
      },
      errors: {},
      processing: false,
    };
  },
  
  methods: {
    submitForm() {
      this.processing = true;
      
      this.$inertia.put(`/admin/users/${this.user.id}`, this.form, {
        preserveState: true,
        onSuccess: () => {
          this.processing = false;
        },
        onError: (errors) => {
          this.errors = errors;
          this.processing = false;
        },
      });
    },
    
    deleteUser() {
      if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        this.$inertia.delete(`/admin/users/${this.user.id}`, {
          onSuccess: () => {
            // Redirect to users index after deletion
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