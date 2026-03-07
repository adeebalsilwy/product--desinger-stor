<script setup>
const props = defineProps({
    title: String,
});

</script>


<template>
     <div class="w-full flex flex-col justify-center items-center my-12" >
            <h1 class="text-2xl font-bold text-slate-800 uppercase">{{ props.title }}</h1>
            <img
                src="../../../public/assets/empty.png"
                alt="empty state"
                class="w-64"
            />
        </div>
</template>


