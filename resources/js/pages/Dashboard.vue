<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import CategoryController from '@/wayfinder/App/Http/Controllers/CategoryController';
import NoteController from '@/wayfinder/App/Http/Controllers/NoteController';
import PosteController from '@/wayfinder/App/Http/Controllers/PosteController';
import { home } from '@/wayfinder/routes';
// import PlaceholderPattern from '@/components/PlaceholderPattern.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Budget',
                href: home(),
            },
        ],
    },
});

const catLoaded = ref({});
const posteLoaded = ref({});
const noteLoaded = ref({});

onMounted(() => {
    fetch(CategoryController.show.url(1))
        .then((response) => {
            return response.json();
        })
        .then((data) => (catLoaded.value = data));

    fetch(PosteController.show.url(1))
        .then((response) => {
            return response.json();
        })
        .then((data) => (posteLoaded.value = data));

    fetch(NoteController.show.url(1))
        .then((response) => {
            return response.json();
        })
        .then((data) => (noteLoaded.value = data));
});
</script>

<template>
    <Head title="Dashboard" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <h1>Specific data</h1>
        <details>
            <summary>Catégories</summary>
            {{ JSON.stringify(catLoaded) }}
        </details>
        <details>
            <summary>Postes</summary>
            {{ JSON.stringify(posteLoaded) }}
        </details>
        <details>
            <summary>Notes</summary>
            {{ JSON.stringify(noteLoaded) }}
        </details>
    </div>
</template>
