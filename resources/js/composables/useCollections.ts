import { BasicIndex, collectionOptions, DbClient } from '@tanstack/db';
import { QueryClient } from '@tanstack/query-core';
import { queryCollectionOptions } from '@tanstack/query-db-collection';
import { inject } from 'vue';
import {
    createNote,
    fetchBudgetCategories,
    fetchBudgetNotes,
    fetchBudgetPostes,
    fetchBudgetUsers,
    patchNote,
    removeNote,
} from '@/api/api';

const queryClient = new QueryClient();

const db = new DbClient({ queryClient });

function createBudgetUsersCollections(budgetId: number) {
    return collectionOptions(`budget:${budgetId}:users`, (client) =>
        queryCollectionOptions({
            id: `budget:${budgetId}:users`,
            queryKey: ['budget', budgetId, 'users'],
            queryFn: () => fetchBudgetUsers(budgetId),
            queryClient: client.requireDependency<QueryClient>('queryClient'),
            getKey: (user) => user.id,
            defaultIndexType: BasicIndex,
        }),
    );
}

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

            onInsert: async ({ transaction, collection }) => {
                await Promise.all(
                    transaction.mutations.map((mutation) => {
                        return createNote(budgetId, mutation.modified);
                    }),
                );
                await collection.utils.refetch()
            },

            onDelete: async ({ transaction, collection }) => {
                await Promise.all(
                    transaction.mutations.map((mutation) => {
                        return removeNote(budgetId, mutation.original.id);
                    }),
                );
                await collection.utils.refetch()

            },

            onUpdate: async ({ transaction, collection }) => {
                await Promise.all(
                    transaction.mutations.map((mutation) => {
                        return patchNote(
                            budgetId,
                            mutation.original.id,
                            mutation.changes,
                        );
                    }),
                );
                await collection.utils.refetch()

            },
        }),
    );
}

let categoriesCollection;
let postesCollection;
let notesCollection;
let usersCollection;

export const useCollections = () => {
    const budgetId = inject('BUDGET_ID');

    if (!budgetId || typeof budgetId != 'number' || Number.isNaN(budgetId)) {
        throw new Error('missing budget_id inject');
    }

    usersCollection = db.collection(createBudgetUsersCollections(budgetId));
    usersCollection.createIndex((row) => row.id);
    categoriesCollection = db.collection(
        createBudgetCategoriesCollection(budgetId),
    );
    categoriesCollection.createIndex((row) => row.id);
    postesCollection = db.collection(createBudgetPostesCollection(budgetId));
    postesCollection.createIndex((row) => row.id);
    notesCollection = db.collection(createBudgetNotesCollection(budgetId));
    notesCollection.createIndex((row) => row.category_id);

    return {
        usersCollection,
        categoriesCollection,
        postesCollection,
        notesCollection,
    };
};
