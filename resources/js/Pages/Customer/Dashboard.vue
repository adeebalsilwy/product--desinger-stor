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
    <div class="min-h-screen bg-gradient-to-br from-brand-bg to-brand-primary py-8">
      <div class="container mx-auto px-4">
        <div class="text-center mb-12">
          <h1 class="font-brand-elegant text-4xl text-white mb-4">Welcome to {{ siteName || "Ahlam's Girls" }} Dashboard</h1>
          <p class="text-brand-gold text-xl">Manage your elegant fashion experience</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
          <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20 text-center">
            <div class="text-4xl mb-4">🛒</div>
            <h3 class="font-brand-elegant text-xl text-brand-primary mb-2">Total Orders</h3>
            <p class="text-3xl font-bold text-brand-rose">{{ stats.total_orders }}</p>
          </div>

          <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20 text-center">
            <div class="text-4xl mb-4">👗</div>
            <h3 class="font-brand-elegant text-xl text-brand-primary mb-2">Saved Designs</h3>
            <p class="text-3xl font-bold text-brand-rose">{{ stats.saved_designs }}</p>
          </div>

          <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20 text-center">
            <div class="text-4xl mb-4">💳</div>
            <h3 class="font-brand-elegant text-xl text-brand-primary mb-2">Spent</h3>
            <p class="text-3xl font-bold text-brand-rose">{{ formatCurrency(stats.total_spent) }}</p>
          </div>

          <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20 text-center">
            <div class="text-4xl mb-4">⭐</div>
            <h3 class="font-brand-elegant text-xl text-brand-primary mb-2">Rewards</h3>
            <p class="text-3xl font-bold text-brand-rose">{{ stats.reward_points }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20">
            <h3 class="font-brand-elegant text-2xl text-brand-primary mb-6">Recent Orders</h3>
            <div class="space-y-4">
              <div v-for="order in recentOrders" :key="order.id" class="flex justify-between items-center p-4 border-b border-brand-gold border-opacity-10 last:border-0">
                <div>
                  <p class="font-medium text-brand-primary">#{{ order.id }}</p>
                  <p class="text-sm text-brand-secondary">{{ formatDate(order.created_at) }}</p>
                </div>
                <div class="text-right">
                  <p class="font-medium text-brand-rose">{{ formatCurrency(order.total_amount) }}</p>
                  <Status :status="order.status" />
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 border border-brand-gold border-opacity-20">
            <h3 class="font-brand-elegant text-2xl text-brand-primary mb-6">Recent Designs</h3>
            <div class="space-y-4">
              <div v-for="design in recentDesigns" :key="design.id" class="flex items-center space-x-4 p-4 border-b border-brand-gold border-opacity-10 last:border-0">
                <div class="w-16 h-16 bg-gradient-to-br from-brand-rose to-brand-lavender rounded-lg flex items-center justify-center">
                  <span class="text-2xl">✂️</span>
                </div>
                <div class="flex-1">
                  <p class="font-medium text-brand-primary">{{ design.name }}</p>
                  <p class="text-sm text-brand-secondary">{{ formatDate(design.created_at) }}</p>
                </div>
                <Link :href="route('designer.edit', { design: design.id })" class="px-3 py-1 bg-brand-gold text-brand-primary rounded-lg text-sm hover:bg-opacity-90 transition-colors">
                  Edit
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>
