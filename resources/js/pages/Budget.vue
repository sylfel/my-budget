<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { getLocalTimeZone, today } from '@internationalized/date';
import { onMounted, provide } from 'vue';
import Editor from '@/components/editor/Editor.vue';
import MonthsSelector from '@/components/filter/MonthsSelector.vue';
import Grid from '@/components/note/Grid.vue';
import SidebarProvider from '@/components/ui/sidebar/SidebarProvider.vue';
import { useFilters } from '@/composables/userFiters';
import { home } from '@/wayfinder/routes';

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

const { budgetId } = defineProps<{
    budgetId: number;
}>();

provide('BUDGET_ID', budgetId);

const { startDate, endDate } = useFilters();

onMounted(() => {
    // TODO : use URL Params ?
    startDate.value = today(getLocalTimeZone()).set({ day: 1, month: 1 });
    endDate.value = startDate.value.add({ years: 1 });
});
</script>

<template>
    <Head title="Budget" />
    <SidebarProvider>
        <main class="w-full">
            <div
                class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
            >
                <div class="flex items-center justify-start gap-4">
                    <h2 class="text-2xl font-bold">My Budget</h2>
                    <MonthsSelector />
                </div>
                <Grid />
            </div>
        </main>
        <Editor />
    </SidebarProvider>
</template>
