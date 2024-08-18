<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";

const form = useForm({
    brand: "",
    model: "",
    license_plate: "",
    daily_rate: "",
});

function submit() {
    form.post(route("cars.store"));
}
</script>

<template>
    <Head title="Add Cars" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Add Cars
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg"
                >
                    <section class="max-w-xl">
                        <header>
                            <h2
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                Add Car Information
                            </h2>
                            <p
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                Create new car details for rent.
                            </p>
                        </header>

                        <form @submit.prevent="submit" class="mt-6 space-y-6">
                            <!-- Brand -->
                            <div>
                                <InputLabel for="brand" value="Brand" />
                                <TextInput
                                    id="brand"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.brand"
                                    required
                                    autocomplete="brand"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.brand"
                                />
                            </div>

                            <!-- Model -->
                            <div>
                                <InputLabel for="model" value="Model" />
                                <TextInput
                                    id="model"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.model"
                                    required
                                    autocomplete="model"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.model"
                                />
                            </div>

                            <!-- License Plate -->
                            <div>
                                <InputLabel
                                    for="license_plate"
                                    value="License Plate"
                                />
                                <TextInput
                                    id="license_plate"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.license_plate"
                                    required
                                    autocomplete="license_plate"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.license_plate"
                                />
                            </div>

                            <!-- Daily Rate -->
                            <div>
                                <InputLabel
                                    for="daily_rate"
                                    value="Daily Rate"
                                />
                                <TextInput
                                    id="daily_rate"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model="form.daily_rate"
                                    required
                                    autocomplete="off"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.daily_rate"
                                />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing"
                                    >Add Car</PrimaryButton
                                >
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
