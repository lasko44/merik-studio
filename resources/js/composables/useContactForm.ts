import { ref, computed } from 'vue';
import axios from 'axios';

export interface ContactFormData {
    name: string;
    email: string;
    phone: string;
    message: string;
    [key: string]: string;
}

export type FormStatus = 'idle' | 'submitting' | 'success' | 'error';

export interface ContactFormOptions {
    endpoint?: string;
    initialData?: Partial<ContactFormData>;
    onSuccess?: () => void;
    onError?: (error: string) => void;
}

interface ApiErrorResponse {
    message?: string;
    errors?: Record<string, string[]>;
}

export function useContactForm(options: ContactFormOptions = {}) {
    const {
        endpoint = '/contact',
        initialData = {},
        onSuccess,
        onError,
    } = options;

    const formData = ref<ContactFormData>({
        name: '',
        email: '',
        phone: '',
        message: '',
        ...initialData,
    });

    const status = ref<FormStatus>('idle');
    const errorMessage = ref('');
    const fieldErrors = ref<Record<string, string>>({});

    const isSubmitting = computed(() => status.value === 'submitting');
    const isSuccess = computed(() => status.value === 'success');
    const isError = computed(() => status.value === 'error');
    const canSubmit = computed(() => !isSubmitting.value);

    const resetForm = () => {
        formData.value = {
            name: '',
            email: '',
            phone: '',
            message: '',
            ...initialData,
        };
        status.value = 'idle';
        errorMessage.value = '';
        fieldErrors.value = {};
    };

    const resetStatus = () => {
        status.value = 'idle';
        errorMessage.value = '';
    };

    const handleSubmit = async () => {
        if (!canSubmit.value) return;

        status.value = 'submitting';
        errorMessage.value = '';
        fieldErrors.value = {};

        try {
            await axios.post(endpoint, formData.value);
            status.value = 'success';
            onSuccess?.();
        } catch (error: unknown) {
            status.value = 'error';

            if (axios.isAxiosError(error)) {
                const data = error.response?.data as ApiErrorResponse | undefined;

                if (data?.message) {
                    errorMessage.value = data.message;
                } else if (data?.errors) {
                    // Extract first error from each field
                    fieldErrors.value = Object.fromEntries(
                        Object.entries(data.errors).map(([key, messages]) => [key, messages[0]])
                    );
                    errorMessage.value = 'Please correct the errors below.';
                } else {
                    errorMessage.value = 'An error occurred. Please try again.';
                }
            } else if (error instanceof Error) {
                errorMessage.value = error.message;
            } else {
                errorMessage.value = 'An unexpected error occurred.';
            }

            onError?.(errorMessage.value);
        }
    };

    const updateField = (field: keyof ContactFormData, value: string) => {
        formData.value[field] = value;
        // Clear field error when user starts typing
        if (fieldErrors.value[field]) {
            delete fieldErrors.value[field];
        }
    };

    return {
        // State
        formData,
        status,
        errorMessage,
        fieldErrors,

        // Computed
        isSubmitting,
        isSuccess,
        isError,
        canSubmit,

        // Methods
        handleSubmit,
        resetForm,
        resetStatus,
        updateField,
    };
}
