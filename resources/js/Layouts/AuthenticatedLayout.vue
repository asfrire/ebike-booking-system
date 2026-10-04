<script setup>
import { ref } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import logo from "@/assets/EES-removebg-preview.png";
import "@fortawesome/fontawesome-free/css/all.min.css";

const showingNavigationDropdown = ref(false);

const props = defineProps({
    addressVerify: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white py-2">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <!-- <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    /> -->

                                    <img
                                        :src="logo"
                                        alt="Logo"
                                        class="h-16 w-16"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                v-if="props.addressVerify === 'verified'"
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                            </div>

                            <div
                                v-if="props.addressVerify === 'verified'"
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('bookings.index')"
                                    :active="route().current('bookings.index')"
                                >
                                    Booking
                                </NavLink>
                            </div>

                            <div
                                v-if="props.addressVerify === 'verified'"
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="'#'"
                                    :active="route().current('logging')"
                                >
                                    Duty Logging
                                </NavLink>
                            </div>

                            <div
                                v-if="props.addressVerify === 'verified'"
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="'#'"
                                    :active="route().current('records')"
                                >
                                    Records
                                </NavLink>
                            </div>

                            <div
                                v-if="props.addressVerify === 'verified'"
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="'#'"
                                    :active="route().current('settings')"
                                >
                                    Settings
                                </NavLink>
                            </div>
                        </div>

                        <!-- Mobile Footer Navigation -->
                        <div
                            v-if="addressVerify === 'verified'"
                            class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-inner sm:hidden flex justify-around py-2"
                        >
                            <!-- Book E-Bike -->
                            <NavLink
                                :href="route('bookings.index')"
                                :active="route().current('bookings.index')"
                                class="flex flex-col items-center"
                            >
                                <i
                                    class="fas fa-car text-xl"
                                    :class="
                                        route().current('bookings.index')
                                            ? 'text-blue-500'
                                            : 'text-gray-600'
                                    "
                                ></i>
                                <span
                                    class="text-xs"
                                    :class="
                                        route().current('booking')
                                            ? 'text-blue-500'
                                            : 'text-gray-600'
                                    "
                                    >Booking</span
                                >
                            </NavLink>

                            <!-- Duty Logging -->
                            <NavLink
                                :href="'#'"
                                :active="route().current('logging')"
                                class="flex flex-col items-center"
                            >
                                <i
                                    class="fas fa-stopwatch text-xl"
                                    :class="
                                        route().current('logging')
                                            ? 'text-blue-500'
                                            : 'text-gray-600'
                                    "
                                ></i>
                                <span
                                    class="text-xs"
                                    :class="
                                        route().current('logging')
                                            ? 'text-blue-500'
                                            : 'text-gray-600'
                                    "
                                    >Duty Logging</span
                                >
                            </NavLink>

                            <!-- Dashboard -->
                            <NavLink
                                :href="route('dashboard')"
                                :active="route().current('dashboard')"
                                class="flex flex-col items-center"
                            >
                                <i
                                    class="fas fa-tachometer-alt text-lg"
                                    :class="
                                        route().current('dashboard')
                                            ? 'text-blue-500'
                                            : 'text-gray-600'
                                    "
                                ></i>
                                <span
                                    class="text-xs"
                                    :class="
                                        route().current('dashboard')
                                            ? 'text-blue-500'
                                            : 'text-gray-600'
                                    "
                                    >Dashboard</span
                                >
                            </NavLink>

                            <!-- Records -->
                            <NavLink
                                :href="'#'"
                                :active="route().current('records')"
                                class="flex flex-col items-center"
                            >
                                <i
                                    class="fas fa-archive text-xl"
                                    :class="
                                        route().current('records')
                                            ? 'text-blue-500'
                                            : 'text-gray-600'
                                    "
                                ></i>
                                <span
                                    class="text-xs"
                                    :class="
                                        route().current('records')
                                            ? 'text-blue-500'
                                            : 'text-gray-600'
                                    "
                                    >Records</span
                                >
                            </NavLink>

                            <!-- Settings -->
                            <NavLink
                                :href="'#'"
                                :active="route().current('settings')"
                                class="flex flex-col items-center"
                            >
                                <i
                                    class="fas fa-tools text-xl"
                                    :class="
                                        route().current('settings')
                                            ? 'text-blue-500'
                                            : 'text-gray-600'
                                    "
                                ></i>
                                <span
                                    class="text-xs"
                                    :class="
                                        route().current('settings')
                                            ? 'text-blue-500'
                                            : 'text-gray-600'
                                    "
                                    >Settings</span
                                >
                            </NavLink>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
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
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
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
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
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
