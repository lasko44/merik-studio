<script setup lang="ts">
import ApplicationIcon from '@/Components/ApplicationIcon.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <!-- Skip Link for Accessibility -->
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-gray-800 focus:text-white focus:rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-700"
        >
            Skip to main content
        </a>

        <div class="flex flex-col items-center">
            <Link href="/" class="flex flex-col items-center">
                <ApplicationIcon class="h-16 w-16 text-gray-800" aria-hidden="true" />
                <span class="mt-2 text-xl font-semibold text-gray-800">Merik Studio</span>
            </Link>
        </div>

        <main
            id="main-content"
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </main>
    </div>
</template>
