<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { budget } from '@/wayfinder/routes';
import type { App } from '@/wayfinder/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Toto',
                href: '/',
            },
        ],
    },
});

defineProps<{
    budgets: App.Models.Budget[];
}>();
</script>

<template>
    <Head title="Welcome"> </Head>
    <div>
        <div>
            <main>
                <h1>Bienvenue sur My Budget</h1>
                <template v-for="data in budgets" :key="data.id">
                    <Link :href="budget(data.id)">Budget {{ data.id }}</Link>
                </template>
            </main>
        </div>
    </div>
</template>
