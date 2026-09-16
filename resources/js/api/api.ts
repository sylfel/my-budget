import type { Errors, FormDataConvertible, Page } from '@inertiajs/core';
import { router } from '@inertiajs/vue3';
import CategoryController from '@/wayfinder/App/Http/Controllers/CategoryController';
import NoteController from '@/wayfinder/App/Http/Controllers/NoteController';
import PosteController from '@/wayfinder/App/Http/Controllers/PosteController';
import type { App } from '@/wayfinder/types';

type InertiaPayload = Record<string, FormDataConvertible>;

function inertiaRequest(
    method: 'post' | 'patch',
    url: string,
    data: InertiaPayload,
): Promise<Page> {
    return new Promise<Page>((resolve, reject) => {
        const options = {
            preserveState: true,
            preserveScroll: true,
            onSuccess: resolve,
            onError: (errors: Errors) => reject(errors),
        };

        if (method === 'post') {
            router.post(url, data, options);

            return;
        }

        router.patch(url, data, options);
    });
}

function inertiaDeleteRequest(url: string): Promise<Page> {
    return new Promise<Page>((resolve, reject) => {
        router.delete(url, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: resolve,
            onError: (errors: Errors) => reject(errors),
        });
    });
}

export async function fetchBudgetCategories(
    budgetId: number,
): Promise<Array<App.Models.Category>> {
    const response = await fetch(CategoryController.show.url(budgetId));

    return response.json();
}
export async function fetchBudgetPostes(
    budgetId: number,
): Promise<Array<App.Models.Poste>> {
    const response = await fetch(PosteController.show.url(budgetId));

    return response.json();
}
export async function fetchBudgetNotes(
    budgetId: number,
): Promise<Array<App.Models.Note>> {
    const response = await fetch(NoteController.show.url(budgetId));

    return response.json();
}

export async function createNote(
    budgetId: number,
    note: Partial<App.Models.Note>,
) {
    const payload: InertiaPayload = {
        label: note.label,
        price: note.price,
        year: note.year,
        month: note.month,
        category_id: note.category_id,
        poste_id: note.poste_id,
        user_id: note.user_id,
    };

    return inertiaRequest(
        'post',
        NoteController.store({ budget: budgetId }).url,
        payload,
    );
}

export async function removeNote(budgetId: number, noteId: number) {
    return inertiaDeleteRequest(
        NoteController.remove.delete([budgetId, noteId]).url,
    );
}

export async function patchNote(
    budgetId: number,
    noteId: number,
    changes: Partial<App.Models.Note>,
) {
    const payload: InertiaPayload = {
        label: changes.label,
        price: changes.price,
        year: changes.year,
        month: changes.month,
        category_id: changes.category_id,
        poste_id: changes.poste_id,
    };

    return inertiaRequest(
        'patch',
        NoteController.update.patch([budgetId, noteId]).url,
        payload,
    );
}
