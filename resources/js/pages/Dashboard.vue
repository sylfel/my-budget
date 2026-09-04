<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { getLocalTimeZone, today } from '@internationalized/date';
import { eq, gte, lte, useLiveQuery } from '@tanstack/vue-db';
import { onMounted } from 'vue';
import MonthsSelector from '@/components/filter/MonthsSelector.vue';
import Button from '@/components/ui/button/Button.vue';
import Popover from '@/components/ui/popover/Popover.vue';
import PopoverContent from '@/components/ui/popover/PopoverContent.vue';
import PopoverTrigger from '@/components/ui/popover/PopoverTrigger.vue';
import SidebarProvider from '@/components/ui/sidebar/SidebarProvider.vue';
import { useCollections } from '@/composables/useCollections';
import { useFilters } from '@/composables/userFiters';
import { home } from '@/wayfinder/routes';
// import PlaceholderPattern from '@/components/PlaceholderPattern.vue';

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

const currencyFormatter = new Intl.NumberFormat(undefined, {
    style: 'currency',
    currency: 'EUR',
});

const { budgetId = 1 } = defineProps<{
    budgetId?: number;
}>();

const { startDate, endDate, startDateFilter, endDateFilter } = useFilters();

onMounted(() => {
    // TODO : use URL Params ?
    startDate.value = today(getLocalTimeZone()).set({ day: 1 });
    endDate.value = startDate.value.copy();
});

const { categoriesCollection, postesCollection, notesCollection } =
    useCollections(budgetId);

/*
const { data: categories, isLoading: isLoadingCategories } = useLiveQuery(
    (q) => {
        return q.from({ cat: categoriesCollection });
    },
);
const { data: postes, isLoading: isLoadingPostes } = useLiveQuery((q) => {
    return q.from({ cat: postesCollection });
});
*/
const { data: notes, isLoading: isLoadingNotes } = useLiveQuery((q) => {
    return q
        .from({ n: notesCollection })
        .join(
            { c: categoriesCollection },
            ({ n, c }) => eq(n.category_id, c.id),
            'inner',
        )
        .join({ p: postesCollection }, ({ n, p }) => eq(n.poste_id, p.id))
        .where(({ n }) => gte(n.year_month, startDateFilter.value))
        .where(({ n }) => lte(n.year_month, endDateFilter.value))
        .orderBy(({ c }) => c.label)
        .orderBy(({ p }) => p.label)
        .orderBy(({ n }) => n.label)
        .select(({ n, c, p }) => ({ ...n, category: c.label, poste: p.label }));
});
</script>

<template>
    <Head title="Dashboard" />
    <SidebarProvider>
        <main class="w-full">
            <!-- <SidebarTrigger /> -->

            <div
                class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
            >
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-2xl font-bold">My Budget</h2>
                    <Popover>
                        <PopoverTrigger asChild>
                            <Button type="button"> Filtrer </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-auto p-0">
                            <MonthsSelector />
                        </PopoverContent>
                    </Popover>
                </div>

                <div v-if="isLoadingNotes">Loading...</div>
                <div v-else>
                    <div v-if="notes.length == 0">Aucune donnée</div>
                    <ul class="max-w-6xl">
                        <li v-for="note in notes" :key="note.id">
                            <div class="flex gap-1 text-left">
                                <div class="flex-1">
                                    {{
                                        (note.month + 1)
                                            .toFixed()
                                            .padStart(2, '0')
                                    }}
                                    /{{ note.year }}
                                </div>
                                <div class="flex-2">{{ note.category }}</div>
                                <div class="flex-3">{{ note.poste }}</div>
                                <div class="flex-4">{{ note.label }}</div>
                                <div class="flex-1 text-right">
                                    {{ currencyFormatter.format(note.price) }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- <ViewCard :budget-id="budgetId" /> -->
            </div>
        </main>
    </SidebarProvider>
</template>
