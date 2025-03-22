<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    href: string;
    active?: boolean;
}>();

const classes = computed(() =>
    props.active
        ? 'block w-full text-gray ps-3 border-l-4 border-transparent text-start focus:outline-none'
        : 'block w-full text-gray ps-3 border-l-4 border-transparent text-start focus:outline-none'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <span class="text-accent">—</span> <slot />
    </Link>
</template>
