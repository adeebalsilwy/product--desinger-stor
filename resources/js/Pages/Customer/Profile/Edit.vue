<script setup>
import CustomerLayout from '@/Layouts/Customer.vue';
import axios from 'axios';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, getCurrentInstance } from 'vue';

const props = defineProps({
  designs: {
    type: Array,
    default: () => [],
  },
});

const page = usePage();
const user = page.props.auth?.user;
const { proxy } = getCurrentInstance();
const t = (key) => (proxy?.$t ? proxy.$t(key) : key);

const form = useForm({
  name: user?.name || '',
  email: user?.email || '',
  phone: user?.phone || '',
});

const showEditForm = ref(false);
const profileImage = ref('/images/logo.jpeg');
const fileInputRef = ref(null);

const triggerFileInput = () => {
  fileInputRef.value?.click();
};

const handleImageChange = (event) => {
  const file = event.target.files?.[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = (e) => {
    profileImage.value = e.target.result;
  };
  reader.readAsDataURL(file);
};

const submit = () => {
  form.patch(route('customer.profile.update'), {
    preserveScroll: true,
    onSuccess: () => {
      showEditForm.value = false;
    },
  });
};

const editDesign = (designId) => {
  router.visit(route('designer.edit', { design: designId }));
};

const deleteDesign = async (designId) => {
  if (!confirm(t('profile.delete_design_confirm'))) return;
  try {
    await axios.delete(`/api/designs/${designId}`);
    window.location.reload();
  } catch (error) {
    alert(t('profile.delete_design_failed'));
  }
};
</script>

<template>
  <CustomerLayout :showNav="false" :showFooter="false" :showCart="false" contentClass="profile-shell">
    <div class="profile-container">
      <header class="profile-header">
        <div class="profile-image-wrap">
          <img class="profile-image" :src="profileImage" :alt="form.name || 'profile'" />
          <button class="camera-icon" type="button" @click="triggerFileInput" aria-label="Upload profile image">
            <i class="fas fa-camera"></i>
          </button>
          <input
            ref="fileInputRef"
            class="hidden-input"
            type="file"
            accept="image/*"
            @change="handleImageChange"
          />
        </div>

        <h1 class="profile-name">{{ form.name || $t('profile.name_placeholder') }}</h1>
        <p class="profile-email">{{ form.email || $t('profile.email_placeholder') }}</p>

        <button class="btn btn-primary" type="button" @click="showEditForm = true">
          <i class="fas fa-edit"></i>
          {{ $t('profile.edit_profile') }}
        </button>
      </header>

      <form class="edit-form" @submit.prevent="submit" v-show="showEditForm">
        <div class="form-group">
          <label for="nameInput">{{ $t('profile.name') }}</label>
          <input id="nameInput" type="text" v-model="form.name" />
        </div>
        <div class="form-group">
          <label for="emailInput">{{ $t('profile.email') }}</label>
          <input id="emailInput" type="email" v-model="form.email" />
        </div>
        <div class="form-group">
          <label for="phoneInput">{{ $t('profile.phone') }}</label>
          <input id="phoneInput" type="text" v-model="form.phone" />
        </div>
        <div class="form-actions">
          <button class="btn btn-success" type="submit" :disabled="form.processing">
            <i class="fas fa-save"></i>
            {{ form.processing ? $t('profile.saving') : $t('profile.save') }}
          </button>
          <button class="btn btn-danger" type="button" @click="showEditForm = false">
            <i class="fas fa-times"></i>
            {{ $t('profile.cancel') }}
          </button>
          <span v-if="$page.props.status === 'profile-updated'" class="success-message">
            {{ $t('profile.saved') }}
          </span>
        </div>
      </form>

      <h2 class="section-title">{{ $t('profile.saved_designs') }}</h2>
      <div v-if="props.designs.length" class="designs-list">
        <div v-for="design in props.designs" :key="design.id" class="design-item">
          <span class="design-title">{{ design.name }}</span>
          <div class="design-actions">
            <button class="btn btn-primary" type="button" @click="editDesign(design.id)">
              <i class="fas fa-edit"></i>
              {{ $t('profile.edit_design') }}
            </button>
            <button class="btn btn-danger" type="button" @click="deleteDesign(design.id)">
              <i class="fas fa-trash"></i>
              {{ $t('profile.delete_design') }}
            </button>
          </div>
        </div>
      </div>
      <div v-else class="designs-empty">
        {{ $t('profile.empty_designs') }}
      </div>
    </div>
  </CustomerLayout>
</template>

<style>
.profile-shell {
  background: #e0e5ec;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.profile-container {
  width: 100%;
  max-width: 520px;
  background: #e0e5ec;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff;
}

.profile-header {
  text-align: center;
  margin-bottom: 20px;
}

.profile-image-wrap {
  position: relative;
  width: 160px;
  height: 160px;
  margin: 0 auto 14px;
}

.profile-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 16px;
  box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff;
  background: #ddd;
  display: block;
}

.camera-icon {
  position: absolute;
  bottom: 10px;
  left: 10px;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #4a7eff;
  color: #fff;
  cursor: pointer;
  box-shadow: 0 2px 5px rgba(0,0,0,.18);
  border: none;
}

.hidden-input {
  display: none;
}

.profile-name {
  font-size: 22px;
  margin: 10px 0 6px;
  color: #4a5568;
}

.profile-email {
  color: #6d7587;
  margin: 0 0 14px;
  font-size: 13px;
}

.btn {
  border: 0;
  cursor: pointer;
  user-select: none;
  padding: 11px 18px;
  border-radius: 15px;
  font-size: 14px;
  color: #6d7587;
  background: #e0e5ec;
  box-shadow: 5px 5px 10px #a3b1c6, -5px -5px 10px #ffffff;
  transition: box-shadow .2s ease, transform .2s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn:hover {
  box-shadow: inset 5px 5px 10px #a3b1c6, inset -5px -5px 10px #ffffff;
}

.btn:active {
  transform: translateY(1px);
}

.btn-primary {
  color: #4a7eff;
}

.btn-danger {
  color: #ff6b6b;
}

.btn-success {
  color: #4caf50;
}

.edit-form {
  margin-top: 18px;
  padding: 18px;
  border-radius: 15px;
  background: #e0e5ec;
  box-shadow: inset 5px 5px 10px #a3b1c6, inset -5px -5px 10px #ffffff;
}

.form-group {
  margin-bottom: 14px;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  color: #4a5568;
  font-weight: bold;
  font-size: 13px;
}

.form-group input {
  width: 100%;
  padding: 11px 12px;
  border: 0;
  border-radius: 12px;
  background: #e0e5ec;
  box-shadow: inset 3px 3px 6px #a3b1c6, inset -3px -3px 6px #ffffff;
  font-size: 14px;
}

.form-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  align-items: center;
}

.success-message {
  color: #4caf50;
  font-weight: bold;
}

.section-title {
  margin: 22px 0 12px;
  font-size: 16px;
  font-weight: bold;
  color: #4a5568;
  position: relative;
  padding-right: 10px;
}

.section-title::after {
  content: "";
  position: absolute;
  bottom: -8px;
  right: 0;
  width: 60px;
  height: 3px;
  background: #4a7eff;
  border-radius: 8px;
}

.designs-list {
  margin-top: 16px;
  display: grid;
  gap: 10px;
}

.design-item {
  background: #e0e5ec;
  border-radius: 15px;
  padding: 12px 14px;
  box-shadow: 5px 5px 10px #a3b1c6, -5px -5px 10px #ffffff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  transition: box-shadow .2s ease;
}

.design-item:hover {
  box-shadow: inset 5px 5px 10px #a3b1c6, inset -5px -5px 10px #ffffff;
}

.design-title {
  font-size: 14px;
  color: #4a5568;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 220px;
}

.design-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.designs-empty {
  margin-top: 10px;
  color: #6d7587;
  text-align: center;
}

@media (max-width: 480px) {
  .profile-container {
    padding: 20px;
  }

  .profile-image-wrap {
    width: 140px;
    height: 140px;
  }

  .btn {
    padding: 10px 14px;
    font-size: 13px;
  }

  .design-title {
    max-width: 140px;
  }
}
</style>
