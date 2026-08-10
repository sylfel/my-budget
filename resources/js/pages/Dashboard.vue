<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { getLocalTimeZone, today } from '@internationalized/date';
import { eq, gte, lte, useLiveQuery } from '@tanstack/vue-db';
import { onMounted } from 'vue';
import AppSidebar from '@/components/AppSidebar.vue';
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

const { data: categories, isLoading: isLoadingCategories } = useLiveQuery(
    (q) => {
        return q.from({ cat: categoriesCollection });
    },
);
const { data: postes, isLoading: isLoadingPostes } = useLiveQuery((q) => {
    return q.from({ cat: postesCollection });
});
const { data: notes, isLoading: isLoadingNotes } = useLiveQuery((q) => {
    return q
        .from({ n: notesCollection })
        .join({ c: categoriesCollection }, ({ n, c }) =>
            eq(n.category_id, c.id),
        )
        .where(({ n }) => gte(n.year_month, startDateFilter.value))
        .where(({ n }) => lte(n.year_month, endDateFilter.value))
        .select(({ n, c }) => ({ ...n, category: c.label }));
});
</script>

<template>
    <Head title="Dashboard" />
    <SidebarProvider>
        <AppSidebar />
        <main>
            <!-- <SidebarTrigger /> -->
            <div
                class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
            >
                <h1>Tanstack DB - JPT</h1>
                <div
                    v-if="
                        isLoadingCategories || isLoadingPostes || isLoadingNotes
                    "
                >
                    Loading...
                </div>
                <ul v-else>
                    <details>
                        <summary>Catégories {{ categories.length }}</summary>
                        <li v-for="category in categories" :key="category.id">
                            {{ category.id }} / {{ category.label }}
                        </li>
                    </details>
                    <details>
                        <summary>Postes {{ postes.length }}</summary>
                        <li v-for="poste in postes" :key="poste.id">
                            {{ poste.id }} / {{ poste.label }}
                        </li>
                    </details>
                    <details open>
                        <summary>Notes {{ notes.length }}</summary>
                        <li v-for="note in notes" :key="note.id">
                            {{ note.month.toFixed().padStart(2, '0') }}/{{
                                note.year
                            }}
                            -
                            {{ currencyFormatter.format(note.price) }}
                        </li>
                    </details>
                </ul>
                <!-- <ViewCard :budget-id="budgetId" /> -->
            </div>
        </main>
    </SidebarProvider>
</template>
