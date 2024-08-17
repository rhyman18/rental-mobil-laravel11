<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Rental Service" />

    <div class="relative flex flex-col justify-center items-center min-h-screen bg-gradient-to-b from-blue-100 to-blue-300 dark:from-gray-800 dark:to-gray-900">
        <div v-if="canLogin" class="absolute top-6 right-6">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="text-sm font-semibold text-blue-700 hover:text-blue-900 dark:text-blue-400 dark:hover:text-white focus:outline-none"
            >
                Dashboard
            </Link>

            <template v-else>
                <Link
                    :href="route('login')"
                    class="text-sm font-semibold text-blue-700 hover:text-blue-900 dark:text-blue-400 dark:hover:text-white focus:outline-none"
                >
                    Log in
                </Link>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ml-4 text-sm font-semibold text-blue-700 hover:text-blue-900 dark:text-blue-400 dark:hover:text-white focus:outline-none"
                >
                    Register
                </Link>
            </template>
        </div>

        <div class="flex flex-col items-center">
            <!-- Logo -->
            <div class="flex justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-auto text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18m6-6l-6-6-6 6" />
                </svg>
            </div>
    
            <!-- Welcome Text -->
            <h1 class="mt-6 text-3xl font-bold text-blue-800 dark:text-white">Welcome to Rental Hub</h1>
            <p class="mt-2 text-center text-blue-600 dark:text-blue-400">Your one-stop solution for car rentals</p>
    
            <!-- Footer -->
            <div class="my-6 text-center text-sm text-blue-600 dark:text-blue-400">
                <p>Laravel v{{ laravelVersion }} | PHP v{{ phpVersion }}</p>
            </div>
        </div>
    </div>
</template>
