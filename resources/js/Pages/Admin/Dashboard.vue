<template>
  <AdminLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="mb-8 text-center">
        <h1 class="font-brand-elegant text-5xl text-brand-gold mb-2 tracking-wide">Ahlam's Girls Admin</h1>
        <p class="text-brand-rose text-xl">Elegant Boutique Management Center</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <div class="bg-gradient-to-br from-brand-primary to-brand-secondary p-6 rounded-2xl text-center border border-brand-gold border-opacity-20 backdrop-blur-sm">
          <div class="text-4xl mb-4">👥</div>
          <p class="text-3xl font-bold text-brand-gold mb-2">{{ stats.total_users }}</p>
          <h3 class="font-brand-elegant text-xl text-white">Customers</h3>
        </div>
        
        <div class="bg-gradient-to-br from-brand-secondary to-brand-bg p-6 rounded-2xl text-center border border-brand-gold border-opacity-20 backdrop-blur-sm">
          <div class="text-4xl mb-4">📦</div>
          <p class="text-3xl font-bold text-brand-gold mb-2">{{ stats.total_orders }}</p>
          <h3 class="font-brand-elegant text-xl text-white">Orders</h3>
        </div>
        
        <div class="bg-gradient-to-br from-brand-bg to-brand-primary p-6 rounded-2xl text-center border border-brand-gold border-opacity-20 backdrop-blur-sm">
          <div class="text-4xl mb-4">⏳</div>
          <p class="text-3xl font-bold text-brand-gold mb-2">{{ stats.pending_orders }}</p>
          <h3 class="font-brand-elegant text-xl text-white">Pending</h3>
        </div>
        
        <div class="bg-gradient-to-br from-brand-secondary to-brand-accent p-6 rounded-2xl text-center border border-brand-gold border-opacity-20 backdrop-blur-sm">
          <div class="text-4xl mb-4">💰</div>
          <p class="text-3xl font-bold text-brand-gold mb-2">{{ formatCurrency(stats.revenue_month) }}</p>
          <h3 class="font-brand-elegant text-xl text-white">Monthly Revenue</h3>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20">
          <h3 class="font-brand-elegant text-2xl text-brand-primary mb-6">Sales Overview</h3>
          <div class="h-80">
            <apexchart type="area" height="320" :options="salesChartOptions" :series="salesData"></apexchart>
          </div>
        </div>
        
        <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20">
          <h3 class="font-brand-elegant text-2xl text-brand-primary mb-6">Order Distribution</h3>
          <div class="h-80">
            <apexchart type="donut" height="320" :options="orderChartOptions" :series="orderDistributionData"></apexchart>
          </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20 mb-8">
        <h3 class="font-brand-elegant text-2xl text-brand-primary mb-6">Recent Orders</h3>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-brand-gold border-opacity-20">
                <th class="pb-4 text-left text-brand-primary font-semibold">Order ID</th>
                <th class="pb-4 text-left text-brand-primary font-semibold">Customer</th>
                <th class="pb-4 text-left text-brand-primary font-semibold">Date</th>
                <th class="pb-4 text-left text-brand-primary font-semibold">Amount</th>
                <th class="pb-4 text-left text-brand-primary font-semibold">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in recentOrders" :key="order.id" class="border-b border-brand-gold border-opacity-10 hover:bg-brand-gold hover:bg-opacity-5 transition-colors">
                <td class="py-4 text-brand-secondary">#{{ order.id }}</td>
                <td class="py-4 text-brand-secondary">{{ order.customer_name }}</td>
                <td class="py-4 text-brand-secondary">{{ formatDate(order.created_at) }}</td>
                <td class="py-4 text-brand-rose font-medium">{{ formatCurrency(order.total_amount) }}</td>
                <td class="py-4">
                  <span class="px-3 py-1 rounded-full text-xs font-medium" 
                        :class="getStatusClass(order.status)">
                    {{ order.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-gradient-to-r from-brand-primary to-brand-secondary rounded-2xl p-8 text-center">
        <h3 class="font-brand-elegant text-3xl text-white mb-4">Ready to Take Action?</h3>
        <p class="text-brand-gold mb-6 max-w-2xl mx-auto">Manage your boutique efficiently with our comprehensive admin tools</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <Link :href="route('admin.products.create')" class="btn-luxury bg-white text-brand-primary hover:bg-opacity-90">
            Add Product
          </Link>
          <Link :href="route('admin.orders.index')" class="btn-luxury bg-brand-gold text-brand-primary hover:bg-opacity-90">
            View Orders
          </Link>
          <Link :href="route('admin.users.create')" class="btn-luxury bg-brand-rose text-white hover:bg-opacity-90">
            Add User
          </Link>
          <Link :href="route('admin.settings.index')" class="btn-luxury bg-brand-accent text-white hover:bg-opacity-90">
            Settings
          </Link>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/Admin.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const stats = ref({
  total_users: 0,
  total_orders: 0,
  pending_orders: 0,
  revenue_month: 0
});

const recentOrders = ref([]);

// Chart data
const salesData = ref([{
  name: 'Sales',
  data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
}]);

const orderDistributionData = ref([44, 55, 13, 43]);

const salesChartOptions = ref({
  chart: {
    id: 'sales-chart',
    toolbar: {
      show: false
    }
  },
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep']
  },
  tooltip: {
    theme: 'dark'
  },
  grid: {
    borderColor: '#f1f1f1'
  },
  stroke: {
    curve: 'smooth'
  },
  markers: {
    size: 4
  },
  colors: ['#ffd700'],
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'dark',
      gradientToColors: ['#ff6b6b'],
      shadeIntensity: 1,
      type: 'horizontal',
      opacityFrom: 1,
      opacityTo: 0.8,
    },
  },
});

const orderChartOptions = ref({
  labels: ['Processing', 'Shipped', 'Delivered', 'Cancelled'],
  colors: ['#3498db', '#f39c12', '#2ecc71', '#e74c3c'],
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 200
      },
      legend: {
        position: 'bottom'
      }
    }
  }],
  plotOptions: {
    pie: {
      donut: {
        labels: {
          show: true,
          total: {
            show: true,
            label: 'Orders',
            color: '#ffffff'
          }
        }
      }
    }
  },
  dataLabels: {
    enabled: true,
    style: {
      colors: ['#fff', '#333']
    }
  },
  tooltip: {
    theme: 'dark'
  }
});

const getStatusClass = (status) => {
  switch(status) {
    case 'pending':
      return 'bg-yellow-100 text-yellow-800';
    case 'processing':
      return 'bg-blue-100 text-blue-800';
    case 'shipped':
      return 'bg-purple-100 text-purple-800';
    case 'delivered':
      return 'bg-green-100 text-green-800';
    case 'cancelled':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2
  }).format(amount);
};

onMounted(() => {
  // Use data from props instead of making an API call
  if (typeof $page !== 'undefined' && $page.props && $page.props.stats) {
    stats.value = $page.props.stats;
  }
  if (typeof $page !== 'undefined' && $page.props && $page.props.recentOrders) {
    recentOrders.value = $page.props.recentOrders;
  }
});
</script>
