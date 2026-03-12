<script setup>
import CustomerLayout from '@/Layouts/Customer.vue';
import { LockClosedIcon } from '@heroicons/vue/solid';
import { Link, useForm } from '@inertiajs/vue3';
import { inject } from 'vue';

const form = useForm({
  email: '',
  password: '',
  remember: false
});

// Use the global settings provided by the plugin
const settings = inject('brandSettings');
const siteName = settings ? settings.value?.site_name : "Ahlam's Girls";

const submitForm = () => {
  form.post(route('login'), {
    onFinish: () => {
      form.reset('password');
    }
  });
};
</script>

<template>
  <CustomerLayout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-brand-bg to-brand-primary py-12 px-4 sm:px-6 lg:px-8">
      <div class="absolute inset-0 bg-[url('/images/login-bg.jpg')] bg-cover bg-center opacity-10"></div>
      
      <div class="max-w-md w-full space-y-8 relative z-10 bg-white bg-opacity-90 backdrop-blur-sm p-8 rounded-2xl shadow-lg border border-brand-gold border-opacity-20">
        <div class="text-center">
          <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-gradient-to-r from-brand-rose to-brand-lavender rounded-xl flex items-center justify-center">
              <span class="text-2xl font-bold text-white">AG</span>
            </div>
          </div>
          
          <h2 class="mt-6 text-3xl font-extrabold text-brand-primary font-brand-elegant">
            Welcome Back
          </h2>
          <p class="mt-2 text-brand-secondary">
            Sign in to your {{ siteName || "Ahlam's Girls" }} account
          </p>
        </div>

        <form class="mt-8 space-y-6" @submit.prevent="submitForm">
          <div class="rounded-md shadow-sm -space-y-px">
            <div>
              <label for="email" class="sr-only">Email address</label>
              <input 
                id="email" 
                name="email" 
                type="email" 
                autocomplete="email" 
                required 
                v-model="form.email"
                class="appearance-none rounded-none relative block w-full px-3 py-3 border border-brand-gold border-opacity-30 placeholder-brand-secondary bg-white bg-opacity-50 text-brand-primary rounded-t-md focus:outline-none focus:ring-brand-gold focus:border-brand-gold focus:z-10 sm:text-sm"
                placeholder="Email address"
              />
            </div>
            <div>
              <label for="password" class="sr-only">Password</label>
              <input 
                id="password" 
                name="password" 
                type="password" 
                autocomplete="current-password" 
                required 
                v-model="form.password"
                class="appearance-none rounded-none relative block w-full px-3 py-3 border border-brand-gold border-opacity-30 placeholder-brand-secondary bg-white bg-opacity-50 text-brand-primary rounded-b-md focus:outline-none focus:ring-brand-gold focus:border-brand-gold focus:z-10 sm:text-sm"
                placeholder="Password"
              />
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input 
                id="remember_me" 
                name="remember" 
                type="checkbox" 
                v-model="form.remember"
                class="h-4 w-4 text-brand-primary focus:ring-brand-gold border-brand-gold border-opacity-30 rounded"
              />
              <label for="remember_me" class="ml-2 block text-sm text-brand-secondary">
                Remember me
              </label>
            </div>

            <div class="text-sm">
              <Link :href="route('password.request')" class="font-medium text-brand-rose hover:text-brand-gold">
                Forgot your password?
              </Link>
            </div>
          </div>

          <div>
            <button 
              type="submit" 
              :disabled="form.processing"
              class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-brand-primary to-brand-secondary hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-gold transition-opacity"
            >
              <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                <LockClosedIcon class="h-5 w-5 text-brand-gold group-hover:text-brand-gold" aria-hidden="true" />
              </span>
              {{ form.processing ? 'Signing in...' : 'Sign in' }}
            </button>
          </div>
        </form>

        <div class="text-center">
          <p class="text-sm text-brand-secondary">
            Don't have an account? 
            <Link :href="route('register')" class="font-medium text-brand-rose hover:text-brand-gold">
              Sign up
            </Link>
          </p>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>
