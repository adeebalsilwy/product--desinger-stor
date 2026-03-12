<script setup>
import InputText from "primevue/inputtext";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import { ref } from 'vue';

const page = usePage();
const showCredentials = ref(false);

const form = useForm({
    email: "",
    password: "",
});

const sampleCredentials = [
    { email: 'admin@dshirts.com', password: 'admin123', role: 'Admin', name: 'Admin User' },
    { email: 'staff@dshirts.com', password: 'staff123', role: 'Staff', name: 'Staff User' },
];

const toast = useToast();

const submit = () => {
    form.post(route("login"), {
        onFinish: () => {
            if (form.errors && Object.keys(form.errors).length > 0) {
                toast.add({
                    severity: "error",
                    summary: "Login Failed",
                    detail: "Please check your email and password",
                    life: 3000,
                });
            }
        },
    });
};

const useCredentials = (credential) => {
    form.email = credential.email;
    form.password = credential.password;
};

const goToCustomerLogin = () => {
    window.location.href = route('customer.login');
};
</script>

<template>
    <div class="bg">
        <Head title="Admin Login" />
        <Toast position="top-center" />
        <div
            class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8 min-h-screen flex flex-col gap-3  items-center"
        >
            <img
                src="../../../../public/assets/logo/d-shirts-white.png"
                alt="D-Shirts"
                class="w-20 "
            />
            <h1 class="text-slate-200 font-main text-xl">
                Log in to Admin Dashboard
            </h1>
            <p class="text-slate-300 text-center">
                For customer access, <a href="#" @click="goToCustomerLogin" class="text-blue-300 hover:text-blue-200 underline">click here</a>
            </p>
            <form
                @submit.prevent="submit"
                class="backdrop-blur-sm bg-white/20 md:w-1/3 w-full px-4 py-10 space-y-8 rounded-xl"
            >
                <div class="w-full flex flex-col gap-1">
                    <InputText
                        type="email"
                        v-model="form.email"
                        placeholder="Email"
                        class="w-full"
                        required
                    />
                    <div v-if="form.errors.email">{{ form.errors.email }}</div>
                </div>
                <div class="w-full flex flex-col gap-1">
                    <InputText
                        type="password"
                        v-model="form.password"
                        placeholder="Password"
                        class="w-full"
                        required
                    />
                    <div v-if="form.errors.password">
                        {{ form.errors.password }}
                    </div>
                </div>
                <div>
                    <button type="submit" class="btn w-full">Login</button>
                </div>

                <!-- Sample Credentials Toggle -->
                <div class="text-center pt-4 border-t border-slate-600">
                    <button
                        @click="showCredentials = !showCredentials"
                        class="text-slate-300 hover:text-white text-sm font-medium"
                    >
                        {{ showCredentials ? 'Hide' : 'Show' }} Sample Credentials
                    </button>

                    <!-- Sample Credentials Panel -->
                    <div v-if="showCredentials" class="mt-4 space-y-3">
                        <div class="text-xs text-slate-400 mb-2">
                            Click on any credential to auto-fill the form:
                        </div>
                        <div 
                            v-for="cred in sampleCredentials" 
                            :key="cred.email"
                            @click="useCredentials(cred)"
                            class="p-3 bg-slate-700/50 rounded-lg cursor-pointer hover:bg-slate-600/50 transition-colors"
                        >
                            <div class="font-medium text-slate-200 text-sm">{{ cred.name }} ({{ cred.role }})</div>
                            <div class="text-slate-300 text-xs">{{ cred.email }}</div>
                            <div class="text-slate-400 text-xs">Password: {{ cred.password }}</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.bg {
    background-image: url('../../../../public/assets/login-bg.jpg');
    background-size: cover;
    background-position: center;
    height: 100vh;
}
</style>
