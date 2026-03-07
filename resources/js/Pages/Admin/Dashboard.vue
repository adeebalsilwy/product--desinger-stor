<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-600">Welcome to your professional admin panel</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <StatCard 
          :value="stats.total_users"
          label="Total Users"
          icon="👥"
          :color="'indigo'"
          class="neumorphic shadow-lg"
        />
        <StatCard 
          :value="stats.total_orders"
          label="Total Orders"
          icon="📦"
          :color="'emerald'"
          class="neumorphic shadow-lg"
        />
        <StatCard 
          :value="stats.pending_orders"
          label="Pending Orders"
          icon="⏳"
          :color="'amber'"
          class="neumorphic shadow-lg"
        />
        <StatCard 
          :value="formatCurrency(stats.revenue_month)"
          label="Monthly Revenue"
          icon="💰"
          :color="'rose'"
          class="neumorphic shadow-lg"
        />
      </div>

      <!-- Charts and Recent Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Monthly Revenue Chart -->
        <div class="neumorphic p-6 shadow-lg rounded-xl">
          <h2 class="text-xl font-semibold mb-4 text-gray-800">Monthly Revenue</h2>
          <div class="h-72">
            <BarChart 
              :data="monthlyRevenue" 
              x-key="month" 
              y-key="revenue" 
              :formatter="(val) => formatCurrency(val)"
            />
          </div>
        </div>

        <!-- Order Status Distribution -->
        <div class="neumorphic p-6 shadow-lg rounded-xl">
          <h2 class="text-xl font-semibold mb-4 text-gray-800">Order Status Distribution</h2>
          <div class="h-72">
            <PieChart 
              :data="orderStatuses" 
              :formatter="(val) => val + ' orders'"
            />
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
        <!-- Recent Orders -->
        <div class="neumorphic p-6 shadow-lg rounded-xl">
          <h2 class="text-xl font-semibold mb-4 text-gray-800">Recent Orders</h2>
          <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
            <div v-for="order in recentOrders" :key="order.id" class="border-b border-gray-200 pb-3 last:border-b-0">
              <div class="flex justify-between items-center">
                <span class="font-medium text-gray-800">#{{ order.id }}</span>
                <span class="text-sm text-gray-600">{{ formatDate(order.created_at) }}</span>
              </div>
              <div class="text-sm text-gray-600">
                {{ order.customer?.name || 'Unknown' }}
              </div>
              <div class="flex justify-between items-center mt-1">
                <span class="font-semibold text-gray-800">{{ formatCurrency(order.total_amount) }}</span>
                <StatusBadge :status="order.status" />
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Users -->
        <div class="neumorphic p-6 shadow-lg rounded-xl">
          <h2 class="text-xl font-semibold mb-4 text-gray-800">Recent Users</h2>
          <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
            <div v-for="user in recentUsers" :key="user.id" class="border-b border-gray-200 pb-3 last:border-b-0">
              <div class="flex justify-between items-center">
                <span class="font-medium text-gray-800">{{ user.name }}</span>
                <span class="text-sm text-gray-600">{{ formatDate(user.created_at) }}</span>
              </div>
              <div class="text-sm text-gray-600">{{ user.email }}</div>
              <div class="mt-1">
                <StatusBadge :status="user.is_active ? 'active' : 'inactive'" />
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Designs -->
        <div class="neumorphic p-6 shadow-lg rounded-xl">
          <h2 class="text-xl font-semibold mb-4 text-gray-800">Recent Designs</h2>
          <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
            <div v-for="design in recentDesigns" :key="design.id" class="border-b border-gray-200 pb-3 last:border-b-0">
              <div class="flex justify-between items-center">
                <span class="font-medium truncate max-w-[150px] text-gray-800">{{ design.name }}</span>
                <span class="text-sm text-gray-600">{{ formatDate(design.created_at) }}</span>
              </div>
              <div class="text-sm text-gray-600">
                {{ design.user?.name || 'Unknown' }}
              </div>
              <div class="mt-1">
                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">
                  {{ design.productType?.name || 'Unknown' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/Admin.vue';
import StatCard from '@/Components/DashboardCard.vue';
import BarChart from '@/Components/BarChart.vue';
import PieChart from '@/Components/PieChart.vue';
import StatusBadge from '@/Components/Status.vue';

export default {
  name: 'AdminDashboard',
  
  components: {
    AdminLayout,
    StatCard,
    BarChart,
    PieChart,
    StatusBadge,
  },
  
  props: {
    stats: Object,
    recentOrders: Array,
    recentUsers: Array,
    recentDesigns: Array,
    monthlyRevenue: Array,
    orderStatuses: Object,
  },
  
  methods: {
    formatCurrency(amount) {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
      }).format(amount);
    },
    
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
      });
    },
  },
};
</script>