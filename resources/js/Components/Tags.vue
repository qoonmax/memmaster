<script setup lang="ts">
import Tag from "@/Components/Tag.vue";
import {computed} from "vue";
import {useTagsStore} from "@/Stores/TagsStore";

useTagsStore().getTags();
const tagsArr = computed(() => useTagsStore().tags);
</script>

<template>
    <template v-for="tag in tagsArr" :key="tag.id">
        <Tag :tag="tag"/>
    </template>
</template>
