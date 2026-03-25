<script setup>
import CustomerLayout from "@/Layouts/Customer.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { inject, onMounted, ref } from "vue";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const settings = inject("brandSettings");
const siteName = settings ? settings.value?.site_name : "Ahlam's Girls";
const bubblesRef = ref(null);

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

const createBubbles = () => {
    if (!bubblesRef.value) return;
    bubblesRef.value.innerHTML = "";
    const count = 14;
    for (let i = 0; i < count; i += 1) {
        const bubble = document.createElement("div");
        bubble.className = "bubble";
        const size = Math.random() * 60 + 22;
        bubble.style.width = `${size}px`;
        bubble.style.height = `${size}px`;
        bubble.style.left = `${Math.random() * 100}%`;
        bubble.style.animationDuration = `${Math.random() * 8 + 10}s`;
        bubble.style.animationDelay = `${Math.random() * 8}s`;
        bubblesRef.value.appendChild(bubble);
    }
};

onMounted(() => {
    createBubbles();
});
</script>

<template>
    <CustomerLayout>
        <div class="auth-page">
            <div class="auth-card">
                <div class="auth-illustration" aria-hidden="true"></div>
                <div class="auth-form">
                    <div class="bubbles-container" ref="bubblesRef"></div>
                    <h1 class="auth-title">{{ $t("auth.register_title") }}</h1>

                    <form class="auth-form-body" @submit.prevent="submit">
                        <div class="input-group">
                            <label for="name">{{ $t("auth.name") }}</label>
                            <input
                                id="name"
                                type="text"
                                v-model="form.name"
                                required
                                autocomplete="name"
                                :placeholder="$t('auth.name_placeholder')"
                            />
                            <div v-if="form.errors.name" class="form-error">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="email">{{ $t("auth.email") }}</label>
                            <input
                                id="email"
                                type="email"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                :placeholder="$t('auth.email_placeholder')"
                            />
                            <div v-if="form.errors.email" class="form-error">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="password">{{ $t("auth.password") }}</label>
                            <input
                                id="password"
                                type="password"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                :placeholder="$t('auth.password_placeholder')"
                            />
                            <div v-if="form.errors.password" class="form-error">
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="password_confirmation">{{ $t("auth.confirm_password") }}</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                :placeholder="$t('auth.confirm_password_placeholder')"
                            />
                            <div v-if="form.errors.password_confirmation" class="form-error">
                                {{ form.errors.password_confirmation }}
                            </div>
                        </div>

                        <button class="btn btn-primary" type="submit" :disabled="form.processing">
                            <i class="fas fa-user-plus"></i>
                            {{ form.processing ? $t('auth.creating_account') : $t('auth.create_account') }}
                        </button>
                    </form>

                    <div class="auth-links">
                        <p>
                            {{ $t("auth.have_account") }}
                            <Link :href="route('login')">{{ $t("auth.sign_in") }}</Link>
                        </p>
                        <p class="auth-site">{{ siteName }}</p>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<style scoped>
.auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px 20px;
    background: #e0e5ec;
}

.auth-card {
    width: 100%;
    max-width: 920px;
    min-height: 460px;
    background: #e0e5ec;
    border-radius: 24px;
    box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff;
    overflow: hidden;
    display: flex;
    direction: ltr;
}

.auth-illustration {
    flex: 1 1 48%;
    background: url("/assets/login-bg.jpg") center/cover no-repeat;
    position: relative;
}

.auth-illustration::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(74,126,255,0.1));
}

.auth-form {
    flex: 1 1 52%;
    padding: 28px;
    position: relative;
    direction: rtl;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.auth-title {
    font-size: 22px;
    margin: 0 0 14px;
    color: #4a5568;
    position: relative;
    z-index: 2;
}

.auth-title::after {
    content: "";
    position: absolute;
    bottom: -10px;
    right: 0;
    width: 60px;
    height: 3px;
    background: #4a7eff;
    border-radius: 10px;
}

.input-group {
    margin: 16px 0;
    position: relative;
    z-index: 2;
}

.input-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
}

.input-group input {
    width: 100%;
    font-size: 14px;
    padding: 12px 14px;
    border: none;
    border-radius: 14px;
    background: #e0e5ec;
    box-shadow: inset 5px 5px 10px #a3b1c6, inset -5px -5px 10px #ffffff;
    transition: box-shadow 0.25s ease;
}

.input-group input:focus {
    outline: 0;
    box-shadow: inset 3px 3px 6px #a3b1c6, inset -3px -3px 6px #ffffff;
}

.btn {
    border: none;
    cursor: pointer;
    border-radius: 14px;
    font-size: 14px;
    padding: 12px 16px;
    color: #6d7587;
    background: #e0e5ec;
    box-shadow: 5px 5px 10px #a3b1c6, -5px -5px 10px #ffffff;
    transition: box-shadow 0.2s, transform 0.2s;
    position: relative;
    z-index: 2;
    width: 100%;
}

.btn:hover {
    box-shadow: inset 5px 5px 10px #a3b1c6, inset -5px -5px 10px #ffffff;
}

.btn:active {
    transform: translateY(1px);
}

.btn-primary {
    color: #4a7eff;
    margin-top: 12px;
}

.auth-links {
    text-align: center;
    margin-top: 14px;
    position: relative;
    z-index: 2;
}

.auth-links a {
    color: #4a7eff;
    text-decoration: none;
}

.auth-links a:hover {
    text-decoration: underline;
}

.auth-site {
    margin-top: 8px;
    font-size: 12px;
    color: #6d7587;
}

.form-error {
    margin-top: 6px;
    font-size: 12px;
    color: #e53e3e;
}

.bubbles-container {
    position: absolute;
    inset: 0;
    z-index: 1;
    pointer-events: none;
    border-radius: 24px;
    overflow: hidden;
}

.bubble {
    position: absolute;
    bottom: -100px;
    background: rgba(74, 126, 255, 0.18);
    border-radius: 50%;
    animation: bubble 14s linear infinite;
    opacity: 0.6;
}

@keyframes bubble {
    0% {
        transform: translateY(0) rotate(0);
        opacity: 0.6;
        border-radius: 50%;
    }
    100% {
        transform: translateY(-900px) rotate(720deg);
        opacity: 0;
        border-radius: 42%;
    }
}

@media (max-width: 820px) {
    .auth-card {
        flex-direction: column;
        direction: rtl;
    }

    .auth-illustration {
        min-height: 180px;
    }

    .auth-form {
        padding: 22px 20px;
    }
}
</style>
