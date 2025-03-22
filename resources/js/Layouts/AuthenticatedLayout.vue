<script setup lang="ts">
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import {Link, usePage} from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

interface PageProps {
    card_counter: number;
    all_card_counter: number;
}

const propsCardCounter = (usePage().props as unknown as PageProps).card_counter;
const propsAllCardCounter = (usePage().props as unknown as PageProps).all_card_counter;
</script>

<template>
    <div>
        <div class="min-h-screen">
            <nav>
                <!-- Primary Navigation Menu -->
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-start h-16">
                        <!-- Logo -->
                        <div class="shrink-0 md:flex items-center hidden">
                            <Link :href="route('pages.cards.repetition')">
                                <ApplicationLogo
                                    class="block h-9 w-auto fill-current text-gray-800"
                                />
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="content-center">
                            <div class="flex border border-gray rounded-2xl p-1">
                                <NavLink :href="route('pages.cards.repetition')" :active="route().current('pages.cards.repetition')" class="indicator">
                                    Repetition
                                    <span v-if="propsCardCounter > 0" class="indicator-item badge bg-dark border border-gray text-xs text-white">{{ propsCardCounter }}</span>
                                </NavLink>
                                <NavLink :href="route('pages.cards.index')" :active="route().current('pages.cards.index')" class="indicator">
                                    All cards
                                    <span v-if="propsAllCardCounter > 0" class="indicator-item badge bg-dark border border-gray text-xs text-white">{{ propsAllCardCounter }}</span>
                                </NavLink>
                                <NavLink href="#" class="text-gray">
                                    Tests
                                </NavLink>
                                <NavLink href="#" class="text-gray">
                                    AI generator
                                </NavLink>
                            </div>
                        </div>


                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-5 py-3 border border-gray text-sm font-medium text-white rounded-2xl"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="bg-dark border border-gray inline-flex items-center justify-center p-2 rounded-xl text-white-gray focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray">
                        <div class="px-4">
                            <div class="text-white-gray">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-gray">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header">
                <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
