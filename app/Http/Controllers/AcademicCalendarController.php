<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AcademicCalendarController extends Controller
{
    /**
     * Display the academic calendar for the landing page
     */
    public function show()
    {
        $calendar = AcademicCalendar::getLatest();
        return view('academic-calendar.show', compact('calendar'));
    }

    /**
     * Store a new academic calendar (replaces existing one)
     */
    public function upload(Request $request)
    {
        $request->validate([
            'calendar_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'term' => 'nullable|string|max:100',
            'session' => 'nullable|string|max:100',
        ]);

        // Delete existing calendar image if it exists
        $existingCalendar = AcademicCalendar::getLatest();
        if ($existingCalendar && Storage::disk('public')->exists($existingCalendar->image_path)) {
            Storage::disk('public')->delete($existingCalendar->image_path);
            $existingCalendar->delete();
        }

        // Store the new image
        $imagePath = $request->file('calendar_image')->store('academic-calendars', 'public');

        // Create new calendar record
        AcademicCalendar::create([
            'image_path' => $imagePath,
            'term' => $request->term,
            'session' => $request->session,
        ]);

        return redirect()->back()->with('success', 'Academic calendar uploaded successfully!');
    }

    /**
     * Remove the academic calendar
     */
    public function destroy()
    {
        $calendar = AcademicCalendar::getLatest();
        
        if ($calendar && Storage::disk('public')->exists($calendar->image_path)) {
            Storage::disk('public')->delete($calendar->image_path);
            $calendar->delete();
        }

        return redirect()->back()->with('success', 'Academic calendar deleted successfully!');
    }
}
