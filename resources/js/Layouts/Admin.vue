<script setup>
import { ref } from "vue";
import Dropdown from "@/Components/Breeze/Dropdown.vue";
import DropdownLink from "@/Components/Breeze/DropdownLink.vue";
import IconsOrder from "@/Icons/Order.vue";
import IconsTshirt from "@/Icons/Tshirt.vue";
import IconsUsers from "@/Icons/Users.vue";
import IconsMoney from "@/Icons/Money.vue";
import ResponsiveNavLink from "@/Components/Breeze/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import DashboardCard from "@/Components/DashboardCard.vue";
import DashboardChartCard from "@/Components/DashboardChartCard.vue";
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const orders_count = computed(() => page.props.orders_count)
const customers_count = computed(() => page.props.customers_count)
const tshirts_count = computed(() => page.props.tshirts_count)
const revenue = computed(() => page.props.revenue)

const showingNavigationDropdown = ref(false);
const sidebarOpen = ref(true);

// Toggle sidebar visibility on mobile
const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
</script>

<template>
    <div class="min-h-screen flex bg-neumorphic">
        <!-- Sidebar -->
        <aside 
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" 
            class="fixed inset-y-0 left-0 z-30 w-64 bg-neumorphic shadow-lg transform transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 neumorphic-box"
        >
            <div class="flex items-center justify-between p-4 border-b border-neumorphic">
                <Link :href="route('home')" class="flex items-center space-x-2">
                    <img
                        src="../../../public/assets/logo/d-shirts.png"
                        alt="d-shirts logo"
                        class="w-10"
                    />
                    <span class="text-xl font-bold text-neumorphic-text">D-Shirts</span>
                </Link>
                
                <!-- Close button for mobile -->
                <button 
                    @click="toggleSidebar" 
                    class="lg:hidden text-neumorphic-text hover:text-neumorphic-text-hover"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <nav class="mt-6 px-2">
                <Link 
                    :href="route('admin.dashboard')" 
                    :class="$page.component === 'Admin/Dashboard' ? 'bg-neumorphic-active text-neumorphic-text border-r-2 border-neumorphic-accent' : 'text-neumorphic-text hover:bg-neumorphic-hover'"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 neumorphic-btn"
                >
                    <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </Link>
                
                <Link 
                    :href="route('admin.orders.index')" 
                    :class="$page.component === 'Admin/Orders' ? 'bg-neumorphic-active text-neumorphic-text border-r-2 border-neumorphic-accent' : 'text-neumorphic-text hover:bg-neumorphic-hover'"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg mt-2 transition-colors duration-200 neumorphic-btn"
                >
                    <IconsOrder class="h-5 w-5 mr-3" />
                    Orders
                    <span class="ml-auto bg-neumorphic-badge text-neumorphic-text text-xs font-medium px-2 py-0.5 rounded-full">
                        {{ orders_count }}
                    </span>
                </Link>
                
                <Link 
                    :href="route('admin.customers.index')" 
                    :class="$page.component === 'Admin/Customers' ? 'bg-neumorphic-active text-neumorphic-text border-r-2 border-neumorphic-accent' : 'text-neumorphic-text hover:bg-neumorphic-hover'"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg mt-2 transition-colors duration-200 neumorphic-btn"
                >
                    <IconsUsers class="h-5 w-5 mr-3" />
                    Customers
                    <span class="ml-auto bg-neumorphic-badge text-neumorphic-text text-xs font-medium px-2 py-0.5 rounded-full">
                        {{ customers_count }}
                    </span>
                </Link>
                
                <Link 
                    :href="route('admin.products.index')" 
                    :class="$page.component === 'Admin/Tshirts' ? 'bg-neumorphic-active text-neumorphic-text border-r-2 border-neumorphic-accent' : 'text-neumorphic-text hover:bg-neumorphic-hover'"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg mt-2 transition-colors duration-200 neumorphic-btn"
                >
                    <IconsTshirt class="h-5 w-5 mr-3" />
                    T-Shirts
                    <span class="ml-auto bg-neumorphic-badge text-neumorphic-text text-xs font-medium px-2 py-0.5 rounded-full">
                        {{ tshirts_count }}
                    </span>
                </Link>
                
                <Link 
                    :href="route('admin.users.index')" 
                    :class="$page.component === 'Admin/Users/Index' ? 'bg-neumorphic-active text-neumorphic-text border-r-2 border-neumorphic-accent' : 'text-neumorphic-text hover:bg-neumorphic-hover'"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg mt-2 transition-colors duration-200 neumorphic-btn"
                >
                    <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Users
                </Link>
                
                <Link 
                    :href="route('admin.designs.index')" 
                    :class="$page.component === 'Admin/Designs/Index' ? 'bg-neumorphic-active text-neumorphic-text border-r-2 border-neumorphic-accent' : 'text-neumorphic-text hover:bg-neumorphic-hover'"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg mt-2 transition-colors duration-200 neumorphic-btn"
                >
                    <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                    Designs
                </Link>
                
                <Link 
                    :href="route('designer.my-designs')" 
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg mt-2 transition-colors duration-200 neumorphic-btn text-neumorphic-text hover:bg-neumorphic-hover"
                >
                    <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Designer
                </Link>
                
                <Link 
                    :href="route('admin.revenue.index')" 
                    :class="$page.component === 'Admin/Revenue' ? 'bg-neumorphic-active text-neumorphic-text border-r-2 border-neumorphic-accent' : 'text-neumorphic-text hover:bg-neumorphic-hover'"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg mt-2 transition-colors duration-200 neumorphic-btn"
                >
                    <IconsMoney class="h-5 w-5 mr-3" />
                    Revenue
                    <span class="ml-auto bg-neumorphic-badge text-neumorphic-text text-xs font-medium px-2 py-0.5 rounded-full">
                        {{ revenue }}
                    </span>
                </Link>
                
                <Link 
                    :href="route('admin.roles.index')" 
                    :class="$page.component === 'Admin/Roles/Index' ? 'bg-neumorphic-active text-neumorphic-text border-r-2 border-neumorphic-accent' : 'text-neumorphic-text hover:bg-neumorphic-hover'"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg mt-2 transition-colors duration-200 neumorphic-btn"
                >
                    <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Roles
                </Link>
                
                <Link 
                    :href="route('admin.permissions.index')" 
                    :class="$page.component === 'Admin/Permissions/Index' ? 'bg-neumorphic-active text-neumorphic-text border-r-2 border-neumorphic-accent' : 'text-neumorphic-text hover:bg-neumorphic-hover'"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg mt-2 transition-colors duration-200 neumorphic-btn"
                >
                    <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Permissions
                </Link>
                
                <Link 
                    :href="route('admin.templates.index')" 
                    :class="$page.component === 'Admin/Templates/Index' ? 'bg-neumorphic-active text-neumorphic-text border-r-2 border-neumorphic-accent' : 'text-neumorphic-text hover:bg-neumorphic-hover'"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg mt-2 transition-colors duration-200 neumorphic-btn"
                >
                    <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Templates
                </Link>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-neumorphic shadow-sm z-10 neumorphic-box">
                <div class="flex items-center justify-between px-4 py-3 sm:px-6">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button 
                            @click="toggleSidebar" 
                            class="lg:hidden mr-4 text-neumorphic-text hover:text-neumorphic-text-hover"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <h1 class="text-xl font-semibold text-neumorphic-text capitalize">
                            {{ $page.component.split('/')[1] || 'Dashboard' }}
                        </h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Settings Dropdown -->
                        <div class="relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-transparent bg-neumorphic px-3 py-2 text-sm font-medium leading-4 text-neumorphic-text transition duration-150 ease-in-out hover:text-neumorphic-text-hover neumorphic-btn"
                                        >
                                            {{ $page.props.auth.user.name }}

                                            <svg
                                                class="-me-0.5 ms-2 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink
                                        :href="route('profile.edit')"
                                        as="button"
                                    >
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Cards Section -->
            <div class="mx-4 my-4">
                <div class="grid lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <DashboardCard
                        route-name="admin.orders.index"
                        active-component="Admin/Orders"
                        :icon="IconsOrder"
                        title="Orders"
                        :count="orders_count"
                        class="neumorphic-box"
                    />
                    <DashboardCard
                        route-name="admin.customers.index"
                        active-component="Admin/Customers"
                        :icon="IconsUsers"
                        title="Customers"
                        :count="customers_count"
                        class="neumorphic-box"
                    />
                    <DashboardCard
                        route-name="admin.products.index"
                        active-component="Admin/Tshirts"
                        :icon="IconsTshirt"
                        title="T-shirts"
                        :count="tshirts_count"
                        class="neumorphic-box"
                    />
                    <DashboardChartCard
                        route-name="admin.revenue.index"
                        active-component="Admin/Revenue"
                        :icon="IconsMoney"
                        title="Revenue"
                        :count="revenue"
                        :chartData="[30, 40, 35, 50, 49, 60, 70, 91, 25]"
                        class="neumorphic-box"
                    />
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 bg-neumorphic">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.bg-neumorphic {
  background: #e0e5ec;
}

.neumorphic-box {
  background: #e0e5ec;
  border-radius: 15px;
  box-shadow: 8px 8px 15px #b8bec7, -8px -8px 15px #ffffff;
  padding: 15px;
}

.neumorphic-btn {
  background: #e0e5ec;
  border-radius: 15px;
  box-shadow: 5px 5px 10px #b8bec7, -5px -5px 10px #ffffff;
  transition: all 0.3s ease;
}

.neumorphic-btn:hover {
  box-shadow: 2px 2px 5px #b8bec7, -2px -2px 5px #ffffff;
}

.neumorphic-btn:active {
  box-shadow: inset 2px 2px 5px #b8bec7, inset -2px -2px 5px #ffffff;
}

.text-neumorphic-text {
  color: #6d7587;
}

.text-neumorphic-text-hover {
  color: #4a5568;
}

.bg-neumorphic-active {
  background: #d2d8e0;
}

.bg-neumorphic-hover {
  background: #d9dde5;
}

.bg-neumorphic-badge {
  background: #d1d8e0;
}
</style>