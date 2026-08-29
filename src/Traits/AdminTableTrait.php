<?php

namespace AdminPlatform\Traits;

use Illuminate\Http\Request;

trait AdminTableTrait
{

    /**
     * Whitelist of columns allowed for sorting to prevent SQL injection.
     * Developers can extend this via the $config['sortable'] key.
     */
    private static array $defaultSortable = [
        'id', 'name', 'email', 'created_at', 'updated_at', 'is_active',
    ];

    public static function adminTable(Request $request, array $config = [], ?callable $transformCallback = null)
    {
        $model = new static;
        $query = $model->newQuery();

        $perPage = min((int) $request->get('per_page', 15), 100);
        $searchColumns = $config['search'] ?? [];
        $filters = $config['filters'] ?? [];

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search') && count($searchColumns)) {
            $search = $request->search;
            $query->where(function ($q) use ($searchColumns, $search) {
                foreach ($searchColumns as $col) {
                    $q->orWhere($col, 'LIKE', "%{$search}%");
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTERS
        |--------------------------------------------------------------------------
        */


        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->$filter);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SORT - با اعتبارسنجی امنیتی
        |--------------------------------------------------------------------------
        */

        $allowedSortColumns = array_merge(
            self::$defaultSortable,
            $config['sortable'] ?? []
        );

        if ($request->filled('sort')) {
            $sortField = $request->input('sort');
            $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

            if (in_array($sortField, $allowedSortColumns, true)) {
                $query->orderBy($sortField, $direction);
            }
        } else {
            $query->orderByDesc('id');
        }

        $rows = $query->paginate($perPage);

        /*
        |--------------------------------------------------------------------------
        | TRANSFORM
        |--------------------------------------------------------------------------
        */

        $data = $rows->items();

        if ($transformCallback && is_callable($transformCallback)) {
            $data = collect($data)->map($transformCallback);
        } elseif (method_exists(static::class, 'adminTransform')) {
            $data = collect($data)->map(fn($row) => static::adminTransform($row));
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        // ✅ اصلاح ساختار پاسخ - مطابق با expectation table.js

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $rows->total(),
            'per_page' => $rows->perPage(),
            'current_page' => $rows->currentPage(),
            'last_page' => $rows->lastPage(),
            'from' => $rows->firstItem(),
            'to' => $rows->lastItem(),
        ]);
    }
}
