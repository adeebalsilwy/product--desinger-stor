<script setup>
import InputText from "primevue/inputtext";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";

const page = usePage();

const form = useForm({
    email: "",
    password: "",
});

const toast = useToast();

const submit = () => {
    form.post(route("login"), {
        onFinish: () => {
            if (Object.keys(page.props[0]["errors"]).length > 0) {
                toast.add({
                    severity: "error",
                    summary: "Warning",
                    detail: "Wrong Credentials",
                    life: 3000,
                });
            }
        },
    });
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
                alt="d-shirts"
                class="w-20 "
            />
            <h1 class="text-slate-200 font-main text-xl">
                Log in to Admin Dashboard
            </h1>
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
