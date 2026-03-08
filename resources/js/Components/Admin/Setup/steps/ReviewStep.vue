<script setup lang="ts">
import { computed } from 'vue';

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

interface FormData {
    // Step 1: Business Info
    businessName: string;
    industry: string;
    description: string;
    targetAudience: string;
    tagline: string;
    yearsInBusiness: string;
    location: string;

    // Step 2: Goals
    primaryGoal: string;
    secondaryGoals: string[];
    callToAction: string;

    // Step 3: Design
    style: string;
    colorMood: string;
    primaryColor: string | null;
    logo: string | null;

    // Step 4: Features
    features: Features;

    // Step 5: Pages
    pages: string[];
    customPages: string[];

    // Step 6: Content
    services: string[];
    companyStory: string;
    achievements: string;
    whyChooseUs: string;
    processSteps: string;
    faqItems: string;
    testimonialNames: string;

    // Contact info
    contactEmail: string;
    contactPhone: string;
    contactAddress: string;
}

interface Props {
    formData: FormData;
    industries: Record<string, string>;
    isGenerating: boolean;
    generationStatus?: string;
    generationError?: string;
}

const props = defineProps<Props>();

const goalLabels: Record<string, string> = {
    leads: 'Generate Leads',
    sales: 'Sell Products/Services',
    information: 'Share Information',
    portfolio: 'Showcase Portfolio',
    community: 'Build Community',
};

const styleLabels: Record<string, string> = {
    modern: 'Modern',
    classic: 'Classic',
    minimal: 'Minimal',
    bold: 'Bold',
};

const moodLabels: Record<string, string> = {
    professional: 'Professional',
    friendly: 'Friendly',
    energetic: 'Energetic',
    calm: 'Calm',
    luxury: 'Luxury',
};

const yearsLabels: Record<string, string> = {
    'new': 'Just Starting',
    '1-2': '1-2 Years',
    '3-5': '3-5 Years',
    '5-10': '5-10 Years',
    '10-20': '10-20 Years',
    '20+': '20+ Years',
};

const featureLabels: Record<string, string> = {
    blog: 'Blog',
    products: 'Products/Services',
    contactForm: 'Contact Form',
    testimonials: 'Testimonials',
    faq: 'FAQ Section',
    team: 'Team/About',
    portfolio: 'Portfolio/Gallery',
    newsletter: 'Newsletter Signup',
};

const enabledFeatures = computed(() => {
    if (!props.formData.features) return [];
    return Object.entries(props.formData.features)
        .filter(([_, enabled]) => enabled)
        .map(([key]) => featureLabels[key] || key);
});

const allPages = computed(() => {
    const pages = [...(props.formData.pages || [])];
    const customPages = (props.formData.customPages || []).filter(p => p && p.trim());
    return [...pages, ...customPages];
});

const filledServices = computed(() => {
    return (props.formData.services || []).filter(s => s && s.trim());
});

const hasContentDetails = computed(() => {
    return filledServices.value.length > 0 ||
        props.formData.companyStory?.trim() ||
        props.formData.whyChooseUs?.trim() ||
        props.formData.achievements?.trim() ||
        props.formData.processSteps?.trim() ||
        props.formData.faqItems?.trim() ||
        props.formData.testimonialNames?.trim();
});

const hasContactInfo = computed(() => {
    return props.formData.contactEmail?.trim() ||
        props.formData.contactPhone?.trim() ||
        props.formData.contactAddress?.trim();
});
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Review Your Choices</h3>
            <p class="mt-1 text-sm text-gray-500">
                Review your selections before we generate your site. You can go back to make changes.
            </p>
        </div>

        <!-- Loading State -->
        <div v-if="isGenerating" class="py-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 mb-4">
                <svg class="w-8 h-8 text-indigo-600 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h4 class="text-lg font-medium text-gray-900 mb-2">Generating Your Site...</h4>
            <p class="text-sm text-gray-500 max-w-md mx-auto">
                {{ generationStatus || 'Our AI is crafting your website content. This may take up to a minute.' }}
            </p>
            <div class="mt-6 space-y-2 text-sm text-gray-500">
                <p class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                    </svg>
                    Analyzing your business information
                </p>
                <p class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                    Generating page content with AI
                </p>
                <p class="flex items-center justify-center gap-2 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2" />
                    </svg>
                    Setting up your site
                </p>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="generationError" class="py-8">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-red-800">Generation Failed</h4>
                        <p class="mt-1 text-sm text-red-700">{{ generationError }}</p>
                        <p class="mt-2 text-sm text-red-600">Please try again or adjust your selections.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Content -->
        <div v-else class="space-y-6">
            <!-- Business Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Business Information</h4>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <dt class="text-gray-500">Name</dt>
                    <dd class="text-gray-900 font-medium">{{ formData.businessName }}</dd>

                    <dt class="text-gray-500">Industry</dt>
                    <dd class="text-gray-900">{{ industries[formData.industry] || formData.industry }}</dd>

                    <template v-if="formData.tagline">
                        <dt class="text-gray-500">Tagline</dt>
                        <dd class="text-gray-900">{{ formData.tagline }}</dd>
                    </template>

                    <template v-if="formData.yearsInBusiness">
                        <dt class="text-gray-500">Experience</dt>
                        <dd class="text-gray-900">{{ yearsLabels[formData.yearsInBusiness] || formData.yearsInBusiness }}</dd>
                    </template>

                    <template v-if="formData.location">
                        <dt class="text-gray-500">Location</dt>
                        <dd class="text-gray-900">{{ formData.location }}</dd>
                    </template>

                    <template v-if="formData.description">
                        <dt class="text-gray-500 col-span-2 mt-2">Description</dt>
                        <dd class="text-gray-900 col-span-2">{{ formData.description }}</dd>
                    </template>

                    <template v-if="formData.targetAudience">
                        <dt class="text-gray-500 col-span-2 mt-2">Target Audience</dt>
                        <dd class="text-gray-900 col-span-2">{{ formData.targetAudience }}</dd>
                    </template>
                </dl>
            </div>

            <!-- Goals -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Goals</h4>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <dt class="text-gray-500">Primary Goal</dt>
                    <dd class="text-gray-900">{{ goalLabels[formData.primaryGoal] || formData.primaryGoal }}</dd>

                    <template v-if="formData.secondaryGoals && formData.secondaryGoals.length">
                        <dt class="text-gray-500">Secondary Goals</dt>
                        <dd class="text-gray-900">{{ formData.secondaryGoals.join(', ') }}</dd>
                    </template>

                    <template v-if="formData.callToAction">
                        <dt class="text-gray-500">Call to Action</dt>
                        <dd class="text-gray-900">{{ formData.callToAction }}</dd>
                    </template>
                </dl>
            </div>

            <!-- Design -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Design</h4>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <dt class="text-gray-500">Style</dt>
                    <dd class="text-gray-900">{{ styleLabels[formData.style] || formData.style }}</dd>

                    <dt class="text-gray-500">Color Mood</dt>
                    <dd class="text-gray-900">{{ moodLabels[formData.colorMood] || formData.colorMood }}</dd>

                    <template v-if="formData.primaryColor">
                        <dt class="text-gray-500">Primary Color</dt>
                        <dd class="text-gray-900 flex items-center gap-2">
                            <span
                                :style="{ backgroundColor: formData.primaryColor }"
                                class="w-4 h-4 rounded-full inline-block border border-gray-300"
                            ></span>
                            {{ formData.primaryColor }}
                        </dd>
                    </template>

                    <dt class="text-gray-500">Logo</dt>
                    <dd class="text-gray-900">{{ formData.logo ? 'Uploaded' : 'None (can add later)' }}</dd>
                </dl>
            </div>

            <!-- Features -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Features</h4>
                <div v-if="enabledFeatures.length" class="flex flex-wrap gap-2">
                    <span
                        v-for="feature in enabledFeatures"
                        :key="feature"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                    >
                        {{ feature }}
                    </span>
                </div>
                <p v-else class="text-sm text-gray-500">No additional features selected</p>
            </div>

            <!-- Pages -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Pages ({{ allPages.length }})</h4>
                <div class="flex flex-wrap gap-2">
                    <span
                        v-for="page in allPages"
                        :key="page"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800 capitalize"
                    >
                        {{ page }}
                    </span>
                </div>
            </div>

            <!-- Content Details -->
            <div v-if="hasContentDetails" class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Content Details</h4>
                <dl class="space-y-3 text-sm">
                    <div v-if="filledServices.length">
                        <dt class="text-gray-500 mb-1">Services/Products</dt>
                        <dd class="text-gray-900">
                            <ul class="list-disc list-inside space-y-1">
                                <li v-for="(service, index) in filledServices" :key="index">
                                    {{ service }}
                                </li>
                            </ul>
                        </dd>
                    </div>

                    <div v-if="formData.whyChooseUs">
                        <dt class="text-gray-500 mb-1">Why Choose You</dt>
                        <dd class="text-gray-900">{{ formData.whyChooseUs }}</dd>
                    </div>

                    <div v-if="formData.companyStory">
                        <dt class="text-gray-500 mb-1">Company Story</dt>
                        <dd class="text-gray-900 line-clamp-3">{{ formData.companyStory }}</dd>
                    </div>

                    <div v-if="formData.achievements">
                        <dt class="text-gray-500 mb-1">Achievements</dt>
                        <dd class="text-gray-900">{{ formData.achievements }}</dd>
                    </div>

                    <div v-if="formData.processSteps">
                        <dt class="text-gray-500 mb-1">Working Process</dt>
                        <dd class="text-gray-900 line-clamp-2">{{ formData.processSteps }}</dd>
                    </div>

                    <div v-if="formData.faqItems">
                        <dt class="text-gray-500 mb-1">FAQ Items</dt>
                        <dd class="text-gray-900 line-clamp-2">{{ formData.faqItems }}</dd>
                    </div>

                    <div v-if="formData.testimonialNames">
                        <dt class="text-gray-500 mb-1">Testimonial Sources</dt>
                        <dd class="text-gray-900">{{ formData.testimonialNames }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Contact Info -->
            <div v-if="hasContactInfo" class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Contact Information</h4>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <template v-if="formData.contactEmail">
                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-gray-900">{{ formData.contactEmail }}</dd>
                    </template>

                    <template v-if="formData.contactPhone">
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="text-gray-900">{{ formData.contactPhone }}</dd>
                    </template>

                    <template v-if="formData.contactAddress">
                        <dt class="text-gray-500 col-span-2">Address</dt>
                        <dd class="text-gray-900 col-span-2">{{ formData.contactAddress }}</dd>
                    </template>
                </dl>
            </div>

            <!-- Ready Message -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-green-800">Ready to generate!</h4>
                        <p class="mt-1 text-sm text-green-700">
                            Click "Generate My Site" to create your website. Our AI will generate custom content based on your selections.
                            You can edit everything after generation.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
