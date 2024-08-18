<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import axios from "axios";

const form = useForm({
    license_plate: "",
    rental_id: "",
    return_date: "",
    duration: 0,
    final_cost: 0,
});

const rentals = ref([]);
const error = ref(""); // To store error messages
const minReturnDate = ref(""); // Minimum return date
const maxReturnDate = ref(""); // Maximum return date

// Find the selected rental details
const selectedRental = computed(() => {
    return rentals.value.find((rental) => rental.id === form.rental_id) || {};
});

const rentalDays = computed(() => {
    if (!form.return_date || !selectedRental.value.start_date) {
        return 0;
    }

    const startDate = new Date(selectedRental.value.start_date);
    const returnDate = new Date(form.return_date);

    const diffTime = returnDate - startDate;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    return diffDays > 0 ? diffDays : 0; // Ensure positive duration
});

const totalCost = computed(() => {
    return rentalDays.value * (selectedRental.value.car.daily_rate || 0);
});

function formatCurrency(value) {
    if (value === null || value === undefined) return "Rp. 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
    }).format(value);
}

async function searchRentals() {
    if (form.license_plate.length < 3) {
        error.value = "License plate must be at least 3 characters long.";
        rentals.value = [];
        minReturnDate.value = "";
        maxReturnDate.value = "";
        return;
    }

    try {
        const response = await axios.post(`/return/available`, {
            license_plate: form.license_plate,
        });
        if (response.data.length > 0) {
            rentals.value = response.data;
            error.value = "";

            // Set min and max dates based on the first rental
            const firstRental = response.data[0];
            const startDate = new Date(firstRental.start_date);
            startDate.setDate(startDate.getDate() + 1); // Add one day

            minReturnDate.value = startDate.toISOString().split("T")[0];
            maxReturnDate.value = firstRental.end_date || ""; // Adjust based on your data
        } else {
            rentals.value = [];
            error.value = "No rentals found for the given license plate.";
            minReturnDate.value = "";
            maxReturnDate.value = "";
        }
    } catch (error) {
        console.error("Failed to fetch rentals:", error);
        error.value = "An error occurred while fetching rentals.";
    }
}

function updateDateConstraints() {
    const selectedRental = rentals.value.find(
        (rental) => rental.id === form.rental_id
    );
    if (selectedRental) {
        const startDate = new Date(selectedRental.start_date);
        startDate.setDate(startDate.getDate() + 1); // Add one day

        minReturnDate.value = startDate.toISOString().split("T")[0];
        maxReturnDate.value = selectedRental.end_date || "";
    } else {
        minReturnDate.value = "";
        maxReturnDate.value = "";
    }
}

function updateDurationAndCost() {
    form.duration = rentalDays.value;
    form.final_cost = totalCost.value;
}

function submit() {
    updateDurationAndCost();
    form.post(route("return.store"));
}
</script>

<template>
    <Head title="Return Car" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Return Car
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
                                Return Car
                            </h2>
                            <p
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                Enter the car's license plate and select from
                                available rentals.
                            </p>
                        </header>

                        <form @submit.prevent="submit" class="mt-6 space-y-6">
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
                                    autocomplete="off"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.license_plate"
                                />
                            </div>

                            <!-- Search Button -->
                            <div>
                                <PrimaryButton @click.prevent="searchRentals">
                                    Search Rentals
                                </PrimaryButton>
                                <InputError class="mt-2" :message="error" />
                            </div>

                            <!-- Select Rental Option -->
                            <div v-if="rentals.length > 0">
                                <InputLabel
                                    for="rental_id"
                                    value="Select Rental"
                                />
                                <select
                                    id="rental_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    v-model="form.rental_id"
                                    required
                                    @change="updateDateConstraints"
                                >
                                    <option disabled value="">
                                        Select the correct rental
                                    </option>
                                    <option
                                        v-for="rental in rentals"
                                        :key="rental.id"
                                        :value="rental.id"
                                    >
                                        {{
                                            rental.car?.brand || "Unknown Brand"
                                        }}
                                        -
                                        {{
                                            rental.car?.model || "Unknown Model"
                                        }}
                                        (Rented on: {{ rental.start_date }})
                                    </option>
                                </select>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.rental_id"
                                />
                            </div>

                            <!-- Return Date -->
                            <div>
                                <InputLabel
                                    for="return_date"
                                    value="Return Date"
                                />
                                <TextInput
                                    id="return_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.return_date"
                                    required
                                    autocomplete="off"
                                    :min="minReturnDate"
                                    :max="maxReturnDate"
                                    @change="updateDurationAndCost"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.return_date"
                                />
                            </div>

                            <!-- Hidden Inputs for Duration and Total Cost -->
                            <input
                                type="hidden"
                                name="duration"
                                :value="form.duration"
                            />
                            <input
                                type="hidden"
                                name="final_cost"
                                :value="form.final_cost"
                            />

                            <!-- Display Car Details -->
                            <div v-if="rentalDays > 0">
                                <div class="mt-4 flex gap-10">
                                    <div>
                                        <InputLabel
                                            for="daily_rate"
                                            value="Daily Rate"
                                        />
                                        <p
                                            class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                                        >
                                            {{
                                                formatCurrency(
                                                    selectedRental.car
                                                        ?.daily_rate
                                                )
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <InputLabel
                                            for="duration"
                                            value="Duration"
                                        />
                                        <p
                                            class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                                        >
                                            {{ rentalDays }} day(s)
                                        </p>
                                    </div>
                                    <div>
                                        <InputLabel
                                            for="final_cost"
                                            value="Total Cost"
                                        />
                                        <p
                                            class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                                        >
                                            {{ formatCurrency(totalCost) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">
                                    Return Car
                                </PrimaryButton>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
