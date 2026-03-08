<script setup lang="ts">
interface Props {
    title?: string;
    email?: string;
    phone?: string;
    address?: string;
    showIcons?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Contact Us',
    email: '',
    phone: '',
    address: '',
    showIcons: true,
});
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <h3 v-if="props.title" class="text-lg font-semibold text-gray-900 mb-4">
            {{ props.title }}
        </h3>
        <div class="space-y-3 text-sm">
            <a v-if="props.email" :href="`mailto:${props.email}`" class="flex items-center gap-2 text-gray-600 hover:text-gray-900">
                <svg v-if="props.showIcons" class="w-4 h-4 flex-shrink-0 sidebar-contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ props.email }}
            </a>
            <a v-if="props.phone" :href="`tel:${props.phone}`" class="flex items-center gap-2 text-gray-600 hover:text-gray-900">
                <svg v-if="props.showIcons" class="w-4 h-4 flex-shrink-0 sidebar-contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                {{ props.phone }}
            </a>
            <p v-if="props.address" class="flex items-start gap-2 text-gray-600">
                <svg v-if="props.showIcons" class="w-4 h-4 flex-shrink-0 mt-0.5 sidebar-contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ props.address }}
            </p>
        </div>
    </div>
</template>

<style scoped>
.sidebar-contact-icon {
    color: var(--primary-color);
}
</style>
