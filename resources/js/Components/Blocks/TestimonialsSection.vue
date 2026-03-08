<script setup lang="ts">
import { computed } from 'vue';

interface Testimonial {
    id?: string;
    name: string;
    role?: string;
    content: string;
    avatar?: string | null;
}

interface Props {
    layout?: 'grid' | 'carousel';
    testimonials?: Testimonial[];
}

const props = withDefaults(defineProps<Props>(), {
    layout: 'grid',
    testimonials: () => [],
});

// Add unique IDs to testimonials
const testimonialsWithIds = computed(() =>
    props.testimonials.map((testimonial, index) => ({
        ...testimonial,
        uniqueId: testimonial.id ?? `testimonial-${testimonial.name.toLowerCase().replace(/\s+/g, '-')}-${index}`,
    }))
);
</script>

<template>
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                :class="[
                    layout === 'grid' ? 'grid gap-8' : 'flex overflow-x-auto gap-6 pb-4',
                    layout === 'grid' && testimonialsWithIds.length >= 3 ? 'md:grid-cols-3' : '',
                    layout === 'grid' && testimonialsWithIds.length === 2 ? 'md:grid-cols-2' : '',
                ]"
            >
                <div
                    v-for="testimonial in testimonialsWithIds"
                    :key="testimonial.uniqueId"
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col"
                    :class="layout === 'carousel' ? 'min-w-[300px] flex-shrink-0' : ''"
                >
                    <!-- Quote icon -->
                    <svg class="w-8 h-8 text-gray-300 mb-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>

                    <!-- Content -->
                    <p class="text-gray-600 flex-grow mb-4 italic">
                        "{{ testimonial.content }}"
                    </p>

                    <!-- Author -->
                    <div class="flex items-center mt-auto pt-4 border-t border-gray-100">
                        <div
                            v-if="testimonial.avatar"
                            class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden mr-3"
                        >
                            <img :src="testimonial.avatar" :alt="testimonial.name" class="w-full h-full object-cover" />
                        </div>
                        <div
                            v-else
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3"
                        >
                            <span class="text-white font-medium text-sm">
                                {{ testimonial.name.charAt(0).toUpperCase() }}
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ testimonial.name }}</p>
                            <p v-if="testimonial.role" class="text-sm text-gray-500">{{ testimonial.role }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
