<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { ref, watch } from 'vue';

interface Props {
    modelValue: string;
    source: string;
    label?: string;
    error?: string;
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Slug',
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const isEditing = ref(false);
const localValue = ref(props.modelValue);

const slugify = (text: string): string => {
    return text
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
};

watch(() => props.source, (newSource) => {
    if (!isEditing.value) {
        const newSlug = slugify(newSource);
        localValue.value = newSlug;
        emit('update:modelValue', newSlug);
    }
});

watch(() => props.modelValue, (newValue) => {
    localValue.value = newValue;
});

const handleInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    localValue.value = target.value;
    emit('update:modelValue', target.value);
};

const handleFocus = () => {
    isEditing.value = true;
};

const handleBlur = () => {
    const slugified = slugify(localValue.value);
    localValue.value = slugified;
    emit('update:modelValue', slugified);
};

const regenerate = () => {
    const newSlug = slugify(props.source);
    localValue.value = newSlug;
    emit('update:modelValue', newSlug);
    isEditing.value = false;
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <InputLabel :value="label" />
            <button
                type="button"
                @click="regenerate"
                class="text-xs text-gray-700 hover:text-gray-600"
            >
                Regenerate
            </button>
        </div>
        <input
            :value="localValue"
            @input="handleInput"
            @focus="handleFocus"
            @blur="handleBlur"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700"
            placeholder="page-slug"
        />
        <InputError :message="error" class="mt-2" />
    </div>
</template>
