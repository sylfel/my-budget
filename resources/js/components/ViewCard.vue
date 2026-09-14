<template>
    <h2>Salut !!</h2>
    <span v-if="isLoading"></span>
    <template v-else>
        <template v-for="category in categories" :key="category.id">
            <CardCategory :category="category" />
        </template>
    </template>
</template>

<script setup lang="ts">
import { useLiveQuery } from '@tanstack/vue-db';
import { useCollections } from '@/composables/useCollections';
import CardCategory from './CardCategory.vue';

const { budgetId = 1 } = defineProps<{
    budgetId?: number;
}>();

const { categoriesCollection } = useCollections();

const { data: categories, isLoading } = useLiveQuery((q) =>
    q.from({ categories: categoriesCollection }),
);
</script>
