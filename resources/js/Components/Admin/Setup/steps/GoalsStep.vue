<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

interface Props {
    primaryGoal: string;
    secondaryGoals: string[];
    callToAction: string;
    errors: Record<string, string>;
}

defineProps<Props>();

const emit = defineEmits<{
    'update:primaryGoal': [value: string];
    'update:secondaryGoals': [value: string[]];
    'update:callToAction': [value: string];
}>();

const goals = [
    { value: 'leads', label: 'Generate Leads', description: 'Collect contact information from potential customers', icon: 'user-plus' },
    { value: 'sales', label: 'Sell Products/Services', description: 'Directly sell products or services online', icon: 'shopping-cart' },
    { value: 'information', label: 'Share Information', description: 'Blog, news, or educational content', icon: 'book-open' },
    { value: 'portfolio', label: 'Showcase Portfolio', description: 'Display your work and projects', icon: 'briefcase' },
    { value: 'community', label: 'Build Community', description: 'Connect with your audience', icon: 'users' },
];

const secondaryGoalOptions = [
    'Build brand awareness',
    'Establish credibility',
    'Provide customer support',
    'Share company news',
    'Recruit talent',
    'Collect email subscribers',
];

const toggleSecondaryGoal = (goal: string, currentGoals: string[]) => {
    if (currentGoals.includes(goal)) {
        emit('update:secondaryGoals', currentGoals.filter(g => g !== goal));
    } else {
        emit('update:secondaryGoals', [...currentGoals, goal]);
    }
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">What are your goals?</h3>
            <p class="mt-1 text-sm text-gray-500">Understanding your goals helps us create the right pages and content.</p>
        </div>

        <div class="space-y-4">
            <div>
                <InputLabel value="Primary Goal *" />
                <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <button
                        v-for="goal in goals"
                        :key="goal.value"
                        type="button"
                        @click="emit('update:primaryGoal', goal.value)"
                        :class="[
                            primaryGoal === goal.value
                                ? 'border-indigo-600 ring-2 ring-indigo-600 bg-indigo-50'
                                : 'border-gray-300 hover:border-gray-400',
                            'relative flex items-start p-4 rounded-lg border cursor-pointer focus:outline-none'
                        ]"
                    >
                        <div class="flex-1 min-w-0">
                            <span class="block text-sm font-medium text-gray-900">
                                {{ goal.label }}
                            </span>
                            <span class="block text-sm text-gray-500">
                                {{ goal.description }}
                            </span>
                        </div>
                        <svg
                            v-if="primaryGoal === goal.value"
                            class="h-5 w-5 text-indigo-600 flex-shrink-0"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <InputError :message="errors.primaryGoal" class="mt-2" />
            </div>

            <div>
                <InputLabel value="Secondary Goals (optional)" />
                <p class="text-xs text-gray-500 mb-2">Select any additional goals that apply</p>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="goal in secondaryGoalOptions"
                        :key="goal"
                        type="button"
                        @click="toggleSecondaryGoal(goal, secondaryGoals)"
                        :class="[
                            secondaryGoals.includes(goal)
                                ? 'bg-indigo-100 text-indigo-800 border-indigo-300'
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                            'inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border transition-colors'
                        ]"
                    >
                        {{ goal }}
                        <svg
                            v-if="secondaryGoals.includes(goal)"
                            class="ml-1.5 h-4 w-4"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <InputLabel for="callToAction" value="Primary Call to Action" />
                <TextInput
                    id="callToAction"
                    :model-value="callToAction"
                    @update:model-value="emit('update:callToAction', $event)"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g., Schedule a consultation, Get a quote, Shop now"
                />
                <p class="mt-1 text-xs text-gray-500">What action do you want visitors to take?</p>
                <InputError :message="errors.callToAction" class="mt-2" />
            </div>
        </div>
    </div>
</template>
