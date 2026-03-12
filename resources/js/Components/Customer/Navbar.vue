<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const accountMenuOpen = ref(false);
const mobileMenuOpen = ref(false);

const toggleAccountMenu = () => {
  accountMenuOpen.value = !accountMenuOpen.value;
};

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value;
};

const closeMobileMenu = () => {
  mobileMenuOpen.value = false;
};

const logout = () => {
  router.post(route('logout'));
};

const setDefaultLogo = (event) => {
  event.target.src = '/images/logo.jpeg';
};
</script>

<template>
  <header class="bg-white bg-opacity-90 backdrop-blur-sm border-b border-brand-gold border-opacity-20 sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4">
      <div class="flex items-center justify-between">
        <!-- Logo -->
        <Link :href="route('home')" class="flex items-center space-x-3 group">
          <img 
            :src="$page.props.settings?.site_logo_url || '/images/logo.jpeg'" 
            :alt="$page.props.settings?.site_name || 'Ahlam\'s Girls Logo'"
            class="w-12 h-12 rounded-xl object-contain group-hover:scale-105 transition-transform duration-300"
            @error="setDefaultLogo"
          />
          <div>
            <h1 class="font-brand-script text-3xl font-bold bg-gradient-to-r from-brand-primary to-brand-rose bg-clip-text text-transparent group-hover:scale-105 transition-transform duration-300">
              {{$page.props.settings?.site_name || 'Ahlam\'s'}}
            </h1>
            <p class="font-sans text-xl font-semibold text-brand-secondary -mt-1">{{$page.props.settings?.brand_tagline || 'GIRLS'}}</p>
          </div>
        </Link>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-8">
          <Link :href="route('home')" 
                class="text-brand-primary hover:text-brand-gold transition-colors font-medium relative group"
                :class="{ 'text-brand-gold': route().current('home') }">
            Home
            <span v-if="route().current('home')" class="absolute -bottom-1 left-0 w-full h-0.5 bg-brand-gold"></span>
          </Link>
          
          <Link :href="route('products.index')" 
                class="text-brand-primary hover:text-brand-gold transition-colors font-medium relative group"
                :class="{ 'text-brand-gold': route().current('products.index') || route().current('tshirts') || route().current('tshirt.page') }">
            Products
            <span v-if="route().current('products.index') || route().current('tshirts') || route().current('tshirt.page')" class="absolute -bottom-1 left-0 w-full h-0.5 bg-brand-gold"></span>
          </Link>
          
          <Link :href="route('designer.create', { productType: 't-shirt' })" 
                class="text-brand-primary hover:text-brand-gold transition-colors font-medium relative group"
                :class="{ 'text-brand-gold': route().current('designer.create') || route().current('designer.edit') }">
            Designer
            <span v-if="route().current('designer.create') || route().current('designer.edit')" class="absolute -bottom-1 left-0 w-full h-0.5 bg-brand-gold"></span>
          </Link>
          
          <Link :href="route('about')" 
                class="text-brand-primary hover:text-brand-gold transition-colors font-medium relative group"
                :class="{ 'text-brand-gold': route().current('about') }">
            About
          </Link>
          
          <Link :href="route('contact')" 
                class="text-brand-primary hover:text-brand-gold transition-colors font-medium relative group"
                :class="{ 'text-brand-gold': route().current('contact') }">
            Contact
          </Link>
        </nav>

        <!-- Right Side Actions -->
        <div class="flex items-center space-x-4">
          <!-- Search -->
          <div class="hidden md:block relative">
            <input 
              type="text" 
              placeholder="Search..." 
              class="pl-10 pr-4 py-2 rounded-full border border-brand-gold border-opacity-30 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:border-transparent bg-white bg-opacity-50"
            >
            <svg class="w-5 h-5 text-brand-secondary absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>

          <!-- Cart -->
          <Link :href="route('cart')" class="relative p-3 rounded-full hover:bg-brand-gold hover:bg-opacity-10 transition-colors">
            <svg class="w-6 h-6 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v5a2 2 0 01-2 2H9a2 2 0 01-2-2v-5m6-5V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2"></path>
            </svg>
            <span v-if="$page.props.cart && $page.props.cart.items_count > 0" class="absolute -top-1 -right-1 bg-brand-rose text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
              {{ $page.props.cart.items_count }}
            </span>
          </Link>

          <!-- Account Menu -->
          <div class="relative" v-if="$page.props.auth">
            <button @click="toggleAccountMenu" class="flex items-center space-x-2 p-2 rounded-full hover:bg-brand-gold hover:bg-opacity-10 transition-colors">
              <div class="w-8 h-8 rounded-full bg-gradient-to-r from-brand-rose to-brand-lavender flex items-center justify-center text-white font-bold">
                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
              </div>
            </button>

            <!-- Account Dropdown -->
            <div v-show="accountMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-2 z-50 border border-brand-gold border-opacity-20">
              <Link :href="route('profile.edit')" class="block px-4 py-2 text-brand-primary hover:bg-brand-gold hover:bg-opacity-10 transition-colors">
                Profile
              </Link>
              <Link :href="route('customer.dashboard')" class="block px-4 py-2 text-brand-primary hover:bg-brand-gold hover:bg-opacity-10 transition-colors">
                My Dashboard
              </Link>
              <Link :href="route('designer.my-designs')" class="block px-4 py-2 text-brand-primary hover:bg-brand-gold hover:bg-opacity-10 transition-colors">
                My Designs
              </Link>
              <form @submit.prevent="logout">
                <button type="submit" class="w-full text-left px-4 py-2 text-brand-primary hover:bg-brand-gold hover:bg-opacity-10 transition-colors">
                  Logout
                </button>
              </form>
            </div>
          </div>

          <!-- Auth Links -->
          <div v-else class="flex items-center space-x-2">
            <Link :href="route('login')" class="px-4 py-2 text-brand-primary hover:text-brand-gold transition-colors font-medium">
              Login
            </Link>
            <!-- Registration is currently disabled -->
          </div>

          <!-- Mobile Menu Button -->
          <button @click="toggleMobileMenu" class="md:hidden p-2 rounded-lg hover:bg-brand-gold hover:bg-opacity-10 transition-colors">
            <svg class="w-6 h-6 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <nav v-show="mobileMenuOpen" class="md:hidden mt-4 pb-4 space-y-2">
        <Link :href="route('home')" 
              class="block py-3 px-4 rounded-lg text-brand-primary hover:bg-brand-gold hover:bg-opacity-10 transition-colors font-medium"
              :class="{ 'bg-brand-gold bg-opacity-10': route().current('home') }"
              @click="closeMobileMenu">
          Home
        </Link>
        
        <Link :href="route('products.index')" 
              class="block py-3 px-4 rounded-lg text-brand-primary hover:bg-brand-gold hover:bg-opacity-10 transition-colors font-medium"
              :class="{ 'bg-brand-gold bg-opacity-10': route().current('products.index') || route().current('tshirts') || route().current('tshirt.page') }"
              @click="closeMobileMenu">
          Products
        </Link>
        
        <Link :href="route('designer.create', { productType: 't-shirt' })" 
              class="block py-3 px-4 rounded-lg text-brand-primary hover:bg-brand-gold hover:bg-opacity-10 transition-colors font-medium"
              :class="{ 'bg-brand-gold bg-opacity-10': route().current('designer.create') || route().current('designer.edit') }"
              @click="closeMobileMenu">
          Designer
        </Link>
        
        <Link :href="route('about')" 
              class="block py-3 px-4 rounded-lg text-brand-primary hover:bg-brand-gold hover:bg-opacity-10 transition-colors font-medium"
              :class="{ 'bg-brand-gold bg-opacity-10': route().current('about') }"
              @click="closeMobileMenu">
          About
        </Link>
        
        <Link :href="route('contact')" 
              class="block py-3 px-4 rounded-lg text-brand-primary hover:bg-brand-gold hover:bg-opacity-10 transition-colors font-medium"
              :class="{ 'bg-brand-gold bg-opacity-10': route().current('contact') }"
              @click="closeMobileMenu">
          Contact
        </Link>
      </nav>
    </div>
  </header>
</template>

<style scoped>
/* Custom scrollbar for mobile menu */
.md\:hidden::-webkit-scrollbar {
   width: 6px;
}
.md\:hidden::-webkit-scrollbar-track {
   background: #16213e;
}
.md\:hidden::-webkit-scrollbar-thumb {
   background: #d4af37;
   border-radius: 3px;
}
</style>
