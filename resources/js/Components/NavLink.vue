<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    href: string;
    active?: boolean;
}>();

const classes = computed(() =>
    props.active
        ? 'inline-flex items-center text-dark text-sm font-medium bg-white py-2 px-4 md:py-2 md:px-5 rounded-xl'
        : 'inline-flex items-center text-white text-sm font-medium bg-transparent py-2 px-4 md:py-2 md:px-5 rounded-xl'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
