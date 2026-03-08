import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import TextInput from '@/Components/TextInput.vue';

describe('TextInput', () => {
    it('renders an input element', () => {
        const wrapper = mount(TextInput, {
            props: {
                modelValue: '',
            },
        });

        expect(wrapper.find('input').exists()).toBe(true);
    });

    it('binds the modelValue correctly', async () => {
        const wrapper = mount(TextInput, {
            props: {
                modelValue: 'initial value',
                'onUpdate:modelValue': (e: string) => wrapper.setProps({ modelValue: e }),
            },
        });

        expect(wrapper.find('input').element.value).toBe('initial value');
    });

    it('emits update:modelValue when input changes', async () => {
        const wrapper = mount(TextInput, {
            props: {
                modelValue: '',
                'onUpdate:modelValue': (e: string) => wrapper.setProps({ modelValue: e }),
            },
        });

        await wrapper.find('input').setValue('new value');

        expect(wrapper.emitted('update:modelValue')).toBeTruthy();
        expect(wrapper.emitted('update:modelValue')![0]).toEqual(['new value']);
    });

    it('has focus method exposed', () => {
        const wrapper = mount(TextInput, {
            props: {
                modelValue: '',
            },
        });

        expect(wrapper.vm.focus).toBeDefined();
        expect(typeof wrapper.vm.focus).toBe('function');
    });

    it('applies correct CSS classes', () => {
        const wrapper = mount(TextInput, {
            props: {
                modelValue: '',
            },
        });

        const input = wrapper.find('input');
        expect(input.classes()).toContain('rounded-md');
        expect(input.classes()).toContain('border-gray-300');
    });
});
