import { ref } from 'vue';
import type { App } from '@/wayfinder/types';

const editorSheet = ref(false);
const currentNoteId = ref<number>(0);
const initialData = ref<Partial<App.Models.Note>>({});

export const useEditor = () => {
    const openEditor = (noteId: number) => {
        currentNoteId.value = noteId;
        initialData.value = {};
        editorSheet.value = true;
    };

    const openCreateEditor = (data: Partial<App.Models.Note>) => {
        currentNoteId.value = 0;
        initialData.value = { ...data };
        editorSheet.value = true;
    };

    const closeEditor = () => {
        currentNoteId.value = 0;
        editorSheet.value = false;
    };

    return {
        editorSheet,
        currentNoteId,
        initialData,
        openEditor,
        openCreateEditor,
        closeEditor,
    };
};
