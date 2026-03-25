<script setup>
import CustomerLayout from '@/Layouts/Customer.vue';
import { Link } from '@inertiajs/vue3';
import Status from '@/Components/Status.vue';
import { inject } from 'vue';

const stats = {
  total_orders: 0,
  saved_designs: 0,
  total_spent: 0,
  reward_points: 0
};

const recentOrders = [];
const recentDesigns = [];

// Use the global settings provided by the plugin
const settings = inject('brandSettings');
const siteName = settings ? settings.value?.site_name : "Ahlam's Girls";

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
</script>

<template>
  <CustomerLayout>
    <div class="dashboard-page-container">
      <div class="container mx-auto px-4">
        <div class="text-center mb-12">
          <h1 class="dashboard-title">{{ $t('dashboard.title') }}</h1>
          <p class="dashboard-subtitle">{{ $t('dashboard.subtitle') }}</p>
        </div>

        <div class="stats-grid">
          <div class="neumorphic-box stat-box">
            <div class="stat-icon">🛒</div>
            <h3 class="stat-title">{{ $t('dashboard.stats.total_orders') }}</h3>
            <p class="stat-value">{{ stats.total_orders }}</p>
          </div>

          <div class="neumorphic-box stat-box">
            <div class="stat-icon">👗</div>
            <h3 class="stat-title">{{ $t('dashboard.stats.saved_designs') }}</h3>
            <p class="stat-value">{{ stats.saved_designs }}</p>
          </div>

          <div class="neumorphic-box stat-box">
            <div class="stat-icon">💳</div>
            <h3 class="stat-title">{{ $t('dashboard.stats.spent') }}</h3>
            <p class="stat-value">{{ formatCurrency(stats.total_spent) }}</p>
          </div>

          <div class="neumorphic-box stat-box">
            <div class="stat-icon">⭐</div>
            <h3 class="stat-title">{{ $t('dashboard.stats.rewards') }}</h3>
            <p class="stat-value">{{ stats.reward_points }}</p>
          </div>
        </div>

        <div class="content-grid">
          <div class="neumorphic-card">
            <h3 class="card-title">{{ $t('dashboard.recent_orders') }}</h3>
            <div class="orders-list">
              <div v-for="order in recentOrders" :key="order.id" class="order-item">
                <div>
                  <p class="order-id">#{{ order.id }}</p>
                  <p class="order-date">{{ formatDate(order.created_at) }}</p>
                </div>
                <div class="order-info">
                  <p class="order-amount">{{ formatCurrency(order.total_amount) }}</p>
                  <Status :status="order.status" />
                </div>
              </div>
              <div v-if="recentOrders.length === 0" class="empty-state">
                <p>{{ $t('dashboard.empty_orders') }}</p>
              </div>
            </div>
          </div>

          <div class="neumorphic-card">
            <h3 class="card-title">{{ $t('dashboard.recent_designs') }}</h3>
            <div class="designs-list">
              <div v-for="design in recentDesigns" :key="design.id" class="design-item">
                <div class="design-preview">
                  <span class="design-icon">✂️</span>
                </div>
                <div class="design-info">
                  <p class="design-name">{{ design.name }}</p>
                  <p class="design-date">{{ formatDate(design.created_at) }}</p>
                </div>
                <Link :href="route('designer.edit', { design: design.id })" class="edit-btn">
                  {{ $t('dashboard.edit') }}
                </Link>
              </div>
              <div v-if="recentDesigns.length === 0" class="empty-state">
                <p>{{ $t('dashboard.empty_designs') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<style scoped>
/* Dashboard Page Container */
.dashboard-page-container {
  background: linear-gradient(135deg, #e0e5ec 0%, #c9d6ff 100%);
  min-height: 100vh;
  padding: 50px 20px;
  position: relative;
}

body.night-mode .dashboard-page-container {
  background: linear-gradient(135deg, #1e1e1e 0%, #3a3a5a 100%);
}

/* Dashboard Title */
.dashboard-title {
  font-family: 'Dancing Script', cursive;
  font-size: 48px;
  color: #4a5568;
  margin-bottom: 10px;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

body.night-mode .dashboard-title {
  color: #ffffff;
}

.dashboard-subtitle {
  font-size: 20px;
  color: #4a5568;
}

body.night-mode .dashboard-subtitle {
  color: #ffffff;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 25px;
  margin-bottom: 40px;
}

.stat-box {
  background: #e0e5ec;
  border-radius: 20px;
  padding: 30px;
  box-shadow:
    8px 8px 15px #a3b1c6,
    -8px -8px 15px #ffffff;
  transition: all 0.3s ease;
  text-align: center;
}

.stat-box:hover {
  box-shadow:
    inset 4px 4px 8px #a3b1c6,
    inset -4px -4px 8px #ffffff;
  transform: translateY(-5px);
}

body.night-mode .stat-box {
  background: #2d3748;
  box-shadow:
    8px 8px 15px #1a202c,
    -8px -8px 15px #4a5568;
}

body.night-mode .stat-box:hover {
  box-shadow:
    inset 4px 4px 8px #1a202c,
    inset -4px -4px 8px #4a5568;
}

/* Stat Icon */
.stat-icon {
  font-size: 48px;
  margin-bottom: 15px;
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

/* Stat Title */
.stat-title {
  font-family: 'Dancing Script', cursive;
  font-size: 22px;
  color: #4a7eff;
  margin-bottom: 10px;
}

body.night-mode .stat-title {
  color: #6ea8ff;
}

/* Stat Value */
.stat-value {
  font-size: 36px;
  font-weight: bold;
  color: #4a7eff;
}

/* Content Grid */
.content-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 25px;
}

.neumorphic-card {
  background: #e0e5ec;
  border-radius: 20px;
  padding: 30px;
  box-shadow:
    8px 8px 15px #a3b1c6,
    -8px -8px 15px #ffffff;
}

body.night-mode .neumorphic-card {
  background: #2d3748;
  box-shadow:
    8px 8px 15px #1a202c,
    -8px -8px 15px #4a5568;
}

/* Card Title */
.card-title {
  font-family: 'Dancing Script', cursive;
  font-size: 28px;
  color: #4a7eff;
  margin-bottom: 25px;
}

body.night-mode .card-title {
  color: #6ea8ff;
}

/* Orders List */
.orders-list {
  space-y: 15px;
}

.order-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  margin-bottom: 10px;
  border-radius: 10px;
  background: #e0e5ec;
  box-shadow:
    inset 3px 3px 6px #a3b1c6,
    inset -3px -3px 6px #ffffff;
  transition: all 0.3s ease;
}

.order-item:hover {
  box-shadow:
    inset 5px 5px 10px #a3b1c6,
    inset -5px -5px 10px #ffffff;
}

body.night-mode .order-item {
  background: #34495e;
  box-shadow:
    inset 3px 3px 6px #1a202c,
    inset -3px -3px 6px #4a5568;
}

/* Order ID */
.order-id {
  font-weight: bold;
  color: #4a7eff;
  font-size: 16px;
}

/* Order Date */
.order-date {
  font-size: 13px;
  color: #4a5568;
  margin-top: 3px;
}

body.night-mode .order-date {
  color: #cbd5e0;
}

/* Order Info */
.order-info {
  text-align: right;
}

/* Order Amount */
.order-amount {
  font-weight: bold;
  color: #e74c3c;
  font-size: 16px;
}

/* Designs List */
.designs-list {
  space-y: 15px;
}

.design-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px;
  margin-bottom: 10px;
  border-radius: 10px;
  background: #e0e5ec;
  box-shadow:
    inset 3px 3px 6px #a3b1c6,
    inset -3px -3px 6px #ffffff;
  transition: all 0.3s ease;
}

.design-item:hover {
  box-shadow:
    inset 5px 5px 10px #a3b1c6,
    inset -5px -5px 10px #ffffff;
}

body.night-mode .design-item {
  background: #34495e;
  box-shadow:
    inset 3px 3px 6px #1a202c,
    inset -3px -3px 6px #4a5568;
}

/* Design Preview */
.design-preview {
  width: 60px;
  height: 60px;
  background: #e0e5ec;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow:
    3px 3px 6px #a3b1c6,
    -3px -3px 6px #ffffff;
}

body.night-mode .design-preview {
  background: #2d3748;
  box-shadow:
    3px 3px 6px #1a202c,
    -3px -3px 6px #4a5568;
}

/* Design Icon */
.design-icon {
  font-size: 28px;
}

/* Design Info */
.design-info {
  flex: 1;
}

/* Design Name */
.design-name {
  font-weight: bold;
  color: #4a7eff;
  font-size: 16px;
}

/* Design Date */
.design-date {
  font-size: 13px;
  color: #4a5568;
  margin-top: 3px;
}

body.night-mode .design-date {
  color: #cbd5e0;
}

/* Edit Button */
.edit-btn {
  padding: 8px 20px;
  border-radius: 12px;
  background: #e0e5ec;
  color: #4a7eff;
  font-weight: bold;
  text-decoration: none;
  box-shadow:
    3px 3px 6px #a3b1c6,
    -3px -3px 6px #ffffff;
  transition: all 0.3s ease;
  border: none;
}

.edit-btn:hover {
  box-shadow:
    inset 2px 2px 4px #a3b1c6,
    inset -2px -2px 4px #ffffff;
  transform: translateY(-2px);
}

body.night-mode .edit-btn {
  background: #2d3748;
  box-shadow:
    3px 3px 6px #1a202c,
    -3px -3px 6px #4a5568;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 30px;
  color: #4a5568;
}

body.night-mode .empty-state {
  color: #cbd5e0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .dashboard-page-container {
    padding: 30px 15px;
  }
  
  .dashboard-title {
    font-size: 36px;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .content-grid {
    grid-template-columns: 1fr;
  }
}
</style>
