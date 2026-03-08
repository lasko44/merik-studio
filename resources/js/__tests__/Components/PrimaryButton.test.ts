import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import PrimaryButton from '@/Components/PrimaryButton.vue';

describe('PrimaryButton', () => {
    it('renders a button element', () => {
        const wrapper = mount(PrimaryButton);

        expect(wrapper.find('button').exists()).toBe(true);
    });

    it('renders slot content', () => {
        const wrapper = mount(PrimaryButton, {
            slots: {
                default: 'Click Me',
            },
        });

        expect(wrapper.text()).toBe('Click Me');
    });

    it('applies correct base CSS classes', () => {
        const wrapper = mount(PrimaryButton);

        const button = wrapper.find('button');
        expect(button.classes()).toContain('inline-flex');
        expect(button.classes()).toContain('items-center');
        expect(button.classes()).toContain('rounded-md');
        expect(button.classes()).toContain('bg-gray-800');
        expect(button.classes()).toContain('text-white');
    });

    it('emits click event when clicked', async () => {
        const wrapper = mount(PrimaryButton);

        await wrapper.find('button').trigger('click');

        expect(wrapper.emitted('click')).toBeTruthy();
    });

    it('can receive additional attributes', () => {
        const wrapper = mount(PrimaryButton, {
            attrs: {
                type: 'submit',
                disabled: true,
            },
        });

        const button = wrapper.find('button');
        expect(button.attributes('type')).toBe('submit');
        expect(button.attributes('disabled')).toBeDefined();
    });
});
