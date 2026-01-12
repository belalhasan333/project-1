<?php

namespace App\Http\Controllers\Api;

use App\Models\Favorite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class FavoriteController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'id' => 'required|integer'
        ]);

        $modelClass = "App\\Models\\" . $request->type;

        if (!class_exists($modelClass)) {
            return response()->json(['message' => 'Invalid type'], 400);
        }

        $model = $modelClass::findOrFail($request->id);

        $favorite = Favorite::where([
            'user_id' => auth()->id(),
            'favoritable_id' => $model->id,
            'favoritable_type' => $modelClass
        ])->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['favorited' => false]);
        }

        Favorite::create([
            'user_id' => auth()->id(),
            'favoritable_id' => $model->id,
            'favoritable_type' => $modelClass
        ]);

        return response()->json(['favorited' => true]);
    }
}
