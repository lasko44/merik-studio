<script setup lang="ts">
import { ref, computed } from 'vue';
import FormSection from '@/Components/Admin/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import axios from 'axios';

interface StripeSettings {
    publishable_key: string | null;
    secret_key_set: boolean;
    webhook_secret_set: boolean;
    test_mode: boolean;
}

interface Props {
    settings: StripeSettings;
    stripePublishableKey: string;
    stripeSecretKey: string;
    stripeWebhookSecret: string;
    stripeTestMode: boolean;
    errors: Record<string, string>;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:stripePublishableKey': [value: string];
    'update:stripeSecretKey': [value: string];
    'update:stripeWebhookSecret': [value: string];
    'update:stripeTestMode': [value: boolean];
}>();

const showSecretKey = ref(false);
const showWebhookSecret = ref(false);
const testingConnection = ref(false);
const connectionResult = ref<{ success: boolean; message: string; test_mode?: boolean } | null>(null);

const secretKeyPlaceholder = computed(() => {
    if (props.settings.secret_key_set && !props.stripeSecretKey) {
        return '••••••••••••••••••••••••••••••••';
    }
    return 'sk_test_...';
});

const webhookSecretPlaceholder = computed(() => {
    if (props.settings.webhook_secret_set && !props.stripeWebhookSecret) {
        return '••••••••••••••••••••••••••••••••';
    }
    return 'whsec_...';
});

const copyWebhookUrl = () => {
    window.navigator.clipboard.writeText(route('stripe.webhook'));
};

const testConnection = async () => {
    testingConnection.value = true;
    connectionResult.value = null;

    try {
        const response = await axios.post(route('admin.settings.test-stripe'));
        connectionResult.value = response.data;
    } catch (error: any) {
        connectionResult.value = {
            success: false,
            message: error.response?.data?.message || 'Connection test failed.',
        };
    } finally {
        testingConnection.value = false;
    }
};
</script>

<template>
    <div class="space-y-6 max-w-2xl">
        <FormSection title="Stripe API Keys" description="Configure your Stripe API credentials for payment processing.">
            <div class="space-y-4">
                <!-- Test Mode Toggle -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Test Mode</p>
                        <p class="text-xs text-gray-500">Use test keys for development and testing</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            :checked="stripeTestMode"
                            @change="emit('update:stripeTestMode', ($event.target as HTMLInputElement).checked)"
                            class="sr-only peer"
                        />
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-800"></div>
                    </label>
                </div>

                <!-- Mode Indicator -->
                <div v-if="stripeTestMode" class="flex items-center gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-sm text-amber-800">Test mode is enabled. No real charges will be made.</span>
                </div>

                <!-- Publishable Key -->
                <div>
                    <InputLabel for="stripe_publishable_key" value="Publishable Key" />
                    <TextInput
                        id="stripe_publishable_key"
                        :model-value="stripePublishableKey"
                        @update:model-value="emit('update:stripePublishableKey', $event)"
                        type="text"
                        class="mt-1 block w-full font-mono text-sm"
                        :placeholder="stripeTestMode ? 'pk_test_...' : 'pk_live_...'"
                    />
                    <p class="mt-1 text-xs text-gray-500">This key is safe to expose in frontend code.</p>
                    <InputError :message="errors.stripe_publishable_key" class="mt-2" />
                </div>

                <!-- Secret Key -->
                <div>
                    <InputLabel for="stripe_secret_key" value="Secret Key" />
                    <div class="relative mt-1">
                        <TextInput
                            id="stripe_secret_key"
                            :model-value="stripeSecretKey"
                            @update:model-value="emit('update:stripeSecretKey', $event)"
                            :type="showSecretKey ? 'text' : 'password'"
                            class="block w-full pr-10 font-mono text-sm"
                            :placeholder="secretKeyPlaceholder"
                        />
                        <button
                            type="button"
                            @click="showSecretKey = !showSecretKey"
                            class="absolute inset-y-0 right-0 flex items-center pr-3"
                        >
                            <svg v-if="showSecretKey" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                            <svg v-else class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Keep this key secret. Never expose it in frontend code.</p>
                    <InputError :message="errors.stripe_secret_key" class="mt-2" />
                </div>

                <!-- Webhook Secret -->
                <div>
                    <InputLabel for="stripe_webhook_secret" value="Webhook Secret" />
                    <div class="relative mt-1">
                        <TextInput
                            id="stripe_webhook_secret"
                            :model-value="stripeWebhookSecret"
                            @update:model-value="emit('update:stripeWebhookSecret', $event)"
                            :type="showWebhookSecret ? 'text' : 'password'"
                            class="block w-full pr-10 font-mono text-sm"
                            :placeholder="webhookSecretPlaceholder"
                        />
                        <button
                            type="button"
                            @click="showWebhookSecret = !showWebhookSecret"
                            class="absolute inset-y-0 right-0 flex items-center pr-3"
                        >
                            <svg v-if="showWebhookSecret" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                            <svg v-else class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Used to verify incoming webhook events from Stripe.</p>
                    <InputError :message="errors.stripe_webhook_secret" class="mt-2" />
                </div>
            </div>
        </FormSection>

        <FormSection title="Connection Test" description="Verify your Stripe credentials are working correctly.">
            <div class="space-y-4">
                <button
                    type="button"
                    @click="testConnection"
                    :disabled="testingConnection"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-700 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150"
                >
                    <svg v-if="testingConnection" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    {{ testingConnection ? 'Testing...' : 'Test Connection' }}
                </button>

                <!-- Connection Result -->
                <div v-if="connectionResult" :class="[
                    'flex items-start gap-3 p-4 rounded-lg',
                    connectionResult.success ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'
                ]">
                    <svg v-if="connectionResult.success" class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p :class="[
                            'text-sm font-medium',
                            connectionResult.success ? 'text-green-800' : 'text-red-800'
                        ]">
                            {{ connectionResult.message }}
                        </p>
                        <p v-if="connectionResult.success && connectionResult.test_mode !== undefined" class="text-xs mt-1" :class="connectionResult.test_mode ? 'text-amber-700' : 'text-green-700'">
                            {{ connectionResult.test_mode ? 'Running in test mode' : 'Running in live mode' }}
                        </p>
                    </div>
                </div>
            </div>
        </FormSection>

        <FormSection title="Webhook Endpoint" description="Configure this endpoint in your Stripe Dashboard.">
            <div class="space-y-3">
                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg border">
                    <code class="flex-1 text-sm text-gray-800 font-mono break-all">{{ route('stripe.webhook') }}</code>
                    <button
                        type="button"
                        @click="copyWebhookUrl"
                        class="p-2 text-gray-500 hover:text-gray-700"
                        title="Copy to clipboard"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500">
                    Add this URL in your Stripe Dashboard under Developers &rarr; Webhooks.
                    Make sure to select the events you want to receive.
                </p>
            </div>
        </FormSection>
    </div>
</template>
