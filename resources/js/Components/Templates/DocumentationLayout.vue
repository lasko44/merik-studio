<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    options?: {
        show_nav?: boolean;
        show_toc?: boolean;
        nav_sticky?: boolean;
    };
}

const props = withDefaults(defineProps<Props>(), {
    options: () => ({}),
});

const showNav = computed(() => props.options?.show_nav ?? true);
const showToc = computed(() => props.options?.show_toc ?? true);
const navSticky = computed(() => props.options?.nav_sticky ?? true);
</script>

<template>
    <div class="max-w-screen-2xl mx-auto">
        <div class="flex">
            <!-- Left Navigation -->
            <aside
                v-if="showNav"
                :class="[
                    'hidden lg:block w-64 flex-shrink-0 border-r border-gray-200',
                    { 'sticky top-0 h-screen overflow-y-auto': navSticky }
                ]"
            >
                <div class="py-8 px-4">
                    <slot name="navigation" />
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 min-w-0">
                <div class="px-4 sm:px-6 lg:px-8 py-8">
                    <div class="prose prose-indigo max-w-none">
                        <slot />
                    </div>
                </div>
            </main>

            <!-- Right TOC -->
            <aside
                v-if="showToc"
                :class="[
                    'hidden xl:block w-64 flex-shrink-0 border-l border-gray-200',
                    { 'sticky top-0 h-screen overflow-y-auto': navSticky }
                ]"
            >
                <div class="py-8 px-4">
                    <slot name="toc" />
                </div>
            </aside>
        </div>
    </div>
</template>
