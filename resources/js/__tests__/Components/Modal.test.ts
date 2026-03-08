import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { mount } from '@vue/test-utils';
import Modal from '@/Components/Modal.vue';

describe('Modal', () => {
    beforeEach(() => {
        // Mock dialog methods not available in happy-dom
        HTMLDialogElement.prototype.showModal = vi.fn();
        HTMLDialogElement.prototype.close = vi.fn();
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it('renders a dialog element', () => {
        const wrapper = mount(Modal);

        expect(wrapper.find('dialog').exists()).toBe(true);
    });

    it('is hidden by default', () => {
        const wrapper = mount(Modal);

        expect(wrapper.props('show')).toBe(false);
    });

    it('emits close event when backdrop is clicked and closeable', async () => {
        const wrapper = mount(Modal, {
            props: {
                show: true,
                closeable: true,
            },
        });

        const backdrop = wrapper.find('.fixed.inset-0.transform');
        await backdrop.trigger('click');

        expect(wrapper.emitted('close')).toBeTruthy();
    });

    it('does not emit close when closeable is false', async () => {
        const wrapper = mount(Modal, {
            props: {
                show: true,
                closeable: false,
            },
        });

        const backdrop = wrapper.find('.fixed.inset-0.transform');
        await backdrop.trigger('click');

        expect(wrapper.emitted('close')).toBeFalsy();
    });

    it('applies correct max-width class based on prop', () => {
        const sizes = ['sm', 'md', 'lg', 'xl', '2xl'] as const;

        sizes.forEach((size) => {
            const wrapper = mount(Modal, {
                props: {
                    show: true,
                    maxWidth: size,
                },
            });

            const modalContent = wrapper.find('.mb-6.transform');
            expect(modalContent.classes()).toContain(`sm:max-w-${size}`);
        });
    });

    it('defaults to 2xl max-width', () => {
        const wrapper = mount(Modal, {
            props: {
                show: true,
            },
        });

        const modalContent = wrapper.find('.mb-6.transform');
        expect(modalContent.classes()).toContain('sm:max-w-2xl');
    });

    it('renders slot content when shown', () => {
        const wrapper = mount(Modal, {
            props: {
                show: true,
            },
            slots: {
                default: '<div class="test-content">Modal Content</div>',
            },
        });

        expect(wrapper.find('.test-content').exists()).toBe(true);
        expect(wrapper.text()).toContain('Modal Content');
    });

    it('has correct aria attributes', () => {
        const wrapper = mount(Modal, {
            props: {
                show: true,
            },
        });

        const dialog = wrapper.find('dialog');
        expect(dialog.attributes('aria-modal')).toBe('true');
    });
});
