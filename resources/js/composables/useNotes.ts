import { eq, gte, lte, useLiveQuery } from '@tanstack/vue-db';
import { useCollections } from '@/composables/useCollections';
import { useFilters } from '@/composables/userFiters';

export const useNotes = () => {
    const { startDateFilter, endDateFilter } = useFilters();

    const { categoriesCollection, postesCollection, notesCollection } =
        useCollections();

    const { collection: notesFiltered } = useLiveQuery(
        (q) =>
            q
                .from({ n: notesCollection })
                .join(
                    { c: categoriesCollection },
                    ({ n, c }) => eq(n.category_id, c.id),
                    'inner',
                )
                .join({ p: postesCollection }, ({ n, p }) =>
                    eq(n.poste_id, p.id),
                )
                .where(({ n }) => gte(n.year_month, startDateFilter.value))
                .where(({ n }) => lte(n.year_month, endDateFilter.value))
                .select(({ n, c, p }) => ({
                    ...n,
                    category: c.label,
                    poste: p.label,
                    credit: c.credit,
                })),
        [startDateFilter, endDateFilter],
    );

    return {
        notesFiltered,
    };
};
