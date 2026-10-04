<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import logo from '@/assets/EES-removebg-preview.png'
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <div>
            <Link href="/">
                <!-- <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" /> -->

                <img 
                    :src="logo" 
                    alt="Logo" 
                    class="h-16 w-16 sm:h-20 sm:w-20 md:h-28 md:w-28 lg:h-60 lg:w-60"
                />

            </Link>
        </div>

        <div
            class="mt-6 w-[95%] sm:w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
