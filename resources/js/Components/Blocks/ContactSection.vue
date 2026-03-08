<script setup lang="ts">
import { computed, watch } from 'vue';
import { useContactForm } from '@/composables/useContactForm';
import { MailIcon, PhoneIcon, MapPinIcon, ClockIcon, CheckIcon } from '@/Components/Icons';

interface Props {
    title?: string;
    email?: string;
    phone?: string;
    address?: string;
    hours?: string;
    showForm?: boolean;
    formFields?: string[];
    submitText?: string;
    successMessage?: string;
    recipientEmail?: string;
    // Legacy prop support
    fields?: string[];
    submit_text?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Contact Us',
    email: '',
    phone: '',
    address: '',
    hours: '',
    showForm: true,
    formFields: () => ['name', 'email', 'message'],
    submitText: 'Send Message',
    successMessage: 'Thank you for your message! We\'ll get back to you soon.',
    recipientEmail: '',
    fields: () => [],
    submit_text: '',
});

// Support both new and legacy props
const activeFields = computed(() => {
    if (props.formFields && props.formFields.length > 0) {
        return props.formFields;
    }
    return props.fields && props.fields.length > 0 ? props.fields : ['name', 'email', 'message'];
});

const buttonText = computed(() => props.submitText || props.submit_text || 'Send Message');

const hasContactInfo = computed(() => props.email || props.phone || props.address || props.hours);
const hasForm = computed(() => props.showForm && activeFields.value.length > 0);

// Use the contact form composable
const {
    formData,
    status,
    errorMessage,
    isSubmitting,
    isSuccess,
    isError,
    handleSubmit: submitForm,
    resetForm,
    resetStatus,
} = useContactForm({
    endpoint: '/contact/submit',
});

// Handle form submission with recipient email
const handleSubmit = async () => {
    // Temporarily add recipient email to form data
    const originalData = { ...formData.value };
    (formData.value as Record<string, string>).recipientEmail = props.recipientEmail;
    await submitForm();
    // Restore original form data structure
    if (isSuccess.value) {
        resetForm();
    } else {
        formData.value = originalData;
    }
};
</script>

<template>
    <section class="py-12 lg:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div :class="[
                'grid gap-12',
                hasContactInfo && hasForm ? 'grid-cols-1 lg:grid-cols-2' : 'grid-cols-1 max-w-2xl mx-auto'
            ]">
                <!-- Contact Info -->
                <div v-if="hasContactInfo">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ title }}</h2>

                    <div class="space-y-4">
                        <div v-if="email" class="flex items-start gap-4">
                            <div
                                class="contact-icon-bg w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                aria-hidden="true"
                            >
                                <MailIcon size="md" class="contact-icon" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <a :href="'mailto:' + email" class="text-gray-900 hover:opacity-80 transition-opacity">{{ email }}</a>
                            </div>
                        </div>

                        <div v-if="phone" class="flex items-start gap-4">
                            <div
                                class="contact-icon-bg w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                aria-hidden="true"
                            >
                                <PhoneIcon size="md" class="contact-icon" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <a :href="'tel:' + phone" class="text-gray-900 hover:opacity-80 transition-opacity">{{ phone }}</a>
                            </div>
                        </div>

                        <div v-if="address" class="flex items-start gap-4">
                            <div
                                class="contact-icon-bg w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                aria-hidden="true"
                            >
                                <MapPinIcon size="md" class="contact-icon" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Address</p>
                                <p class="text-gray-900 whitespace-pre-line">{{ address }}</p>
                            </div>
                        </div>

                        <div v-if="hours" class="flex items-start gap-4">
                            <div
                                class="contact-icon-bg w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                aria-hidden="true"
                            >
                                <ClockIcon size="md" class="contact-icon" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Hours</p>
                                <p class="text-gray-900">{{ hours }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div v-if="hasForm" class="bg-white rounded-xl p-8 shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">Send Us a Message</h3>

                    <!-- Success Message -->
                    <div v-if="isSuccess" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <CheckIcon size="md" class="text-green-500 flex-shrink-0" />
                            <p class="text-green-800">{{ successMessage }}</p>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="isError" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <p class="text-red-800">{{ errorMessage }}</p>
                        </div>
                    </div>

                    <form
                        v-if="!isSuccess"
                        class="space-y-4"
                        @submit.prevent="handleSubmit"
                    >
                        <div v-if="activeFields.includes('name')">
                            <label for="contact-name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input
                                id="contact-name"
                                v-model="formData.name"
                                type="text"
                                name="name"
                                required
                                autocomplete="name"
                                class="contact-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:outline-none"
                            />
                        </div>
                        <div v-if="activeFields.includes('email')">
                            <label for="contact-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input
                                id="contact-email"
                                v-model="formData.email"
                                type="email"
                                name="email"
                                required
                                autocomplete="email"
                                class="contact-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:outline-none"
                            />
                        </div>
                        <div v-if="activeFields.includes('phone')">
                            <label for="contact-phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input
                                id="contact-phone"
                                v-model="formData.phone"
                                type="tel"
                                name="phone"
                                autocomplete="tel"
                                class="contact-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:outline-none"
                            />
                        </div>
                        <div v-if="activeFields.includes('message')">
                            <label for="contact-message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea
                                id="contact-message"
                                v-model="formData.message"
                                name="message"
                                rows="4"
                                required
                                class="contact-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:outline-none"
                            ></textarea>
                        </div>
                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="contact-submit-btn w-full px-6 py-3 text-white font-semibold rounded-lg transition-colors hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="isSubmitting" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sending...
                            </span>
                            <span v-else>{{ buttonText }}</span>
                        </button>
                    </form>

                    <!-- Show form again after success -->
                    <div v-if="isSuccess" class="text-center">
                        <button
                            type="button"
                            class="contact-link text-sm hover:underline"
                            @click="resetStatus"
                        >
                            Send another message
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.contact-icon-bg {
    background-color: color-mix(in srgb, var(--primary-color) 10%, white);
}

.contact-icon {
    color: var(--primary-color);
}

.contact-input:focus {
    --tw-ring-color: var(--primary-color);
    border-color: var(--primary-color);
}

.contact-submit-btn {
    background-color: var(--primary-color);
}

.contact-link {
    color: var(--primary-color);
}
</style>
