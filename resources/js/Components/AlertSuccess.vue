<script setup>
import { ref } from "vue";

const props = defineProps({
    message: {
        type: String,
        default: "",
    },
});

const showAlert = ref(true);

const closeAlert = () => {
    showAlert.value = false;
};
</script>

<template>
    <transition name="fade">
        <div
            v-if="showAlert && message"
            class="flex items-center p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-400"
            role="alert"
        >
            <!-- Checkmark Icon -->
            <svg
                class="w-5 h-5 flex-shrink-0 mr-3"
                aria-hidden="true"
                fill="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    fill-rule="evenodd"
                    d="M12 2a10 10 0 1 1-10 10A10 10 0 0 1 12 2zm-1 14a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1 1 1 0 0 0-1 1v3a1 1 0 0 0 1 1zm1-8a1 1 0 0 0-2 0v4a1 1 0 0 0 2 0V8z"
                    clip-rule="evenodd"
                ></path>
            </svg>
            <span class="sr-only">Success</span>
            <div class="flex-1">{{ message }}</div>
            <button
                @click="closeAlert"
                class="ml-4 text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300"
            >
                <svg
                    class="w-5 h-5"
                    aria-hidden="true"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        fill-rule="evenodd"
                        d="M6.293 6.293a1 1 0 0 1 1.414 0L12 9.586l4.293-4.293a1 1 0 0 1 1.414 1.414L13.414 11l4.293 4.293a1 1 0 0 1-1.414 1.414L12 12.414l-4.293 4.293a1 1 0 0 1-1.414-1.414L10.586 12 6.293 7.707a1 1 0 0 1 0-1.414z"
                        clip-rule="evenodd"
                    ></path>
                </svg>
                <span class="sr-only">Close</span>
            </button>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0;
}
</style>
