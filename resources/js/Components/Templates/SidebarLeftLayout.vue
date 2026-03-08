<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    options?: {
        sidebar_width?: string;
        sidebar_sticky?: boolean;
    };
}

const props = withDefaults(defineProps<Props>(), {
    options: () => ({}),
});

const sidebarWidth = computed(() => {
    const width = props.options?.sidebar_width || '1/3';
    return {
        '1/4': 'lg:col-span-1',
        '1/3': 'lg:col-span-1',
        '2/5': 'lg:col-span-2',
    }[width] || 'lg:col-span-1';
});

const mainWidth = computed(() => {
    const width = props.options?.sidebar_width || '1/3';
    return {
        '1/4': 'lg:col-span-3',
        '1/3': 'lg:col-span-2',
        '2/5': 'lg:col-span-3',
    }[width] || 'lg:col-span-2';
});

const gridCols = computed(() => {
    const width = props.options?.sidebar_width || '1/3';
    return {
        '1/4': 'lg:grid-cols-4',
        '1/3': 'lg:grid-cols-3',
        '2/5': 'lg:grid-cols-5',
    }[width] || 'lg:grid-cols-3';
});

const isSticky = computed(() => props.options?.sidebar_sticky ?? true);
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div :class="['grid grid-cols-1 gap-8', gridCols]">
            <!-- Sidebar (left) -->
            <aside :class="['order-2 lg:order-1', sidebarWidth]">
                <div :class="{ 'sticky top-8': isSticky }" class="space-y-6">
                    <slot name="sidebar" />
                </div>
            </aside>

            <!-- Main content -->
            <div :class="['order-1 lg:order-2', mainWidth]">
                <slot name="content" />
            </div>
        </div>
    </div>
</template>
