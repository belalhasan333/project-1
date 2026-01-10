<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:profiles,email',
            'avater' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(['name', 'email']);

        if ($request->hasFile('avater')) {
            $file = $request->file('avater');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('profiles', $filename, 'public');

            $data['avater'] = $filename;
        }

        $profile = Profile::create($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $profile
        ], 201);
    }
}
