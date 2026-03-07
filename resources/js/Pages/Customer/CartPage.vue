<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import Customer from "@/Layouts/Customer.vue";
import { ref, computed, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import Remove from "@/Icons/Remove.vue";
import { useTextHelpers } from "@/plugins/textHelpers";
import Tshirt from "@/Icons/Tshirt.vue";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import InputText from "primevue/inputtext";
import FloatLabel from "primevue/floatlabel";
import Select from "primevue/select";
import { countriesList } from "@/plugins/coutries";
import { useForm } from "@inertiajs/vue3";
import Card from "@/Icons/Card.vue";
import Popover from "primevue/popover";
import CopyText from "@/Components/CopyText.vue";

defineOptions({ layout: Customer });

const props = defineProps({
    clientSecret: {
        type: Object,
    },
});

const page = usePage();

const toast = useToast();

const cart = computed(() => page.props.cart);

const cartTotal = computed(() =>
    page.props.cart.reduce(
        (acc, item) => acc + item.tshirt_price * item.quantity,
        0
    )
);

const textHelper = useTextHelpers();

function removeItem(id) {
    router.delete(route("cart.remove", { id: id }), {
        onSuccess: () => {
            toast.add({
                severity: "info",
                summary: "Item removed",
                detail: "Item removed from cart",
                life: 3000,
            });
        },
        onError: () => {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: "Item could not be removed from cart",
                life: 3000,
            });
        },
    });
}

function increaseQuantity(id) {
    router.post(route("cart.increaseQuantity", { id: id }));
}

function decreaseQuantity(id) {
    router.post(route("cart.decreaseQuantity", { id: id }));
}

const countries = countriesList();
const selectedCountry = ref();

const checkoutForm = useForm({
    fullname: "",
    email: "",
    phone: "",
    address: "",
    zipcode: "",
    country: "",
});

const checkProcessing = ref(false);
function handleCheckoutForm() {
    if (page.props.auth) {
        toast.add({
            severity: "warn",
            summary: "Warning",
            detail: "You can't order as Admin, please logout first",
            life: 3000,
        });
        return;
    }
    checkProcessing.value = true; // Disable the button
    checkoutForm.country = selectedCountry.value;
    axios
        .post(route("cart.checkout"), checkoutForm, {
            headers: {
                "X-Inertia": false,
            },
        })
        .then((response) => {
            window.location.href = response.data.url; // Redirect to Stripe Checkout
        })
        .catch((error) => {
            if (error.response && error.response.status === 422) {
                // Handle validation errors
                const errors = error.response.data.errors;
                const firstError = Object.values(errors)[0][0]; // Get the first error message
                toast.add({
                    severity: "error",
                    summary: "Validation Error",
                    detail: firstError,
                    life: 3000,
                });
            } else {
                // Handle other errors
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "An error occurred",
                    life: 3000,
                });
            }
        })
        .finally(() => {
            checkProcessing.value = false; // Re-enable the button after the request completes
        });
}

const card = ref();
const showCard = (event) => {
    card.value.toggle(event);
};
</script>

<template>
    <div class="">
        <Head title="Cart" />
        <Toast position="top-center" />
        <Popover ref="card" class="w-fit text-nowrap">
            <div class="space-y-2">
                <div>
                    <CopyText text="4242 4242 4242 4242" class="text-xl" />
                </div>
                <div class="flex justify-between">
                    <CopyText text="12/34" />
                    <CopyText text="123" />
                </div>
            </div>
        </Popover>

        <div class="max-w-7xl mx-auto pt-10 px-8 space-y-6">
            <!-- Top Navigation -->
            <div
                class="flex justify-between items-center md:w-2/3 w-full pe-2 md:pt-0 pt-4"
            >
                <h1 class="font-secondary font-semibold text-xl">
                    Shopping cart
                </h1>
                <Link
                    v-if="cart.length > 0"
                    class="bg-slate-700 text-slate-200 px-2 py-1p rounded-md flex justify-center items-center gap-2"
                    :href="route('home')"
                >
                    <Tshirt class="w-[1.3rem]" />
                    <p>Back to shop</p>
                </Link>
            </div>
            <div class="w-full h-[1px] bg-slate-500"></div>

            <!-- Plain Cart -->
            <div v-if="cart.length > 0" class="flex md:flex-row flex-col gap-4">
                <!-- Cart Items -->
                <div
                    class="md:w-2/3 w-full flex flex-col divide-y-2 md:divide-gray-500 h-[75vh] overflow-y-auto"
                >
                    <!-- Mobile Cart Items -->
                    <div
                        v-for="item in cart"
                        :key="item.tshirt_id"
                        class="md:hidden relative flex md:flex-row flex-col items-center gap-3 bg-gray-100 p-2 rounded-md shadow-md mb-2"
                    >
                        <div class="flex gap-2">
                            <div class="w-1/2">
                                <img
                                    :src="item.tshirt_image"
                                    alt=""
                                    class="w-full object-cover"
                                />
                            </div>
                            <div class="space-y-3 pt-4">
                                <p>
                                    Size:<span
                                        class="ms-2 font-semibold bg-gray-100 py-1 px-2 rounded-md shadow-md"
                                        >{{ item.size }}</span
                                    >
                                </p>
                                <p>
                                    Price:<span
                                        class="ms-2 font-semibold bg-gray-100 py-1 px-2 rounded-md shadow-md"
                                    >
                                        ${{ item.tshirt_price }}</span
                                    >
                                </p>
                                <div class="text-center space-y-1 pt-4">
                                    <div
                                        class="flex justify-center items-center gap-2"
                                    >
                                        <button
                                            @click="
                                                decreaseQuantity(item.item_id)
                                            "
                                            :disabled="item.quantity == 1"
                                            class="bg-slate-400 hover:bg-slate-500 text-white w-8 h-8 rounded-full disabled:cursor-not-allowed disabled:bg-slate-300"
                                        >
                                            -
                                        </button>
                                        <p
                                            class="border border-slate-400 py-2 px-4 rounded-md bg-white"
                                        >
                                            {{ item.quantity }}
                                        </p>
                                        <button
                                            @click="
                                                increaseQuantity(item.item_id)
                                            "
                                            :disabled="item.quantity == 10"
                                            class="bg-slate-400 hover:bg-slate-500 text-white w-8 h-8 rounded-full disabled:cursor-not-allowed disabled:bg-slate-300"
                                        >
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="w-full flex justify-around items-center gap-2"
                        >
                            <p class="font-main font-semibold">
                                {{
                                    textHelper.limitText(item.tshirt_title, 25)
                                }}
                            </p>
                            <div
                                class="w-3/12 flex flex-col justify-center items-center gap-1"
                            >
                                <p>Subtotal:</p>
                                <p class="text-2xl font-main text-teal-600">
                                    ${{ item.tshirt_price * item.quantity }}
                                </p>
                            </div>
                        </div>
                        <div
                            @click="removeItem(item.item_id)"
                            class="absolute right-3 top-3"
                        >
                            <Remove
                                class="w-5 h-5 text-slate-700 hover:text-red-800 cursor-pointer"
                            />
                        </div>
                    </div>
                    <!-- Desktop Cart Items -->
                    <div
                        v-for="item in cart"
                        :key="item.tshirt_id"
                        class="hidden relative md:flex items-center h-40 gap-3"
                    >
                        <div class="w-2/12">
                            <img
                                :src="item.tshirt_image"
                                alt=""
                                class="w-full object-cover"
                            />
                        </div>
                        <div class="w-4/12 space-y-3">
                            <p class="font-main font-semibold">
                                {{
                                    textHelper.limitText(item.tshirt_title, 25)
                                }}
                            </p>
                            <p>
                                Size:<span
                                    class="ms-2 font-semibold bg-gray-100 py-1 px-2 rounded-md shadow-md"
                                    >{{ item.size }}</span
                                >
                            </p>
                            <p>
                                Price:<span
                                    class="ms-2 font-semibold bg-gray-100 py-1 px-2 rounded-md shadow-md"
                                >
                                    ${{ item.tshirt_price }}</span
                                >
                            </p>
                        </div>
                        <div class="w-3/12 text-center space-y-1">
                            <p class="text-sm text-gray-500">Quantity</p>
                            <div class="flex justify-center items-center gap-2">
                                <button
                                    @click="decreaseQuantity(item.item_id)"
                                    :disabled="item.quantity == 1"
                                    class="bg-slate-400 hover:bg-slate-500 text-white w-8 h-8 rounded-full disabled:cursor-not-allowed disabled:bg-slate-300"
                                >
                                    -
                                </button>
                                <p
                                    class="border border-slate-400 py-2 px-4 rounded-md bg-white"
                                >
                                    {{ item.quantity }}
                                </p>
                                <button
                                    @click="increaseQuantity(item.item_id)"
                                    :disabled="item.quantity == 10"
                                    class="bg-slate-400 hover:bg-slate-500 text-white w-8 h-8 rounded-full disabled:cursor-not-allowed disabled:bg-slate-300"
                                >
                                    +
                                </button>
                            </div>
                        </div>
                        <div
                            class="w-3/12 flex flex-col justify-center items-center gap-1"
                        >
                            <p>Subtotal:</p>
                            <p class="text-2xl font-main text-teal-600">
                                ${{ item.tshirt_price * item.quantity }}
                            </p>
                        </div>
                        <div
                            @click="removeItem(item.item_id)"
                            class="absolute right-3 top-3"
                        >
                            <Remove
                                class="w-5 h-5 text-slate-700 hover:text-red-800 cursor-pointer"
                            />
                        </div>
                    </div>
                </div>
                <!-- Checkout Form -->
                <form
                    @submit.prevent="handleCheckoutForm()"
                    class="md:w-1/3 w-full bg-slate-50 border border-slate-300 rounded-xl shadow-xl min-h-[75vh] my-4 p-4 space-y-4 divide-y-2 overflow-y-auto"
                >
                    <!-- Contact Information -->
                    <div class="flex flex-col gap-4 w-full">
                        <p
                            class="font-secondary text-xs font-bold text-slate-400"
                        >
                            Contact Information
                        </p>
                        <FloatLabel variant="on" class="w-full">
                            <InputText
                                id="name"
                                v-model="checkoutForm.fullname"
                                class="w-full"
                            />
                            <label for="name">Full Name</label>
                        </FloatLabel>
                        <FloatLabel variant="on" class="w-full">
                            <InputText
                                id="email"
                                v-model="checkoutForm.email"
                                class="w-full"
                            />
                            <label for="email">Email</label>
                        </FloatLabel>
                        <FloatLabel variant="on" class="w-full">
                            <InputText
                                id="phone"
                                v-model="checkoutForm.phone"
                                class="w-full"
                            />
                            <label for="phone">Phone</label>
                        </FloatLabel>
                    </div>
                    <!-- Shipping Address -->
                    <div class="flex flex-col gap-4 w-full py-2">
                        <p
                            class="font-secondary text-xs font-bold text-slate-400"
                        >
                            Shipping Address
                        </p>
                        <FloatLabel variant="on" class="w-full">
                            <InputText
                                id="address"
                                v-model="checkoutForm.address"
                                class="w-full"
                            />
                            <label for="address">Address</label>
                        </FloatLabel>
                        <div class="flex gap-2 w-full">
                            <FloatLabel variant="on" class="w-1/2">
                                <InputText
                                    id="zip_code"
                                    v-model="checkoutForm.zipcode"
                                    class="w-full"
                                />
                                <label for="zip_code">Zip Code</label>
                            </FloatLabel>
                            <Select
                                v-model="selectedCountry"
                                :options="countries"
                                filter
                                optionLabel="name"
                                placeholder="Select a Country"
                                class="w-1/2 md:w-56"
                            >
                                <template #value="slotProps">
                                    <div
                                        v-if="slotProps.value"
                                        class="flex items-center"
                                    >
                                        <img
                                            :alt="slotProps.value.label"
                                            src="https://primefaces.org/cdn/primevue/images/flag/flag_placeholder.png"
                                            :class="`mr-2 flag flag-${slotProps.value.code.toLowerCase()}`"
                                            style="width: 18px"
                                        />
                                        <div>{{ slotProps.value.name }}</div>
                                    </div>
                                    <span v-else>
                                        {{ slotProps.placeholder }}
                                    </span>
                                </template>
                                <template #option="slotProps">
                                    <div class="flex items-center">
                                        <img
                                            :alt="slotProps.option.label"
                                            src="https://primefaces.org/cdn/primevue/images/flag/flag_placeholder.png"
                                            :class="`mr-2 flag flag-${slotProps.option.code.toLowerCase()}`"
                                            style="width: 18px"
                                        />
                                        <div>{{ slotProps.option.name }}</div>
                                    </div>
                                </template>
                            </Select>
                        </div>
                    </div>
                    <!-- Total Price -->
                    <div class="pt-2 flex justify-end items-end gap-2">
                        <p
                            class="font-secondary text-lg font-semibold text-slate-400"
                        >
                            Total Price:
                        </p>
                        <p
                            class="text-3xl font-main text-teal-700 bg-white p-2 border border-slate-300 rounded-md shadow-md"
                        >
                            ${{ parseFloat(cartTotal).toFixed(2) }}
                        </p>
                    </div>
                    <!-- Checkout Button -->
                    <div>
                        <div class="flex justify-start items-center gap-2 my-2">
                            <p class="text-xs font-main text-slate-700">
                                Want to test the payment? Use a Stripe test
                                card! 👉
                            </p>
                            <div @click="showCard">
                                <Card
                                    class="w-6 h-6 text-slate-700 hover:text-green-500 cursor-pointer"
                                />
                            </div>
                        </div>
                        <button
                            class="btn w-full"
                            :class="
                                checkProcessing
                                    ? 'cursor-wait bg-green-300'
                                    : 'bg-green-500'
                            "
                            :disabled="checkProcessing"
                        >
                            <span v-if="checkProcessing">...</span>
                            <span v-else>Proceed to checkout</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Empty Cart -->
            <div
                v-else
                class="h-[80vh] flex flex-col justify-center items-center"
            >
                <img
                    src="../../../../public/assets/empty-cart.png"
                    alt=""
                    class="w-64"
                />
                <div class="place-items-center">
                    <p class="font-main mb-4">
                        You don’t have any items in your cart.
                    </p>
                    <Link :href="route('home')" class="btn"
                        >Continue Shopping</Link
                    >
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
