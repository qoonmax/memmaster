<script setup lang="ts">

import {useTagsStore} from "@/Stores/TagsStore";
import {computed} from "vue";

const props = defineProps<{
    tag: {
        id: number;
        name: string;
    };
}>();


const isSelected = computed(() => {
    return useTagsStore().tagIsSelected(props.tag);
});
</script>

<template>
    <span @click="useTagsStore().selectTag(props.tag)" :class="isSelected ? 'text-accent' : ''" class="pr-2 cursor-pointer">#{{ props.tag.name }} </span>
</template>
