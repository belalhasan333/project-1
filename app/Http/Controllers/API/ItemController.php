<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    // List
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10); // default 10

        $items = Item::orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'next_page_url' => $items->nextPageUrl(),      // <-- Next page link
                'prev_page_url' => $items->previousPageUrl()  // <-- Previous page link
            ],
            'data' => $items->items()
        ]);
    }
    // Favorite toggle
    public function toggleFavorite($id)
    {
        $item = Item::findOrFail($id);

        $item->is_favorite = ! $item->is_favorite;
        $item->save();

        return response()->json([
            'message' => 'Favorite status updated',
            'is_favorite' => $item->is_favorite
        ]);
    }
}
