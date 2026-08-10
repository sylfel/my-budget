import type { DateValue } from '@internationalized/date';
import { getLocalTimeZone } from '@internationalized/date';
import { computed, ref, watch } from 'vue';

const dateFormatter = Intl.DateTimeFormat(undefined, {
    year: 'numeric',
    month: 'short',
});

const startDate = ref<DateValue>();
const endDate = ref<DateValue>();
const usePeriod = ref(false);

watch(startDate, (newStart) => {
    if (!newStart || !endDate.value) {
        return;
    }

    if (newStart.compare(endDate.value) > 0 || !usePeriod.value) {
        endDate.value = newStart;
    }
});
watch(endDate, (newEnd) => {
    if (!newEnd || !startDate.value) {
        return;
    }

    if (newEnd.compare(startDate.value) < 0) {
        startDate.value = newEnd;
    }
});
watch(usePeriod, (newUsePeriod) => {
    if (newUsePeriod) {
        return;
    }

    endDate.value = startDate.value;
});

export const useFilters = () => {
    const startDateFilter = computed(() => {
        if (!startDate.value) {
            return '';
        }

        return `${startDate.value.year}${(startDate.value.month - 1).toFixed().padStart(2, '0')}`;
    });
    const endDateFilter = computed(() => {
        if (!endDate.value) {
            return '';
        }

        return `${endDate.value.year}${(endDate.value.month - 1).toFixed().padStart(2, '0')}`;
    });

    const startDateFormatted = computed(() => {
        if (!startDate.value) {
            return '';
        }

        return dateFormatter.format(startDate.value.toDate(getLocalTimeZone()));
    });

    const endDateFormatted = computed(() => {
        if (!endDate.value) {
            return '';
        }

        return dateFormatter.format(endDate.value.toDate(getLocalTimeZone()));
    });

    return {
        usePeriod,
        startDate,
        endDate,
        startDateFilter,
        endDateFilter,
        startDateFormatted,
        endDateFormatted,
    };
};
