<template>
  <div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Order Details</h1>
          <p class="text-gray-600 mt-2">Order #{{ order.id || 'N/A' }} • {{ order.created_at || 'N/A' }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3">
          <button 
            @click="goBack"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors"
          >
            Back to Orders
          </button>
        </div>
      </div>

      <!-- Order Status and Payment Summary -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Order Status -->
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Status</h2>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Status</span>
              <Status :status="order.status || 'N/A'" />
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Tracking Number</span>
              <span class="font-medium">{{ order.tracking_number || 'Not assigned' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Date</span>
              <span class="font-medium">{{ order.created_at || 'N/A' }}</span>
            </div>
          </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Payment Status</span>
              <PaymentStatus :type="order.payment_status || 'N/A'" />
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Payment ID</span>
              <span class="font-medium">{{ order.payment_id || 'N/A' }}</span>
            </div>
            <div v-if="order.reference_number" class="flex justify-between">
              <span class="text-gray-600">Reference Number</span>
              <span class="font-medium">{{ order.reference_number }}</span>
            </div>
            <div v-if="order.transfer_date" class="flex justify-between">
              <span class="text-gray-600">Transfer Date</span>
              <span class="font-medium">{{ order.transfer_date }}</span>
            </div>
            <div v-if="order.bank_details" class="flex justify-between">
              <span class="text-gray-600">Bank Details</span>
              <span class="font-medium">{{ order.bank_details }}</span>
            </div>
          </div>
        </div>

        <!-- Receipt Section -->
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Receipt</h2>
          <div class="space-y-3">
            <div v-if="order.receipt_path">
              <span class="text-gray-600">Receipt Uploaded</span>
              <div class="mt-2">
                <a 
                  :href="`/storage/${order.receipt_path}`" 
                  target="_blank"
                  class="text-blue-600 hover:text-blue-800 underline"
                >
                  View Receipt
                </a>
              </div>
            </div>
            <div v-else>
              <span class="text-gray-600">No receipt uploaded</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Customer Information -->
      <div class="bg-white rounded-xl shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Name & Contact</h3>
            <div class="mt-2 space-y-1">
              <p class="text-lg font-medium text-gray-900">{{ order.customer?.name || 'N/A' }}</p>
              <p class="text-gray-600">{{ order.customer?.email || 'N/A' }}</p>
              <p class="text-gray-600">{{ order.customer?.phone || 'N/A' }}</p>
            </div>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Shipping Address</h3>
            <div class="mt-2 space-y-1">
              <p class="text-gray-900">{{ order.customer?.address || 'N/A' }}</p>
              <p class="text-gray-600">{{ order.customer?.zipcode || 'N/A' }}</p>
              <p class="text-gray-600">{{ order.customer?.country || 'N/A' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Items -->
      <div class="bg-white rounded-xl shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
        
        <!-- T-shirts Section -->
        <div v-if="order.tshirts && order.tshirts.length > 0">
          <h3 class="text-md font-medium text-gray-700 mb-3">T-Shirts</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="tshirt in order.tshirts" :key="tshirt.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img 
                          class="h-10 w-10 rounded-md object-cover" 
                          :src="getImageUrl(tshirt.images)" 
                          :alt="tshirt.title || 'Product Image'"
                          @error="setDefaultImage"
                        />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ tshirt.title || 'Untitled Product' }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ tshirt.pivot?.size || 'N/A' }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ tshirt.pivot?.quantity || 0 }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ parseFloat(tshirt.pivot?.price || 0).toFixed(2) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    ${{ (parseFloat(tshirt.pivot?.price || 0) * (tshirt.pivot?.quantity || 0)).toFixed(2) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Products Section -->
        <div v-if="order.products && order.products.length > 0">
          <h3 class="text-md font-medium text-gray-700 mb-3 mt-6">Products</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in order.products" :key="product.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img 
                          class="h-10 w-10 rounded-md object-cover" 
                          :src="product.main_image || '/images/logo.jpeg'" 
                          :alt="product.name || 'Product Image'"
                          @error="setDefaultImage"
                        />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ product.name || 'Untitled Product' }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.pivot?.size || 'N/A' }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.pivot?.quantity || 0 }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ parseFloat(product.pivot?.price || 0).toFixed(2) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    ${{ (parseFloat(product.pivot?.price || 0) * (product.pivot?.quantity || 0)).toFixed(2) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- No items message -->
        <div v-if="(!order.tshirts || order.tshirts.length === 0) && (!order.products || order.products.length === 0)" class="text-center py-8">
          <p class="text-gray-500">No items found in this order.</p>
        </div>
      </div>

      <!-- Order Totals -->
      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
        <div class="border-t border-gray-200 pt-4">
          <div class="flex justify-between text-lg font-medium text-gray-900 mb-2">
            <span>Total Amount</span>
            <span>${{ parseFloat(order.total_amount || 0).toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-base text-gray-600">
            <span>Total Items</span>
            <span>{{ (order.total_tshirts || 0) + (order.total_products || 0) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import Admin from '@/Layouts/Admin.vue';
import Status from '@/Components/Status.vue';
import PaymentStatus from '@/Components/PaymentStatus.vue';

defineOptions({ layout: Admin });

const props = defineProps({
  order: {
    type: Object,
    required: true
  }
});

const goBack = () => {
  router.visit(route('admin.orders.index'));
};

const getImageUrl = (images) => {
  if (!images || !Array.isArray(images) || images.length === 0) {
    return '/images/logo.jpeg';
  }
  
  // Find the image with order: 1 or return the first one
  const primaryImage = images.find(img => img.order === 1);
  return primaryImage ? primaryImage.url : images[0].url;
};

const setDefaultImage = (event) => {
  event.target.src = '/images/logo.jpeg';
};
</script>