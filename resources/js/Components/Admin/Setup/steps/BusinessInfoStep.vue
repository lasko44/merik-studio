<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

interface Props {
    businessName: string;
    industry: string;
    description: string;
    targetAudience: string;
    tagline: string;
    yearsInBusiness: string;
    location: string;
    industries: Record<string, string>;
    errors: Record<string, string>;
}

defineProps<Props>();

const emit = defineEmits<{
    'update:businessName': [value: string];
    'update:industry': [value: string];
    'update:description': [value: string];
    'update:targetAudience': [value: string];
    'update:tagline': [value: string];
    'update:yearsInBusiness': [value: string];
    'update:location': [value: string];
    'industry-change': [value: string];
}>();

const handleIndustryChange = (event: Event) => {
    const value = (event.target as HTMLSelectElement).value;
    emit('update:industry', value);
    emit('industry-change', value);
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Tell us about your business</h3>
            <p class="mt-1 text-sm text-gray-500">The more details you provide, the better content we can generate for your site.</p>
        </div>

        <div class="space-y-4">
            <!-- Business Name -->
            <div>
                <InputLabel for="businessName" value="Business / Site Name *" />
                <TextInput
                    id="businessName"
                    :model-value="businessName"
                    @update:model-value="emit('update:businessName', $event)"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g., Acme Consulting"
                    required
                />
                <InputError :message="errors.businessName" class="mt-2" />
            </div>

            <!-- Industry -->
            <div>
                <InputLabel for="industry" value="Industry / Niche *" />
                <select
                    id="industry"
                    :value="industry"
                    @change="handleIndustryChange"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                    <option v-for="(label, value) in industries" :key="value" :value="value">
                        {{ label }}
                    </option>
                </select>
                <InputError :message="errors.industry" class="mt-2" />
            </div>

            <!-- Two column layout for smaller fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <InputLabel for="yearsInBusiness" value="Years in Business" />
                    <select
                        id="yearsInBusiness"
                        :value="yearsInBusiness"
                        @change="emit('update:yearsInBusiness', ($event.target as HTMLSelectElement).value)"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                        <option value="">Select...</option>
                        <option value="new">Just starting</option>
                        <option value="1-2">1-2 years</option>
                        <option value="3-5">3-5 years</option>
                        <option value="5-10">5-10 years</option>
                        <option value="10-20">10-20 years</option>
                        <option value="20+">20+ years</option>
                    </select>
                </div>

                <div>
                    <InputLabel for="location" value="Location / Service Area" />
                    <TextInput
                        id="location"
                        :model-value="location"
                        @update:model-value="emit('update:location', $event)"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="e.g., New York, NY or Nationwide"
                    />
                </div>
            </div>

            <!-- Tagline -->
            <div>
                <InputLabel for="tagline" value="Tagline / Slogan (if you have one)" />
                <TextInput
                    id="tagline"
                    :model-value="tagline"
                    @update:model-value="emit('update:tagline', $event)"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g., Your success is our business"
                />
                <p class="mt-1 text-xs text-gray-500">Leave blank and we'll create one for you</p>
            </div>

            <!-- Description -->
            <div>
                <InputLabel for="description" value="What does your business do? *" />
                <textarea
                    id="description"
                    :value="description"
                    @input="emit('update:description', ($event.target as HTMLTextAreaElement).value)"
                    rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="Describe your business, products, or services. Be specific - this helps us create better content. For example: 'We provide IT consulting services for small businesses, specializing in cloud migration, cybersecurity, and managed IT support.'"
                ></textarea>
                <p class="mt-1 text-xs text-gray-500">{{ description.length }}/1000 characters - Be detailed!</p>
                <InputError :message="errors.description" class="mt-2" />
            </div>

            <!-- Target Audience -->
            <div>
                <InputLabel for="targetAudience" value="Who are your ideal customers?" />
                <textarea
                    id="targetAudience"
                    :value="targetAudience"
                    @input="emit('update:targetAudience', ($event.target as HTMLTextAreaElement).value)"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="Describe your ideal customers. Include details like: industry, company size, job titles, pain points, or demographics. For example: 'Small to medium business owners (10-100 employees) who struggle with outdated technology and need reliable IT support.'"
                ></textarea>
                <InputError :message="errors.targetAudience" class="mt-2" />
            </div>
        </div>
    </div>
</template>
