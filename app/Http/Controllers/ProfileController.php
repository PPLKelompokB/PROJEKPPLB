<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'photo_profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'photo_profile.image' => 'File harus berupa gambar.',
            'photo_profile.mimes' => 'Format foto harus berupa JPG, JPEG, atau PNG.',
            'photo_profile.max' => 'Ukuran foto maksimal adalah 2 MB.',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->location = $request->location;

        if ($request->hasFile('photo_profile')) {
            // Delete old photo if exists in storage (checking both photo_profile and photo fields)
            $oldPhotos = array_filter([$user->photo_profile, $user->photo]);
            foreach ($oldPhotos as $oldPhoto) {
                if (str_starts_with($oldPhoto, 'storage/')) {
                    $oldPath = str_replace('storage/', '', $oldPhoto);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }

            // Store new file
            $file = $request->file('photo_profile');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_photos', $fileName, 'public');

            // Save paths (synchronize photo_profile and photo for compatibility)
            $user->photo_profile = 'storage/' . $path;
            $user->photo = 'storage/' . $path;
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui.');
    }
}
