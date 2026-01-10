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
        $query = Meditation::with('category'); // eager load category

        // filter by category slug
        if ($request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $meditations = $query->select('id', 'title', 'cover_image', 'audio', 'duration', 'category_id')
                             ->latest()
                             ->get()
                             ->map(function($m) {
                                 return [
                                     'id' => $m->id,
                                     'title' => $m->title,
                                     'image' => asset('storage/' . $m->cover_image),
                                     'audio' => asset('storage/' . $m->audio),
                                     'duration' => $m->duration,
                                     'category' => $m->category ? $m->category->name : null,
                                 ];
                             });

        return response()->json($meditations);
    }

    // Store meditation
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'category_id' => 'required|exists:categories,id', // category foreign key
            'cover_image' => 'required|image|mimes:jpg,png|max:5120',
            'audio' => 'required|mimes:mp3,wav,ogg|max:20480',
            'duration' => 'required|integer'
        ]);

        $cover = $request->file('cover_image')->store('covers', 'public');
        $audio = $request->file('audio')->store('audios', 'public');

        $meditation = Meditation::create([
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'category_id' => $data['category_id'],
            'cover_image' => $cover,
            'audio' => $audio,
            'duration' => $data['duration']
        ]);

        return response()->json($meditation, 201);
    }

    // Play meditation
    public function show($id)
    {
        $m = Meditation::with('category')->findOrFail($id);

        return response()->json([
            'id' => $m->id,
            'title' => $m->title,
            'image' => asset('storage/' . $m->cover_image),
            'audio' => asset('storage/' . $m->audio),
            'duration' => $m->duration,
            'category' => $m->category ? $m->category->name : null
        ]);
    }
}
