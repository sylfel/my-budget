<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { getLocalTimeZone, today } from '@internationalized/date';
import { onMounted, provide } from 'vue';
import Editor from '@/components/editor/Editor.vue';
import MonthsSelector from '@/components/filter/MonthsSelector.vue';
import TableView from '@/components/note/TableView.vue';
import SidebarProvider from '@/components/ui/sidebar/SidebarProvider.vue';
import { useFilters } from '@/composables/useFilters';
import { home } from '@/wayfinder/routes';
import { useEditor } from '@/composables/useEditor';
import Button from '@/components/ui/button/Button.vue';

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

const { openCreateEditor } = useEditor();

const onNewNote = () => {
    openCreateEditor({
        year: startDate.value?.year,
        month: (startDate.value?.month || 1) - 1,
    });
};
</script>

<template>

    <Head title="Budget" />
    <SidebarProvider>
        <main class="w-full">
            <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div class="flex items-center gap-4 justify-between">
                    <MonthsSelector />

                    <Button @click="onNewNote()">
                        New note
                    </Button>

                </div>
                <TableView />
            </div>
        </main>
        <Editor />
    </SidebarProvider>
</template>
