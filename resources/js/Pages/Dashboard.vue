<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AlertSuccess from "@/Components/AlertSuccess.vue";
import { Head, usePage } from "@inertiajs/vue3";

defineProps({
    rentals: {
        type: Array,
    },
});

const columns = [
    { data: "id" },
    { data: "car.brand" },
    { data: "car.model" },
    { data: "car.license_plate" },
    { data: "car.daily_rate" },
    { data: "estimated_cost" },
    { data: "start_date" },
    { data: "end_date" },
    { data: "is_returned" },
];

const options = {
    responsive: true,
    select: true,
    layout: {
        topStart: [
            {
                buttons: [
                    {
                        extend: "searchPanes",
                        config: {
                            cascadePanes: true,
                            collapse: false,
                            threshold: 1,
                            columns: [1, 2, 3, 8],
                        },
                    },
                ],
            },
            "pageLength",
        ],
        bottomStart: {
            buttons: ["copy", "csv", "excel", "print"],
        },
        bottom: "info",
    },
};

const alertMessage = usePage().props.flash.success || "";
const user = usePage().props.auth.user;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg"
                >
                    <div class="text-gray-900 dark:text-gray-100">
                        <h2
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            Profile Information
                        </h2>

                        <div class="mt-4 flex flex-wrap items-center gap-10">
                            <!-- Default SVG photo -->
                            <svg
                                class="w-36 h-40 text-gray-800 dark:text-white"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M12 20a7.966 7.966 0 0 1-5.002-1.756l.002.001v-.683c0-1.794 1.492-3.25 3.333-3.25h3.334c1.84 0 3.333 1.456 3.333 3.25v.683A7.966 7.966 0 0 1 12 20ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10c0 5.5-4.44 9.963-9.932 10h-.138C6.438 21.962 2 17.5 2 12Zm10-5c-1.84 0-3.333 1.455-3.333 3.25S10.159 13.5 12 13.5c1.84 0 3.333-1.455 3.333-3.25S13.841 7 12 7Z"
                                    clip-rule="evenodd"
                                />
                            </svg>

                            <div class="flex gap-20 flex-wrap">
                                <div class="flex flex-col gap-3">
                                    <p>
                                        <strong>Name:</strong> {{ user.name }}
                                    </p>
                                    <p>
                                        <strong>Email:</strong> {{ user.email }}
                                    </p>
                                    <p>
                                        <strong>Address:</strong>
                                        {{ user.address || "Not provided" }}
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3">
                                    <p>
                                        <strong>Phone:</strong>
                                        {{
                                            user.phone_number || "Not provided"
                                        }}
                                    </p>
                                    <p>
                                        <strong>Driver License:</strong>
                                        {{
                                            user.driver_license ||
                                            "Not provided"
                                        }}
                                    </p>
                                    <p>
                                        <strong>Joined:</strong>
                                        {{
                                            new Date(
                                                user.created_at
                                            ).toLocaleDateString("en-US")
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <AlertSuccess :message="alertMessage" />

                <div
                    class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg"
                >
                    <header>
                        <h2
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            Rent List
                        </h2>

                        <p
                            class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                        >
                            Browse through your rented cars.
                        </p>
                    </header>

                    <DataTable
                        :data="rentals"
                        :columns="columns"
                        :options="options"
                        class="display"
                    >
                        <thead>
                            <tr>
                                <td class="w-16">ID</td>
                                <td>Car Brand</td>
                                <td>Car Model</td>
                                <td>License Plate</td>
                                <td>Daily Rate</td>
                                <td>Estimated Price</td>
                                <td>Start Date</td>
                                <td>End Date</td>
                                <td>Status</td>
                            </tr>
                        </thead>
                    </DataTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
