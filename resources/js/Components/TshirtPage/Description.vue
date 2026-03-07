<script setup>
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";
import Info from "@/Icons/Info.vue";
import Size from "@/Icons/Size.vue";
import { ref, watch } from "vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
});

const sizes = {
    IN: {
        Chest: {
            XS: "36in",
            S: "40in",
            M: "44in",
            L: "48in",
            XL: "52in",
        },
        Length: {
            XS: "28in",
            S: "29in",
            M: "30in",
            L: "31in",
            XL: "32in",
        },
    },
    CM: {
        Chest: {
            XS: "91.4cm",
            S: "101.6cm",
            M: "111.8cm",
            L: "121.9cm",
            XL: "132.1cm",
        },
        Length: {
            XS: "71.1cm	",
            S: "73.7cm",
            M: "76.2cm",
            L: "78.7cm",
            XL: "81.3cm",
        },
    },
};

const measure = ref("IN");
const currentSize = ref(sizes.IN);
watch(measure, () => {
    currentSize.value = sizes[measure.value];
});
</script>

<template>
    <div class="card">
        <Tabs value="0" class="shadow-xl">
            <TabList>
                <Tab
                    value="0"
                    class="w-1/2 flex justify-center items-center gap-2"
                >
                    <p>Description</p>
                    <Info class="w-5 h-5" />
                </Tab>
                <Tab
                    value="1"
                    class="w-1/2 flex justify-center items-center gap-2"
                >
                    <p>Size Guide</p>
                    <Size class="w-5 h-5" />
                </Tab>
            </TabList>
            <TabPanels>
                <TabPanel value="0" class="space-y-4">
                    <p class="font-main font-bold text-xl text-slate-800">
                        {{ props.title }}
                    </p>
                    <p class="font-main text-slate-600">
                        {{ props.description }}
                    </p>
                    <div class="space-y-2">
                        <p
                            class="font-main font-semibold text-slate-800 text-lg"
                        >
                            Features
                        </p>
                        <ul class="list-disc list-inside space-y-4 ms-4">
                            <li>Relaxed, comfortable fit for everyday wear.</li>
                            <li>
                                Premium 5.3 oz (180 gsm) fabric with exceptional
                                durability—crafted from 100% preshrunk cotton
                                for solid colors, and soft cotton blends for
                                heather variations.
                            </li>
                            <li>
                                Double-stitched seams for added strength and
                                longevity.
                            </li>
                            <li>
                                Designed for breathability and all-day comfort,
                                perfect for layering or wearing solo.
                            </li>
                            <li>
                                Printed responsibly by a third-party partner
                                adhering to International Labor Organization
                                standards.
                            </li>
                        </ul>
                    </div>
                </TabPanel>
                <TabPanel value="1">
                    <div class="flex md:flex-row flex-col">
                        <div class="md:w-2/3 w-full order-2 md:order-1">
                            <h3 class="font-main font-bold text-xl">
                                How to choose the perfect size
                            </h3>
                            <ol
                                class="list-decimal list-inside space-y-2 ms-4 mt-2"
                            >
                                <li>
                                    Grab your favorite t-shirt and lay it flat.
                                </li>
                                <li>
                                    For the chest, measure 2.5cm / 1in under the
                                    left armpit, to 2.5cm / 1in under the right
                                    armpit. Multiply this measurement by 2.
                                </li>
                                <li>
                                    For the length, measure from the top of the
                                    shoulder next to the collar straight to the
                                    bottom of the shirt.
                                </li>
                                <li>
                                    Match this chest and length information to
                                    the size guide to find your ideal size.
                                </li>
                            </ol>
                        </div>
                        <div class="md:w-1/3 w-full flex justify-center order-1 md:order-2 md:py-0 py-4">
                            <img
                                src="../../../../public/assets/tshirt-size.svg"
                                alt=""
                                class="w-1/2 "
                            />
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-xl font-bold mb-4">
                            Product Measurements
                        </h3>
                        <div class="flex gap-2 m-2 ">
                            <div>
                                <label
                                    class="cursor-pointer py-1 px-2 rounded-lg border-2 border-slate-500"
                                    :class="
                                        measure === 'IN'
                                            ? 'bg-slate-700  text-slate-200'
                                            : 'bg-white text-slate-600 hover:bg-slate-100'
                                    "
                                    for="IN"
                                    >IN
                                </label>
                                <input
                                    class="hidden"
                                    type="radio"
                                    name="dataFilter"
                                    v-model="measure"
                                    value="IN"
                                    id="IN"
                                />
                            </div>
                            <div>
                                <label
                                    class="cursor-pointer py-1 px-2 rounded-lg border-2 border-slate-500"
                                    :class="
                                        measure === 'CM'
                                            ? 'bg-slate-700  text-slate-200'
                                            : 'bg-white text-slate-600 hover:bg-slate-100'
                                    "
                                    for="CM"
                                    >CM
                                </label>
                                <input
                                    class="hidden"
                                    type="radio"
                                    name="dataFilter"
                                    v-model="measure"
                                    value="CM"
                                    id="CM"
                                />
                            </div>
                        </div>
                        <div class="w-full overflow-x-auto">
                            <table
                                class="w-full border-collapse border border-gray-300 text-center"
                            >
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-300 p-2 w-1/6">
                                            Size
                                        </th>
                                        <th class="border border-gray-300 p-2 w-1/6">
                                            XS 
                                        </th> 
                                        <th class="border border-gray-300 p-2 w-1/6">
                                            S 
                                        </th> 
                                        <th class="border border-gray-300 p-2 w-1/6">
                                            M 
                                        </th> 
                                        <th class="border border-gray-300 p-2 w-1/6">
                                            L 
                                        </th> 
                                        <th class="border border-gray-300 p-2 w-1/6">
                                            XL
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 p-2">
                                            Chest
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            {{ currentSize.Chest.XS }}
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            {{ currentSize.Chest.S }}
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            {{ currentSize.Chest.M }}
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            {{ currentSize.Chest.L }}
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            {{ currentSize.Chest.XL }}
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 p-2">
                                            Length
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            {{ currentSize.Length.XS }}
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            {{ currentSize.Length.S }}
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            {{ currentSize.Length.M }}
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            {{ currentSize.Length.L }}
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            {{ currentSize.Length.XL }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </TabPanel>
            </TabPanels>
        </Tabs>
    </div>
</template>
