<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
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
    features: Features;
    errors: Record<string, string>;
}

defineProps<Props>();

const emit = defineEmits<{
    'update:features': [value: Features];
}>();

const featureOptions = [
    {
        key: 'contactForm',
        label: 'Contact Form',
        description: 'Let visitors get in touch with you',
        icon: 'mail',
    },
    {
        key: 'blog',
        label: 'Blog',
        description: 'Share news, updates, and articles',
        icon: 'pencil',
    },
    {
        key: 'products',
        label: 'Products / Services',
        description: 'Showcase and sell your offerings',
        icon: 'shopping-bag',
    },
    {
        key: 'testimonials',
        label: 'Testimonials',
        description: 'Display customer reviews and feedback',
        icon: 'star',
    },
    {
        key: 'faq',
        label: 'FAQ Section',
        description: 'Answer common questions',
        icon: 'question',
    },
    {
        key: 'team',
        label: 'Team / About Section',
        description: 'Introduce your team members',
        icon: 'users',
    },
    {
        key: 'portfolio',
        label: 'Portfolio / Gallery',
        description: 'Showcase your work and projects',
        icon: 'image',
    },
    {
        key: 'newsletter',
        label: 'Newsletter Signup',
        description: 'Collect email subscribers',
        icon: 'newspaper',
    },
];

const toggleFeature = (key: string, currentFeatures: Features) => {
    emit('update:features', {
        ...currentFeatures,
        [key]: !currentFeatures[key as keyof Features],
    });
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Features</h3>
            <p class="mt-1 text-sm text-gray-500">Select the features you want to include on your website.</p>
        </div>

        <div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button
                    v-for="feature in featureOptions"
                    :key="feature.key"
                    type="button"
                    @click="toggleFeature(feature.key, features)"
                    :class="[
                        features[feature.key as keyof Features]
                            ? 'border-indigo-600 bg-indigo-50'
                            : 'border-gray-300 hover:border-gray-400',
                        'relative flex items-start p-4 rounded-lg border cursor-pointer focus:outline-none transition-colors'
                    ]"
                >
                    <div class="flex-shrink-0 mr-3">
                        <div
                            :class="[
                                features[feature.key as keyof Features]
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-gray-100 text-gray-500',
                                'w-10 h-10 rounded-lg flex items-center justify-center transition-colors'
                            ]"
                        >
                            <!-- Mail Icon -->
                            <svg v-if="feature.icon === 'mail'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <!-- Pencil Icon -->
                            <svg v-else-if="feature.icon === 'pencil'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            <!-- Shopping Bag Icon -->
                            <svg v-else-if="feature.icon === 'shopping-bag'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <!-- Star Icon -->
                            <svg v-else-if="feature.icon === 'star'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <!-- Question Icon -->
                            <svg v-else-if="feature.icon === 'question'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <!-- Users Icon -->
                            <svg v-else-if="feature.icon === 'users'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <!-- Image Icon -->
                            <svg v-else-if="feature.icon === 'image'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <!-- Newspaper Icon -->
                            <svg v-else-if="feature.icon === 'newspaper'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 text-left">
                        <span class="block text-sm font-medium text-gray-900">
                            {{ feature.label }}
                        </span>
                        <span class="block text-xs text-gray-500 mt-0.5">
                            {{ feature.description }}
                        </span>
                    </div>
                    <div class="flex-shrink-0 ml-2">
                        <div
                            :class="[
                                features[feature.key as keyof Features]
                                    ? 'bg-indigo-600'
                                    : 'bg-gray-200',
                                'w-5 h-5 rounded flex items-center justify-center transition-colors'
                            ]"
                        >
                            <svg
                                v-if="features[feature.key as keyof Features]"
                                class="w-3.5 h-3.5 text-white"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</template>
