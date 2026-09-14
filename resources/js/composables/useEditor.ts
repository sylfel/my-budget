import { ref } from 'vue';

const editorSheet = ref(false);
const currentNoteId = ref<number>(0);

export const useEditor = () => {
    const openEditor = (noteId: number) => {
        currentNoteId.value = noteId;
        editorSheet.value = true;
    };

    const openCreateEditor = () => {
        currentNoteId.value = 0;
        editorSheet.value = true;
    };

    const closeEditor = () => {
        currentNoteId.value = 0;
        editorSheet.value = false;
    };

    return {
        editorSheet,
        currentNoteId,
        openEditor,
        openCreateEditor,
        closeEditor,
    };
};
