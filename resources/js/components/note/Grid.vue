<template>
    <div class="mb-4 flex items-center justify-between gap-2">
        <div class="text-sm text-muted-foreground">Notes</div>
        <Button variant="default" size="sm" @click="openCreateEditor()">
            New note
        </Button>
    </div>

    <div v-if="isLoadingNotes">Loading...</div>
    <div v-else>
        <div v-if="notes.length == 0">Aucune donnée</div>
        <div class="p-2" :class="total > 0 ? 'bg-green-200' : 'bg-red-100'">
            <div class="flex gap-1 text-left">
                <div class="flex-1">Total</div>
                <div>{{ currencyFormatter.format(total / 100) }}</div>
            </div>
        </div>
        <ul
            class="[&>li]:p-2 [&>li]:hover:cursor-pointer [&>li]:hover:bg-amber-100"
        >
            <li
                v-for="note in notes"
                :key="note.id"
                @click="openEditor(note.id)"
            >
                <div class="flex gap-1 text-left">
                    <div class="flex-1">
                        {{ (note.month + 1).toFixed().padStart(2, '0') }}
                        /{{ note.year }}
                    </div>
                    <div class="flex-2">{{ note.category }}</div>
                    <div class="flex-3">{{ note.poste }}</div>
                    <div class="flex-4">{{ note.label }}</div>
                    <div
                        class="flex-1 text-right"
                        :class="{ 'bg-green-100': note.credit }"
                    >
                        {{ currencyFormatter.format(note.price / 100) }}
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <!-- <ViewCard :budget-id="budgetId" /> -->
</template>

<script setup lang="ts">
import { eq, gte, lte, useLiveQuery } from '@tanstack/vue-db';
import { computed } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import { useCollections } from '@/composables/useCollections';
import { useEditor } from '@/composables/useEditor';
import { useFilters } from '@/composables/userFiters';
import { currencyFormatter } from '@/lib/utils';

const { startDateFilter, endDateFilter } = useFilters();

const { categoriesCollection, postesCollection, notesCollection } =
    useCollections();

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
        .select(({ n, c, p }) => ({
            ...n,
            category: c.label,
            poste: p.label,
            credit: c.credit,
        }));
});

const total = computed(() =>
    notes.value.reduce(
        (acc, note) => acc + note.price * (note.credit ? 1 : -1),
        0,
    ),
);

const { openEditor, openCreateEditor } = useEditor();
</script>
