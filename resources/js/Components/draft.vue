<script setup>
import { ref, onMounted, nextTick } from "vue";
import { ProductService } from "@/service/ProductService";

onMounted(() => {
    ProductService.getProductsSmall().then((data) => (products.value = data));
});

const op = ref();
const selectedProduct = ref();

const displayProduct = (event, product) => {
    op.value.hide();

    if (selectedProduct.value?.id === product.id) {
        selectedProduct.value = null;
    } else {
        selectedProduct.value = product;

        nextTick(() => {
            op.value.show(event);
        });
    }
};

const hidePopover = () => {
    op.value.hide();
};

</script>
<template>
    <div class="card">
        <DataTable
            :value="products"
            :rows="5"
            paginator
            tableStyle="min-width: 50rem"
        >
            <Column header="Details" class="w-1/6">
                <template #body="slotProps">
                    <Button
                        type="button"
                        @click="displayProduct($event, slotProps.data)"
                        icon="pi pi-search"
                        severity="secondary"
                        rounded
                    ></Button>
                </template>
            </Column>
        </DataTable>

        <Popover ref="op">
            <div v-if="selectedProduct" class="rounded flex flex-col">
                <div class="text-lg font-medium mt-1">
                    {{ selectedProduct.name }}
                </div>

                <div class="flex gap-2">
                    <Button
                        icon="pi pi-heart"
                        outlined
                        @click="hidePopover"
                    ></Button>
                </div>
            </div>
        </Popover>
    </div>
</template>
