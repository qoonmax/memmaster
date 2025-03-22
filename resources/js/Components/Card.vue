<script setup lang="ts">

import {computed, onBeforeMount, onMounted, ref, watch} from "vue";
import {useCardsStore} from "@/Stores/CardsStore";
import { Link } from '@inertiajs/vue3'

const props = defineProps<{
    card: {
        slug: string;
        stage: number;
        content: string;
        next_repeat_at: string;
        tags: string;
    };
}>();

const isBlur = ref(true);
isBlur.value = route().current('pages.cards.repetition');

const disableBlur = () => {
    if (isBlur.value === true) {
        isBlur.value = false;
    }
}

const blurClass = computed(() => {
    return isBlur.value ? 'blur-xl' : 'blur-0';
});
</script>

<template>
    <div class="indicator w-auto">
        <Link :href="route('pages.cards.show', props.card.slug)" class="px-2 cursor-pointer absolute top-5 right-5 text-accent text-2xl font-bold focus:outline-none z-30" reload>
            &#x22EE;
        </Link>

        <span class="indicator-item indicator-top indicator-center badge bg-dark border-gray text-gray">
            {{ props.card.stage }}/8
        </span>
        <div @click="disableBlur()" :class="isBlur ? 'cursor-pointer' : ''" class="w-full rounded-3xl bg-dark border border-gray p-7">
            <div class="grid blur-0 content-between h-full transition ease-linear duration-200" :class="blurClass">
                <div v-html="props.card.content" class="html-content text-white-gray"></div>
                <div class="grid grid-cols-2 mt-5">
                    <button @click="useCardsStore().repeatCard(props.card.slug)" :disabled="isBlur" class="text-accent hover:underline hover:underline-offset-4 hover:decoration-wavy">
                        repeated
                    </button>
                    <button @click="useCardsStore().skipCard(props.card.slug)" :disabled="isBlur" class="text-gray hover:underline hover:underline-offset-4 hover:decoration-wavy">
                        skip
                    </button>
                </div>
            </div>
            <div v-if="isBlur" @click="disableBlur" class="absolute inset-0 flex justify-center items-center transition ease-linear duration-200">
                <svg class="h-7 w-7 opacity-50 cursor-pointer" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M15.0007 12C15.0007 13.6569 13.6576 15 12.0007 15C10.3439 15 9.00073 13.6569 9.00073 12C9.00073 10.3431 10.3439 9 12.0007 9C13.6576 9 15.0007 10.3431 15.0007 12Z" stroke="#9a9a9e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12.0012 5C7.52354 5 3.73326 7.94288 2.45898 12C3.73324 16.0571 7.52354 19 12.0012 19C16.4788 19 20.2691 16.0571 21.5434 12C20.2691 7.94291 16.4788 5 12.0012 5Z" stroke="#9a9a9e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
            </div>
        </div>
    </div>
</template>
