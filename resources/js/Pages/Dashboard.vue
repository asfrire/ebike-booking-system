<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import { onMounted, ref } from "vue";

window.Pusher = Pusher;

// Props from Laravel
const props = defineProps({
    addressVerify: String,
    bookings: Array,
    reverb: Object,
});

const bookingList = ref([...props.bookings]);

// Example: listen to booking events
onMounted(() => {
    window.Echo.channel("bookings").listen(".BookingCreated", (event) => {
        console.log("📡 New booking received:", event);
        bookingList.value.unshift(event); // add on top
    });
});

console.log("Address Verify:", props.addressVerify);
console.log("Bookings:", props.bookings);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout :address-verify="props.addressVerify">
        <template #header v-if="props.addressVerify === 'verified'">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

                <!-- Recent Bookings -->
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6"
                >
                    <h2 class="text-lg font-bold mb-4">
                        📌 My Recent Bookings
                    </h2>

                    <ul v-if="props.bookings.length > 0" class="space-y-3">
                        <li
                            v-for="booking in bookingList"
                            :key="booking.id"
                            class="p-4 border rounded shadow-sm bg-gray-50"
                        >
                            <p><strong>Pax:</strong> {{ booking.pax }}</p>
                            <p>
                                <strong>Pick-up:</strong> {{ booking.pickup }}
                            </p>
                            <p>
                                <strong>Drop-off:</strong> {{ booking.dropoff }}
                            </p>
                            <p>
                                <strong>Time:</strong> {{ booking.pickup_time }}
                            </p>
                            <p>
                                <strong>Status:</strong>
                                {{
                                    booking.is_pick_up ? "Picked up" : "Waiting"
                                }}
                            </p>
                        </li>
                    </ul>

                    <div v-else class="text-gray-500">No bookings yet.</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
