<script setup>
import { Head, Link } from "@inertiajs/vue3";
import CustomerLayout from '@/Layouts/Customer.vue';
import { ref, getCurrentInstance } from 'vue';

const form = ref({
    name: '',
    email: '',
    message: ''
});

const instance = getCurrentInstance();
const t = (key) => instance?.proxy?.$t ? instance.proxy.$t(key) : key;

const submitForm = () => {
    console.log('Form submitted:', form.value);
    // Add form submission logic here
    alert(t('contact.form.success'));
    form.value = { name: '', email: '', message: '' };
};
</script>

<template>
    <CustomerLayout>
        <div class="contact-page-container">
            <!-- Wave Effect -->
            <div class="waves-container">
                <div class="waves">
                    <svg viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
                        <defs>
                            <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 v44h-352z" />
                        </defs>
                        <g class="parallax">
                            <use xlink:href="#gentle-wave" x="48" y="0" />
                            <use xlink:href="#gentle-wave" x="48" y="3" />
                            <use xlink:href="#gentle-wave" x="48" y="5" />
                            <use xlink:href="#gentle-wave" x="48" y="7" />
                        </g>
                    </svg>
                </div>
            </div>
            
            <div class="contact-content">
                <h1 class="contact-title">{{ $t('contact.title') }}</h1>
                <p class="contact-description">
                    {{ $t('contact.description') }}
                </p>
                
                <div class="contact-grid">
                    <div class="contact-form">
                        <form @submit.prevent="submitForm">
                            <div class="input-group">
                                <label for="name">{{ $t('contact.form.name') }}</label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    v-model="form.name"
                                    required 
                                    :placeholder="$t('contact.form.name_placeholder')"
                                >
                            </div>
                            
                            <div class="input-group">
                                <label for="email">{{ $t('contact.form.email') }}</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    v-model="form.email"
                                    required 
                                    :placeholder="$t('contact.form.email_placeholder')"
                                >
                            </div>
                            
                            <div class="input-group">
                                <label for="message">{{ $t('contact.form.message') }}</label>
                                <textarea 
                                    id="message" 
                                    v-model="form.message"
                                    required 
                                    :placeholder="$t('contact.form.message_placeholder')"
                                ></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> {{ $t('contact.form.submit') }}
                            </button>
                        </form>
                    </div>
                    
                    <div class="contact-info">
                        <div class="contact-item">
                            <a href="tel:2556625566">
                                <i class="fas fa-phone"></i>
                                <span>255-662-5566</span>
                            </a>
                        </div>
                        
                        <div class="contact-item">
                            <a href="mailto:support@ahlamsgirls.com">
                                <i class="fas fa-envelope"></i>
                                <span>support@ahlamsgirls.com</span>
                            </a>
                        </div>
                        
                        <div class="contact-item">
                            <a href="https://www.google.com/maps" target="_blank">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $t('contact.info.location') }}</span>
                            </a>
                        </div>
                        
                        <div class="contact-item">
                            <Link :href="route('home')">
                                <i class="fas fa-comment"></i>
                                <span>{{ $t('contact.info.live_chat') }}</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<style scoped>
/* Contact Page Container */
.contact-page-container {
    background: #e0e5ec;
    min-height: 100vh;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    color: #4a5568;
}

.contact-content {
    width: 90%;
    max-width: 450px;
    background: #e0e5ec;
    border-radius: 50px;
    padding: 5px;
    box-shadow: 
        8px 8px 15px #a3b1c6,
        -8px -8px 15px #ffffff;
    text-align: center;
    position: relative;
    overflow: hidden;
    z-index: 2;
}

/* Contact Title */
.contact-title {
    font-size: 28px;
    margin-bottom: 30px;
    color: #4a5568;
    position: relative;
}

.contact-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    right: 0;
    width: 50px;
    height: 3px;
    background: #4a7eff;
    border-radius: 3px;
}

/* Contact Description */
.contact-description {
    margin-bottom: 30px;
    color: #4a5568;
    line-height: 1.6;
}

/* Input Group */
.input-group {
    margin-bottom: 25px;
    text-align: left;
}

.input-group label {
    display: block;
    margin-bottom: 8px;
    color: #4a5568;
    font-weight: bold;
}

.input-group input,
.input-group textarea {
    width: 100%;
    padding: 15px;
    border: none;
    border-radius: 15px;
    background: #e0e5ec;
    box-shadow: 
        inset 5px 5px 10px #a3b1c6,
        inset -5px -5px 10px #ffffff;
    font-size: 16px;
    transition: all 0.3s ease;
}

.input-group textarea {
    min-height: 150px;
    resize: vertical;
}

.input-group input:focus,
.input-group textarea:focus {
    outline: none;
    box-shadow: 
        inset 3px 3px 6px #a3b1c6,
        inset -3px -3px 6px #ffffff;
}

/* Buttons */
.btn {
    border: none;
    padding: 12px 25px;
    border-radius: 15px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 10px 5px;
    color: #6d7587;
    background: #e0e5ec;
    box-shadow: 
        5px 5px 10px #a3b1c6,
        -5px -5px 10px #ffffff;
}

.btn:hover {
    box-shadow: 
        inset 5px 5px 10px #a3b1c6,
        inset -5px -5px 10px #ffffff;
}

.btn:active {
    box-shadow: 
        inset 3px 3px 6px #a3b1c6,
        inset -3px -3px 6px #ffffff;
}

.btn-primary {
    color: #4a7eff;
    width: 100%;
    margin: 20px 0;
}

/* Contact Info */
.contact-info {
    margin-top: 30px;
    text-align: left;
}

.contact-item {
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
}

.contact-page-container:dir(rtl) .input-group {
    text-align: right;
}

.contact-page-container:dir(rtl) .contact-info {
    text-align: right;
}

.contact-page-container:dir(rtl) .contact-item {
    justify-content: flex-end;
}

.contact-item a {
    display: flex;
    align-items: center;
    color: #4a5568;
    text-decoration: none;
    transition: all 0.3s ease;
}

.contact-item a:hover {
    color: #4a7eff;
}

.contact-item i {
    margin-left: 10px;
    font-size: 20px;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #e0e5ec;
    box-shadow: 
        3px 3px 6px #a3b1c6,
        -3px -3px 6px #ffffff;
}

.contact-item:hover i {
    box-shadow: 
        inset 3px 3px 6px #a3b1c6,
        inset -3px -3px 6px #ffffff;
}

/* Contact Grid */
.contact-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-top: 30px;
}

@media (min-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr 1fr;
    }
}

/* Wave Effect */
.waves-container {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    overflow: hidden;
    height: 80px;
    z-index: 1;
}

.waves {
    position: relative;
    width: 100%;
    height: 100%;
}

.waves svg {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.parallax > use {
    animation: move-forever 25s cubic-bezier(.55,.5,.45,.5) infinite;
    fill: rgba(74, 126, 255, 0.2);
}

.parallax > use:nth-child(1) {
    animation-delay: -2s;
    animation-duration: 7s;
}

.parallax > use:nth-child(2) {
    animation-delay: -3s;
    animation-duration: 10s;
}

.parallax > use:nth-child(3) {
    animation-delay: -4s;
    animation-duration: 13s;
}

.parallax > use:nth-child(4) {
    animation-delay: -5s;
    animation-duration: 20s;
}

@keyframes move-forever {
    0% {
        transform: translate3d(-90px,0,0);
    }
    100% { 
        transform: translate3d(85px,0,0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .waves-container {
        height: 40px;
    }
    
    .contact-content {
        max-width: 100%;
    }
    
    .contact-grid {
        grid-template-columns: 1fr;
    }
}
</style>
