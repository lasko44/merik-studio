<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import CheckIcon from '@/Components/Icons/CheckIcon.vue';

interface Plan {
    id?: string;
    name: string;
    price: string;
    period: string;
    description: string;
    features: string[];
    featured?: boolean;
}

interface Props {
    plans: Plan[];
}

const props = defineProps<Props>();

// Add unique IDs to plans
const plansWithIds = computed(() =>
    props.plans.map((plan, index) => ({
        ...plan,
        uniqueId: plan.id ?? `plan-${plan.name.toLowerCase().replace(/\s+/g, '-')}-${index}`,
    }))
);
</script>

<template>
    <section class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div
                    v-for="plan in plansWithIds"
                    :key="plan.uniqueId"
                    class="relative rounded-2xl p-8 transition-shadow"
                    :class="[
                        plan.featured
                            ? 'pricing-featured text-white shadow-xl scale-105'
                            : 'bg-white border border-gray-200 shadow-sm hover:shadow-md'
                    ]"
                >
                    <!-- Featured Badge -->
                    <div
                        v-if="plan.featured"
                        class="pricing-badge absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 text-white text-sm font-medium rounded-full"
                    >
                        Most Popular
                    </div>

                    <h3
                        class="text-xl font-semibold mb-2"
                        :class="plan.featured ? 'text-white' : 'text-gray-900'"
                    >
                        {{ plan.name }}
                    </h3>

                    <div class="mb-4">
                        <span
                            class="text-4xl font-bold"
                            :class="plan.featured ? 'text-white' : 'text-gray-900'"
                        >
                            {{ plan.price }}
                        </span>
                        <span
                            v-if="plan.period"
                            class="text-sm ml-1"
                            :class="plan.featured ? 'pricing-featured-light' : 'text-gray-500'"
                        >
                            {{ plan.period }}
                        </span>
                    </div>

                    <p
                        class="text-sm mb-6"
                        :class="plan.featured ? 'pricing-featured-light' : 'text-gray-600'"
                    >
                        {{ plan.description }}
                    </p>

                    <ul class="space-y-3 mb-8">
                        <li
                            v-for="feature in plan.features"
                            :key="`${plan.uniqueId}-${feature}`"
                            class="flex items-center gap-2 text-sm"
                            :class="plan.featured ? 'text-white' : 'text-gray-600'"
                        >
                            <CheckIcon
                                size="md"
                                :class="plan.featured ? 'pricing-featured-light' : 'pricing-checkmark'"
                                class="flex-shrink-0"
                            />
                            {{ feature }}
                        </li>
                    </ul>

                    <Link
                        href="/contact"
                        :class="[
                            'block w-full py-3 text-center font-semibold rounded-lg transition-colors hover:opacity-90',
                            plan.featured ? 'bg-white pricing-btn-featured' : 'text-white pricing-btn'
                        ]"
                    >
                        Get Started
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.pricing-featured {
    background-color: var(--primary-color);
}

.pricing-badge {
    background-color: color-mix(in srgb, var(--primary-color) 85%, black);
}

.pricing-featured-light {
    color: color-mix(in srgb, var(--primary-color) 40%, white);
}

.pricing-checkmark {
    color: var(--success-color, #10B981);
}

.pricing-btn {
    background-color: var(--primary-color);
}

.pricing-btn-featured {
    color: var(--primary-color);
}
</style>
