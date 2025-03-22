<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
</script>

<template>
    <Head title="Сервис для интервального повторения" />
    <div class="min-h-screen flex items-center justify-center">

        <div class="relative w-full max-w-4xl px-6">
            <header class="text-center pb-5">
                <p class="mb-3 text-gray">
                    Create cards and regularly review them for better memorization.
                </p>
                <h1 class="text-4xl font-medium text-white">
                    A convenient service for <span class="text-accent underline underline-offset-7 decoration-wavy">spaced repetition</span>
                </h1>
            </header>

            <main class="mt-10 flex flex-col items-center space-y-4">
                <div class="flex gap-5 content-center items-center">
                    <Link
                        :href="route('login')"
                        class="btn bg-accent rounded-2xl py-3 px-5 text-dark border-none"
                    >
                        Log in
                    </Link>
                    <p v-if="canRegister" class="text-gray">or</p>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="text-gray hover:underline hover:underline-offset-4 hover:decoration-wavy"
                    >
                        Sign up
                    </Link>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
:root {
    --color-dark: #0c0c0b;
    --color-black: #161616;
    --color-dark-gray: #222222;
    --color-accent: #dafc08;
    --color-gray: #9a9a9e;
    --color-white-gray: #e1e1e1;
    --color-red: #ec0606;
}

body {
    background-color: var(--color-dark);
}
</style>
