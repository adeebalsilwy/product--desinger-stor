<script setup>
import CustomerLayout from '@/Layouts/Customer.vue';
import { inject } from 'vue';

// Use the global settings provided by the plugin
const settings = inject('brandSettings');
const siteName = settings ? settings.value?.site_name : "Ahlam's Girls";
</script>

<template>
  <CustomerLayout>
    <div class="min-h-screen bg-gradient-to-br from-brand-bg to-brand-primary py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
          <h1 class="font-brand-elegant text-4xl text-white mb-4">Terms of Use</h1>
          <p class="text-brand-gold text-xl">Understanding your agreement with {{ siteName || "Ahlam's Girls" }}</p>
        </div>

        <div class="bg-white bg-opacity-90 backdrop-blur-sm rounded-2xl p-8 border border-brand-gold border-opacity-20">
          <div class="prose prose-lg max-w-none">
            <h2 class="font-brand-elegant text-2xl text-brand-primary mb-6">Welcome to {{ siteName || "Ahlam's Girls" }}</h2>
            
            <p class="text-brand-secondary mb-6">
              These terms and conditions outline the rules and regulations for the use of {{ siteName || "Ahlam's Girls" }}'s Website, located at {{ window.location.origin }}.
            </p>
            
            <p class="text-brand-secondary mb-6">
              By accessing this website we assume you accept these terms and conditions. Do not continue to use {{ siteName || "Ahlam's Girls" }} if you do not agree to take all of the terms and conditions stated on this page.
            </p>

            <h3 class="font-brand-elegant text-xl text-brand-primary mt-8 mb-4">Cookies</h3>
            <p class="text-brand-secondary mb-6">
              We employ the use of cookies. By accessing {{ siteName || "Ahlam's Girls" }}, you agreed to use cookies in agreement with the {{ siteName || "Ahlam's Girls" }}'s Privacy Policy.
            </p>

            <h3 class="font-brand-elegant text-xl text-brand-primary mt-8 mb-4">License</h3>
            <p class="text-brand-secondary mb-6">
              Unless otherwise stated, {{ siteName || "Ahlam's Girls" }} and/or its licensors own the intellectual property rights for all material on {{ siteName || "Ahlam's Girls" }}. All intellectual property rights are reserved. You may access this from {{ siteName || "Ahlam's Girls" }} for your own personal use subjected to restrictions set in these terms and conditions.
            </p>

            <h3 class="font-brand-elegant text-xl text-brand-primary mt-8 mb-4">User Responsibilities</h3>
            <ul class="list-disc pl-6 text-brand-secondary mb-6 space-y-2">
              <li>You must not republish material from {{ siteName || "Ahlam's Girls" }}</li>
              <li>You must not sell, rent or sub-license material from {{ siteName || "Ahlam's Girls" }}</li>
              <li>You must not reproduce, duplicate or copy material from {{ siteName || "Ahlam's Girls" }}</li>
              <li>You must not engage in any data mining or similar extraction activities</li>
            </ul>

            <h3 class="font-brand-elegant text-xl text-brand-primary mt-8 mb-4">Intellectual Property Rights</h3>
            <p class="text-brand-secondary mb-6">
              "All content on this website (logos, images, text, etc.) is owned by {{ siteName || "Ahlam's Girls" }} and:" is proprietary to {{ siteName || "Ahlam's Girls" }} or its content suppliers and is protected by international copyright laws. Your use of any content on this website is governed by these Terms of Use and applicable law.
            </p>

            <h3 class="font-brand-elegant text-xl text-brand-primary mt-8 mb-4">Limitation of Liability</h3>
            <p class="text-brand-secondary mb-6">
              In no event shall {{ siteName || "Ahlam's Girls" }}, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your access to or use of or inability to access or use the service.
            </p>

            <h3 class="font-brand-elegant text-xl text-brand-primary mt-8 mb-4">Changes to These Terms</h3>
            <p class="text-brand-secondary mb-6">
              We reserve the right, at our sole discretion, to modify or replace these terms at any time. By continuing to access or use our service after those revisions become effective, you agree to be bound by the revised terms.
            </p>

            <div class="mt-12 pt-8 border-t border-brand-gold border-opacity-20">
              <p class="text-brand-secondary text-sm">
                If you have any questions about these Terms of Use, please contact us at {{ settings?.site_email || 'contact@ahlamsgirls.com' }}.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>
