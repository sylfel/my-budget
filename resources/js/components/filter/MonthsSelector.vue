<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button>{{ plageDate }}</Button>
        </PopoverTrigger>
        <PopoverContent class="w-80" align="start" side="right">
            <div class="flex items-center space-x-2">
                <Checkbox id="period" v-model="usePeriod" />
                <Label for="period">Période ?</Label>
            </div>
            <div class="my-2 text-center">{{ usePeriod ? 'De' : 'Mois' }}</div>
            <MonthSelector v-model="startDate" @click="handleStartDateClick" />
            <template v-if="usePeriod">
                <div class="my-2 text-center">à</div>
                <MonthSelector v-model="endDate" />
            </template>
        </PopoverContent>
    </Popover>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import MonthSelector from '@/components/filter/MonthSelector.vue';
import Button from '@/components/ui/button/Button.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import Label from '@/components/ui/label/Label.vue';
import Popover from '@/components/ui/popover/Popover.vue';
import PopoverContent from '@/components/ui/popover/PopoverContent.vue';
import PopoverTrigger from '@/components/ui/popover/PopoverTrigger.vue';
import { useFilters } from '@/composables/userFiters.js';

const open = ref(false);

const { startDate, endDate, startDateFormatted, endDateFormatted, usePeriod } =
    useFilters();

const plageDate = computed(() => {
    if (usePeriod.value) {
        return `De ${startDateFormatted.value} à ${endDateFormatted.value}`;
    }

    return startDateFormatted.value;
});

const handleStartDateClick = () => {
    if (usePeriod.value) {
        return;
    }

    open.value = false;
};
</script>
