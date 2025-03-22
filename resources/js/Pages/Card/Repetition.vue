<script setup lang="ts">

import {Head, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Card from "@/Components/Card.vue";
import CreateCardHeader from "@/Components/CreateCardHeader.vue";
import {useCardsStore} from "@/Stores/CardsStore";
import {computed} from "vue";
import Filters from "@/Components/Filters.vue";
import Tags from "@/Components/Tags.vue";
import {PageProps} from "@/types";

useCardsStore().getCardForRepetition();

const cardsArr = computed(() => useCardsStore().cards);
const cardCounterText = computed(function () {
    const cardCounter = (usePage().props as unknown as PageProps).card_counter as number;

    if (cardCounter > 0) {
        return '(' + cardCounter + ') • ';
    }
    return '';
});
</script>

<template>
    <Head :title="cardCounterText + 'Interval repetition'" />

    <AuthenticatedLayout>
        <template #header>
            <CreateCardHeader />
        </template>

        <div class="pt-6 pb-12">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <Filters/>
                <div class="flex flex-wrap text-gray">
                    <Tags/>
                </div>
                <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 py-8">
                    <template v-for="card in cardsArr" :key="card.slug">
                        <Card :card="card"/>
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
