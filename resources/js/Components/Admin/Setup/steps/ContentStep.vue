<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';

interface Props {
    services: string[];
    companyStory: string;
    achievements: string;
    whyChooseUs: string;
    processSteps: string;
    faqItems: string;
    testimonialNames: string;
    contactEmail: string;
    contactPhone: string;
    contactAddress: string;
    errors: Record<string, string>;
}

defineProps<Props>();

const emit = defineEmits<{
    'update:services': [value: string[]];
    'update:companyStory': [value: string];
    'update:achievements': [value: string];
    'update:whyChooseUs': [value: string];
    'update:processSteps': [value: string];
    'update:faqItems': [value: string];
    'update:testimonialNames': [value: string];
    'update:contactEmail': [value: string];
    'update:contactPhone': [value: string];
    'update:contactAddress': [value: string];
}>();

const updateService = (index: number, value: string, currentServices: string[]) => {
    const newServices = [...currentServices];
    newServices[index] = value;
    emit('update:services', newServices);
};

const addService = (currentServices: string[]) => {
    if (currentServices.length < 6) {
        emit('update:services', [...currentServices, '']);
    }
};

const removeService = (index: number, currentServices: string[]) => {
    const newServices = currentServices.filter((_, i) => i !== index);
    emit('update:services', newServices);
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Tell us more about your business</h3>
            <p class="mt-1 text-sm text-gray-500">
                This information helps us create rich, detailed content for your pages. The more you share, the better your site will be!
            </p>
        </div>

        <div class="space-y-6">
            <!-- Services/Products Offered -->
            <div>
                <InputLabel value="Your Main Services or Products *" />
                <p class="text-xs text-gray-500 mb-2">List your primary services or products with brief descriptions</p>
                <div class="space-y-3">
                    <div v-for="(service, index) in services" :key="index" class="flex items-start gap-2">
                        <span class="text-sm text-gray-500 w-6 pt-2">{{ index + 1 }}.</span>
                        <div class="flex-1">
                            <TextInput
                                :model-value="service"
                                @update:model-value="updateService(index, $event, services)"
                                type="text"
                                class="w-full"
                                :placeholder="[
                                    'e.g., Website Design - Custom, mobile-responsive websites that convert visitors into customers',
                                    'e.g., SEO Services - Improve your search rankings and drive organic traffic',
                                    'e.g., Digital Marketing - Social media management, PPC, and email campaigns',
                                ][index] || 'Add another service or product...'"
                            />
                        </div>
                        <button
                            v-if="services.length > 1"
                            type="button"
                            @click="removeService(index, services)"
                            class="p-2 text-gray-400 hover:text-red-500"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <button
                        v-if="services.length < 6"
                        type="button"
                        @click="addService(services)"
                        class="inline-flex items-center px-3 py-1.5 text-sm text-indigo-600 hover:text-indigo-700"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add another service
                    </button>
                </div>
                <InputError :message="errors['services']" class="mt-2" />
            </div>

            <!-- Why Choose Us -->
            <div>
                <InputLabel for="whyChooseUs" value="Why should customers choose you?" />
                <p class="text-xs text-gray-500 mb-2">What makes you different from competitors? List your key advantages.</p>
                <textarea
                    id="whyChooseUs"
                    :value="whyChooseUs"
                    @input="emit('update:whyChooseUs', ($event.target as HTMLTextAreaElement).value)"
                    rows="3"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="e.g., 15+ years experience, 500+ satisfied clients, 24/7 support, money-back guarantee, free consultation, certified experts, local team, fastest turnaround in the industry..."
                ></textarea>
                <InputError :message="errors.whyChooseUs" class="mt-2" />
            </div>

            <!-- Company Story -->
            <div>
                <InputLabel for="companyStory" value="Your Company Story (for About page)" />
                <p class="text-xs text-gray-500 mb-2">How did your business start? What drives you? What's your mission?</p>
                <textarea
                    id="companyStory"
                    :value="companyStory"
                    @input="emit('update:companyStory', ($event.target as HTMLTextAreaElement).value)"
                    rows="4"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="e.g., Founded in 2010 by John Smith, our company started with a simple mission: to help small businesses compete in the digital world. After 10 years in corporate IT, John saw how many businesses struggled with technology. Today, we've helped over 500 businesses transform their operations..."
                ></textarea>
                <p class="mt-1 text-xs text-gray-500">{{ companyStory.length }}/1500 characters</p>
                <InputError :message="errors.companyStory" class="mt-2" />
            </div>

            <!-- Process/How it Works -->
            <div>
                <InputLabel for="processSteps" value="How does working with you work?" />
                <p class="text-xs text-gray-500 mb-2">Describe your process in steps (great for building trust)</p>
                <textarea
                    id="processSteps"
                    :value="processSteps"
                    @input="emit('update:processSteps', ($event.target as HTMLTextAreaElement).value)"
                    rows="3"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="e.g., Step 1: Free Consultation - We discuss your needs and goals. Step 2: Custom Proposal - We create a tailored plan. Step 3: Implementation - Our team gets to work. Step 4: Launch & Support - We launch and provide ongoing support."
                ></textarea>
                <InputError :message="errors.processSteps" class="mt-2" />
            </div>

            <!-- Achievements -->
            <div>
                <InputLabel for="achievements" value="Achievements, Awards & Credentials" />
                <p class="text-xs text-gray-500 mb-2">Any certifications, awards, partnerships, or notable achievements?</p>
                <textarea
                    id="achievements"
                    :value="achievements"
                    @input="emit('update:achievements', ($event.target as HTMLTextAreaElement).value)"
                    rows="2"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="e.g., Google Certified Partner, BBB A+ Rating, Best of City Award 2023, Microsoft Gold Partner, 4.9 star rating on Google..."
                ></textarea>
                <InputError :message="errors.achievements" class="mt-2" />
            </div>

            <!-- FAQ Items -->
            <div>
                <InputLabel for="faqItems" value="Common Questions Your Customers Ask" />
                <p class="text-xs text-gray-500 mb-2">List questions and answers (we'll create an FAQ section)</p>
                <textarea
                    id="faqItems"
                    :value="faqItems"
                    @input="emit('update:faqItems', ($event.target as HTMLTextAreaElement).value)"
                    rows="4"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="Q: How much do your services cost?
A: Our pricing varies based on project scope. We offer free consultations to provide accurate quotes.

Q: How long does a typical project take?
A: Most projects are completed within 2-4 weeks, depending on complexity.

Q: Do you offer ongoing support?
A: Yes! All our services include 30 days of free support, with affordable maintenance plans available."
                ></textarea>
                <InputError :message="errors.faqItems" class="mt-2" />
            </div>

            <!-- Testimonial Names -->
            <div>
                <InputLabel for="testimonialNames" value="Happy Customers (for testimonials)" />
                <p class="text-xs text-gray-500 mb-2">List some customer names/companies - we'll create sample testimonials you can edit</p>
                <TextInput
                    id="testimonialNames"
                    :model-value="testimonialNames"
                    @update:model-value="emit('update:testimonialNames', $event)"
                    type="text"
                    class="w-full"
                    placeholder="e.g., John Smith (ABC Corp), Sarah Johnson (XYZ Inc), Mike Brown (Local Business)"
                />
                <InputError :message="errors.testimonialNames" class="mt-2" />
            </div>

            <!-- Contact Information Section -->
            <div class="border-t border-gray-200 pt-6 mt-6">
                <h4 class="text-md font-medium text-gray-900 mb-4">Contact Information</h4>
                <p class="text-sm text-gray-500 mb-4">This will appear on your Contact page and throughout the site.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Contact Email -->
                    <div>
                        <InputLabel for="contactEmail" value="Business Email *" />
                        <TextInput
                            id="contactEmail"
                            :model-value="contactEmail"
                            @update:model-value="emit('update:contactEmail', $event)"
                            type="email"
                            class="w-full"
                            placeholder="hello@yourbusiness.com"
                        />
                        <InputError :message="errors.contactEmail" class="mt-2" />
                    </div>

                    <!-- Contact Phone -->
                    <div>
                        <InputLabel for="contactPhone" value="Business Phone" />
                        <TextInput
                            id="contactPhone"
                            :model-value="contactPhone"
                            @update:model-value="emit('update:contactPhone', $event)"
                            type="tel"
                            class="w-full"
                            placeholder="(555) 123-4567"
                        />
                        <InputError :message="errors.contactPhone" class="mt-2" />
                    </div>
                </div>

                <!-- Contact Address -->
                <div class="mt-4">
                    <InputLabel for="contactAddress" value="Business Address" />
                    <TextInput
                        id="contactAddress"
                        :model-value="contactAddress"
                        @update:model-value="emit('update:contactAddress', $event)"
                        type="text"
                        class="w-full"
                        placeholder="123 Main St, Suite 100, City, State 12345"
                    />
                    <InputError :message="errors.contactAddress" class="mt-2" />
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-green-800">The more you share, the better!</h4>
                        <p class="mt-1 text-sm text-green-700">
                            Every detail you provide helps us create professional, conversion-focused content.
                            Don't worry about perfect writing - our AI will polish everything into compelling copy.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
