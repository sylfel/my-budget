<template>
    <div>
        <hr />
        {{ props.category.label }}
    </div>
</template>

<script setup lang="ts">
import type { App } from '@/wayfinder/types';
const props = defineProps<{
    category: App.Models.Category;
}>();
</script>

<style scoped>
div {
    border: solid 1px #666;
    padding: 1em;
    margin: 1em;
}
</style>
