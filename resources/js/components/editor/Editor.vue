<template>
    <Sheet v-model:open="editorSheet" @update:open="onClose">
        <SheetContent class="sm:max-w-xl">
            <SheetHeader>
                <SheetTitle>{{ isCreate ? 'New note' : 'Edit note' }}</SheetTitle>
                <SheetDescription>
                    {{
                        isCreate
                            ? 'Create a new note for this budget.'
                            : "Make changes to your note here. Click save when you're done."
                    }}
                </SheetDescription>
            </SheetHeader>

            <div v-if="currentNote" class="grid flex-1 auto-rows-min gap-6 overflow-auto px-4">

                <div class="grid gap-3">
                    <Label>Period</Label>
                    <Popover>
                        <PopoverTrigger as-child>
                            <Button type="button" variant="outline" class="w-full justify-between">
                                {{ noteDateLabel }}
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-80" align="center" side="top">
                            <div class="my-2 text-center">Mois</div>
                            <MonthSelector v-model="noteDate" />
                        </PopoverContent>
                    </Popover>
                    <div class="space-y-1">
                        <InputError :message="fieldError('year')" />
                        <InputError :message="fieldError('month')" />
                    </div>
                </div>

                <div class="grid gap-3">
                    <Label for="note-category">Category</Label>
                    <Select :model-value="currentNote.category_id ?? undefined" @update:model-value="onCategoryChange">
                        <SelectTrigger id="note-category" class="w-full">
                            <SelectValue placeholder="Select a category" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="fieldError('category_id')" />
                </div>

                <div class="grid gap-3">
                    <Label for="note-poste">Poste</Label>
                    <Select :model-value="currentNote.poste_id ?? undefined" @update:model-value="onPosteChange"
                        :disabled="!postesReady || availablePostes.length === 0">
                        <SelectTrigger id="note-poste" class="w-full">
                            <SelectValue placeholder="Select a poste" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="poste in availablePostes" :key="poste.id" :value="poste.id">
                                {{ poste.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="fieldError('poste_id')" />
                </div>

                <div class="grid gap-3">
                    <Label for="note-label">Label</Label>
                    <Input id="note-label" :model-value="currentNote.label ?? ''" @update:model-value="updateNoteLabel"
                        placeholder="Rent, groceries..." />
                    <InputError :message="fieldError('label')" />
                </div>

                <div class="grid gap-3">
                    <Label for="note-price">Price</Label>
                    <Input id="note-price" type="number" :model-value="(currentNote.price ?? 0) / 100" step="0.01"
                        @update:model-value="onPriceChange" />
                    <InputError :message="fieldError('price')" />
                </div>
            </div>

            <SheetFooter class="mt-4 flex-col items-stretch gap-2 sm:items-stretch">
                <Button type="button" :disabled="isSaving" @click="submit">
                    {{ isSaving ? 'Saving...' : isCreate ? 'Create note' : 'Save changes' }}
                </Button>

                <SheetClose as-child>
                    <Button type="button" variant="outline">Close</Button>
                </SheetClose>

                <div v-if="!isCreate">
                    <Dialog v-model:open="confirmDeleteOpen">
                        <DialogTrigger as-child>
                            <Button
                                variant="ghost"
                                size="sm"
                                class="w-full text-destructive hover:bg-destructive/10 hover:text-destructive"
                            >
                                Delete
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogTitle>Remove note</DialogTitle>
                            <DialogDescription>
                                Are you sure you want to remove this note?
                            </DialogDescription>
                            <DialogFooter class="gap-2">
                                <DialogClose as-child>
                                    <Button variant="secondary">Cancel</Button>
                                </DialogClose>
                                <Button variant="destructive" :disabled="isDeleting" @click="handleDelete">
                                    {{ isDeleting ? 'Removing...' : 'Remove note' }}
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>
                </div>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>

<script setup lang="ts">
import { getLocalTimeZone, today, type DateValue } from '@internationalized/date';
import { useLiveQuery } from '@tanstack/vue-db';
import { usePage } from '@inertiajs/vue3';
import { computed, inject, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import InputError from '@/components/InputError.vue';
import MonthSelector from '@/components/filter/MonthSelector.vue';
import { useCollections } from '@/composables/useCollections.js';
import { useEditor } from '@/composables/useEditor.js';
import { useFilters } from '@/composables/userFiters.js';
import type { App } from '@/wayfinder/types.js';
import Button from '../ui/button/Button.vue';
import Popover from '../ui/popover/Popover.vue';
import PopoverContent from '../ui/popover/PopoverContent.vue';
import PopoverTrigger from '../ui/popover/PopoverTrigger.vue';
import Dialog from '../ui/dialog/Dialog.vue';
import DialogClose from '../ui/dialog/DialogClose.vue';
import DialogContent from '../ui/dialog/DialogContent.vue';
import DialogDescription from '../ui/dialog/DialogDescription.vue';
import DialogFooter from '../ui/dialog/DialogFooter.vue';
import DialogTitle from '../ui/dialog/DialogTitle.vue';
import DialogTrigger from '../ui/dialog/DialogTrigger.vue';
import Input from '../ui/input/Input.vue';
import Label from '../ui/label/Label.vue';
import Select from '../ui/select/Select.vue';
import SelectContent from '../ui/select/SelectContent.vue';
import SelectItem from '../ui/select/SelectItem.vue';
import SelectTrigger from '../ui/select/SelectTrigger.vue';
import SelectValue from '../ui/select/SelectValue.vue';
import Sheet from '../ui/sheet/Sheet.vue';
import SheetClose from '../ui/sheet/SheetClose.vue';
import SheetContent from '../ui/sheet/SheetContent.vue';
import SheetDescription from '../ui/sheet/SheetDescription.vue';
import SheetFooter from '../ui/sheet/SheetFooter.vue';
import SheetHeader from '../ui/sheet/SheetHeader.vue';
import SheetTitle from '../ui/sheet/SheetTitle.vue';

const { editorSheet, currentNoteId, closeEditor } = useEditor();
const { startDate } = useFilters();
const budgetId = inject<number>('BUDGET_ID');
const page = usePage();
const { categoriesCollection, postesCollection, notesCollection } =
    useCollections();
const confirmDeleteOpen = ref(false);
const isDeleting = ref(false);
const isSaving = ref(false);

const currentNote = ref<Partial<App.Models.Note> | null>(null);
const isCreate = computed(() => currentNoteId.value === 0);
const formErrors = ref<Record<string, string | string[] | undefined>>({});

const noteDate = computed<DateValue | undefined>({
    get: () => {
        if (!currentNote.value || currentNote.value.year === undefined || currentNote.value.month === undefined) {
            const activeDate = startDate.value ?? today(getLocalTimeZone());
            return activeDate.set({ day: 1 });
        }

        return today(getLocalTimeZone()).set({
            year: Number(currentNote.value.year),
            month: Number(currentNote.value.month) + 1,
            day: 1,
        });
    },
    set: (value) => {
        if (!currentNote.value || !value) {
            return;
        }

        currentNote.value.year = value.year;
        currentNote.value.month = value.month - 1;
        clearFieldError('year');
        clearFieldError('month');
    },
});

const noteDateLabel = computed(() => {
    const dateValue = noteDate.value;

    if (!dateValue) {
        return 'Select a month';
    }

    return Intl.DateTimeFormat(undefined, {
        year: 'numeric',
        month: 'short',
    }).format(dateValue.toDate(getLocalTimeZone()));
});

watch(
    () => page.props.errors,
    (errors) => {
        formErrors.value = (errors as Record<string, string | string[] | undefined>) ?? {};
    },
    { immediate: true, deep: true },
);

const syncCreateDateFromFilter = () => {
    if (!isCreate.value) {
        return;
    }

    const activeDate = startDate.value ?? today(getLocalTimeZone());
    currentNote.value = {
        ...(currentNote.value ?? buildEmptyNote()),
        year: activeDate.year,
        month: activeDate.month - 1,
    };
};

watch(
    editorSheet,
    (open) => {
        if (!open) {
            formErrors.value = {};
            return;
        }

        if (isCreate.value) {
            syncCreateDateFromFilter();
        }
    },
);

watch(
    () => [startDate.value?.year, startDate.value?.month, editorSheet.value, isCreate.value],
    ([year, month, open, createMode]) => {
        if (!open || !createMode) {
            return;
        }

        if (typeof year !== 'number' || typeof month !== 'number') {
            return;
        }

        syncCreateDateFromFilter();
    },
    { immediate: true },
);

const fieldError = (field: string) => {
    const message = formErrors.value[field];

    if (Array.isArray(message)) {
        return message[0];
    }

    return message;
};

const clearFieldError = (field: string) => {
    if (!formErrors.value[field]) {
        return;
    }

    delete formErrors.value[field];
};

const { data: categories } = useLiveQuery((q) => {
    return q
        .from({ cat: categoriesCollection })
        .orderBy(({ cat }) => cat.label);
});

const { data: postes, isReady: postesReady } = useLiveQuery((q) => {
    return q
        .from({ poste: postesCollection })
        .orderBy(({ poste }) => poste.label);
});

const availablePostes = computed(() => {
    if (!currentNote.value?.category_id) {
        return [];
    }

    return postes.value.filter(
        (poste) => poste.category_id === currentNote.value?.category_id,
    );
});

const buildEmptyNote = (): Partial<App.Models.Note> => {
    const activeDate = startDate.value ?? today(getLocalTimeZone());

    return {
        id: 0,
        label: '',
        price: 0,
        year: activeDate.year,
        month: activeDate.month - 1,
        category_id: categories.value?.[0]?.id ?? 0,
        poste_id: null,
    };
};

watch(
    [currentNoteId, categories],
    () => {
        if (currentNoteId.value > 0) {
            const note = notesCollection.get(currentNoteId.value);
            currentNote.value = note ? { ...note } : buildEmptyNote();
            return;
        }

        currentNote.value = buildEmptyNote();
    },
    { immediate: true },
);

const onClose = (state: boolean) => {
    if (state === false) {
        closeEditor();
    }
};

const updateNoteLabel = (value: unknown) => {
    if (!currentNote.value) {
        return;
    }

    currentNote.value.label = String(value ?? '');
    clearFieldError('label');
};

const onPriceChange = (value: unknown) => {
    const rawValue = Number(value ?? 0);
    if (!currentNote.value) {
        return;
    }

    const roundedValue = Number.isFinite(rawValue)
        ? Math.round((rawValue + Number.EPSILON) * 100) / 100
        : 0;

    currentNote.value.price = roundedValue * 100;
    clearFieldError('price');
};

const onCategoryChange = (value: unknown) => {
    if (!currentNote.value) {
        return;
    }

    currentNote.value.category_id = Number(value ?? 0);
    currentNote.value.poste_id = null;
    clearFieldError('category_id');
    clearFieldError('poste_id');
};

const onPosteChange = (value: unknown) => {
    if (!currentNote.value) {
        return;
    }

    currentNote.value.poste_id = value === null || value === undefined ? null : Number(value);
    clearFieldError('poste_id');
};

const submit = async () => {
    if (!budgetId || !currentNote.value) {
        return;
    }

    const payload = {
        label: String(currentNote.value.label ?? '').trim() || null,
        price: Number(currentNote.value.price ?? 0),
        category_id: Number(currentNote.value.category_id ?? 0),
        poste_id:
            currentNote.value.poste_id === null || currentNote.value.poste_id === undefined
                ? null
                : Number(currentNote.value.poste_id),
        year: Number(currentNote.value.year),
        month: Number(currentNote.value.month),
    };

    isSaving.value = true;

    try {
        if (isCreate.value) {
            const tx = notesCollection.insert({
                ...payload,
                id: Date.now(),
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString(),
                user_id: 0,
            } as App.Models.Note);
            await tx.isPersisted.promise;
            closeEditor();
            toast.success('Note created');
            return;
        }

        const tx = notesCollection.update(currentNoteId.value, (draft) => {
            if (!currentNote.value) {
                return;
            }

            draft.category_id = payload.category_id;
            draft.poste_id = payload.poste_id;
            draft.price = payload.price;
            draft.label = payload.label;
            draft.year = payload.year;
            draft.month = payload.month;
        });
        await tx.isPersisted.promise;
        closeEditor();
        toast.success('Note updated');
    } catch (error: unknown) {
        const message =
            error && typeof error === 'object' && 'errors' in error
                ? Object.values((error as Record<string, unknown>).errors ?? {})
                    .flatMap((value) => (Array.isArray(value) ? value : [value]))
                    .filter((value): value is string => typeof value === 'string')
                    .at(0) ?? 'Unable to save the note.'
                : 'Unable to save the note.';

        toast.error(message);
    } finally {
        isSaving.value = false;
    }
};

const handleDelete = async () => {
    if (!budgetId || !currentNoteId.value || !currentNote.value) {
        return;
    }

    isDeleting.value = true;

    try {
        const tx = notesCollection.delete(currentNoteId.value);
        await tx.isPersisted.promise;
        closeEditor();
        toast.success('Note deleted');
    } catch (error: unknown) {
        toast.error('Error while deleting note');
        console.error(error);
    } finally {
        isDeleting.value = false;
        confirmDeleteOpen.value = false;
    }
};
</script>
