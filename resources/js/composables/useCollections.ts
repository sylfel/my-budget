import { BasicIndex, collectionOptions, DbClient } from '@tanstack/db';
import { QueryClient } from '@tanstack/query-core';
import { queryCollectionOptions } from '@tanstack/query-db-collection';
import { inject } from 'vue';
import type { RouteDefinition } from '@/wayfinder';
import CategoryController from '@/wayfinder/App/Http/Controllers/CategoryController';
import NoteController from '@/wayfinder/App/Http/Controllers/NoteController';
import PosteController from '@/wayfinder/App/Http/Controllers/PosteController';
import type { App } from '@/wayfinder/types';

const queryClient = new QueryClient();

const db = new DbClient({ queryClient });

type Method = 'get' | 'post' | 'put' | 'delete' | 'patch' | 'head' | 'options';
async function doFetch(
    routeDef: RouteDefinition<Method, string | Record<string, string>>,
    data: unknown = undefined,
) {
    const headers: HeadersInit = [['X-Requested-With', 'XMLHttpRequest']];
    let body = null;

    if (data) {
        headers.push(['Content-Type', 'application/json']);
        body = JSON.stringify(data);
    }

    const response = await fetch(routeDef.url, {
        method: routeDef.method.toUpperCase(),
        headers,
        body,
    });

    if (!response.ok) {
        let cause = null;

        if (response.status === 422) {
            try {
                cause = await response.json();
            } catch {}
        }

        throw new Error(`HTTP Error : ${response.status}`, {
            cause,
        });
    }

    return response;
}

async function fetchBudgetCategories(
    budgetId: number,
): Promise<Array<App.Models.Category>> {
    const response = await doFetch(CategoryController.show.get(budgetId));

    return response.json();
}
async function fetchBudgetPostes(
    budgetId: number,
): Promise<Array<App.Models.Poste>> {
    const response = await doFetch(PosteController.show.get(budgetId));

    return response.json();
}
async function fetchBudgetNotes(
    budgetId: number,
): Promise<Array<App.Models.Note>> {
    const response = await doFetch(NoteController.show.get(budgetId));

    return response.json();
}

async function removeNote(budgetId: number, noteId: number) {
    return doFetch(NoteController.remove.delete([budgetId, noteId]));
}

async function patchNote(
    budgetId: number,
    noteId: number,
    changes: Partial<App.Models.Note>,
) {
    return doFetch(NoteController.update.patch([budgetId, noteId]), changes);
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
