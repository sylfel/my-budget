import { createCollection } from '@tanstack/db';
import { QueryClient } from '@tanstack/query-core';
import { queryCollectionOptions } from '@tanstack/query-db-collection';
import CategoryController from '@/wayfinder/App/Http/Controllers/CategoryController';
import NoteController from '@/wayfinder/App/Http/Controllers/NoteController';
import PosteController from '@/wayfinder/App/Http/Controllers/PosteController';
import type { App } from '@/wayfinder/types';

const queryClient = new QueryClient();

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
    function createBudgetCategoriesCollection(
        queryClient: QueryClient,
        budgetId: number,
    ) {
        return createCollection(
            queryCollectionOptions<App.Models.Category>({
                queryKey: ['budget', budgetId, 'categories'],
                queryFn: () => fetchBudgetCategories(budgetId),
                queryClient,
                getKey: (category) => category.id,
            }),
        );
    }

    function createBudgetPostesCollection(
        queryClient: QueryClient,
        budgetId: number,
    ) {
        return createCollection(
            queryCollectionOptions<App.Models.Poste>({
                queryKey: ['budget', budgetId, 'postes'],
                queryFn: () => fetchBudgetPostes(budgetId),
                queryClient,
                getKey: (poste) => poste.id,
            }),
        );
    }
    function createBudgetNotesCollection(
        queryClient: QueryClient,
        budgetId: number,
    ) {
        return createCollection(
            queryCollectionOptions<App.Models.Note>({
                queryKey: ['budget', budgetId, 'notes'],
                queryFn: () => fetchBudgetNotes(budgetId),
                queryClient,
                getKey: (note) => note.id,
            }),
        );
    }

    const categoriesCollection = createBudgetCategoriesCollection(
        queryClient,
        budgetId,
    );
    const postesCollection = createBudgetPostesCollection(
        queryClient,
        budgetId,
    );
    const notesCollection = createBudgetNotesCollection(queryClient, budgetId);

    return {
        categoriesCollection,
        postesCollection,
        notesCollection,
    };
};
