<script setup>
import { Head } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Customer from "@/Layouts/Customer.vue";
import ImageCarousel from "@/Components/ImageCarousel.vue";
import Price from "@/Components/TshirtPage/Price.vue";
import Reviews from "@/Components/TshirtPage/Reviews.vue";
import InputNumber from "primevue/inputnumber";
import Description from "@/Components/TshirtPage/Description.vue";
import Quality from "@/Icons/Quality.vue";
import Card from "@/Icons/Card.vue";
import World from "@/Icons/World.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import { usePage } from "@inertiajs/vue3";

defineOptions({ layout: Customer });

const props = defineProps({
    tshirt: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const cartDuplicationError = computed(() => page.props[0].errors.cart);

const toast = useToast();

const mainImage = props.tshirt.images
    .map((image) => (image.order === 1 ? image.url : null))
    .filter(Boolean);
const cartForm = useForm({
    tshirtId: props.tshirt.id,
    tshirtTitle: props.tshirt.title,
    tshirtImage: mainImage[0],
    tshirtPrice: props.tshirt.price,
    size: "",
    quantity: 1,
});

function handleAddToCart() {
    // check if user is logged in
    if (page.props.auth) {
        toast.add({
            severity: "warn",
            summary: "Warning",
            detail: "You can't order as Admin, please logout first",
            life: 3000,
        });
        return;
    }
    if (cartForm.size === "") {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Please select a size",
            life: 3000,
        });
        return;
    }

    cartForm.post(route("cart.add"), {
        onSuccess: () => {
             if (Object.keys(page.props[0].errors).length === 0) {
                toast.add({
                    severity: "success",
                    summary: "Success",
                    detail: "Added to cart successfully",
                    life: 3000,
                });
            } else {
                toast.add({
                    severity: "warn",
                    summary: "Warning",
                    detail: page.props[0].errors.cart || "An error occurred",
                    life: 3000,
                });
            }
        },
    });
}

const sizes = ["XS", "S", "M", "L", "XL", "XXL"];
</script>

<template>
    <div class="py-16">
        <Toast position="top-center" />
        <Head title="Tshirt Page" />
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="flex flex-col md:gap-16 gap-4 md:flex-row py-2 px-4">
                <form
                    @submit.prevent="handleAddToCart"
                    class="md:w-1/2 w-full order-2 flex flex-col gap-8 justify-between"
                >
                    <div class="">
                        <div class="space-y-2 mb-6">
                            <p
                                class="md:text-3xl text-2xl font-bold text-gray-800 font-main"
                            >
                                {{ tshirt.title }}
                            </p>
                            <Price :price="tshirt.price" />
                            <Reviews :num="tshirt.title" />
                            <div class="bg-slate-500 h-[1px] w-full"></div>
                        </div>
                        <div class="flex flex-col gap-6">
                            <!-- Size -->
                            <div class="space-y-1">
                                <p
                                    class="text-lg text-slate-800 font-main ms-1"
                                >
                                    Size
                                </p>
                                <div class="flex gap-2">
                                    <div v-for="size in sizes" :key="size">
                                        <label
                                            class="cursor-pointer py-2 px-4 rounded-lg border-2 border-slate-500"
                                            :class="
                                                cartForm.size === size
                                                    ? 'bg-slate-700  text-slate-200'
                                                    : 'bg-white text-slate-600 hover:bg-slate-100'
                                            "
                                            :for="size"
                                            >{{ size }}</label
                                        >
                                        <input
                                            class="hidden"
                                            type="radio"
                                            name="dataFilter"
                                            v-model="cartForm.size"
                                            :value="size"
                                            :id="size"
                                        />
                                    </div>
                                </div>
                            </div>
                            <!-- Quantity -->
                            <div class="">
                                <p
                                    class="text-lg text-slate-800 font-main ms-1"
                                >
                                    Quantity
                                </p>
                                <InputNumber
                                    v-model="cartForm.quantity"
                                    inputId="horizontal-buttons"
                                    :min="1"
                                    :max="10"
                                    showButtons
                                    buttonLayout="horizontal"
                                    :step="1"
                                >
                                    <div>
                                        <p>+</p>
                                    </div>
                                    <div>
                                        <p>-</p>
                                    </div>
                                </InputNumber>
                            </div>
                            <!-- Features -->
                            <div class="space-y-1 mt-4">
                                <div
                                    class="flex justify-start items-center gap-2 text-slate-500"
                                >
                                    <Quality class="w-4 h-4" />
                                    <p>Premium quality material</p>
                                </div>
                                <div
                                    class="flex justify-start items-center gap-2 text-slate-500"
                                >
                                    <Card class="w-4 h-4" />
                                    <p>100% Secured Payment</p>
                                </div>
                                <div
                                    class="flex justify-start items-center gap-2 text-slate-500"
                                >
                                    <World class="w-4 h-4" />
                                    <p>Free shipping worldwide</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button
                    :class="
                        cartForm.processing
                            ? 'cursor-not-allowed bg-slate-500'
                            : ' bg-green-500 hover:bg-green-600'
                    "
                    :disabled="cartForm.processing"
                    class="btn w-full !p-3">Add to Cart</button>
                </form>

                <div class="md:w-1/2 w-full order-1 flex flex-col gap-24">
                    <ImageCarousel
                        :images="tshirt.images.map((image) => image.url)"
                    />
                </div>
            </div>
            <div>
                <Description
                    :title="tshirt.title"
                    :description="tshirt.description"
                />
            </div>
        </div>
    </div>
</template>
