<script setup lang="ts">
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import TextAlign from '@tiptap/extension-text-align';
import Underline from '@tiptap/extension-underline';
import { watch, ref } from 'vue';

interface Props {
    modelValue: string;
    placeholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Start writing...',
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const linkUrl = ref('');
const showLinkModal = ref(false);

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit.configure({
            heading: {
                levels: [1, 2, 3],
            },
        }),
        Link.configure({
            openOnClick: false,
            HTMLAttributes: {
                class: 'text-gray-700 underline hover:text-gray-600',
            },
        }),
        Placeholder.configure({
            placeholder: props.placeholder,
        }),
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
        Underline,
    ],
    editorProps: {
        attributes: {
            class: 'prose prose-sm max-w-none focus:outline-none min-h-[120px] px-3 py-2',
        },
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

watch(() => props.modelValue, (newValue) => {
    if (editor.value && newValue !== editor.value.getHTML()) {
        editor.value.commands.setContent(newValue, { emitUpdate: false });
    }
});

const setLink = () => {
    if (linkUrl.value) {
        editor.value?.chain().focus().extendMarkRange('link').setLink({ href: linkUrl.value }).run();
    } else {
        editor.value?.chain().focus().extendMarkRange('link').unsetLink().run();
    }
    showLinkModal.value = false;
    linkUrl.value = '';
};

const openLinkModal = () => {
    linkUrl.value = editor.value?.getAttributes('link').href || '';
    showLinkModal.value = true;
};

const removeLink = () => {
    editor.value?.chain().focus().unsetLink().run();
    showLinkModal.value = false;
    linkUrl.value = '';
};
</script>

<template>
    <div class="rich-text-editor border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-gray-700 focus-within:border-gray-700">
        <!-- Toolbar -->
        <div v-if="editor" class="flex flex-wrap items-center gap-0.5 px-2 py-1.5 bg-gray-50 border-b border-gray-200">
            <!-- Text Style Group -->
            <div class="flex items-center gap-0.5 pr-2 border-r border-gray-300" role="group" aria-label="Text formatting">
                <button
                    type="button"
                    @click="editor.chain().focus().toggleBold().run()"
                    :class="[
                        'p-1.5 rounded hover:bg-gray-200 transition-colors',
                        editor.isActive('bold') ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Bold (Ctrl+B)"
                    aria-label="Bold"
                    :aria-pressed="editor.isActive('bold')"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor.chain().focus().toggleItalic().run()"
                    :class="[
                        'p-1.5 rounded hover:bg-gray-200 transition-colors',
                        editor.isActive('italic') ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Italic (Ctrl+I)"
                    aria-label="Italic"
                    :aria-pressed="editor.isActive('italic')"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4h4m-2 0v16m-4 0h8" transform="skewX(-10)" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor.chain().focus().toggleUnderline().run()"
                    :class="[
                        'p-1.5 rounded hover:bg-gray-200 transition-colors',
                        editor.isActive('underline') ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Underline (Ctrl+U)"
                    aria-label="Underline"
                    :aria-pressed="editor.isActive('underline')"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v7a5 5 0 0010 0V4M5 20h14" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor.chain().focus().toggleStrike().run()"
                    :class="[
                        'p-1.5 rounded hover:bg-gray-200 transition-colors',
                        editor.isActive('strike') ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Strikethrough"
                    aria-label="Strikethrough"
                    :aria-pressed="editor.isActive('strike')"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.5 10.5H6.5M12 6a4 4 0 00-4 4m0 4a4 4 0 004 4 4 4 0 004-4" />
                    </svg>
                </button>
            </div>

            <!-- Headings Group -->
            <div class="flex items-center gap-0.5 px-2 border-r border-gray-300" role="group" aria-label="Heading levels">
                <button
                    type="button"
                    @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
                    :class="[
                        'px-1.5 py-1 rounded hover:bg-gray-200 transition-colors text-xs font-bold',
                        editor.isActive('heading', { level: 1 }) ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Heading 1"
                    aria-label="Heading 1"
                    :aria-pressed="editor.isActive('heading', { level: 1 })"
                >
                    H1
                </button>
                <button
                    type="button"
                    @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                    :class="[
                        'px-1.5 py-1 rounded hover:bg-gray-200 transition-colors text-xs font-bold',
                        editor.isActive('heading', { level: 2 }) ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Heading 2"
                    aria-label="Heading 2"
                    :aria-pressed="editor.isActive('heading', { level: 2 })"
                >
                    H2
                </button>
                <button
                    type="button"
                    @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                    :class="[
                        'px-1.5 py-1 rounded hover:bg-gray-200 transition-colors text-xs font-bold',
                        editor.isActive('heading', { level: 3 }) ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Heading 3"
                    aria-label="Heading 3"
                    :aria-pressed="editor.isActive('heading', { level: 3 })"
                >
                    H3
                </button>
                <button
                    type="button"
                    @click="editor.chain().focus().setParagraph().run()"
                    :class="[
                        'px-1.5 py-1 rounded hover:bg-gray-200 transition-colors text-xs font-medium',
                        editor.isActive('paragraph') && !editor.isActive('heading') ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Normal text"
                    aria-label="Paragraph"
                    :aria-pressed="editor.isActive('paragraph') && !editor.isActive('heading')"
                >
                    P
                </button>
            </div>

            <!-- Lists Group -->
            <div class="flex items-center gap-0.5 px-2 border-r border-gray-300" role="group" aria-label="List formatting">
                <button
                    type="button"
                    @click="editor.chain().focus().toggleBulletList().run()"
                    :class="[
                        'p-1.5 rounded hover:bg-gray-200 transition-colors',
                        editor.isActive('bulletList') ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Bullet list"
                    aria-label="Bullet list"
                    :aria-pressed="editor.isActive('bulletList')"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <circle cx="2" cy="6" r="1" fill="currentColor" />
                        <circle cx="2" cy="12" r="1" fill="currentColor" />
                        <circle cx="2" cy="18" r="1" fill="currentColor" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor.chain().focus().toggleOrderedList().run()"
                    :class="[
                        'p-1.5 rounded hover:bg-gray-200 transition-colors',
                        editor.isActive('orderedList') ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Numbered list"
                    aria-label="Numbered list"
                    :aria-pressed="editor.isActive('orderedList')"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6h13M7 12h13M7 18h13" />
                        <text x="2" y="7" font-size="6" fill="currentColor">1</text>
                        <text x="2" y="13" font-size="6" fill="currentColor">2</text>
                        <text x="2" y="19" font-size="6" fill="currentColor">3</text>
                    </svg>
                </button>
            </div>

            <!-- Alignment Group -->
            <div class="flex items-center gap-0.5 px-2 border-r border-gray-300" role="group" aria-label="Text alignment">
                <button
                    type="button"
                    @click="editor.chain().focus().setTextAlign('left').run()"
                    :class="[
                        'p-1.5 rounded hover:bg-gray-200 transition-colors',
                        editor.isActive({ textAlign: 'left' }) ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Align left"
                    aria-label="Align left"
                    :aria-pressed="editor.isActive({ textAlign: 'left' })"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M3 12h12M3 18h18" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor.chain().focus().setTextAlign('center').run()"
                    :class="[
                        'p-1.5 rounded hover:bg-gray-200 transition-colors',
                        editor.isActive({ textAlign: 'center' }) ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Align center"
                    aria-label="Align center"
                    :aria-pressed="editor.isActive({ textAlign: 'center' })"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M6 12h12M3 18h18" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor.chain().focus().setTextAlign('right').run()"
                    :class="[
                        'p-1.5 rounded hover:bg-gray-200 transition-colors',
                        editor.isActive({ textAlign: 'right' }) ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Align right"
                    aria-label="Align right"
                    :aria-pressed="editor.isActive({ textAlign: 'right' })"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M9 12h12M3 18h18" />
                    </svg>
                </button>
            </div>

            <!-- Link & Quote -->
            <div class="flex items-center gap-0.5 px-2 border-r border-gray-300">
                <button
                    type="button"
                    @click="openLinkModal"
                    :class="[
                        'p-1.5 rounded hover:bg-gray-200 transition-colors',
                        editor.isActive('link') ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Add link"
                    aria-label="Add link"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor.chain().focus().toggleBlockquote().run()"
                    :class="[
                        'p-1.5 rounded hover:bg-gray-200 transition-colors',
                        editor.isActive('blockquote') ? 'bg-gray-200 text-gray-700' : 'text-gray-600'
                    ]"
                    title="Quote"
                    aria-label="Block quote"
                    :aria-pressed="editor.isActive('blockquote')"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10.5h3a1 1 0 001-1v-3a1 1 0 00-1-1H8a1 1 0 00-1 1v6a3 3 0 003 3m6-6h3a1 1 0 001-1v-3a1 1 0 00-1-1h-3a1 1 0 00-1 1v6a3 3 0 003 3" />
                    </svg>
                </button>
            </div>

            <!-- Undo/Redo -->
            <div class="flex items-center gap-0.5 px-2" role="group" aria-label="Undo and redo">
                <button
                    type="button"
                    @click="editor.chain().focus().undo().run()"
                    :disabled="!editor.can().undo()"
                    class="p-1.5 rounded hover:bg-gray-200 transition-colors text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed"
                    title="Undo (Ctrl+Z)"
                    aria-label="Undo"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l4-4m-4 4l4 4" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor.chain().focus().redo().run()"
                    :disabled="!editor.can().redo()"
                    class="p-1.5 rounded hover:bg-gray-200 transition-colors text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed"
                    title="Redo (Ctrl+Y)"
                    aria-label="Redo"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a5 5 0 00-5 5v2m15-7l-4-4m4 4l-4 4" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Editor Content -->
        <EditorContent :editor="editor" class="bg-white" />

        <!-- Link Modal -->
        <div v-if="showLinkModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" role="dialog" aria-modal="true" aria-labelledby="link-modal-title">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <h3 id="link-modal-title" class="text-lg font-medium text-gray-900 mb-4">Insert Link</h3>
                <div class="mb-4">
                    <label for="link-url" class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                    <input
                        id="link-url"
                        v-model="linkUrl"
                        type="url"
                        placeholder="https://example.com"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        @keydown.enter.prevent="setLink"
                    />
                </div>
                <div class="flex justify-end gap-2">
                    <button
                        v-if="editor?.isActive('link')"
                        type="button"
                        @click="removeLink"
                        class="px-3 py-2 text-sm font-medium text-red-600 hover:text-red-700"
                    >
                        Remove Link
                    </button>
                    <button
                        type="button"
                        @click="showLinkModal = false"
                        class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-800"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="setLink"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-md hover:bg-gray-900"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.rich-text-editor .ProseMirror {
    min-height: 120px;
    outline: none;
}

.rich-text-editor .ProseMirror p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    float: left;
    color: #9ca3af;
    pointer-events: none;
    height: 0;
}

.rich-text-editor .ProseMirror:focus {
    outline: none;
}

/* Prose styling overrides */
.rich-text-editor .prose h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}

.rich-text-editor .prose h2 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-top: 0.75rem;
    margin-bottom: 0.5rem;
}

.rich-text-editor .prose h3 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-top: 0.5rem;
    margin-bottom: 0.25rem;
}

.rich-text-editor .prose p {
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

.rich-text-editor .prose ul,
.rich-text-editor .prose ol {
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
    padding-left: 1.5rem;
}

.rich-text-editor .prose blockquote {
    border-left: 3px solid #e5e7eb;
    padding-left: 1rem;
    font-style: italic;
    color: #6b7280;
}
</style>
