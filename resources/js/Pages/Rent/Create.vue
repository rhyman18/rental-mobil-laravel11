<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, watch, onMounted, computed } from "vue";
import axios from "axios";

// Initialize form with default values
const today = new Date().toISOString().split("T")[0]; // Current date

// Function to get tomorrow's date
function getTomorrowDate(date) {
    const tomorrow = new Date(date);
    tomorrow.setDate(tomorrow.getDate() + 1);
    return tomorrow.toISOString().split("T")[0];
}

const form = useForm({
    car_id: "",
    start_date: today,
    end_date: "", // End date will be set in onMounted
});

const availableCars = ref([]);
const selectedCar = ref(null);
const searchQuery = ref(""); // Search query

// Computed property to filter cars based on search query
const filteredCars = computed(() => {
    const query = searchQuery.value.toLowerCase();
    return availableCars.value.filter(
        (car) =>
            car.brand.toLowerCase().includes(query) ||
            car.model.toLowerCase().includes(query)
    );
});

// Computed property to calculate the number of days between start and end dates
const durationInDays = computed(() => {
    const start = new Date(form.start_date);
    const end = new Date(form.end_date);
    const timeDiff = end - start;
    return Math.ceil(timeDiff / (1000 * 60 * 60 * 24)); // Convert milliseconds to days
});

// Computed property to calculate the total cost based on daily rate and duration
const totalCost = computed(() => {
    if (!selectedCar.value || durationInDays.value <= 0) return 0;
    return selectedCar.value.daily_rate * durationInDays.value;
});

// Computed property to dynamically set the min value for end_date as one day after start_date
const minEndDate = computed(() => {
    return getTomorrowDate(form.start_date);
});

// Fetch available cars when start_date or end_date changes
watch(
    [() => form.start_date, () => form.end_date],
    ([newStartDate, newEndDate]) => {
        if (newStartDate && newEndDate) {
            fetchAvailableCars(newStartDate, newEndDate);
        }
    }
);

// Function to fetch available cars based on the selected start and end dates
async function fetchAvailableCars(startDate, endDate) {
    try {
        const response = await axios.get(route("rent.available"), {
            params: {
                start_date: startDate,
                end_date: endDate,
            },
        });
        availableCars.value = response.data.availableCars;
        // Reset selected car if it's not in the new available cars list
        if (
            selectedCar.value &&
            !availableCars.value.find((car) => car.id === selectedCar.value.id)
        ) {
            selectedCar.value = null;
        }
    } catch (error) {
        console.error("Error fetching available cars", error);
    }
}

// Watch for changes in selected car and update daily rate
watch(
    () => form.car_id,
    (newCarId) => {
        selectedCar.value =
            availableCars.value.find((car) => car.id === newCarId) || null;
    }
);

// Method to format currency
function formatCurrency(value) {
    if (value === null || value === undefined) return "Rp. 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
    }).format(value);
}

// Form submission function
function submit() {
    form.post(route("rent.store"));
}

// Fetch available cars initially if dates are set
onMounted(() => {
    form.end_date = getTomorrowDate(today); // Set end date to tomorrow's date

    if (form.start_date && form.end_date) {
        fetchAvailableCars(form.start_date, form.end_date);
    }
});
</script>

<template>
    <Head title="Rent" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Rent
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
                                Rent Car
                            </h2>
                            <p
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                Select a car and duration of rent based on your
                                needs.
                            </p>
                        </header>

                        <form @submit.prevent="submit" class="mt-6 space-y-6">
                            <!-- Start Date -->
                            <div>
                                <InputLabel
                                    for="start_date"
                                    value="Start Date"
                                />
                                <TextInput
                                    id="start_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.start_date"
                                    required
                                    autocomplete="start_date"
                                    :min="today"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.start_date"
                                />
                            </div>

                            <!-- End Date -->
                            <div>
                                <InputLabel for="end_date" value="End Date" />
                                <TextInput
                                    id="end_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.end_date"
                                    required
                                    autocomplete="end_date"
                                    :min="minEndDate"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.end_date"
                                />
                            </div>

                            <!-- Car Selection -->
                            <div>
                                <InputLabel for="car_id" value="Car" />
                                <select
                                    id="car_id"
                                    v-model="form.car_id"
                                    required
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                >
                                    <option value="" disabled>
                                        Select a car
                                    </option>
                                    <option
                                        v-for="car in filteredCars"
                                        :key="car.id"
                                        :value="car.id"
                                    >
                                        {{ car.brand }} - {{ car.model }}
                                    </option>
                                </select>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.car_id"
                                />
                            </div>

                            <!-- Car Search -->
                            <div>
                                <InputLabel
                                    for="car_search"
                                    value="Search Car"
                                />
                                <TextInput
                                    id="car_search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="searchQuery"
                                    placeholder="Search by brand or model"
                                />
                            </div>

                            <div v-if="selectedCar">
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
                                                    selectedCar.daily_rate
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
                                            {{ durationInDays }} day(s)
                                        </p>
                                    </div>
                                    <div>
                                        <InputLabel
                                            for="total_cost"
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

                            <!-- Submit Button -->
                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">
                                    Rent Car
                                </PrimaryButton>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
