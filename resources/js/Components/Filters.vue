<script setup lang="ts">
    import {useCardsStore} from "@/Stores/CardsStore";
    import TextInput from "@/Components/TextInput.vue";

    const activeClass = (filter: string) => {
        const filterState = useCardsStore().getFilter(filter);
        return filterState ? 'text-accent' : 'text-gray';
    }
</script>

<template>
    <div class="flex flex-wrap items-center gap-2">
        <div class="mb-5 mr-3">
            <span class="text-gray">Search: </span>
            <TextInput
                v-model="useCardsStore().search"
                placeholder="enter card name..."
                class="search-field"
                onbeforeinput="console.log(2222)"
            />
        </div>
        <div class="mb-5">
            <span class="text-gray">Filters: </span>
            <button @click="useCardsStore().filterToggle('all')" :class="activeClass('all')" class="hover:underline hover:underline-offset-4 hover:decoration-wavy">All cards</button>
        </div>
    </div>
</template>
