<script setup lang="ts">
interface Props {
    options?: Record<string, unknown>;
}

withDefaults(defineProps<Props>(), {
    options: () => ({}),
});
</script>

<template>
    <div class="w-full">
        <slot />
    </div>
</template>
