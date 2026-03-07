<script setup>
import Admin from "@/Layouts/Admin.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { debounce } from "lodash";
import { ref, watch } from "vue";
import { useTextHelpers } from "@/plugins/textHelpers";

defineOptions({ layout: Admin });

const props = defineProps({
    customers: {
        type: Object,
        required: true,
    },
    searchTerm: String,
});

// This is the immediate search input value
const search = ref(props.searchTerm);
// This is the debounced search value that will be used for highlighting
const debouncedSearch = ref(props.searchTerm);

watch(
    search,
    debounce((query) => {
        // Update the table
        router.get(
            route("customers"),
            {
                search: query,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
        // Update the debounced search value
        debouncedSearch.value = query;
    }, 300)
);

const textHelpers = useTextHelpers();
</script>

<template>
    <div class="pb-12">
        <Head title="Customers" />

        <!-- Search -->
        <div class="flex items-center justify-end my-3">
            <input
                v-model="search"
                type="search"
                name="search"
                placeholder="Search"
                class="w-64 h-10 px-3 border-b-2 border-gray-200 focus:border-gray-300 rounded-lg text-sm outline-none focus:ring-0"
            />
        </div>

        <!-- Table -->
        <div class="max-w-7xl overflow-x-auto table-container">
            <table
                class="min-w-full divide-y divide-gray-200 bg-white shadow-md"
            >
                <thead class="">
                    <tr>
                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase w-1/12"
                        >
                            #
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase w-2/12"
                        >
                            Name
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase w-3/12"
                        >
                            Email
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase w-1/12"
                        >
                            Address
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase text-nowrap w-1/12"
                        >
                            Orders(T-shirts)
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-start"
                        >
                            Spent
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr
                        v-if="customers.data.length > 0"
                        v-for="customer in customers.data"
                        :key="customer.id"
                        class="hover:bg-gray-50 cursor-pointer"
                    >
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"
                        >
                            {{ customer.id }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"
                            v-html="
                                textHelpers.highlightText(
                                    textHelpers.limitText(customer.name, 20),
                                    debouncedSearch
                                )
                            "
                        />

                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"
                            v-html="
                                textHelpers.highlightText(
                                    textHelpers.limitText(customer.email, 30),
                                    debouncedSearch
                                )
                            "
                        />
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 w-12 overflow-hidden"
                        >
                            <div class="flex items-center justify-start gap-1">
                                <p
                                    class="font-bold"
                                    v-html="
                                        textHelpers.highlightText(
                                            customer.country,
                                            debouncedSearch
                                        )
                                    "
                                />
                                <p>,</p>
                                <p
                                    class="font-bold"
                                    v-html="
                                        textHelpers.highlightText(
                                            customer.zipcode,
                                            debouncedSearch
                                        )
                                    "
                                />
                            </div>
                            <p
                                class="text-sm"
                                v-html="
                                    textHelpers.highlightText(
                                        textHelpers.limitText(
                                            customer.address,
                                            50
                                        ),
                                        debouncedSearch
                                    )
                                "
                            />
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 text-center"
                        >
                            {{ customer.total_orders }} ({{
                                customer.total_tshirts_bought
                            }})
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-center font-bold"
                        >
                            {{ customer.total_spent ?? 0 }} $
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="100%" class="text-center py-4">
                            <p class="text-gray-700">No customers found</p>
                        </td>
                    </tr>
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
                        customers.from
                    }}</span>
                    to
                    <span class="text-green-600 font-bold text-lg"
                        >{{ customers.to }}
                    </span>
                    of {{ customers.total }} customers
                </p>
            </div>
            <nav class="">
                <div class="flex items-center -space-x-px h-8 text-sm">
                    <template
                        v-for="(link, index) in customers.links"
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
                                    index === customers.links.length - 1,
                            }"
                        />
                        <p
                            v-else
                            v-html="link.label"
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-slate-200 border border-gray-300"
                            :class="{
                                'rounded-l-lg': index === 0,
                                'rounded-r-lg':
                                    index === customers.links.length - 1,
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
</style>
