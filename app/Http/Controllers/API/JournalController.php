<?php

namespace App\Http\Controllers\Api;

use App\Models\Journal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class JournalController extends Controller
{
    public function index()
    {
        return Journal::with('category')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'description' => 'nullable',
            'voice_note' => 'nullable|file|mimes:mp3,wav,m4a',
            'images.*' => 'nullable|image'
        ]);

        // voice note
        $voicePath = null;
        if ($request->hasFile('voice_note')) {
            $path = $request->file('voice_note')->store('journals/voice', 'public');
            $voicePath = Storage::url($path);
        }

        // multiple images
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('journals/images', 'public');
                $images[] = Storage::url($path);
            }
        }

        $journal = Journal::create([
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'date' => $request->date,
            'description' => $request->description,
            'voice_note' => $voicePath,
            'images' => $images
        ]);

        return $journal->load('category');
    }


    public function show(Journal $journal)
    {
        $this->authorizeJournal($journal);
        return $journal->load('category');
    }

    public function update(Request $request, Journal $journal)
    {
        $this->authorizeJournal($journal);

        $journal->update($request->only([
            'title',
            'category_id',
            'date',
            'description'
        ]));

        return $journal;
    }

    public function destroy(Journal $journal)
    {
        $this->authorizeJournal($journal);
        $journal->delete();

        return response()->json(['message' => 'Journal deleted']);
    }

    private function authorizeJournal(Journal $journal)
    {
        abort_if($journal->user_id !== auth()->id(), 403);
    }
}
