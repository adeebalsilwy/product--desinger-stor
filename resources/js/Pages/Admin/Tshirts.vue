<script setup>
import Admin from "@/Layouts/Admin.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import { ref } from "vue";
import UploadImage from "@/Components/UploadImage.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import InputText from "primevue/inputtext";
import FloatLabel from "primevue/floatlabel";
import InputNumber from "primevue/inputnumber";
import Textarea from "primevue/textarea";
import ToggleSwitch from "primevue/toggleswitch";
import { useTextHelpers } from "@/plugins/textHelpers";
import Pen from "@/Icons/Pen.vue";
import Remove from "@/Icons/Remove.vue";
import Target from "@/Icons/Target.vue";
import ListingStatus from "@/Components/ListingStatus.vue";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";
import EmptyState from "@/Components/EmptyState.vue";

defineOptions({ layout: Admin });

const props = defineProps({
    tshirts: {
        type: Object,
    },
});

const textHelper = useTextHelpers();

const toast = useToast();
const showAddTshirtModal = ref(false);
const showEditTshirtModal = ref(false);

// Initialize creation form
const createForm = useForm({
    title: "",
    price: 24,
    description: "",
    mainImage: null,
    secondImage: null,
    thirdImage: null,
    forthImage: null,
    fifthImage: null,
});

function handleCreateTshirt() {
    const formData = new FormData();

    // Append images to formData
    if (createForm.mainImage)
        formData.append("mainImage", createForm.mainImage);
    if (createForm.secondImage)
        formData.append("secondImage", createForm.secondImage);
    if (createForm.thirdImage)
        formData.append("thirdImage", createForm.thirdImage);
    if (createForm.forthImage)
        formData.append("forthImage", createForm.forthImage);
    if (createForm.fifthImage)
        formData.append("fifthImage", createForm.fifthImage);

    // Append other form fields to formData
    formData.append("title", createForm.title);
    formData.append("price", createForm.price);
    formData.append("description", createForm.description);

    // Use Inertia to post the formData
    createForm.post("/admin/t-shirts", {
        data: formData,
        forceFormData: true, // Needed to ensure multipart/form-data
        preserveState: false,
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "T-Shirt added successfully",
                life: 3000,
            });
        },
        onError: () => {
            if (createForm.errors.authorization) {
                toast.add({
                    severity: "info",
                    summary: "Not Authorized 👮🏻‍♂️",
                    detail: createForm.errors.authorization,
                });
            } else {
                const errorMessage = Object.values(createForm.errors)[0];
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: errorMessage,
                    life: 3000,
                });
            }
        },
    });
}

// Initialize edit form
const editForm = useForm({
    title: "",
    price: 0,
    description: "",
    listed: true,
    mainImage: null,
    secondImage: null,
    thirdImage: null,
    forthImage: null,
    fifthImage: null,
});

// Edit form preview images
const mainImagePrev = ref(null);
const secondImagePrev = ref(null);
const thirdImagePrev = ref(null);
const forthImagePrev = ref(null);
const fifthImagePrev = ref(null);

function openEditModal(tshirt) {
    showEditTshirtModal.value = true;

    editForm.id = tshirt.id;
    editForm.title = tshirt.title;
    editForm.price = tshirt.price;
    editForm.description = tshirt.description;
    editForm.listed = Boolean(tshirt.listed);

    editForm.mainImage = "originalImage";
    editForm.secondImage = "originalImage";
    editForm.thirdImage = "originalImage";
    editForm.forthImage = "originalImage";
    editForm.fifthImage = "originalImage";

    // preview images
    const imagesArray = Object.values(tshirt.otherImages);
    mainImagePrev.value = tshirt.mainImage?.url || null;
    secondImagePrev.value =
        imagesArray.find((img) => img.order === 2)?.url || null;
    thirdImagePrev.value =
        imagesArray.find((img) => img.order === 3)?.url || null;
    forthImagePrev.value =
        imagesArray.find((img) => img.order === 4)?.url || null;
    fifthImagePrev.value =
        imagesArray.find((img) => img.order === 5)?.url || null;
}

function handleUpdateTshirt() {
    const editFormData = new FormData();

    // Append images to formData
    if (editForm.mainImage)
        editFormData.append("mainImage", editForm.mainImage);
    if (editForm.secondImage)
        editFormData.append("secondImage", editForm.secondImage);
    if (editForm.thirdImage)
        editFormData.append("thirdImage", editForm.thirdImage);
    if (editForm.forthImage)
        editFormData.append("forthImage", editForm.forthImage);
    if (editForm.fifthImage)
        editFormData.append("fifthImage", editForm.fifthImage);

    // Append other form fields to editFormData
    editFormData.append("title", editForm.title);
    editFormData.append("price", editForm.price);
    editFormData.append("description", editForm.description);
    editFormData.append("listed", editForm.listed);

    // Use Inertia to post the editFormData
    editForm.post(route("t-shirts.update", editForm.id), {
        data: editFormData,
        forceFormData: true, // Needed to ensure multipart/form-data
        preserveState: false,
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "T-Shirt updated successfully",
                life: 3000,
            });
        },
        onError: () => {
            if (editForm.errors.authorization) {
                toast.add({
                    severity: "info",
                    summary: "Not Authorized 👮🏻‍♂️",
                    detail: editForm.errors.authorization,
                });
            } else {
                const errorMessage = Object.values(editForm.errors)[0];
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: errorMessage,
                    life: 3000,
                });
            }
        },
    });
}

const confirmDelete = useConfirm();

const confirmDeleteTshirt = (tshirtId) => {
    confirmDelete.require({
        group: "templating",
        message: "Are you sure you want to proceed?",
        header: "Delete this T-Shirt ?",
        rejectProps: {
            label: "Cancel",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Delete",
            severity: "danger",
        },
        accept: () => {
            router.delete(route("t-shirts.destroy", tshirtId), {
                onSuccess: page => {
                    toast.add({
                        severity: "success",
                        summary: "Success",
                        detail: "T-Shirt deleted successfully",
                        life: 3000,
                    });
                },
                onError: errors => {
                    toast.add({
                        severity: "error",
                        summary: "Error",
                        detail: errors.authorization,
                        life: 3000,
                    });
                },
            });
        },
    });
};
</script>

<template>
    <div>
        <Head title="T-Shirts" />
        <Toast />
        <ConfirmDialog group="templating" class="w-full md:w-1/2 lg:w-1/3">
            <template #message="slotProps">
                <div class="flex flex-col items-center">
                    <div class="bg-rose-500 rounded-full p-3 mb-4">
                        <svg
                            class="h-8 w-8 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">
                        Are you sure?
                    </h2>
                    <p class="text-gray-600 mb-6">
                        Deleting this T-Shirt will also remove all related
                        images and orders. If you just want to hide it from the
                        public, you can uncheck the "Listed" checkbox on the
                        edit page.
                    </p>
                </div>
            </template>
        </ConfirmDialog>

        <!-- Add T-Shirt Modal -->
        <Dialog
            class="mx-2"
            v-model:visible="showAddTshirtModal"
            modal
            header="Create New T-Shirt"
            :style="{ width: '70rem' }"
        >
            <form class="p-2" @submit.prevent="handleCreateTshirt">
                <!-- Infos Section -->
                <div class="mb-4">
                    <div
                        class="w-full flex md:flex-row flex-col md:gap-2 gap-6 my-6"
                    >
                        <FloatLabel variant="on" class="md:w-3/4 w-full">
                            <InputText
                                id="title"
                                v-model="createForm.title"
                                class="w-full"
                            />
                            <label for="title">T-shirt Title</label>
                        </FloatLabel>
                        <div class="md:w-1/4 w-full">
                            <InputNumber
                                class="w-full"
                                :min="0"
                                v-model="createForm.price"
                                inputId="price"
                                showButtons
                                mode="currency"
                                currency="USD"
                                fluid
                            />
                        </div>
                    </div>
                    <FloatLabel variant="on" class="w-full">
                        <Textarea
                            class="w-full"
                            id="description"
                            v-model="createForm.description"
                            rows="3"
                            cols="30"
                            style="resize: none"
                        />
                        <label for="description">T-shirt Description</label>
                    </FloatLabel>
                </div>

                <!-- Images Section -->
                <div
                    class="w-full md:h-[30rem] h-full flex md:flex-row flex-col justify-center items-center gap-4 mb-4"
                >
                    <!-- Main Image -->
                    <div class="md:w-1/2 w-full">
                        <UploadImage
                            id="mainImage"
                            width="w-full"
                            height="h-[30rem]"
                            label="Main Image"
                            v-model="createForm.mainImage"
                        />
                    </div>

                    <!-- Other Images -->
                    <div
                        class="md:w-1/2 w-full h-[30rem] grid grid-cols-2 gap-4"
                    >
                        <UploadImage
                            id="secondImage"
                            width="w-full"
                            height="h-[14.5rem]"
                            label="Second Image"
                            v-model="createForm.secondImage"
                        />
                        <UploadImage
                            id="thirdImage"
                            width="w-full"
                            height="h-[14.5rem]"
                            label="Third Image"
                            v-model="createForm.thirdImage"
                        />
                        <UploadImage
                            id="forthImage"
                            width="w-full"
                            height="h-[14.5rem]"
                            label="Forth Image"
                            v-model="createForm.forthImage"
                        />
                        <UploadImage
                            id="fifthImage"
                            width="w-full"
                            height="h-[14.5rem]"
                            label="Fifth Image"
                            v-model="createForm.fifthImage"
                        />
                    </div>
                </div>

                <!-- Submit Button -->
                <button
                    class="rounded-md p-2 w-full text-white text-base font-semibold transition-all duration-100 ease-in-out"
                    :class="
                        createForm.processing
                            ? 'cursor-not-allowed bg-slate-500'
                            : ' bg-green-500 hover:bg-green-600'
                    "
                    :disabled="createForm.processing"
                >
                    <span v-if="createForm.processing">Creating...</span>
                    <span v-else>Create the T-shirt</span>
                </button>
            </form>
        </Dialog>

        <!-- Edit T-shirt Modal -->
        <Dialog
            class="mx-2"
            v-model:visible="showEditTshirtModal"
            modal
            header="EditT-Shirt"
            :style="{ width: '70rem' }"
        >
            <form class="p-2" @submit.prevent="handleUpdateTshirt">
                <div class="mb-4">
                    <!-- Title, Price, and Listed Section -->
                    <div
                        class="w-full flex md:flex-row flex-col md:gap-2 gap-6 my-6"
                    >
                        <FloatLabel variant="on" class="md:w-9/12 w-full">
                            <InputText
                                id="title"
                                v-model="editForm.title"
                                class="w-full"
                            />
                            <label for="title">T-shirt Title</label>
                        </FloatLabel>
                        <div class="md:w-2/12 w-full">
                            <InputNumber
                                class="w-full"
                                :min="0"
                                v-model="editForm.price"
                                inputId="price"
                                showButtons
                                mode="currency"
                                currency="USD"
                                fluid
                            />
                        </div>
                        <div
                            class="md:w-1/12 w-full flex flex-col justify-center md:items-center items-start gap-0 md:order-last order-first"
                        >
                            <label class="md:text-xs text-base" for="listed">{{
                                editForm.listed ? "Listed" : "Unlisted"
                            }}</label>
                            <ToggleSwitch
                                v-model="editForm.listed"
                                id="listed"
                                class="md:w-full w-full"
                            />
                        </div>
                    </div>

                    <!-- Description Section -->
                    <FloatLabel variant="on" class="w-full">
                        <Textarea
                            class="w-full"
                            id="description"
                            v-model="editForm.description"
                            rows="5"
                            cols="30"
                            style="resize: none"
                        />
                        <label for="description">T-shirt Description</label>
                    </FloatLabel>

                    <!-- Images Section -->
                    <div
                        class="w-full md:h-[30rem] h-full flex md:flex-row flex-col justify-center items-center gap-4 mb-4"
                    >
                        <!-- Main Image -->
                        <div class="md:w-1/2 w-full">
                            <UploadImage
                                id="mainImage"
                                width="w-full"
                                height="h-[30rem]"
                                label="Main Image"
                                v-model="editForm.mainImage"
                                :defaultImage="mainImagePrev"
                            />
                        </div>

                        <!-- Other Images -->
                        <div
                            class="md:w-1/2 w-full h-[30rem] grid grid-cols-2 gap-4"
                        >
                            <UploadImage
                                id="secondImage"
                                width="w-full"
                                height="h-[14.5rem]"
                                label="Second Image"
                                v-model="editForm.secondImage"
                                :defaultImage="secondImagePrev"
                            />
                            <UploadImage
                                id="thirdImage"
                                width="w-full"
                                height="h-[14.5rem]"
                                label="Third Image"
                                v-model="editForm.thirdImage"
                                :defaultImage="thirdImagePrev"
                            />
                            <UploadImage
                                id="forthImage"
                                width="w-full"
                                height="h-[14.5rem]"
                                label="Forth Image"
                                v-model="editForm.forthImage"
                                :defaultImage="forthImagePrev"
                            />
                            <UploadImage
                                id="fifthImage"
                                width="w-full"
                                height="h-[14.5rem]"
                                label="Fifth Image"
                                v-model="editForm.fifthImage"
                                :defaultImage="fifthImagePrev"
                            />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                        class="rounded-md p-2 w-full text-white text-base font-semibold transition-all duration-100 ease-in-out"
                        :class="
                            editForm.processing
                                ? 'cursor-not-allowed bg-slate-500'
                                : ' bg-green-500 hover:bg-green-600'
                        "
                        :disabled="editForm.processing"
                    >
                        <span v-if="editForm.processing">Updating...</span>
                        <span v-else>Update the T-shirt</span>
                    </button>
                </div>
            </form>
        </Dialog>

        <!-- Add T-Shirt button -->
        <div class="w-full flex justify-end">
            <button
                @click="showAddTshirtModal = !showAddTshirtModal"
                class="btn"
            >
                Add T-Shirt
            </button>
        </div>

        <!-- T-shirts List -->
        <div
            v-if="tshirts.data.length > 0"
            class="w-full overflow-x-auto grid lg:grid-cols-3 md:grid-cols-2 grid-flow-cols-1 gap-12 pb-8 pt-6"
        >
            <div
                v-for="tshirt in tshirts.data"
                :key="tshirt.id"
                class="flex flex-col gap-2 bg-slate-200 w-full h-full p-3 rounded-md shadow-md border-2 border-gray-300"
            >
                <div class="relative">
                    <img
                        :src="tshirt.mainImage?.url"
                        class="w-full h-full object-cover rounded-md"
                    />
                    <p class="absolute top-2 left-2 text-sm text-red z-10">
                        <ListingStatus :is-listed="!!tshirt.listed" />
                    </p>
                    <a
                        :href="`/t-shirt/${tshirt.slug}`"
                        target="_blank"
                        class="absolute top-2 right-2 text-sm text-red z-10 flex items-center gap-1"
                    >
                        <Target
                            class="w-5 h-5 text-slate-600 hover:h-6 hover:w-6 hover:text-blue-500 smooth"
                        />
                    </a>
                </div>
                <div class="flex justify-between my-3 px-1 items-center gap-1">
                    <p class="text-lg font-bold text-nowrap">
                        {{ textHelper.limitText(tshirt.title, 30) }}
                    </p>
                </div>
                <div class="flex justify-between items-center gap-4 mb-3">
                    <div
                        class="bg-teal-600 w-1/4 p-2 border border-white rounded-md text-start text-white flex items-center gap-1"
                    >
                        <p>Price:</p>
                        <p class="font-bold w-full text-center">
                            {{ "$" + tshirt.price }}
                        </p>
                    </div>
                    <div
                        class="bg-teal-600 w-1/4 p-2 border border-white rounded-md text-start text-white flex items-center gap-1"
                    >
                        <p>Sells:</p>
                        <p class="font-bold w-full text-center">
                            {{ tshirt.totalSells }}
                        </p>
                    </div>
                    <div
                        class="bg-teal-800 w-1/2 p-2 border border-white rounded-md text-start text-white flex items-center gap-1"
                    >
                        <p>Revenue:</p>
                        <p class="font-bold text-xl w-full text-center">
                            {{ "$" + tshirt.totalRevenue }}
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-2">
                    <template v-for="i in 4" :key="i">
                        <div
                            class="flex flex-col gap-2 bg-white w-full h-24 rounded-md shadow-md"
                        >
                            <template
                                v-if="
                                    Object.values(tshirt.otherImages).find(
                                        (img) => img.order === i + 1
                                    )
                                "
                            >
                                <img
                                    :src="
                                        Object.values(tshirt.otherImages).find(
                                            (img) => img.order === i + 1
                                        ).url
                                    "
                                    class="w-full h-full object-cover rounded-md"
                                />
                            </template>
                            <div v-else class="w-12 h-12 rounded-md"></div>
                        </div>
                    </template>
                </div>
                <div class="flex gap-2 w-full">
                    <button
                        class="rounded-md p-2 text-white text-sm font-semibold bg-slate-500 hover:bg-green-600 transition-all duration-100 ease-in-out w-1/2 flex items-center gap-2 justify-center"
                        @click="openEditModal(tshirt)"
                    >
                        <Pen class="w-4 h-4" />
                        Edit
                    </button>
                    <button
                        class="rounded-md p-2 text-white text-sm font-semibold bg-slate-800 hover:bg-red-600 transition-all duration-100 ease-in-out w-1/2 flex items-center gap-2 justify-center"
                        @click="confirmDeleteTshirt(tshirt.id)"
                    >
                        <Remove class="w-4 h-4" />
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else>
            <EmptyState title="No T-Shirts Yet !" />
        </div>

        <!-- Pagination -->
        <div
            v-if="tshirts.data.length > 0"
            class="mt-4 mb-12 flex md:flex-row flex-col md:gap-0 gap-2 justify-between items-center w-full"
        >
            <!-- results -->
            <div class="md:order-1 order-2">
                <p class="text-base text-slate-800">
                    Showing
                    <span class="text-green-600 font-bold text-lg">{{
                        tshirts.from
                    }}</span>
                    to
                    <span class="text-green-600 font-bold text-lg"
                        >{{ tshirts.to }}
                    </span>
                    of {{ tshirts.total }} tshirts
                </p>
            </div>
            <nav class="">
                <div class="flex items-center -space-x-px h-8 text-sm">
                    <template
                        v-for="(link, index) in tshirts.links"
                        :key="link.url"
                    >
                        <Link
                            :preserve-scroll="true"
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 transition-colors"
                            :class="{
                                'text-gray-500 bg-white hover:bg-gray-100 hover:text-gray-700':
                                    !link.active,
                                'bg-green-500 text-white hover:bg-green-600':
                                    link.active,
                                'rounded-l-lg': index === 0,
                                'rounded-r-lg':
                                    index === tshirts.links.length - 1,
                            }"
                        />
                        <p
                            v-else
                            v-html="link.label"
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-slate-200 border border-gray-300"
                            :class="{
                                'rounded-l-lg': index === 0,
                                'rounded-r-lg':
                                    index === tshirts.links.length - 1,
                            }"
                        />
                    </template>
                </div>
            </nav>
        </div>
    </div>
</template>
