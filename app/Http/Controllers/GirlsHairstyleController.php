<?php

namespace App\Http\Controllers;

use App\Models\GirlsHairstyle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GirlsHairstyleController extends Controller
{
    /**
     * Display the girls hairstyle for the landing page
     */
    public function show()
    {
        $hairstyle = GirlsHairstyle::getLatest();
        return view('girls-hairstyles.show', compact('hairstyle'));
    }

    /**
     * Store a new girls hairstyle (replaces existing one)
     */
    public function upload(Request $request)
    {
        $request->validate([
            'hairstyle_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'term' => 'nullable|string|max:100',
            'session' => 'nullable|string|max:100',
        ]);

        // Delete existing hairstyle image if it exists
        $existingHairstyle = GirlsHairstyle::getLatest();
        if ($existingHairstyle && Storage::disk('public')->exists($existingHairstyle->image_path)) {
            Storage::disk('public')->delete($existingHairstyle->image_path);
            $existingHairstyle->delete();
        }

        // Store the new image
        $imagePath = $request->file('hairstyle_image')->store('girls-hairstyles', 'public');

        // Create new hairstyle record
        GirlsHairstyle::create([
            'image_path' => $imagePath,
            'term' => $request->term,
            'session' => $request->session,
        ]);

        return redirect()->back()->with('success', 'Girls hairstyle uploaded successfully!');
    }

    /**
     * Remove the girls hairstyle
     */
    public function destroy()
    {
        $hairstyle = GirlsHairstyle::getLatest();
        
        if ($hairstyle && Storage::disk('public')->exists($hairstyle->image_path)) {
            Storage::disk('public')->delete($hairstyle->image_path);
            $hairstyle->delete();
        }

        return redirect()->back()->with('success', 'Girls hairstyle deleted successfully!');
    }
}
