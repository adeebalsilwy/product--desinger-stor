<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Analytics</h1>
        <p class="text-gray-600">Track your business metrics</p>
      </div>

      <!-- Date Range Filter -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form @submit.prevent="applyFilters" class="flex flex-wrap gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input 
              v-model="filters.start_date" 
              type="date" 
              class="border rounded px-3 py-2"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input 
              v-model="filters.end_date" 
              type="date" 
              class="border rounded px-3 py-2"
            />
          </div>
          <div class="flex items-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
              Apply Filters
            </button>
          </div>
        </form>
      </div>

      <!-- Analytics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <StatCard 
          :value="analytics.revenue"
          label="Revenue"
          icon="💰"
          :color="'green'"
        />
        <StatCard 
          :value="analytics.orders"
          label="Orders"
          icon="📦"
          :color="'blue'"
        />
        <StatCard 
          :value="analytics.users"
          label="New Users"
          icon="👤"
          :color="'purple'"
        />
        <StatCard 
          :value="analytics.designs"
          label="Designs"
          icon="🎨"
          :color="'yellow'"
        />
      </div>

      <!-- Daily Revenue Chart -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Daily Revenue</h2>
        <div class="h-80">
          <LineChart 
            :data="dailyRevenue" 
            x-key="date" 
            y-key="total" 
            :formatter="(val) => formatCurrency(val)"
          />
        </div>
      </div>

      <!-- Comparison Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Revenue vs Previous Period -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Period Comparison</h2>
          <div class="space-y-4">
            <div class="flex justify-between items-center p-4 bg-gray-50 rounded">
              <span class="font-medium">Current Period</span>
              <span class="text-lg font-semibold">{{ formatCurrency(analytics.revenue) }}</span>
            </div>
            <div class="flex justify-between items-center p-4 bg-gray-50 rounded">
              <span class="font-medium">Previous Period</span>
              <span class="text-lg font-semibold">{{ formatCurrency(previousPeriodRevenue) }}</span>
            </div>
            <div class="flex justify-between items-center p-4 bg-green-50 rounded">
              <span class="font-medium">Difference</span>
              <span class="text-lg font-semibold text-green-600">{{ formatCurrency(revenueDifference) }}</span>
            </div>
          </div>
        </div>

        <!-- Top Products/Designs -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Top Performing</h2>
          <div class="space-y-3">
            <div v-for="i in 5" :key="i" class="flex justify-between items-center">
              <span>Item {{ i }}</span>
              <span class="text-sm text-gray-600">{{ Math.floor(Math.random() * 100) }} orders</span>
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
import LineChart from '@/Components/DashboardChartCard.vue';

export default {
  name: 'AdminAnalytics',
  
  components: {
    AdminLayout,
    StatCard,
    LineChart,
  },
  
  props: {
    analytics: Object,
    dailyRevenue: Array,
    filters: Object,
  },
  
  data() {
    return {
      filters: { ...this.filters },
    };
  },
  
  computed: {
    previousPeriodRevenue() {
      // This would come from the server in a real implementation
      return this.analytics.revenue * 0.85; // 15% less than current period for demo
    },
    
    revenueDifference() {
      return this.analytics.revenue - this.previousPeriodRevenue;
    },
  },
  
  methods: {
    formatCurrency(amount) {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
      }).format(amount);
    },
    
    applyFilters() {
      // Submit form to apply filters
      this.$inertia.post('/admin/analytics', this.filters);
    },
  },
};
</script>