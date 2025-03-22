<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 sm:px-6 lg:px-8 pt-6 sm:pt-0">
        <div>
            <Link href="/">
                <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 p-8 bg-dark border border-gray rounded-3xl shadow-md overflow-hidden"
        >
            <slot />
        </div>
    </div>
</template>
