<template>
    <div class="mb-4 flex items-center justify-end gap-2">
        <Button variant="default" size="sm" @click="onNewNote()">
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
        <ul class="[&>li]:p-2 [&>li]:hover:cursor-pointer [&>li]:hover:bg-amber-100">
            <li v-for="note in notes" :key="note.id" @click="openEditor(note.id)">
                <div class="flex gap-1 text-left">
                    <div class="flex-1">
                        {{ (note.month + 1).toFixed().padStart(2, '0') }}
                        /{{ note.year }}
                    </div>
                    <div class="flex-2">{{ note.category }}</div>
                    <div class="flex-3">{{ note.poste }}</div>
                    <div class="flex-4">{{ note.label }}</div>
                    <div class="flex-1 text-right" :class="{ 'bg-green-100': note.credit }">
                        {{ currencyFormatter.format(note.price / 100) }}
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <!-- <ViewCard :budget-id="budgetId" /> -->
</template>

<script setup lang="ts">
import { sum, useLiveQuery } from '@tanstack/vue-db';
import { computed } from 'vue';
import Button from '@/components/ui/button/Button.vue';

import { useEditor } from '@/composables/useEditor';
import { useNotes } from '@/composables/useNotes';
import { currencyFormatter } from '@/lib/utils';
import { useFilters } from '@/composables/useFilters';

const { startDate } = useFilters();

const { notesFiltered } = useNotes();

const { data: notes, isLoading: isLoadingNotes } = useLiveQuery(
    (q) =>
        q
            .from({ n: notesFiltered.value })
            .orderBy(({ n }) => n.category)
            .orderBy(({ n }) => n.poste)
            .orderBy(({ n }) => n.label),
    [notesFiltered],
);

const { data: result } = useLiveQuery(
    (q) =>
        q
            .from({ n: notesFiltered.value })
            .select(({ n }) => ({ total: sum(n.price) }))
            .findOne(),
    [notesFiltered],
);

const total = computed(() => result.value?.total || 0);

const { openEditor, openCreateEditor } = useEditor();

const onNewNote = () => {
    openCreateEditor({
        year: startDate.value?.year,
        month: (startDate.value?.month || 1) - 1
    })
}
</script>
