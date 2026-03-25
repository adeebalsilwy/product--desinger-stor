<script setup>
import CustomerLayout from '@/Layouts/Customer.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { inject, onMounted, ref } from 'vue';

const form = useForm({
  email: '',
  password: '',
  remember: false
});

const settings = inject('brandSettings');
const siteName = settings ? settings.value?.site_name : "Ahlam's Girls";
const siteLogo = settings ? settings.value?.logo : "/assets/logo.png";
const bubblesRef = ref(null);

const submitForm = () => {
  form.post(route('login'), {
    onFinish: () => {
      form.reset('password');
    }
  });
};

const loginWithDemoCredentials = () => {
  form.email = 'customer@dshirts.com';
  form.password = 'customer123';
  submitForm();
};

const createBubbles = () => {
  if (!bubblesRef.value) return;
  bubblesRef.value.innerHTML = '';
  const count = 14;
  for (let i = 0; i < count; i += 1) {
    const bubble = document.createElement('div');
    bubble.className = 'bubble';
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
          <div class="auth-logo">
            <img :src="siteLogo" :alt="siteName" />
          </div>
          <h1 class="auth-title">{{ $t('auth.login_title') }}</h1>

          <div class="social-login">
            <div class="social-buttons">
              <button class="social-btn google-btn" type="button">
                <i class="fab fa-google"></i>
                {{ $t('auth.google') }}
              </button>
              <button class="social-btn facebook-btn" type="button">
                <i class="fab fa-facebook-f"></i>
                {{ $t('auth.facebook') }}
              </button>
            </div>
            <div class="divider">
              <span class="divider-text">{{ $t('auth.divider') }}</span>
            </div>
          </div>

          <form class="auth-form-body" @submit.prevent="submitForm">
            <div class="input-group">
              <label for="email">{{ $t('auth.email') }}</label>
              <input
                id="email"
                type="email"
                v-model="form.email"
                required
                autocomplete="email"
                :placeholder="$t('auth.email_placeholder')"
              />
            </div>
            <div class="input-group">
              <label for="password">{{ $t('auth.password') }}</label>
              <input
                id="password"
                type="password"
                v-model="form.password"
                required
                autocomplete="current-password"
                :placeholder="$t('auth.password_placeholder')"
              />
            </div>

            <div class="auth-actions">
              <label class="remember">
                <input type="checkbox" v-model="form.remember" />
                <span>{{ $t('auth.remember') }}</span>
              </label>
              <!-- Forgot password link temporarily disabled -->
            </div>

            <button class="btn btn-primary" type="submit" :disabled="form.processing">
              <i class="fas fa-sign-in-alt"></i>
              {{ form.processing ? $t('auth.signing_in') : $t('auth.sign_in') }}
            </button>
          </form>

          <div class="demo-login-section">
            <button class="btn btn-demo" @click="loginWithDemoCredentials" type="button">
              <i class="fas fa-user-secret"></i>
              {{ $t('auth.demo_login') }}
            </button>
          </div>

          <div class="auth-links">
            <p>
              {{ $t('auth.no_account') }}
              <Link :href="route('register')">{{ $t('auth.create_account') }}</Link>
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

.social-login {
  margin: 22px 0 14px;
  position: relative;
  z-index: 2;
}

.social-buttons {
  display: flex;
  gap: 12px;
  justify-content: center;
  flex-wrap: wrap;
}

.social-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border-radius: 14px;
  border: 0;
  background: #e0e5ec;
  box-shadow: 5px 5px 10px #a3b1c6, -5px -5px 10px #ffffff;
  cursor: pointer;
  transition: box-shadow 0.2s;
}

.social-btn:hover {
  box-shadow: inset 5px 5px 10px #a3b1c6, inset -5px -5px 10px #ffffff;
}

.google-btn {
  color: #db4437;
}

.facebook-btn {
  color: #4267b2;
}

.divider {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 18px 0;
}

.divider::before,
.divider::after {
  content: "";
  flex: 1;
  border-bottom: 1px solid #d1d9e6;
}

.divider-text {
  color: #6d7587;
  font-size: 13px;
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

.auth-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  margin-top: 10px;
  color: #6d7587;
}

.remember {
  display: inline-flex;
  gap: 6px;
  align-items: center;
}

.auth-link {
  color: #4a7eff;
  text-decoration: none;
}

.auth-site {
  margin-top: 8px;
  font-size: 12px;
  color: #6d7587;
}

.auth-logo {
  text-align: center;
  margin-bottom: 15px;
}

.auth-logo img {
  max-width: 120px;
  max-height: 60px;
  object-fit: contain;
}

.demo-login-section {
  margin: 15px 0;
  text-align: center;
}

.btn-demo {
  background: #f0f0f0;
  color: #4a7eff;
  border: none;
  cursor: pointer;
  border-radius: 14px;
  font-size: 14px;
  padding: 10px 16px;
  box-shadow: 5px 5px 10px #a3b1c6, -5px -5px 10px #ffffff;
  transition: box-shadow 0.2s, transform 0.2s;
  width: auto;
}

.btn-demo:hover {
  box-shadow: inset 5px 5px 10px #a3b1c6, inset -5px -5px 10px #ffffff;
}

.btn-demo:active {
  transform: translateY(1px);
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
