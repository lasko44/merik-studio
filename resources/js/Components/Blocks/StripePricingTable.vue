<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

interface Plan {
    name: string;
    price: string;
    period?: string;
    priceId?: string;
    description?: string;
    features: string[];
    featured?: boolean;
    buttonText?: string;
}

interface Props {
    plans?: Plan[];
}

const props = withDefaults(defineProps<Props>(), {
    plans: () => [],
});

const loadingPlan = ref<string | null>(null);
const error = ref<string | null>(null);

const handleCheckout = async (plan: Plan) => {
    if (!plan.priceId) {
        error.value = `${plan.name} is not available for purchase.`;
        return;
    }

    loadingPlan.value = plan.priceId;
    error.value = null;

    try {
        const response = await axios.post('/checkout/create-session', {
            price_id: plan.priceId,
            quantity: 1,
        });

        if (response.data.url) {
            window.location.href = response.data.url;
        }
    } catch (err: any) {
        error.value = err.response?.data?.error || 'Unable to process checkout.';
        loadingPlan.value = null;
    }
};
</script>

<template>
    <div class="py-12">
        <!-- Error Message -->
        <div v-if="error" class="mb-8 max-w-md mx-auto">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm text-red-600">{{ error }}</p>
            </div>
        </div>

        <!-- Pricing Cards -->
        <div :class="[
            'grid gap-8 mx-auto',
            plans.length === 1 ? 'max-w-md' : '',
            plans.length === 2 ? 'max-w-2xl grid-cols-1 md:grid-cols-2' : '',
            plans.length >= 3 ? 'max-w-5xl grid-cols-1 md:grid-cols-2 lg:grid-cols-3' : ''
        ]">
            <div
                v-for="(plan, index) in plans"
                :key="index"
                :class="[
                    'relative bg-white rounded-2xl shadow-sm border-2 transition-all duration-300',
                    plan.featured
                        ? 'border-indigo-500 ring-2 ring-indigo-500 scale-105'
                        : 'border-gray-200 hover:border-gray-300'
                ]"
            >
                <!-- Featured Badge -->
                <div
                    v-if="plan.featured"
                    class="absolute -top-4 left-1/2 -translate-x-1/2 bg-indigo-500 text-white text-sm font-semibold px-4 py-1 rounded-full"
                >
                    Most Popular
                </div>

                <div class="p-8">
                    <!-- Plan Name -->
                    <h3 class="text-lg font-semibold text-gray-900">{{ plan.name }}</h3>

                    <!-- Price -->
                    <div class="mt-4 flex items-baseline">
                        <span class="text-4xl font-bold text-gray-900">{{ plan.price }}</span>
                        <span v-if="plan.period" class="ml-1 text-gray-500">{{ plan.period }}</span>
                    </div>

                    <!-- Description -->
                    <p v-if="plan.description" class="mt-4 text-sm text-gray-600">
                        {{ plan.description }}
                    </p>

                    <!-- Features -->
                    <ul class="mt-6 space-y-3">
                        <li
                            v-for="(feature, featureIndex) in plan.features"
                            :key="featureIndex"
                            class="flex items-start gap-3"
                        >
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm text-gray-600">{{ feature }}</span>
                        </li>
                    </ul>

                    <!-- CTA Button -->
                    <button
                        @click="handleCheckout(plan)"
                        :disabled="loadingPlan === plan.priceId || !plan.priceId"
                        :class="[
                            'mt-8 w-full py-3 px-4 rounded-lg font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2',
                            plan.featured
                                ? 'bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2'
                                : plan.priceId
                                    ? 'bg-gray-900 text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2'
                                    : 'bg-gray-200 text-gray-500 cursor-not-allowed'
                        ]"
                    >
                        <svg v-if="loadingPlan === plan.priceId" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ loadingPlan === plan.priceId ? 'Processing...' : (plan.buttonText || 'Get Started') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
