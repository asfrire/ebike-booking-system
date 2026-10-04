<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";

const props = defineProps({
    bookings: Array,
    addressVerify: String,
});

// Booking form
const form = useForm({
    pax: 1,
    pickup: "",
    dropoff: "",
    pickup_time: "Now",
});

const submitBooking = () => {
    form.post(route("bookings.store"), {
        onSuccess: () => form.reset(),
    });
};

onMounted(() => {
    window.Echo.channel("bookings").listen("BookingCreated", (event) => {
        console.log("New booking:", event.booking);
        props.bookings.push(event.booking); // update list without refresh
    });
});
</script>

<template>
    <Head title="Bookings" />

    <AuthenticatedLayout :address-verify="props.addressVerify">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Bookings
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Booking Form -->
                        <div class="p-6 max-w-3xl mx-auto">
                            <h1 class="text-2xl font-bold mb-6">Bookings</h1>

                            <form
                                @submit.prevent="submitBooking"
                                class="space-y-4 bg-white p-4 shadow rounded-lg"
                            >
                                <div>
                                    <label class="block font-medium"
                                        >Passengers</label
                                    >
                                    <input
                                        type="number"
                                        min="1"
                                        v-model="form.pax"
                                        class="mt-1 block w-full border rounded p-2"
                                    />
                                    <span
                                        v-if="form.errors.pax"
                                        class="text-red-500 text-sm"
                                        >{{ form.errors.pax }}</span
                                    >
                                </div>

                                <div>
                                    <label class="block font-medium"
                                        >Pick-up</label
                                    >
                                    <input
                                        type="text"
                                        v-model="form.pickup"
                                        class="mt-1 block w-full border rounded p-2"
                                    />
                                    <span
                                        v-if="form.errors.pickup"
                                        class="text-red-500 text-sm"
                                        >{{ form.errors.pickup }}</span
                                    >
                                </div>

                                <div>
                                    <label class="block font-medium"
                                        >Drop-off</label
                                    >
                                    <input
                                        type="text"
                                        v-model="form.dropoff"
                                        class="mt-1 block w-full border rounded p-2"
                                    />
                                    <span
                                        v-if="form.errors.dropoff"
                                        class="text-red-500 text-sm"
                                        >{{ form.errors.dropoff }}</span
                                    >
                                </div>

                                <div>
                                    <label class="block font-medium"
                                        >Pick-up Time</label
                                    >
                                    <select
                                        v-model="form.pickup_time"
                                        class="mt-1 block w-full border rounded p-2"
                                    >
                                        <option value="Now">Now</option>
                                        <option value="1min">1min</option>
                                        <option value="2mins">2mins</option>
                                        <option value="3mins">3mins</option>
                                        <option value="4mins">4mins</option>
                                        <option value="5mins">5mins</option>
                                        <option value="6mins">6mins</option>
                                        <option value="7mins">7mins</option>
                                        <option value="8mins">8mins</option>
                                        <option value="9mins">9mins</option>
                                        <option value="10mins">10mins</option>
                                    </select>
                                    <span
                                        v-if="form.errors.pickup_time"
                                        class="text-red-500 text-sm"
                                    >
                                        {{ form.errors.pickup_time }}
                                    </span>
                                </div>

                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                    :disabled="form.processing"
                                >
                                    Book Now
                                </button>
                            </form>

                            <!-- Booking List -->
                            <div class="mt-8">
                                <h2 class="text-xl font-semibold mb-4">
                                    My Bookings
                                </h2>
                                <div
                                    v-if="props.bookings.length === 0"
                                    class="text-gray-500"
                                >
                                    No bookings yet.
                                </div>
                                <ul class="space-y-3">
                                    <li
                                        v-for="booking in props.bookings"
                                        :key="booking.id"
                                        class="p-4 bg-gray-100 rounded shadow"
                                    >
                                        <p>
                                            <strong>Pax:</strong>
                                            {{ booking.pax }}
                                        </p>
                                        <p>
                                            <strong>Pick-up:</strong>
                                            {{ booking.pickup }}
                                        </p>
                                        <p>
                                            <strong>Drop-off:</strong>
                                            {{ booking.dropoff }}
                                        </p>
                                        <p>
                                            <strong>Time:</strong>
                                            {{ booking.pickup_time }}
                                        </p>
                                        <p>
                                            <strong>Status:</strong>
                                            {{
                                                booking.is_pick_up
                                                    ? "Picked up"
                                                    : "Waiting"
                                            }}
                                        </p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
