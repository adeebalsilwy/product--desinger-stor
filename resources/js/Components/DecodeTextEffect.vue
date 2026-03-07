<script setup>
import { ref, onMounted } from "vue";

const props = defineProps({
    text: {
        type: String,
        required: true,
    },
});

const decodedTextRef = ref(null);

const shuffle = (array) => {
    let currentIndex = array.length,
        temporaryValue,
        randomIndex;

    while (0 !== currentIndex) {
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex -= 1;

        temporaryValue = array[currentIndex];
        array[currentIndex] = array[randomIndex];
        array[randomIndex] = temporaryValue;
    }
    return array;
};

const decodeText = () => {
    if (!decodedTextRef.value) return;

    const text = decodedTextRef.value;
    const children = text.children;

    // Reset previous states
    for (let i = 0; i < children.length; i++) {
        children[i].classList.remove("state-1", "state-2", "state-3");
    }

    // Create state array
    const state = Array.from({ length: children.length }, (_, i) => i);
    const shuffled = shuffle(state);

    for (let i = 0; i < shuffled.length; i++) {
        const child = children[shuffled[i]];
        if (child.classList.contains("text-animation")) {
            const state1Time = Math.round(Math.random() * (2000 - 300)) + 50;
            setTimeout(() => firstStages(child), state1Time);
        }
    }
};

const firstStages = (child) => {
    if (child.classList.contains("state-2")) {
        child.classList.add("state-3");
    } else if (child.classList.contains("state-1")) {
        child.classList.add("state-2");
    } else if (!child.classList.contains("state-1")) {
        child.classList.add("state-1");
        setTimeout(() => secondStages(child), 100);
    }
};

const secondStages = (child) => {
    if (child.classList.contains("state-1")) {
        child.classList.add("state-2");
        setTimeout(() => thirdStages(child), 100);
    } else if (!child.classList.contains("state-1")) {
        child.classList.add("state-1");
    }
};

const thirdStages = (child) => {
    if (child.classList.contains("state-2")) {
        child.classList.add("state-3");
    }
};

onMounted(decodeText);
</script>

<template>
    <div
        :class="$attrs.class"
        class="w-full text-center text-3xl "
        @mouseenter="decodeText"
    >
        <div ref="decodedTextRef" class="decode-text">
            <div
                v-for="(char, index) in text"
                :key="index"
                class="text-animation inline-block relative text-transparent"
            >
                {{ char === " " ? "\u00A0" : char }}
            </div>
        </div>
    </div>
</template>

<style scoped>
.text-animation {
    position: relative;
}

.text-animation::before {
    content: "";
    color: black;
    position: absolute;
    top: 50%;
    left: 50%;
    background: #0e182d;
    width: 0;
    height: 1.2em;
    transform: translate(-50%, -55%);
}

.text-animation.state-1::before {
    width: 1px;
}

.text-animation.state-2::before {
    width: 0.9em;
}

.text-animation.state-3 {
    color: inherit;
}

.text-animation.state-3::before {
    width: 0;
}
</style>
