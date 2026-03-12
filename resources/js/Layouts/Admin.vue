<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const sidebarOpen = ref(true);
const showProfileDropdown = ref(false);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const toggleProfileDropdown = () => {
    showProfileDropdown.value = !showProfileDropdown.value;
};

// Close sidebar when clicking outside (on larger screens)
const handleClickOutside = (event) => {
    const sidebar = document.querySelector('aside');
    const menuButton = document.querySelector('button[onclick="toggleSidebar"]');
    
    if (sidebarOpen.value && 
        sidebar && 
        !sidebar.contains(event.target) && 
        menuButton && 
        !menuButton.contains(event.target) &&
        window.innerWidth < 1024) {
        sidebarOpen.value = false;
    }
    
    // Close profile dropdown when clicking outside
    const profileMenu = document.querySelector('.profile-dropdown');
    const profileButton = document.querySelector('.profile-button');
    
    if (showProfileDropdown.value && 
        profileMenu && 
        !profileMenu.contains(event.target) && 
        profileButton && 
        !profileButton.contains(event.target)) {
        showProfileDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const setDefaultLogo = (event) => {
    event.target.src = '/images/logo.jpeg';
};
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gradient-elegant">
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-30 w-64 bg-brand-primary text-white transition-transform duration-300 ease-in-out transform"
            :class="{ '-translate-x-full lg:translate-x-0': !sidebarOpen, 'translate-x-0': sidebarOpen }"
        >
            <div class="flex items-center justify-between p-6 border-b border-brand-gold border-opacity-20">
                <div class="flex items-center space-x-3">
                    <img 
                      :src="$page.props.settings?.site_logo_url || '/images/logo.jpeg'" 
                      :alt="$page.props.settings?.site_name || 'Ahlam\'s Girls Logo'"
                      class="w-10 h-10 rounded-lg object-contain"
                      @error="setDefaultLogo"
                    />
                    <div>
                        <h1 class="font-brand-elegant text-xl font-bold text-white">{{$page.props.settings?.site_name || 'Ahlam\'s Girls'}}</h1>
                        <p class="text-xs text-white">Admin Panel</p>
                    </div>
                </div>
                <button 
                    @click="toggleSidebar" 
                    class="lg:hidden text-brand-gold hover:text-white transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <nav class="p-4 space-y-2">
                <Link 
                    :href="route('admin.dashboard')" 
                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-brand-gold hover:bg-opacity-20 border border-transparent hover:border-brand-gold hover:border-opacity-30"
                    :class="{ 'bg-brand-gold bg-opacity-20 border border-brand-gold border-opacity-30': route().current('admin.dashboard') }"
                >
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                    </svg>
                    <span>Dashboard</span>
                </Link>

                <Link 
                    :href="route('admin.users.index')" 
                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-brand-gold hover:bg-opacity-20 border border-transparent hover:border-brand-gold hover:border-opacity-30"
                    :class="{ 'bg-brand-gold bg-opacity-20 border border-brand-gold border-opacity-30': route().current('admin.users.index') }"
                >
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <span>Users</span>
                </Link>

                <Link 
                    :href="route('admin.products.index')" 
                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-brand-gold hover:bg-opacity-20 border border-transparent hover:border-brand-gold hover:border-opacity-30"
                    :class="{ 'bg-brand-gold bg-opacity-20 border border-brand-gold border-opacity-30': route().current('admin.products.index') }"
                >
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>Products</span>
                </Link>

                <Link 
                    :href="route('admin.orders.index')" 
                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-brand-gold hover:bg-opacity-20 border border-transparent hover:border-brand-gold hover:border-opacity-30"
                    :class="{ 'bg-brand-gold bg-opacity-20 border border-brand-gold border-opacity-30': route().current('admin.orders.index') }"
                >
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span>Orders</span>
                </Link>

                <Link 
                    :href="route('admin.designs.index')" 
                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-brand-gold hover:bg-opacity-20 border border-transparent hover:border-brand-gold hover:border-opacity-30"
                    :class="{ 'bg-brand-gold bg-opacity-20 border border-brand-gold border-opacity-30': route().current('admin.designs.index') }"
                >
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                    </svg>
                    <span>Designs</span>
                </Link>

                <Link 
                    :href="route('admin.templates.index')" 
                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-brand-gold hover:bg-opacity-20 border border-transparent hover:border-brand-gold hover:border-opacity-30"
                    :class="{ 'bg-brand-gold bg-opacity-20 border border-brand-gold border-opacity-30': route().current('admin.templates.index') }"
                >
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                    <span>Templates</span>
                </Link>

                <Link 
                    :href="route('admin.roles.index')" 
                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-brand-gold hover:bg-opacity-20 border border-transparent hover:border-brand-gold hover:border-opacity-30"
                    :class="{ 'bg-brand-gold bg-opacity-20 border border-brand-gold border-opacity-30': route().current('admin.roles.index') }"
                >
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Roles</span>
                </Link>

                <Link 
                    :href="route('admin.permissions.index')" 
                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-brand-gold hover:bg-opacity-20 border border-transparent hover:border-brand-gold hover:border-opacity-30"
                    :class="{ 'bg-brand-gold bg-opacity-20 border border-brand-gold border-opacity-30': route().current('admin.permissions.index') }"
                >
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>Permissions</span>
                </Link>

                <Link 
                    :href="route('admin.settings.index')" 
                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-brand-gold hover:bg-opacity-20 border border-transparent hover:border-brand-gold hover:border-opacity-30"
                    :class="{ 'bg-brand-gold bg-opacity-20 border border-brand-gold border-opacity-30': route().current('admin.settings.index') }"
                >
                    <svg class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Settings</span>
                </Link>
            </nav>
        </aside>

        <!-- Main Content -->
        <main :class="sidebarOpen ? 'ml-64' : 'ml-0'" class="lg:ml-64 flex-1 transition-all duration-300 ease-in-out">
            <!-- Top Navigation -->
            <header class="bg-white bg-opacity-90 backdrop-blur-sm border-b border-brand-gold border-opacity-20 sticky top-0 z-20">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <button 
                            @click="toggleSidebar" 
                            class="lg:hidden mr-4 text-brand-primary hover:text-brand-gold transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        
                        <h2 class="text-2xl font-bold text-brand-primary font-brand-elegant">
                            {{ $page.component.split('/').pop() }}
                        </h2>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input 
                                type="text" 
                                placeholder="Search..." 
                                class="pl-10 pr-4 py-2 rounded-lg border border-brand-gold border-opacity-30 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent bg-white bg-opacity-50"
                            >
                            <svg class="w-5 h-5 text-brand-secondary absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>

                        <div class="relative">
                            <button class="p-2 rounded-full hover:bg-brand-gold hover:bg-opacity-10 transition-colors text-brand-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-6H4v6zM16 3h5v5h-5V3zM4 3h6v6H4V3z"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="relative">
                            <button 
                                @click="toggleProfileDropdown" 
                                class="flex items-center space-x-3 profile-button"
                            >
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-brand-rose to-brand-lavender flex items-center justify-center text-white font-bold">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="font-medium text-brand-primary">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-sm text-brand-secondary">{{ $page.props.auth.user.role }}</p>
                                </div>
                                <svg 
                                    :class="{ 'rotate-180': showProfileDropdown }" 
                                    class="w-4 h-4 text-brand-secondary transition-transform" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Profile Dropdown Menu -->
                            <div 
                                v-if="showProfileDropdown" 
                                class="profile-dropdown absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-brand-gold border-opacity-20 z-50 py-2"
                            >
                                <Link 
                                    :href="route('profile.edit')" 
                                    class="block px-4 py-2 text-sm text-brand-primary hover:bg-brand-gold hover:bg-opacity-10 transition-colors"
                                    @click="showProfileDropdown = false"
                                >
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    My Profile
                                </Link>
                                <Link 
                                    :href="route('logout')" 
                                    method="post" 
                                    as="button" 
                                    class="block w-full text-left px-4 py-2 text-sm text-brand-primary hover:bg-brand-gold hover:bg-opacity-10 transition-colors"
                                    @click="showProfileDropdown = false"
                                >
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Sign Out
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-6">
                <slot />
            </div>
        </main>
    </div>
</template>