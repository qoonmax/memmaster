<script setup lang="ts">
import {onMounted, onUnmounted, ref} from "vue";
import { Link } from '@inertiajs/vue3'
import {Inertia} from "@inertiajs/inertia";

const handleKeydown = (event: KeyboardEvent) => {
    if (event.metaKey && event.key === 'k') { // MetaKey is for ⌘ on Mac
        Inertia.visit(route('pages.cards.show', 'new'));
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <div>
        <Link :href="route('pages.cards.show', 'new')" class="btn bg-accent rounded-2xl py-3 px-5 text-dark border-none">
            Create new card
        </Link>
        <span class="px-2 text-white text-sm">
            or
        </span>
        <kbd class="kbd bg-transparent text-white border-gray cursor-pointer">⌘</kbd>
        <span class="text-white">
            +
        </span>
        <kbd class="kbd bg-transparent text-white border-gray cursor-pointer">k</kbd>
    </div>
</template>
