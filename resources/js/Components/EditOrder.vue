<script setup>
import { ref } from "vue";
import Dialog from "primevue/dialog";
import Select from "primevue/select";
import InputText from "primevue/inputtext";
import Pen from "@/Icons/Pen.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";

const props = defineProps({
    orderId: {
        type: Number,
        required: true,
    },
    status: {
        type: String,
        required: true,
    },
    trackingNumber: {
        type: [String, null],
        required: true,
    },
});
const toast = useToast();


const showModal = ref(false);

const statuses = ref([
    { name: "⏸️ Pending", value: "pending" },
    { name: "♻️ Processing", value: "processing" },
    { name: "🚚 Delivered", value: "delivered" },
    { name: "⛔ Cancelled", value: "cancelled" },
]);

const form = useForm({
    status: statuses.value.find((s) => s.value === props.status) || null,
    tracking_number: props.trackingNumber,
});

function updateOrder() {
    form.put(route("orders.update", props.orderId), {
        preserveState: false,
        onSuccess: () => {
            showModal.value = false;
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "Order updated successfully",
                life: 3000,
            });
        },
        onError: () => {
            const errorMessage = Object.values(form.errors)[0];
            toast.add({
                severity: "error",
                summary: "Error",
                detail: errorMessage,
                life: 3000,
            });
        },
    });
}
</script>

<template>
    <Toast />
    <div class="" @click.stop="showModal = !showModal">
        <Pen
            class="text-slate-500 w-16 h-16 hover:text-slate-600 hover:scale-125 smooth p-6"
        />
    </div>

    <Dialog
        v-model:visible="showModal"
        modal
        header="Update Order"
        :style="{ width: '25rem' }"
    >
        <form @submit.prevent="updateOrder" class="flex flex-col gap-4">
            <div class="flex flex-col justify-center">
                <p>Status</p>
                <Select
                    v-model="form.status"
                    :options="statuses"
                    optionLabel="name"
                    placeholder="Select a State"
                    class="w-full"
                    checkmark
                />
            </div>
            <div class="flex flex-col justify-center">
                <p>Tracking</p>
                <InputText type="text" v-model="form.tracking_number" />
            </div>

            <button
                class="btn"
                :class="
                    form.processing
                        ? 'cursor-not-allowed bg-green-300'
                        : 'bg-green-500'
                "
                :disabled="form.processing"
            >
                <span v-if="form.processing">updating...</span>
                <span v-else>Update</span>
            </button>
        </form>
    </Dialog>
</template>
