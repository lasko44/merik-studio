<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

interface Props {
    modelValue: string;
    label: string;
    error?: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const updateColor = (event: Event) => {
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', target.value);
};

const updateText = (event: Event) => {
    const target = event.target as HTMLInputElement;
    let value = target.value;
    if (!value.startsWith('#')) {
        value = '#' + value;
    }
    if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
        emit('update:modelValue', value.toUpperCase());
    }
};
</script>

<template>
    <div>
        <InputLabel :value="label" />
        <div class="mt-1 flex items-center gap-2">
            <input
                type="color"
                :value="modelValue"
                @input="updateColor"
                class="h-10 w-10 cursor-pointer rounded border border-gray-300 p-1"
            />
            <input
                type="text"
                :value="modelValue"
                @blur="updateText"
                class="block flex-1 rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm uppercase"
                maxlength="7"
                placeholder="#000000"
            />
        </div>
        <InputError :message="error" class="mt-2" />
    </div>
</template>
