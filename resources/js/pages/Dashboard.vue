<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import SidebarProvider from '@/components/ui/sidebar/SidebarProvider.vue';
import { budget, home } from '@/wayfinder/routes';
import type { App } from '@/wayfinder/types';
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Budget',
                href: home(),
            },
        ],
    },
});

defineProps<{
    budgets: App.Models.Budget[];
}>();
</script>

<template>
    <Head title="Dashboard" />
    <SidebarProvider>
        <main class="w-full">
            <!-- <SidebarTrigger /> -->

            <div
                class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
            >
                <div class="flex items-center justify-start gap-4">
                    <h2 class="text-2xl font-bold">My Budget</h2>

                    <template v-for="data in budgets" :key="data.id">
                        <TextLink :href="budget(data.id)">Budget 1</TextLink>
                    </template>
                </div>
            </div>
        </main>
    </SidebarProvider>
</template>
