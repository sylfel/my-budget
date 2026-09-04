import { collectionOptions, DbClient } from '@tanstack/db';
import { QueryClient } from '@tanstack/query-core';
import { queryCollectionOptions } from '@tanstack/query-db-collection';
import CategoryController from '@/wayfinder/App/Http/Controllers/CategoryController';
import NoteController from '@/wayfinder/App/Http/Controllers/NoteController';
import PosteController from '@/wayfinder/App/Http/Controllers/PosteController';
import type { App } from '@/wayfinder/types';

const queryClient = new QueryClient();

const db = new DbClient({ queryClient });

async function fetchBudgetCategories(
    budgetId: number,
): Promise<Array<App.Models.Category>> {
    const response = await fetch(CategoryController.show.url(budgetId));

    return response.json();
}
async function fetchBudgetPostes(
    budgetId: number,
): Promise<Array<App.Models.Poste>> {
    const response = await fetch(PosteController.show.url(budgetId));

    return response.json();
}
async function fetchBudgetNotes(
    budgetId: number,
): Promise<Array<App.Models.Note>> {
    const response = await fetch(NoteController.show.url(budgetId));

    return response.json();
}

export const useCollections = (budgetId: number) => {
    function createBudgetCategoriesCollection(budgetId: number) {
        return collectionOptions(`budget:${budgetId}:categories`, (client) =>
            queryCollectionOptions({
                id: `budget:${budgetId}:categories`,
                queryKey: ['budget', budgetId, 'categories'],
                queryFn: () => fetchBudgetCategories(budgetId),
                queryClient:
                    client.requireDependency<QueryClient>('queryClient'),
                getKey: (category) => category.id,
            }),
        );
    }

    function createBudgetPostesCollection(budgetId: number) {
        return collectionOptions(`budget:${budgetId}:postes`, (client) =>
            queryCollectionOptions({
                id: `budget:${budgetId}:postes`,
                queryKey: ['budget', budgetId, 'postes'],
                queryFn: () => fetchBudgetPostes(budgetId),
                queryClient:
                    client.requireDependency<QueryClient>('queryClient'),
                getKey: (poste) => poste.id,
            }),
        );
    }
    function createBudgetNotesCollection(budgetId: number) {
        return collectionOptions(`budget:${budgetId}:notes`, (client) =>
            queryCollectionOptions({
                id: `budget:${budgetId}:notes`,
                queryKey: ['budget', budgetId, 'notes'],
                queryFn: () => fetchBudgetNotes(budgetId),
                queryClient:
                    client.requireDependency<QueryClient>('queryClient'),
                getKey: (note) => note.id,
            }),
        );
    }

    const categoriesCollection = db.collection(
        createBudgetCategoriesCollection(budgetId),
    );
    const postesCollection = db.collection(
        createBudgetPostesCollection(budgetId),
    );
    const notesCollection = db.collection(
        createBudgetNotesCollection(budgetId),
    );

    return {
        categoriesCollection,
        postesCollection,
        notesCollection,
    };
};
