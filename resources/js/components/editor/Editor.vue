<template>
    <Sheet v-model:open="editorSheet" @update:open="onClose">
        <SheetContent>
            <SheetHeader>
                <SheetTitle>Edit note</SheetTitle>
                <SheetDescription>
                    Make changes to your note here. Click save when you're done.
                </SheetDescription>
            </SheetHeader>

            <div
                v-if="currentNote && currentCategory"
                class="grid flex-1 auto-rows-min gap-6 overflow-auto px-4"
            >
                <div class="grid gap-3">
                    <Label for="category">Catégorie</Label>
                    <Select id="category" v-model="currentNote.category_id">
                        <SelectTrigger class="w-full">
                            <SelectValue>{{
                                currentCategory.label
                            }}</SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            <template v-for="cat of categories" :key="cat.id">
                                <SelectItem :value="cat.id">
                                    {{ cat.label }}
                                </SelectItem>
                            </template>
                        </SelectContent>
                    </Select>
                </div>

                <div class="grid gap-3">
                    <Label for="category">Poste</Label>
                    <Select
                        id="category"
                        v-model="currentNote.poste_id"
                        :disabled="availablePoste.length === 0"
                    >
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select a poste" />
                        </SelectTrigger>
                        <SelectContent>
                            <template
                                v-for="poste of availablePoste"
                                :key="poste.id"
                            >
                                <SelectItem :value="poste.id">
                                    {{ poste.label }}
                                </SelectItem>
                            </template>
                        </SelectContent>
                    </Select>
                </div>

                <div class="grid gap-3">
                    <Label for="sheet-demo-username">Price</Label>
                    <Input
                        type="number"
                        id="sheet-demo-username"
                        :model-value="currentNote.price / 100"
                        @update:model-value="onUpdatePrice"
                    />
                </div>

                <div class="grid gap-3">
                    <Label for="sheet-demo-name">Name</Label>
                    <Input
                        id="sheet-demo-name"
                        :model-value="currentNote.label ?? undefined"
                        @update:model-value="onUpdateLabel"
                    />
                </div>
            </div>

            <SheetFooter>
                <Button :disabled="isSaving" type="submit" @click="onSave">
                    {{ isSaving ? 'Saving...' : 'Save changes' }}
                </Button>
                <SheetClose as-child>
                    <Button variant="outline" @click="onCancel"> Close </Button>
                </SheetClose>

                <Dialog v-model:open="confirmDeleteOpen">
                    <DialogTrigger as-child>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                        >
                            <Trash2 class="h-4 w-4" />
                            <span>Remove</span>
                        </Button>
                    </DialogTrigger>

                    <DialogContent>
                        <DialogTitle>Remove note</DialogTitle>
                        <DialogDescription>
                            Are you sure you want to remove this note ?
                        </DialogDescription>
                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button
                                    :disabled="isDeleting"
                                    variant="secondary"
                                    >Cancel</Button
                                >
                            </DialogClose>
                            <Button
                                variant="destructive"
                                :disabled="isDeleting"
                                @click="handleDelete"
                            >
                                {{ isDeleting ? 'Removing...' : 'Remove note' }}
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>

<script setup lang="ts">
import { Trash2 } from '@lucide/vue';
import { eq, useLiveQuery } from '@tanstack/vue-db';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { useCollections } from '@/composables/useCollections.js';
import { useEditor } from '@/composables/useEditor.js';
import type { App } from '@/wayfinder/types.js';
import Button from '../ui/button/Button.vue';
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
const { categoriesCollection, postesCollection, notesCollection } =
    useCollections();

const currentNote = ref<App.Models.Note>();
watch(currentNoteId, (newId) => {
    const note = notesCollection.get(newId);

    if (!note) {
        currentNote.value = undefined;

        return;
    }

    currentNote.value = { ...note };
});
const currentCategory = computed(() =>
    categoriesCollection.get(currentNote.value?.category_id),
);

watch(
    () => currentNote.value?.category_id,
    (_, oldId) => {
        if (!currentNote.value || !oldId) {
            return;
        }

        currentNote.value.poste_id = null;
    },
);

const { data: availablePoste } = useLiveQuery((q) => {
    return q
        .from({ p: postesCollection })
        .where(({ p }) => eq(p.category_id, currentCategory.value?.id))
        .orderBy(({ p }) => p.label);
});

const confirmDeleteOpen = ref(false);

const { data: categories } = useLiveQuery((q) => {
    return q
        .from({ cat: categoriesCollection })
        .orderBy(({ cat }) => cat.label);
});

const isDeleting = ref(false);

const handleDelete = async () => {
    isDeleting.value = true;

    try {
        const tx = notesCollection.delete(currentNoteId.value);
        await tx.isPersisted.promise;
        closeEditor();
        toast.success('Note deleted');
    } catch (error) {
        toast.error('Error while deleting note');
        console.error('Delete failed:', error);
    } finally {
        isDeleting.value = false;
        confirmDeleteOpen.value = false;
    }
};

const onCancel = () => {
    console.error('TODO');
};
const onClose = (state: boolean) => {
    if (state === false) {
        closeEditor();
    }
};

const isSaving = ref(false);
const onSave = async () => {
    isSaving.value = true;

    try {
        const tx = notesCollection.update(currentNoteId.value, (draft) => {
            if (!currentNote.value) {
                return;
            }

            draft.category_id = currentNote.value.category_id;
            draft.poste_id = currentNote.value.poste_id;
            draft.price = currentNote.value.price;
            draft.label = currentNote.value.label;
        });
        await tx.isPersisted.promise;
        closeEditor();
        toast.success('Note updated');
    } catch (error: unknown) {
        if (error instanceof Error && error.cause) {
            console.info('cause', error.cause);
            toast.error(error.cause.message);
        } else {
            toast.error('Error while updating note');
        }
    } finally {
        isSaving.value = false;
    }
};

const onUpdateLabel = (label: string | number) => {
    if (!currentNote.value) {
        return;
    }

    const newLabel = label.toString().trim();
    currentNote.value.label = newLabel.length == 0 ? null : newLabel;
};

const onUpdatePrice = (price: any) => {
    if (!currentNote.value) {
        return;
    }

    const newPrice = parseFloat(price);

    if (Number.isNaN(newPrice)) {
        return;
    }

    const roundedPrice = Math.trunc((newPrice * 10000) / 100);
    console.info('PRICE', price, newPrice, roundedPrice);
    currentNote.value.price = roundedPrice;
};
</script>
