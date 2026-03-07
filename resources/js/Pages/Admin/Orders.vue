<script setup>
import Admin from "@/Layouts/Admin.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, nextTick, watch } from "vue";
import { useTextHelpers } from "@/plugins/textHelpers";
import Status from "@/Components/Status.vue";
import CopyText from "@/Components/CopyText.vue";
import EditOrder from "@/Components/EditOrder.vue";
import Popover from "primevue/popover";
import Copy from "@/Icons/Copy.vue";
import PaymentStatus from "@/Components/PaymentStatus.vue";

defineOptions({ layout: Admin });

const props = defineProps({
    orders: {
        type: Object,
        required: true,
    },
    currentFilter: String,
});

// Keep track of expanded rows
const expandedRows = ref(new Set());

// Toggle row expansion
const toggleRow = (orderId) => {
    if (expandedRows.value.has(orderId)) {
        expandedRows.value.delete(orderId);
    } else {
        expandedRows.value.add(orderId);
    }
};

const textHelper = useTextHelpers();

const op = ref();
const selectedOrder = ref(null);
const toggleAddressPopOver = (event, customer) => {
    op.value.hide();

    // Create an object with the order details
    const orderDetails = {
        id: customer.id,
        country: customer.country,
        zipcode: customer.zipcode,
        address: customer.address,
    };

    if (selectedOrder.value?.id === customer.id) {
        selectedOrder.value = null;
    } else {
        selectedOrder.value = orderDetails;
        nextTick(() => {
            op.value.show(event);
        });
    }
};
const copyAddress = () => {
    op.value.hide();
    const fullAddress = `${selectedOrder.value.country}, ${selectedOrder.value.zipcode}, ${selectedOrder.value.address}`;
    navigator.clipboard.writeText(fullAddress);
};

const selectedFilter = ref(props.currentFilter);

watch(selectedFilter, (newFilter) => {
    router.get(route("orders"), {
        filter: newFilter,
    });
});
</script>

<template>
    <!-- Table -->
    <div class="pb-12">
        <Head title="Orders" />

        <!-- Address Popover -->
        <Popover ref="op">
            <div v-if="selectedOrder">
                <div class="flex items-center justify-start gap-1 relative pe-8">
                    <Copy
                        @click="copyAddress"
                        class="w-5 h-5 absolute top-0 right-0 text-slate-600 cursor-pointer hover:h-[1.3rem] hover:w-[1.3rem] smooth"
                    />
                    <p class="font-bold">
                        {{ selectedOrder.country }}
                    </p>
                    <p>,</p>
                    <p class="">{{ selectedOrder.zipcode }}</p>
                </div>
                <p class="text-sm">
                    {{ textHelper.limitText(selectedOrder.address, 50) }}
                </p>
            </div>
        </Popover>

        <!-- Filters -->
        <div class="flex gap-2 mb-2 mt-9">
            <div>
                <label
                    class="cursor-pointer p-1 rounded-lg border-2 border-slate-500"
                    :class="
                        selectedFilter === 'all'
                            ? 'bg-slate-700  text-slate-200'
                            : 'bg-white text-slate-600 hover:bg-slate-100'
                    "
                    for="all"
                    >All</label
                >
                <input
                    class="hidden"
                    type="radio"
                    name="ordersFilter"
                    v-model="selectedFilter"
                    value="all"
                    id="all"
                />
            </div>
            <div>
                <label
                    class="cursor-pointer p-1 rounded-lg border-2 border-slate-500"
                    :class="
                        selectedFilter === 'pending'
                            ? 'bg-yellow-400 text-white'
                            : 'bg-white text-slate-600 hover:bg-slate-100'
                    "
                    for="pending"
                    >Pending</label
                >
                <input
                    class="hidden"
                    type="radio"
                    name="ordersFilter"
                    v-model="selectedFilter"
                    value="pending"
                    id="pending"
                />
            </div>
            <div>
                <label
                    class="cursor-pointer p-1 rounded-lg border-2 border-slate-500"
                    :class="
                        selectedFilter === 'processing'
                            ? 'bg-blue-500 text-white'
                            : 'bg-white text-slate-600 hover:bg-slate-100'
                    "
                    for="processing"
                    >Processing</label
                >
                <input
                    class="hidden"
                    type="radio"
                    name="ordersFilter"
                    v-model="selectedFilter"
                    value="processing"
                    id="processing"
                />
            </div>
            <div>
                <label
                    class="cursor-pointer p-1 rounded-lg border-2 border-slate-500"
                    :class="
                        selectedFilter === 'delivered'
                            ? 'bg-emerald-500 text-white'
                            : 'bg-white text-slate-600 hover:bg-slate-100'
                    "
                    for="delivered"
                    >Delivered</label
                >
                <input
                    class="hidden"
                    type="radio"
                    name="ordersFilter"
                    v-model="selectedFilter"
                    value="delivered"
                    id="delivered"
                />
            </div>
            <div>
                <label
                    class="cursor-pointer p-1 rounded-lg border-2 border-slate-500"
                    :class="
                        selectedFilter === 'cancelled'
                            ? 'bg-red-500 text-white'
                            : 'bg-white text-slate-600 hover:bg-slate-100'
                    "
                    for="cancelled"
                    >Cancelled</label
                >
                <input
                    class="hidden"
                    type="radio"
                    name="ordersFilter"
                    v-model="selectedFilter"
                    value="cancelled"
                    id="cancelled"
                />
            </div>
        </div>

        <!-- Table -->
        <div class="max-w-7xl overflow-x-auto table-container">
            <table
                class="min-w-full divide-y divide-gray-200 bg-white shadow-md table-auto"
            >
                <thead class="">
                    <tr>
                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase"
                        >
                            #
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase"
                        >
                            Customer
                        </th>
                        <th
                            scope="col"
                            class="px-2 py-3 text-start text-xs font-medium text-gray-500 uppercase text-nowrap"
                        >
                            Shipping Address
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase text-nowrap"
                        >
                            Items
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase"
                        >
                            Amount
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase"
                        >
                            <p class="ms-2">Order Status</p>
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase text-nowrap"
                        >
                            Tracking Number
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase"
                        >
                            <p class="ms-2">Payment Status</p>
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-start"
                        >
                            Date
                        </th>
                        <th
                            scope="col"
                            class="w-24 py-3 text-xs font-medium text-gray-500 uppercase text-center"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template
                        v-if="orders.data.length > 0"
                        v-for="order in orders.data"
                        :key="order.id"
                    >
                        <!-- Main Order Row -->
                        <tr
                            @click="toggleRow(order.id)"
                            class="cursor-pointer"
                            :class="{
                                ' bg-gray-200 hover:bg-gray-300':
                                    expandedRows.has(order.id),
                                ' hover:bg-gray-50 cursor-pointer ':
                                    !expandedRows.has(order.id),
                            }"
                        >
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 align-middle"
                            >
                                <div class="flex items-center">
                                    <span class="mr-2">{{ order.id }}</span>
                                    <!-- Expand/Collapse Icon -->
                                    <svg
                                        :class="{
                                            'transform rotate-180':
                                                expandedRows.has(order.id),
                                        }"
                                        class="w-4 h-4 transition-all duration-300 ease-in-out"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 align-middle"
                            >
                                <p class="font-bold">
                                    {{
                                        textHelper.limitText(
                                            order.customer.name,
                                            20
                                        )
                                    }}
                                </p>
                                <p class="text-sm">
                                    {{
                                        textHelper.limitText(
                                            order.customer.email,
                                            35
                                        )
                                    }}
                                </p>
                            </td>
                            <!-- // -->
                            <td>
                                <div
                                    @click.stop="
                                        toggleAddressPopOver(
                                            $event,
                                            order.customer
                                        )
                                    "
                                    class="bg-slate-200 p-1 rounded-md text-slate-600 hover:bg-slate-300 smooth"
                                >
                                    {{textHelper.limitText(
                                            order.customer.country,
                                            15
                                        )}}
                                </div>
                            </td>
                            <!-- // -->
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 text-center align-middle"
                            >
                                {{ order.total_tshirts }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-bold text-center align-middle"
                            >
                                {{ order.total_amount + " $" ?? 0 }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 align-middle"
                            >
                                <Status :type="order.status" />
                            </td>
                            <td class=" ">
                                <div class="flex justify-center">
                                    <CopyText
                                        v-if="order.tracking_number"
                                        :text="order.tracking_number"
                                        message="Text copied!"
                                        class="bg-slate-200 rounded-md text-slate-500 w-fit px-2 hover:bg-slate-300 smooth"
                                    />
                                    <p
                                        v-else
                                        class="text-gray-500 text-sm text-center"
                                    >
                                        N/A
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 align-middle">
                                <PaymentStatus :type=" order.payment_status" />
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 align-middle"
                            >
                                <div>
                                   <p> {{ order.created_at }}</p>
                                    <p>( {{ order.created_at_human }})</p>
                                </div>
                            </td>
                            <td class="">
                                <div class="flex justify-center">
                                    <EditOrder
                                        :order-id="order.id"
                                        :status="order.status"
                                        :tracking-number="order.tracking_number"
                                    />
                                </div>
                            </td>
                        </tr>

                        <!-- Expanded T-shirts Details Row -->
                        <tr v-if="expandedRows.has(order.id)">
                            <td colspan="10" class="bg-slate-200 py-2 px-3">
                                <div class="grid grid-cols-4 gap-3">
                                    <div
                                        v-for="tshirt in order.tshirts"
                                        :key="tshirt.id"
                                        class="border rounded p-3 bg-white shadow-sm hover:shadow-md"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center"
                                        >
                                            <img
                                                class="w-1/2 object-cover"
                                                :src="
                                                    tshirt.images.find(
                                                        (img) => img.order === 1
                                                    ).url
                                                "
                                                alt=""
                                            />
                                            <div
                                                class="w-full text-center flex flex-col justify-center items-center"
                                            >
                                                <p class="font-bold">
                                                    {{
                                                        textHelper.limitText(
                                                            tshirt.title,
                                                            30
                                                        )
                                                    }}
                                                </p>
                                                <p class="my-2">size: <span class="font-semibold bg-white py-1 px-2 rounded  shadow-md border border-slate-600">{{ tshirt.pivot.size }}</span></p>
                                                <div
                                                    class="flex justify-between items-center gap-4 mt-1 w-full"
                                                >
                                                    <div
                                                        class="bg-teal-600 w-1/2 p-1 border border-white rounded-md text-start text-white flex items-center gap-1"
                                                    >
                                                        <p>Price:</p>
                                                        <p
                                                            class="font-bold w-full text-center"
                                                        >
                                                            {{
                                                                "$" +
                                                                tshirt.price
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div
                                                        class="bg-teal-600 w-1/2 p-1 border border-white rounded-md text-start text-white flex items-center gap-1"
                                                    >
                                                        <p>Quantity:</p>
                                                        <p
                                                            class="font-bold w-full text-center"
                                                        >
                                                            {{
                                                                tshirt.pivot
                                                                    .quantity
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <td colspan="100%" class="text-center py-4">
                                <p class="text-gray-700">No orders found</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- table footer -->
        <div
            class="my-4 flex md:flex-row flex-col md:gap-0 gap-2 justify-between items-center w-full"
        >
            <!-- results -->
            <div class="md:order-1 order-2">
                <p class="text-base text-slate-800">
                    Showing
                    <span class="text-green-600 font-bold text-lg">{{
                        orders.from
                    }}</span>
                    to
                    <span class="text-green-600 font-bold text-lg"
                        >{{ orders.to }}
                    </span>
                    of {{ orders.total }} orders
                </p>
            </div>
            <nav class="">
                <div class="flex items-center -space-x-px h-8 text-sm">
                    <template
                        v-for="(link, index) in orders.links"
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
                                    index === orders.links.length - 1,
                            }"
                        />
                        <p
                            v-else
                            v-html="link.label"
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-slate-200 border border-gray-300"
                            :class="{
                                'rounded-l-lg': index === 0,
                                'rounded-r-lg':
                                    index === orders.links.length - 1,
                            }"
                        />
                    </template>
                </div>
            </nav>
        </div>
    </div>
</template>

<style scoped>
/* Scrollbar */
.table-container::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

.table-container::-webkit-scrollbar-track {
    background-color: #ebebeb;
    -webkit-border-radius: 10px;
    border-radius: 10px;
}

.table-container::-webkit-scrollbar-thumb {
    -webkit-border-radius: 10px;
    border-radius: 10px;
    background: #bdc4d5;
}

.transform {
    transition: transform 0.2s ease-in-out;
}
</style>
