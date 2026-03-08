<script setup lang="ts">
import { computed } from 'vue';

interface Specification {
    label: string;
    value: string;
}

interface Props {
    title?: string;
    layout?: 'table' | 'list' | 'grid';
    specifications?: Specification[];
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Specifications',
    layout: 'table',
    specifications: () => [],
});

const filteredSpecs = computed(() => {
    return props.specifications.filter(spec => spec.label && spec.value);
});

const hasSpecs = computed(() => filteredSpecs.value.length > 0);
</script>

<template>
    <section class="product-specifications">
        <h2 v-if="title" class="text-2xl font-bold text-gray-900 mb-6">
            {{ title }}
        </h2>

        <template v-if="hasSpecs">
            <!-- Table Layout -->
            <div v-if="layout === 'table'" class="overflow-hidden border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="(spec, index) in filteredSpecs"
                            :key="index"
                            :class="index % 2 === 0 ? 'bg-gray-50' : 'bg-white'"
                        >
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap w-1/3">
                                {{ spec.label }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ spec.value }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- List Layout -->
            <dl v-else-if="layout === 'list'" class="space-y-4">
                <div
                    v-for="(spec, index) in filteredSpecs"
                    :key="index"
                    class="border-b border-gray-200 pb-4 last:border-0 last:pb-0"
                >
                    <dt class="text-sm font-medium text-gray-500">{{ spec.label }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ spec.value }}</dd>
                </div>
            </dl>

            <!-- Grid Layout -->
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    v-for="(spec, index) in filteredSpecs"
                    :key="index"
                    class="bg-gray-50 rounded-lg p-4"
                >
                    <dt class="text-sm font-medium text-gray-500">{{ spec.label }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ spec.value }}</dd>
                </div>
            </div>
        </template>

        <!-- Empty State -->
        <div v-else class="bg-gray-50 rounded-lg p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">No specifications available</p>
        </div>
    </section>
</template>
