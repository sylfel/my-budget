<template>
    <MonthPickerRoot
        v-slot="{ grid }"
        v-model="currentDate"
        class="mt-1 rounded-xl border bg-white p-4 shadow-sm"
    >
        <MonthPickerHeader class="flex items-center justify-between">
            <MonthPickerPrev
                class="focus:shadow-green10 inline-flex size-8 cursor-pointer items-center justify-center rounded-md bg-transparent text-black hover:bg-stone-50 focus:shadow-[0_0_0_2px] active:scale-98 active:transition-all"
            >
                <ChevronLeft />
            </MonthPickerPrev>
            <MonthPickerHeading class="text-sm font-medium text-black" />
            <MonthPickerNext
                class="focus:shadow-green10 inline-flex size-8 cursor-pointer items-center justify-center rounded-md bg-transparent text-black hover:bg-stone-50 focus:shadow-[0_0_0_2px] active:scale-98 active:transition-all"
            >
                <ChevronRight />
            </MonthPickerNext>
        </MonthPickerHeader>
        <div class="pt-4">
            <MonthPickerGrid class="w-full border-collapse select-none">
                <MonthPickerGridBody class="grid gap-y-1">
                    <MonthPickerGridRow
                        v-for="(months, index) in grid.rows"
                        :key="`month-${index}`"
                        class="grid grid-cols-4 gap-x-1"
                    >
                        <MonthPickerCell
                            v-for="month in months"
                            :key="month.toString()"
                            :date="month"
                            class="relative text-center text-sm"
                        >
                            <MonthPickerCellTrigger
                                :month="month"
                                class="focus:shadow-green10 hover:bg-green5 relative flex size-12 items-center justify-center rounded-lg text-sm font-normal whitespace-nowrap text-black outline-none before:absolute before:top-1 before:hidden before:size-1 before:rounded-full before:bg-white focus:shadow-[0_0_0_2px] data-selected:bg-amber-600 data-[disabled]:text-black/30 data-[selected]:text-white data-[today]:before:block data-[today]:before:bg-amber-600 data-[unavailable]:pointer-events-none data-[unavailable]:text-black/30 data-[unavailable]:line-through"
                            />
                        </MonthPickerCell>
                    </MonthPickerGridRow>
                </MonthPickerGridBody>
            </MonthPickerGrid>
        </div>
    </MonthPickerRoot>
</template>

<script setup lang="ts">
import type { DateValue } from '@internationalized/date';
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import {
    MonthPickerCell,
    MonthPickerCellTrigger,
    MonthPickerGrid,
    MonthPickerGridBody,
    MonthPickerGridRow,
    MonthPickerHeader,
    MonthPickerHeading,
    MonthPickerNext,
    MonthPickerPrev,
    MonthPickerRoot,
} from 'reka-ui';
import { onMounted, ref, watch } from 'vue';

const model = defineModel<DateValue>();
const currentDate = ref<DateValue>();
onMounted(() => {
    currentDate.value = model.value?.copy();
});

watch(model, (curDate) => {
    if (!curDate) {
        return;
    }

    currentDate.value = curDate;
});

watch(currentDate, (newDate) => {
    model.value = newDate;
});
</script>
