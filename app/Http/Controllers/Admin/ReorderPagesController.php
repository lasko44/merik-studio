<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pages\ReorderPagesRequest;
use App\Services\PageService;
use Illuminate\Http\JsonResponse;

/**
 * Handles reordering pages via drag-and-drop.
 */
class ReorderPagesController extends Controller
{
    /**
     * Reorder pages based on the new sort order.
     */
    public function __invoke(ReorderPagesRequest $request, PageService $service): JsonResponse
    {
        $service->reorder($request->validated('page_ids'));

        return response()->json(['success' => true]);
    }
}
