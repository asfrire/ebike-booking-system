<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    block: "",
    lot: "",
    street: "",
    subdivision: "",
    mobile_number: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

const validateBlock = (event) => {
  const value = Number(event.target.value);
  if (value > 30) {
    event.target.value = 30; // force max
    form.block = 30;
  }
  if (value < 1 && event.target.value !== "") {
    event.target.value = 1; // force min
    form.block = 1;
  }
};

const validateLot = (event) => {
  const value = Number(event.target.value);
  if (value > 90) {
    event.target.value = 90; // force max
    form.lot = 90;
  }
  if (value < 1 && event.target.value !== "") {
    event.target.value = 1; // force min
    form.lot = 1;
  }
};

</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="mobile_number" value="Mobile Number" />

                <TextInput
                    id="mobile_number"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.mobile_number"
                    required
                    autofocus
                    autocomplete="mobile_number"
                />

                <InputError class="mt-2" :message="form.errors.mobile_number" />
            </div>
            
            <div>
                <div class="mt-4 flex items-center gap-4">
                    <!-- Subdivision -->
                    <div>
                        <InputLabel for="subdivision" value="Subdivision" />
                        <select 
                            v-model="form.subdivision" 
                            class="mt-1 block w-[39vw] sm:w-[18.5vw] rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >

                            <option>Primera</option>
                            <option>Sonera</option>
                        </select>
                    </div>

                    <!-- Street -->
                    <div>
                        <InputLabel for="street" value="Street" />
                        <TextInput
                            id="street"
                            type="text"
                            class="mt-1 block w-[39vw] sm:w-[18.5vw]"
                            v-model="form.street"
                            required
                            autocomplete="street"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.street"
                        />
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-4">
                    <!-- Block -->
                    <div>
                        <InputLabel for="block" value="Block" />
                        <TextInput
                            id="block"
                            type="number"
                            class="mt-1 block w-[39vw] sm:w-[18.5vw]"
                            v-model="form.block"
                            required
                            autofocus
                            autocomplete="block"
                            min="1"
                            max="30"
                            @input="validateBlock"
                        />
                        <InputError class="mt-2" :message="form.errors.block" />
                    </div>  
                    
                    <!-- Lot -->
                    <div>
                        <InputLabel for="lot" value="Lot" />
                        <TextInput
                            id="lot"
                            type="number"
                            class="mt-1 lot w-[39vw] sm:w-[18.5vw]"
                            v-model="form.lot"
                            required
                            autofocus
                            min="1"
                            max="90"
                            autocomplete="lot"
                            @input="validateLot"
                        />
                        <InputError class="mt-2" :message="form.errors.lot" />
                    </div>
                </div>
            </div>
            

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
