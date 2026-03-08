<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onUnmounted, onMounted, watch } from 'vue';
import axios from 'axios';
import BusinessInfoStep from '@/Components/Admin/Setup/steps/BusinessInfoStep.vue';
import GoalsStep from '@/Components/Admin/Setup/steps/GoalsStep.vue';
import DesignStep from '@/Components/Admin/Setup/steps/DesignStep.vue';
import FeaturesStep from '@/Components/Admin/Setup/steps/FeaturesStep.vue';
import PagesStep from '@/Components/Admin/Setup/steps/PagesStep.vue';
import ContentStep from '@/Components/Admin/Setup/steps/ContentStep.vue';
import ReviewStep from '@/Components/Admin/Setup/steps/ReviewStep.vue';

interface WizardConfig {
    industries: Record<string, string>;
    colorMoods: string[];
    industryPresets: Record<string, { pages: string[]; features: string[] }>;
}

interface SavedWizardData {
    businessName?: string;
    industry?: string;
    description?: string;
    targetAudience?: string;
    tagline?: string;
    yearsInBusiness?: string;
    location?: string;
    primaryGoal?: string;
    secondaryGoals?: string[];
    callToAction?: string;
    style?: string;
    colorMood?: string;
    primaryColor?: string | null;
    logo?: string | null;
    features?: Record<string, boolean>;
    pages?: string[];
    customPages?: string[];
    services?: string[];
    companyStory?: string;
    achievements?: string;
    whyChooseUs?: string;
    processSteps?: string;
    faqItems?: string;
    testimonialNames?: string;
    contactEmail?: string;
    contactPhone?: string;
    contactAddress?: string;
}

interface Props {
    config: WizardConfig;
    savedData: SavedWizardData | null;
}

const props = defineProps<Props>();

const STORAGE_KEY = 'merik_setup_wizard_progress';

const currentStep = ref(1);
const totalSteps = 7;
const isGenerating = ref(false);
const generationStatus = ref('');
const generationError = ref('');
const showSavedNotification = ref(false);
const hasSavedProgress = ref(false);
let pollInterval: number | null = null;

const form = useForm({
    // Step 1: Business Info
    businessName: '',
    industry: 'other',
    description: '',
    targetAudience: '',
    tagline: '',
    yearsInBusiness: '',
    location: '',

    // Step 2: Goals
    primaryGoal: 'leads' as 'leads' | 'sales' | 'information' | 'portfolio' | 'community',
    secondaryGoals: [] as string[],
    callToAction: '',

    // Step 3: Design
    style: 'modern' as 'modern' | 'classic' | 'minimal' | 'bold',
    colorMood: 'professional' as 'professional' | 'friendly' | 'energetic' | 'calm' | 'luxury',
    primaryColor: null as string | null,
    logo: null as string | null,

    // Step 4: Features
    features: {
        blog: false,
        products: false,
        contactForm: true,
        testimonials: false,
        faq: false,
        team: false,
        portfolio: false,
        newsletter: false,
    },

    // Step 5: Pages
    pages: ['home', 'about', 'contact'] as string[],
    customPages: [] as string[],

    // Step 6: Content - Detailed questions for better AI content
    services: ['', '', ''] as string[],
    companyStory: '',
    achievements: '',
    whyChooseUs: '',
    processSteps: '',
    faqItems: '',
    testimonialNames: '',

    // Optional contact info
    contactEmail: '',
    contactPhone: '',
    contactAddress: '',
});

// Save progress to localStorage
const saveProgress = () => {
    try {
        const formData = form.data();
        const data = {
            step: currentStep.value,
            formData: formData,
            savedAt: new Date().toISOString(),
        };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        hasSavedProgress.value = true;

        console.log('Progress saved at step', currentStep.value, 'with', Object.keys(formData).length, 'fields');

        // Show notification
        showSavedNotification.value = true;
        setTimeout(() => {
            showSavedNotification.value = false;
        }, 2000);
    } catch (error) {
        console.error('Failed to save progress:', error);
    }
};

// Load progress from localStorage
const loadProgress = () => {
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            const data = JSON.parse(saved);

            // Load form data - use Object.assign to properly update reactive form
            if (data.formData) {
                // For each field, directly set it on the form
                form.businessName = data.formData.businessName ?? '';
                form.industry = data.formData.industry ?? 'other';
                form.description = data.formData.description ?? '';
                form.targetAudience = data.formData.targetAudience ?? '';
                form.tagline = data.formData.tagline ?? '';
                form.yearsInBusiness = data.formData.yearsInBusiness ?? '';
                form.location = data.formData.location ?? '';
                form.primaryGoal = data.formData.primaryGoal ?? 'leads';
                form.secondaryGoals = data.formData.secondaryGoals ?? [];
                form.callToAction = data.formData.callToAction ?? '';
                form.style = data.formData.style ?? 'modern';
                form.colorMood = data.formData.colorMood ?? 'professional';
                form.primaryColor = data.formData.primaryColor ?? null;
                form.logo = data.formData.logo ?? null;
                form.features = data.formData.features ?? {
                    blog: false,
                    products: false,
                    contactForm: true,
                    testimonials: false,
                    faq: false,
                    team: false,
                    portfolio: false,
                    newsletter: false,
                };
                form.pages = data.formData.pages ?? ['home', 'about', 'contact'];
                form.customPages = data.formData.customPages ?? [];
                form.services = data.formData.services ?? ['', '', ''];
                form.companyStory = data.formData.companyStory ?? '';
                form.achievements = data.formData.achievements ?? '';
                form.whyChooseUs = data.formData.whyChooseUs ?? '';
                form.processSteps = data.formData.processSteps ?? '';
                form.faqItems = data.formData.faqItems ?? '';
                form.testimonialNames = data.formData.testimonialNames ?? '';
                form.contactEmail = data.formData.contactEmail ?? '';
                form.contactPhone = data.formData.contactPhone ?? '';
                form.contactAddress = data.formData.contactAddress ?? '';
            }

            // Load step
            currentStep.value = data.step || 1;
            hasSavedProgress.value = true;

            console.log('Loaded progress from step', data.step);
            return true;
        }
    } catch (error) {
        console.error('Failed to load progress:', error);
    }
    return false;
};

// Clear saved progress
const clearProgress = () => {
    try {
        localStorage.removeItem(STORAGE_KEY);
        hasSavedProgress.value = false;
    } catch (error) {
        console.error('Failed to clear progress:', error);
    }
};

// Load saved data from backend
const loadFromBackend = (data: SavedWizardData) => {
    form.businessName = data.businessName ?? '';
    form.industry = data.industry ?? 'other';
    form.description = data.description ?? '';
    form.targetAudience = data.targetAudience ?? '';
    form.tagline = data.tagline ?? '';
    form.yearsInBusiness = data.yearsInBusiness ?? '';
    form.location = data.location ?? '';
    form.primaryGoal = (data.primaryGoal ?? 'leads') as typeof form.primaryGoal;
    form.secondaryGoals = data.secondaryGoals ?? [];
    form.callToAction = data.callToAction ?? '';
    form.style = (data.style ?? 'modern') as typeof form.style;
    form.colorMood = (data.colorMood ?? 'professional') as typeof form.colorMood;
    form.primaryColor = data.primaryColor ?? null;
    form.logo = data.logo ?? null;
    form.features = {
        blog: data.features?.blog ?? false,
        products: data.features?.products ?? false,
        contactForm: data.features?.contactForm ?? true,
        testimonials: data.features?.testimonials ?? false,
        faq: data.features?.faq ?? false,
        team: data.features?.team ?? false,
        portfolio: data.features?.portfolio ?? false,
        newsletter: data.features?.newsletter ?? false,
    };
    form.pages = data.pages ?? ['home', 'about', 'contact'];
    form.customPages = data.customPages ?? [];
    form.services = data.services ?? ['', '', ''];
    form.companyStory = data.companyStory ?? '';
    form.achievements = data.achievements ?? '';
    form.whyChooseUs = data.whyChooseUs ?? '';
    form.processSteps = data.processSteps ?? '';
    form.faqItems = data.faqItems ?? '';
    form.testimonialNames = data.testimonialNames ?? '';
    form.contactEmail = data.contactEmail ?? '';
    form.contactPhone = data.contactPhone ?? '';
    form.contactAddress = data.contactAddress ?? '';
    hasSavedProgress.value = true;
};

// Check if there's saved progress on mount
onMounted(() => {
    // Priority: backend saved data > localStorage
    if (props.savedData && Object.keys(props.savedData).length > 0) {
        console.log('Loading wizard data from backend');
        loadFromBackend(props.savedData);
    } else {
        const hasProgress = localStorage.getItem(STORAGE_KEY);
        hasSavedProgress.value = !!hasProgress;

        // Auto-load if there's saved progress in localStorage
        if (hasProgress) {
            loadProgress();
        }
    }
});

// Auto-save when step changes
watch(currentStep, () => {
    saveProgress();
});

const steps = [
    { number: 1, title: 'Business', description: 'Tell us about your business' },
    { number: 2, title: 'Goals', description: 'What do you want to achieve?' },
    { number: 3, title: 'Design', description: 'Choose your style' },
    { number: 4, title: 'Features', description: 'Select features' },
    { number: 5, title: 'Pages', description: 'Choose your pages' },
    { number: 6, title: 'Content', description: 'Add your content' },
    { number: 7, title: 'Review', description: 'Review and generate' },
];

const canProceed = computed(() => {
    switch (currentStep.value) {
        case 1:
            return form.businessName.trim().length > 0 && form.description.trim().length > 0;
        case 2:
            return form.primaryGoal.length > 0;
        case 3:
            return form.style.length > 0 && form.colorMood.length > 0;
        case 4:
            return true;
        case 5:
            return form.pages.length > 0;
        case 6:
            // At least one service should be filled
            return form.services.some(s => s.trim().length > 0);
        case 7:
            return true;
        default:
            return true;
    }
});

const nextStep = () => {
    if (currentStep.value < totalSteps && canProceed.value) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const goToStep = (step: number) => {
    if (step <= currentStep.value || canProceed.value) {
        currentStep.value = step;
    }
};

const pollJobStatus = async (jobId: string) => {
    try {
        const response = await axios.get(route('admin.setup.status', { jobId }));
        const data = response.data;

        generationStatus.value = data.message || 'Processing...';

        if (data.status === 'completed') {
            // Stop polling
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
            // Clear saved progress on successful generation
            clearProgress();
            // Redirect to dashboard
            router.visit(route('admin.dashboard'), {
                onSuccess: () => {
                    // Success message will be shown via flash
                }
            });
        } else if (data.status === 'failed') {
            // Stop polling
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
            isGenerating.value = false;
            generationError.value = data.message || 'Generation failed';
        }
    } catch (error) {
        console.error('Failed to poll status:', error);
    }
};

const generateSite = async () => {
    isGenerating.value = true;
    generationStatus.value = 'Starting site generation...';
    generationError.value = '';

    try {
        const response = await axios.post(route('admin.setup.generate'), form.data());
        const jobId = response.data.job_id;

        // Start polling for status
        pollInterval = window.setInterval(() => {
            pollJobStatus(jobId);
        }, 2000);

        // Also poll immediately
        pollJobStatus(jobId);
    } catch (error: any) {
        isGenerating.value = false;
        generationError.value = error.response?.data?.message || 'Failed to start generation';
    }
};

const applyIndustryPreset = (industry: string) => {
    const preset = props.config.industryPresets[industry];
    if (preset) {
        form.pages = [...preset.pages];
        preset.features.forEach((feature: string) => {
            if (feature in form.features) {
                (form.features as Record<string, boolean>)[feature] = true;
            }
        });
    }
};

// Cleanup polling on unmount
onUnmounted(() => {
    if (pollInterval) {
        clearInterval(pollInterval);
    }
});
</script>

<template>
    <Head title="Setup Wizard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Setup Wizard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.error" class="mb-6 rounded-lg bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="ml-3 text-sm text-red-700">{{ $page.props.flash.error }}</p>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <!-- Progress Steps -->
                    <div class="border-b border-gray-200 px-6 py-6">
                        <nav aria-label="Progress">
                            <ol class="flex items-center justify-between">
                                <li v-for="(step, index) in steps" :key="step.number" class="relative flex-1">
                                    <!-- Connector line -->
                                    <div
                                        v-if="index !== 0"
                                        class="absolute left-0 top-4 -translate-y-1/2 w-full -translate-x-1/2"
                                    >
                                        <div
                                            :class="[
                                                step.number <= currentStep ? 'bg-indigo-600' : 'bg-gray-200',
                                                'h-0.5 w-full transition-colors'
                                            ]"
                                        ></div>
                                    </div>

                                    <!-- Step indicator -->
                                    <button
                                        type="button"
                                        @click="goToStep(step.number)"
                                        class="relative flex flex-col items-center group"
                                        :class="{ 'cursor-not-allowed': step.number > currentStep && !canProceed }"
                                    >
                                        <span
                                            :class="[
                                                step.number < currentStep
                                                    ? 'bg-indigo-600 text-white'
                                                    : step.number === currentStep
                                                        ? 'border-2 border-indigo-600 bg-white text-indigo-600'
                                                        : 'border-2 border-gray-300 bg-white text-gray-500',
                                                'flex h-8 w-8 items-center justify-center rounded-full text-sm font-medium transition-colors z-10'
                                            ]"
                                        >
                                            <svg v-if="step.number < currentStep" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                            </svg>
                                            <span v-else>{{ step.number }}</span>
                                        </span>
                                        <span
                                            :class="[
                                                step.number <= currentStep ? 'text-indigo-600' : 'text-gray-500',
                                                'mt-2 text-xs font-medium text-center hidden sm:block'
                                            ]"
                                        >
                                            {{ step.title }}
                                        </span>
                                    </button>
                                </li>
                            </ol>
                        </nav>

                        <!-- Current step title for mobile -->
                        <div class="mt-4 text-center sm:hidden">
                            <span class="text-sm font-medium text-indigo-600">
                                Step {{ currentStep }}: {{ steps[currentStep - 1].title }}
                            </span>
                        </div>
                    </div>

                    <!-- Step Content -->
                    <div class="p-6">
                        <BusinessInfoStep
                            v-if="currentStep === 1"
                            v-model:businessName="form.businessName"
                            v-model:industry="form.industry"
                            v-model:description="form.description"
                            v-model:targetAudience="form.targetAudience"
                            v-model:tagline="form.tagline"
                            v-model:yearsInBusiness="form.yearsInBusiness"
                            v-model:location="form.location"
                            :industries="config.industries"
                            :errors="form.errors"
                            @industry-change="applyIndustryPreset"
                        />

                        <GoalsStep
                            v-if="currentStep === 2"
                            v-model:primaryGoal="form.primaryGoal"
                            v-model:secondaryGoals="form.secondaryGoals"
                            v-model:callToAction="form.callToAction"
                            :errors="form.errors"
                        />

                        <DesignStep
                            v-if="currentStep === 3"
                            v-model:style="form.style"
                            v-model:colorMood="form.colorMood"
                            v-model:primaryColor="form.primaryColor"
                            v-model:logo="form.logo"
                            :errors="form.errors"
                        />

                        <FeaturesStep
                            v-if="currentStep === 4"
                            v-model:features="form.features"
                            :errors="form.errors"
                        />

                        <PagesStep
                            v-if="currentStep === 5"
                            v-model:pages="form.pages"
                            v-model:customPages="form.customPages"
                            :features="form.features"
                            :errors="form.errors"
                        />

                        <ContentStep
                            v-if="currentStep === 6"
                            v-model:services="form.services"
                            v-model:companyStory="form.companyStory"
                            v-model:achievements="form.achievements"
                            v-model:whyChooseUs="form.whyChooseUs"
                            v-model:processSteps="form.processSteps"
                            v-model:faqItems="form.faqItems"
                            v-model:testimonialNames="form.testimonialNames"
                            v-model:contactEmail="form.contactEmail"
                            v-model:contactPhone="form.contactPhone"
                            v-model:contactAddress="form.contactAddress"
                            :errors="form.errors"
                        />

                        <ReviewStep
                            v-if="currentStep === 7"
                            :form-data="form"
                            :industries="config.industries"
                            :is-generating="isGenerating"
                            :generation-status="generationStatus"
                            :generation-error="generationError"
                        />
                    </div>

                    <!-- Save Notification -->
                    <transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 translate-y-1"
                    >
                        <div
                            v-if="showSavedNotification"
                            class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 z-50"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Progress saved!
                        </div>
                    </transition>

                    <!-- Navigation Buttons -->
                    <div class="border-t border-gray-200 px-6 py-4 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                @click="prevStep"
                                :disabled="currentStep === 1"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back
                            </button>

                            <!-- Save Progress Button -->
                            <button
                                type="button"
                                @click="saveProgress"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900"
                                title="Save your progress to continue later"
                            >
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Save
                            </button>

                            <!-- Clear Progress (if saved) -->
                            <button
                                v-if="hasSavedProgress"
                                type="button"
                                @click="clearProgress"
                                class="inline-flex items-center px-2 py-1 text-xs text-gray-500 hover:text-red-600"
                                title="Clear saved progress and start fresh"
                            >
                                Clear saved
                            </button>
                        </div>

                        <button
                            v-if="currentStep < totalSteps"
                            type="button"
                            @click="nextStep"
                            :disabled="!canProceed"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Next
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <button
                            v-else
                            type="button"
                            @click="generateSite"
                            :disabled="form.processing || isGenerating"
                            class="inline-flex items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg v-if="isGenerating" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            {{ isGenerating ? 'Generating Your Site...' : 'Generate My Site' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
