import { BasicIndex, collectionOptions, DbClient } from '@tanstack/db';
import { QueryClient } from '@tanstack/query-core';
import { queryCollectionOptions } from '@tanstack/query-db-collection';
import { inject } from 'vue';
import {
    createNote,
    fetchBudgetCategories,
    fetchBudgetNotes,
    fetchBudgetPostes,
    patchNote,
    removeNote,
} from '@/api/api';

const queryClient = new QueryClient();

const db = new DbClient({ queryClient });

function createBudgetCategoriesCollection(budgetId: number) {
    return collectionOptions(`budget:${budgetId}:categories`, (client) =>
        queryCollectionOptions({
            id: `budget:${budgetId}:categories`,
            queryKey: ['budget', budgetId, 'categories'],
            queryFn: () => fetchBudgetCategories(budgetId),
            queryClient: client.requireDependency<QueryClient>('queryClient'),
            getKey: (category) => category.id,
            defaultIndexType: BasicIndex,
        }),
    );
}

function createBudgetPostesCollection(budgetId: number) {
    return collectionOptions(`budget:${budgetId}:postes`, (client) =>
        queryCollectionOptions({
            id: `budget:${budgetId}:postes`,
            queryKey: ['budget', budgetId, 'postes'],
            queryFn: () => fetchBudgetPostes(budgetId),
            queryClient: client.requireDependency<QueryClient>('queryClient'),
            getKey: (poste) => poste.id,
            defaultIndexType: BasicIndex,
        }),
    );
}
function createBudgetNotesCollection(budgetId: number) {
    return collectionOptions(`budget:${budgetId}:notes`, (client) =>
        queryCollectionOptions({
            id: `budget:${budgetId}:notes`,
            queryKey: ['budget', budgetId, 'notes'],
            queryFn: () => fetchBudgetNotes(budgetId),
            queryClient: client.requireDependency<QueryClient>('queryClient'),
            getKey: (note) => note.id,
            defaultIndexType: BasicIndex,

            onInsert: async ({ transaction }) => {
                await Promise.all(
                    transaction.mutations.map((mutation) => {
                        return createNote(budgetId, mutation.modified);
                    }),
                );
            },

            onDelete: async ({ transaction }) => {
                await Promise.all(
                    transaction.mutations.map((mutation) => {
                        return removeNote(budgetId, mutation.original.id);
                    }),
                );
            },

            onUpdate: async ({ transaction }) => {
                await Promise.all(
                    transaction.mutations.map((mutation) => {
                        return patchNote(
                            budgetId,
                            mutation.original.id,
                            mutation.changes,
                        );
                    }),
                );
            },
        }),
    );
}

let categoriesCollection;
let postesCollection;
let notesCollection;

export const useCollections = () => {
    const budgetId = inject('BUDGET_ID');

    if (!budgetId || typeof budgetId != 'number' || Number.isNaN(budgetId)) {
        throw new Error('missing budget_id inject');
    }

    categoriesCollection = db.collection(
        createBudgetCategoriesCollection(budgetId),
    );
    categoriesCollection.createIndex((row) => row.id);
    postesCollection = db.collection(createBudgetPostesCollection(budgetId));
    postesCollection.createIndex((row) => row.id);
    notesCollection = db.collection(createBudgetNotesCollection(budgetId));
    notesCollection.createIndex((row) => row.category_id);

    return {
        categoriesCollection,
        postesCollection,
        notesCollection,
    };
};
