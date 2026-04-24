<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsletterController extends Controller
{
    /**
     * Display the newsletter for the landing page
     */
    public function show()
    {
        $newsletter = Newsletter::getLatest();
        return view('newsletters.show', compact('newsletter'));
    }

    /**
     * Store a new newsletter (replaces existing one)
     */
    public function upload(Request $request)
    {
        $request->validate([
            'newsletter_pdf' => 'required|mimes:pdf|max:10240',
            'term' => 'nullable|string|max:100',
            'session' => 'nullable|string|max:100',
        ]);

        // Delete existing newsletter PDF if it exists
        $existingNewsletter = Newsletter::getLatest();
        if ($existingNewsletter && Storage::disk('public')->exists($existingNewsletter->pdf_path)) {
            Storage::disk('public')->delete($existingNewsletter->pdf_path);
            $existingNewsletter->delete();
        }

        // Store the new PDF
        $pdfPath = $request->file('newsletter_pdf')->store('newsletters', 'public');

        // Create new newsletter record
        Newsletter::create([
            'pdf_path' => $pdfPath,
            'term' => $request->term,
            'session' => $request->session,
        ]);

        return redirect()->back()->with('success', 'Newsletter uploaded successfully!');
    }

    /**
     * Remove the newsletter
     */
    public function destroy()
    {
        $newsletter = Newsletter::getLatest();
        
        if ($newsletter && Storage::disk('public')->exists($newsletter->pdf_path)) {
            Storage::disk('public')->delete($newsletter->pdf_path);
            $newsletter->delete();
        }

        return redirect()->back()->with('success', 'Newsletter deleted successfully!');
    }
}
