<script setup lang="ts">
import { ref } from 'vue';

interface Props {
    title: string;
    description?: string;
    collapsible?: boolean;
    defaultOpen?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    collapsible: false,
    defaultOpen: true,
});

const isOpen = ref(props.defaultOpen);

const toggle = () => {
    if (props.collapsible) {
        isOpen.value = !isOpen.value;
    }
};
</script>

<template>
    <div class="border border-gray-200 rounded-lg">
        <button
            type="button"
            :class="[
                'w-full px-4 py-3 flex items-center justify-between text-left',
                collapsible ? 'cursor-pointer hover:bg-gray-50' : 'cursor-default',
            ]"
            @click="toggle"
        >
            <div>
                <h4 class="text-sm font-medium text-gray-900">{{ title }}</h4>
                <p v-if="description" class="mt-0.5 text-xs text-gray-500">{{ description }}</p>
            </div>
            <svg
                v-if="collapsible"
                :class="[
                    'h-5 w-5 text-gray-400 transition-transform',
                    isOpen ? 'rotate-180' : '',
                ]"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div v-show="isOpen" class="px-4 pb-4">
            <slot />
        </div>
    </div>
</template>
