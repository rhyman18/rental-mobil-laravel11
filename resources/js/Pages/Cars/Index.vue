<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AlertSuccess from "@/Components/AlertSuccess.vue";
import LinkButton from "@/Components/LinkButton.vue";
import { Head, usePage } from "@inertiajs/vue3";

defineProps({
    cars: {
        type: Array,
    },
});

const columns = [
    { data: "id" },
    { data: "brand" },
    { data: "model" },
    { data: "license_plate" },
    { data: "daily_rate" },
    { data: "available" },
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
                            columns: [1, 2, 5],
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
</script>

<template>
    <Head title="Cars" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2
                    class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
                >
                    Cars
                </h2>
                <LinkButton :href="route('cars.create')">Add Cars</LinkButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <AlertSuccess :message="alertMessage" />
                <div
                    class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg"
                >
                    <header>
                        <h2
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            Cars List
                        </h2>

                        <p
                            class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                        >
                            Browse through our collection of cars available for
                            rent.
                        </p>
                    </header>

                    <DataTable
                        :data="cars"
                        :columns="columns"
                        :options="options"
                        class="display"
                    >
                        <thead>
                            <tr>
                                <td class="w-16">ID</td>
                                <td>Brand</td>
                                <td>Model</td>
                                <td>License Plate</td>
                                <td>Daily Rate</td>
                                <td>Status</td>
                            </tr>
                        </thead>
                    </DataTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
