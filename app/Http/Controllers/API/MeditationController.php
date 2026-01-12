<?php

namespace App\Http\Controllers\Api;

use App\Models\Meditation;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MeditationController extends Controller
{
    // List of meditations
    public function index(Request $request)
    {
        $query = Meditation::with('category')
            ->where('user_id', auth()->id());

        if ($request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $meditations = $query->latest()->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'title' => $m->title,
                'image' => asset('storage/' . $m->cover_image),
                'audio' => asset('storage/' . $m->audio),
                'duration' => $m->duration,
                'category' => $m->category?->name,
            ];
        });

        return response()->json($meditations);
    }


    // Store meditation
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'category_id' => 'required|integer',
            'cover_image' => 'required|file|mimes:jpg,png,jpeg',
            'audio' => 'required|file|mimes:mp3,wav|max:10240',
            'duration' => 'required|integer',
        ]);

        $coverPath = $request->file('cover_image')->store('covers', 'public');
        $audioPath = $request->file('audio')->store('audios', 'public');

        // Create meditation
        $meditation = Meditation::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'category_id' => $request->category_id,
            'cover_image' => $coverPath,
            'audio' => $audioPath,
            'duration' => $request->duration,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Meditation created successfully',
            'data' => [
                'title' => $meditation->title,
                'slug' => $meditation->slug,
                'category_id' => $meditation->category_id,
                'cover_image' => asset('storage/' . $meditation->cover_image),
                'audio' => asset('storage/' . $meditation->audio),
                'duration' => $meditation->duration,
                'user_id' => $meditation->user_id,
                'created_at' => $meditation->created_at,
                'updated_at' => $meditation->updated_at,
                'id' => $meditation->id,
            ]
        ]);
    }




    // Play meditation
    public function show($id)
    {
        $m = Meditation::where('user_id', auth()->id())
            ->with('category')
            ->findOrFail($id);

        return response()->json([
            'id' => $m->id,
            'title' => $m->title,
            'image' => asset('storage/' . $m->cover_image),
            'audio' => asset('storage/' . $m->audio),
            'duration' => $m->duration,
            'category' => $m->category?->name
        ]);
    }
}
