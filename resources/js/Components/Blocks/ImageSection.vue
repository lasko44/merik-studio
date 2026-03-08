<script setup lang="ts">
interface Props {
    src: string | null;
    alt?: string;
    caption?: string;
    width?: string;
    rounded?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    src: null,
    alt: '',
    caption: '',
    width: '100%',
    rounded: false,
});
</script>

<template>
    <div v-if="props.src" class="py-8">
        <figure class="max-w-4xl mx-auto px-4">
            <img
                :src="props.src"
                :alt="props.alt"
                :style="{ width: props.width }"
                :class="[
                    'mx-auto',
                    props.rounded ? 'rounded-lg' : ''
                ]"
            />
            <figcaption v-if="props.caption" class="mt-3 text-center text-sm text-gray-600">
                {{ props.caption }}
            </figcaption>
        </figure>
    </div>
</template>
