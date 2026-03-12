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
const paymentMethod = ref('credit_card'); // New state for payment method

function handleCheckoutForm() {
    // Check if user is logged in
    if (page.props.auth && page.props.auth.user) {
        // Check if user is admin/staff
        const userRole = page.props.auth.user.role || 'customer';
        
        if (['admin', 'staff'].includes(userRole.toLowerCase())) {
            // Admin warning notification indicating test mode
            toast.add({
                severity: "warn",
                summary: "Admin Test Mode",
                detail: "You're logged in as admin. This is a test checkout operation. Real orders require customer login.",
                life: 5000,
            });
            // Allow admin to proceed with test functionality
        }
    }

    checkProcessing.value = true; // Disable the button for processing
    
    if (paymentMethod.value === 'credit_card') {
        // Credit card payment (existing functionality)
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
    } else if (paymentMethod.value === 'bank_transfer') {
        // Bank transfer payment
        handleBankTransfer();
    }
}

function handleBankTransfer() {
    // Validate form data
    if (!checkoutForm.fullname || !checkoutForm.email || !checkoutForm.phone || 
        !checkoutForm.address || !checkoutForm.zipcode || !selectedCountry.value) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Please fill in all required fields",
            life: 3000,
        });
        checkProcessing.value = false;
        return;
    }

    // Show additional form for bank transfer details
    const referenceNumber = prompt("Please enter your bank transfer reference number:");
    if (!referenceNumber) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Reference number is required for bank transfer",
            life: 3000,
        });
        checkProcessing.value = false;
        return;
    }

    // Prepare bank transfer data
    const bankTransferData = {
        ...checkoutForm.data(),
        country: selectedCountry.value,
        reference_number: referenceNumber,
        amount: (cartTotal.value * 1.08).toFixed(2),
        transfer_date: new Date().toISOString().split('T')[0],
        bank_details: prompt("Enter bank details (optional):") || null
    };

    axios
        .post(route("cart.bank.transfer"), bankTransferData, {
            headers: {
                "X-Inertia": false,
            },
        })
        .then((response) => {
            if (response.data.success) {
                toast.add({
                    severity: "success",
                    summary: "Success",
                    detail: response.data.message,
                    life: 5000,
                });
                // Clear the cart and redirect to thank you page after a delay
                setTimeout(() => {
                    window.location.href = route('thankYou');
                }, 2000);
            } else {
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: response.data.message || "An error occurred during bank transfer",
                    life: 3000,
                });
            }
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
                    detail: error.response?.data?.message || "An error occurred during bank transfer",
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
    <div class="bg-gray-50 min-h-screen">
        <Head title="Cart" />
        <Toast position="top-center" />
        <Popover ref="card" class="w-fit text-nowrap">
            <div class="space-y-2 p-4">
                <div>
                    <CopyText text="4242 4242 4242 4242" class="text-xl font-mono" />
                </div>
                <div class="flex justify-between">
                    <CopyText text="12/34" />
                    <CopyText text="123" />
                </div>
            </div>
        </Popover>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
                    <p class="text-gray-600 mt-2">{{ cart.length }} item{{ cart.length !== 1 ? 's' : '' }} in cart</p>
                </div>
                <Link
                    v-if="cart.length > 0"
                    :href="route('home')"
                    class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                >
                    <Tshirt class="w-5 h-5 mr-2" />
                    Continue Shopping
                </Link>
            </div>

            <!-- Cart Content -->
            <div v-if="cart.length > 0" class="grid lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">Your Items</h2>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div
                                v-for="item in cart"
                                :key="item.tshirt_id"
                                class="p-6 hover:bg-gray-50 transition-colors"
                            >
                                <div class="flex flex-col sm:flex-row gap-6">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        <img
                                            :src="item.tshirt_image"
                                            :alt="item.tshirt_title"
                                            class="w-24 h-24 object-cover rounded-lg shadow-sm"
                                        />
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                                            {{ textHelper.limitText(item.tshirt_title, 60) }}
                                        </h3>
                                        
                                        <div class="flex flex-wrap gap-4 mb-4">
                                            <div class="flex items-center">
                                                <span class="text-gray-500 mr-2">Size:</span>
                                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">
                                                    {{ item.size }}
                                                </span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-gray-500 mr-2">Price:</span>
                                                <span class="font-semibold text-gray-900">
                                                    ${{ item.tshirt_price }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Quantity Controls -->
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="flex items-center border border-gray-300 rounded-lg">
                                            <button
                                                @click="decreaseQuantity(item.item_id)"
                                                :disabled="item.quantity <= 1"
                                                class="px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-l-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                            >
                                                -
                                            </button>
                                            <span class="px-4 py-2 min-w-[3rem] text-center">
                                                {{ item.quantity }}
                                            </span>
                                            <button
                                                @click="increaseQuantity(item.item_id)"
                                                :disabled="item.quantity >= 10"
                                                class="px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-r-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                            >
                                                +
                                            </button>
                                        </div>
                                        
                                        <div class="text-center">
                                            <p class="text-sm text-gray-500">Subtotal</p>
                                            <p class="text-lg font-bold text-blue-600">
                                                ${{ (item.tshirt_price * item.quantity).toFixed(2) }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Remove Button -->
                                    <div class="flex items-start sm:items-center justify-center">
                                        <button
                                            @click="removeItem(item.item_id)"
                                            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors"
                                            title="Remove item"
                                        >
                                            <Remove class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium">${{ cartTotal.toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium text-green-600">Free</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax</span>
                                <span class="font-medium">${{ (cartTotal * 0.08).toFixed(2) }}</span>
                            </div>
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900">Total</span>
                                    <span class="text-2xl font-bold text-blue-600">
                                        ${{ (cartTotal * 1.08).toFixed(2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Checkout Form -->
                        <form @submit.prevent="handleCheckoutForm()" class="space-y-4">
                            <div class="space-y-3">
                                <FloatLabel variant="on" class="w-full">
                                    <InputText
                                        id="fullname"
                                        v-model="checkoutForm.fullname"
                                        class="w-full"
                                        required
                                    />
                                    <label for="fullname">Full Name</label>
                                </FloatLabel>
                                
                                <FloatLabel variant="on" class="w-full">
                                    <InputText
                                        id="email"
                                        v-model="checkoutForm.email"
                                        type="email"
                                        class="w-full"
                                        required
                                    />
                                    <label for="email">Email</label>
                                </FloatLabel>
                                
                                <FloatLabel variant="on" class="w-full">
                                    <InputText
                                        id="phone"
                                        v-model="checkoutForm.phone"
                                        class="w-full"
                                        required
                                    />
                                    <label for="phone">Phone</label>
                                </FloatLabel>
                            </div>

                            <div class="space-y-3 pt-4">
                                <FloatLabel variant="on" class="w-full">
                                    <InputText
                                        id="address"
                                        v-model="checkoutForm.address"
                                        class="w-full"
                                        required
                                    />
                                    <label for="address">Shipping Address</label>
                                </FloatLabel>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <FloatLabel variant="on" class="w-full">
                                        <InputText
                                            id="zipcode"
                                            v-model="checkoutForm.zipcode"
                                            class="w-full"
                                            required
                                        />
                                        <label for="zipcode">Zip Code</label>
                                    </FloatLabel>
                                    
                                    <Select
                                        v-model="selectedCountry"
                                        :options="countries"
                                        filter
                                        optionLabel="name"
                                        placeholder="Country"
                                        class="w-full"
                                        required
                                    >
                                        <template #value="slotProps">
                                            <div v-if="slotProps.value" class="flex items-center">
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

                            <!-- Test Card Info -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start gap-2">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="text-sm text-blue-700">
                                        <p class="font-medium mb-1">Payment Options</p>
                                        <div class="flex gap-2 mb-2">
                                            <label class="inline-flex items-center">
                                                <input
                                                    type="radio"
                                                    v-model="paymentMethod"
                                                    value="credit_card"
                                                    class="mr-2"
                                                />
                                                <span>Credit Card</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input
                                                    type="radio"
                                                    v-model="paymentMethod"
                                                    value="bank_transfer"
                                                    class="mr-2"
                                                />
                                                <span>Bank Transfer</span>
                                            </label>
                                        </div>
                                        
                                        <div v-if="paymentMethod === 'credit_card'" class="space-y-2">
                                            <p class="mb-2">Use test card for checkout:</p>
                                            <div @click="showCard" class="inline-flex items-center px-3 py-1 bg-white border border-blue-300 rounded-md cursor-pointer hover:bg-blue-50">
                                                <Card class="w-4 h-4 mr-2 text-blue-600" />
                                                <span class="text-blue-600 font-mono text-sm">4242 4242 4242 4242</span>
                                            </div>
                                        </div>
                                        
                                        <div v-if="paymentMethod === 'bank_transfer'" class="space-y-2">
                                            <p class="mb-2">Bank Transfer Instructions:</p>
                                            <ul class="text-xs space-y-1">
                                                <li>• Transfer funds to our designated account</li>
                                                <li>• Include reference number in transfer</li>
                                                <li>• Upload proof of payment in order details</li>
                                                <li>• Order will be processed upon confirmation</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <button
                                type="submit"
                                :disabled="checkProcessing"
                                :class="[
                                    checkProcessing
                                        ? 'cursor-not-allowed bg-blue-400'
                                        : 'bg-blue-600 hover:bg-blue-700 hover:shadow-lg hover:-translate-y-0.5',
                                    'w-full py-3 px-4 rounded-lg font-semibold text-white transition-all duration-300 transform'
                                ]"
                            >
                                <span v-if="checkProcessing">Processing...</span>
                                <span v-else-if="paymentMethod === 'credit_card'">Proceed to Checkout - ${{ (cartTotal * 1.08).toFixed(2) }}</span>
                                <span v-else>Submit Bank Transfer - ${{ (cartTotal * 1.08).toFixed(2) }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Empty Cart -->
            <div v-else class="text-center py-20">
                <div class="max-w-md mx-auto">
                    <div class="w-32 h-32 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h3>
                    <p class="text-gray-600 mb-8">Looks like you haven't added any items to your cart yet.</p>
                    <Link
                        :href="route('home')"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                    >
                        Start Shopping
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>