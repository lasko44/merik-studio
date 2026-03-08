<script setup lang="ts">
import { computed } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

interface Features {
    blog: boolean;
    products: boolean;
    contactForm: boolean;
    testimonials: boolean;
    faq: boolean;
    team: boolean;
    portfolio: boolean;
    newsletter: boolean;
}

interface Props {
    pages: string[];
    customPages: string[];
    features: Features;
    errors: Record<string, string>;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:pages': [value: string[]];
    'update:customPages': [value: string[]];
}>();

const pageOptions = computed(() => {
    const base = [
        { value: 'home', label: 'Home', description: 'Your main landing page', required: true },
        { value: 'about', label: 'About', description: 'Tell your story' },
        { value: 'services', label: 'Services', description: 'What you offer' },
        { value: 'contact', label: 'Contact', description: 'How to reach you' },
    ];

    // Add feature-specific pages
    if (props.features.blog) {
        base.push({ value: 'blog', label: 'Blog', description: 'Your articles and posts' });
    }
    if (props.features.portfolio) {
        base.push({ value: 'portfolio', label: 'Portfolio', description: 'Showcase your work' });
    }
    if (props.features.faq) {
        base.push({ value: 'faq', label: 'FAQ', description: 'Frequently asked questions' });
    }
    if (props.features.testimonials) {
        base.push({ value: 'testimonials', label: 'Testimonials', description: 'Customer reviews' });
    }
    if (props.features.team) {
        base.push({ value: 'team', label: 'Team', description: 'Meet the team' });
    }
    if (props.features.products) {
        base.push({ value: 'products', label: 'Products', description: 'Your products catalog' });
    }

    return base;
});

const togglePage = (page: string) => {
    if (page === 'home') return; // Home is always required

    if (props.pages.includes(page)) {
        emit('update:pages', props.pages.filter(p => p !== page));
    } else {
        emit('update:pages', [...props.pages, page]);
    }
};

const addCustomPage = () => {
    emit('update:customPages', [...props.customPages, '']);
};

const updateCustomPage = (index: number, value: string) => {
    const newCustomPages = [...props.customPages];
    newCustomPages[index] = value;
    emit('update:customPages', newCustomPages);
};

const removeCustomPage = (index: number) => {
    const newCustomPages = [...props.customPages];
    newCustomPages.splice(index, 1);
    emit('update:customPages', newCustomPages);
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Choose Your Pages</h3>
            <p class="mt-1 text-sm text-gray-500">Select the pages you want on your website. You can always add more later.</p>
        </div>

        <div class="space-y-4">
            <!-- Standard Pages -->
            <div>
                <InputLabel value="Standard Pages" />
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button
                        v-for="page in pageOptions"
                        :key="page.value"
                        type="button"
                        @click="togglePage(page.value)"
                        :disabled="page.required"
                        :class="[
                            pages.includes(page.value)
                                ? 'border-indigo-600 bg-indigo-50'
                                : 'border-gray-300 hover:border-gray-400',
                            page.required ? 'cursor-not-allowed opacity-75' : 'cursor-pointer',
                            'relative flex items-center p-3 rounded-lg border focus:outline-none transition-colors'
                        ]"
                    >
                        <div
                            :class="[
                                pages.includes(page.value)
                                    ? 'bg-indigo-600 border-indigo-600'
                                    : 'bg-white border-gray-300',
                                'w-5 h-5 rounded border flex-shrink-0 flex items-center justify-center mr-3'
                            ]"
                        >
                            <svg
                                v-if="pages.includes(page.value)"
                                class="w-3.5 h-3.5 text-white"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1 text-left">
                            <span class="block text-sm font-medium text-gray-900">
                                {{ page.label }}
                                <span v-if="page.required" class="text-xs text-gray-500 ml-1">(Required)</span>
                            </span>
                            <span class="block text-xs text-gray-500">{{ page.description }}</span>
                        </div>
                    </button>
                </div>
                <InputError :message="errors.pages" class="mt-2" />
            </div>

            <!-- Custom Pages -->
            <div>
                <InputLabel value="Custom Pages (optional)" />
                <p class="text-xs text-gray-500 mb-2">Add any additional pages you need</p>

                <div class="space-y-2">
                    <div v-for="(customPage, index) in customPages" :key="index" class="flex items-center gap-2">
                        <TextInput
                            :model-value="customPage"
                            @update:model-value="updateCustomPage(index, $event)"
                            type="text"
                            class="flex-1"
                            placeholder="e.g., Pricing, Our Process, Gallery"
                        />
                        <button
                            type="button"
                            @click="removeCustomPage(index)"
                            class="p-2 text-gray-400 hover:text-red-500 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button
                    type="button"
                    @click="addCustomPage"
                    class="mt-2 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Custom Page
                </button>
            </div>

            <!-- Summary -->
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">
                    <span class="font-medium">{{ pages.length + customPages.filter(p => p.trim()).length }}</span>
                    pages will be created
                </p>
            </div>
        </div>
    </div>
</template>
