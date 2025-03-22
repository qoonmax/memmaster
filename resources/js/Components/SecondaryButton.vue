<script setup lang="ts">
withDefaults(
    defineProps<{
        type?: 'button' | 'submit' | 'reset';
    }>(),
    {
        type: 'button',
    }
);
</script>

<template>
    <button
        :type="type"
        class="btn inline-flex items-center py-3 px-5 bg-dark border border-gray rounded-2xl text-white"
    >
        <slot />
    </button>
</template>
