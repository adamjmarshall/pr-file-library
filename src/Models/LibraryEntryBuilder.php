<?php

namespace AdamMarshall\FilamentFileLibrary\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * `entry_id` is a computed column (from a UNION of two real tables), not a
 * declared table column, so SQLite doesn't apply its usual integer type
 * affinity to it: comparing it against the string key Livewire always sends
 * back (e.g. "1") silently matches nothing, even though the underlying value
 * is a genuine integer. Casting the incoming key here fixes that at the
 * source, portably, rather than fighting per-driver SQL CAST syntax.
 *
 * @template TModel of LibraryEntry
 *
 * @extends Builder<TModel>
 */
class LibraryEntryBuilder extends Builder
{
    public function whereKey($id)
    {
        if (is_numeric($id) && ! is_array($id)) {
            $id = (int) $id;
        }

        return parent::whereKey($id);
    }
}
